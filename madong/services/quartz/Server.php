<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */
namespace madong\services\quartz;

use madong\services\quartz\event\EventBootstrap;
use WebmanTech\SymfonyLock\Locker;
use Workerman\Connection\TcpConnection;
use Workerman\Crontab\Crontab;
use Workerman\Lib\Timer;
use Webman\RedisQueue\Client as RedisClient;
use Workerman\Worker;

/**
 * @method getAllTask() 获取所有任务
 * @method getTask($id) 获取单个任务
 * @method writeRunLog($insert_data = []) 写入运行日志
 * @method updateTaskRunState($id, $last_running_time) 更新任务状态
 */
class Server
{
    /**
     * @var Worker 进程
     */
    protected $worker;

    protected $redis;

    /**
     * 调试模式
     *
     * @var bool
     */
    protected $debug = false;
    /**
     * 是否运行定时任务呢
     *
     * @var bool
     */
    protected $enable = false;

    /**
     * 记录日志
     *
     * @var bool
     */
    protected $writeLog = true;

    protected $config = [];

    /**
     * 任务进程池
     *
     * @var Crontab[] array
     */
    private $crontabPool = [];

    /**
     * @var \Workerman\Redis\Client 订阅实例
     */
    protected $subscribeClient;

    /**
     * @var \Workerman\Redis\Client 通知实例
     */
    protected $publishClient;

    /**
     * @param Worker $worker
     *
     * @return void
     * 进程启动
     */
    public function onWorkerStart(Worker $worker)
    {
        $config         = config('plugin.wf.app');
        $this->debug    = $config['debug'] ?? true;
        $this->writeLog = $config['write_log'] ?? true;
        $this->enable   = $config['enable'] ?? true;

        if (!$this->enable) {
            $this->writeln('定时任务未开启，如需开启，请修改配置 \plugin\wf\config\app.php enable = true ');
            return false;
        }
        $this->writeln("定时任务消息通道：{$config['listen']}，请注意端口是否冲突，");
        $this->writeln("如果需要修改端口，请修改 \plugin\\wf\config\app.php，process.php 两个文件");

        $this->config = $config;
        $this->worker = $worker;
        //订阅事件
        $this->subscribeEvent();
        //初始化任务
        $this->crontabInit();
    }

    /**
     * @return void
     * 订阅事件
     */
    private function subscribeEvent()
    {

        //创建发布实例
        if (!$this->publishClient) {
            $this->publishClient = $this->redisCreate();
            $this->writeln('启用心跳');
            Timer::add(30, function () {//保持链接活跃
//                $this->writeln('发送心跳');
                $this->publishClient->send('ping', ['name' => 'ping', 'age' => 1]);
            });
        }

        // 创建订阅实例
        if (!$this->subscribeClient) {
            $this->subscribeClient = $this->redisCreate();
        }

        //订阅一个心跳链接
        $this->subscribeClient->subscribe('ping', function ($data) {
//            $this->writeln('收到心跳：'.json_encode($data,256));
        });

        //订阅任务改变事件
        $this->subscribeClient->subscribe('change_contrab', function ($data) {
            $args = $data['args'] ?? '';
            $this->crontabReload($args);
        });
    }

    function redisCreate()
    {
        $connection_name = 'default';
        try {
            $redis = RedisClient::connection($connection_name);
        } catch (\Exception $e) {
            $this->writeln('Redis初始化失败');
            return false;
        }
        return $redis;
    }

    /**
     * @param TcpConnection $connection
     * @param               $data
     *
     * @return void
     */
    public function onMessage(TcpConnection $connection, $data)
    {
        $data   = json_decode($data, true);
        $method = $data['method'] ?? '';
        $args   = $data['args'] ?? '';
        // 这里只有保留一个重启方法,对任务进行任何操作,直接调用重启解决
        if (!in_array($method, ['crontabReload'])) {
            $connection->send(json_encode(['code' => 400, 'msg' => "{$method} is not found"]));
            return;
        }
        $this->writeln('发送重启通知');
        //通知所有进程该任务进行重启
        $this->publishClient->send('change_contrab', ['method' => 'crontabReload', 'args' => $args]);
        $connection->send(json_encode(['code' => 200, 'msg' => "ok"]));
    }

    /**
     * 重启定时任务
     *
     * @param array $param
     */
    private function crontabReload(array $param): void
    {
        $ids = explode(',', (string)($param['id'] ?? ''));
        foreach ($ids as $id) {
            if (isset($this->crontabPool[$id])) {
                $this->writeln('销毁定时器，ID：' . $id);
                $this->crontabPool[$id]['crontab']->destroy();
                unset($this->crontabPool[$id]);
            }
            $this->crontabRun($id);
        }
    }

    /**
     * 初始化定时任务
     *
     * @return void
     */
    private function crontabInit()
    {
        $data = CrontabService::getTaskAll('id');
        $this->writeln('定时器任务数：' . count($data));
        if (!empty($data)) {
            foreach ($data as $item) {
                $this->crontabRun($item['id']);
            }
        }
    }

    /**
     * 创建定时器
     *
     * @param $id
     */
    private function crontabRun($id)
    {
        $data = CrontabService::getTask($id);
        if (empty($data)) {
            return;
        }
        if (intval($data['status']) === 0) {
            return;
        }
        $crontab = new Crontab($data['rule'], function () use (&$data) { //这里传参必须传引用
            //首次不执行
            if (!$data['firstStart']) {
                $data['firstStart'] = true;
                return false;
            }
            //运行次数加1,很重要,多进程情况下用来检测当前次数是否已执行
            $data['running_times'] += 1;
            $uuid                  = $this->createTaskUuid($data);
            //获取锁.
            $lock = Locker::cash($uuid);
            // 获取锁失败
            if (!$lock) return;

            try {
                $reData = CrontabService::runOneTask($data['id']);
                //$this->writeln( $data['title'].' 任务#' . $data['id'] . ' ' . $data['rule'] . ' ' . $data['target'], $reData['code']);
                $this->isSingleton($data);
            } finally {
                // 释放锁
                $lock->release();
            }
        });

        $this->crontabPool[$data['id']] = [
            'id'          => $data['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'crontab'     => $crontab,
        ];
    }

    /**
     * 是否单次
     *
     * @param $crontab
     *
     * @return void
     */
    private function isSingleton($crontab)
    {
        if ($crontab['singleton'] == 0 && isset($this->crontabPool[$crontab['id']])) {
            $this->writeln("定时器销毁", true);
            $this->crontabPool[$crontab['id']]['crontab']->destroy();
            unset($this->crontabPool[$crontab['id']]);
        }
    }

    /**
     * @param $crontab
     *
     * @return string
     * 创建任务uuid
     */
    private function createTaskUuid($crontab)
    {
        // 以任务id + 运行次数作为唯一id, 这样在过期范围内,只会有一个进程能执行到该次任务
        return 'crontab_' . $crontab['id'] . '_' . $crontab['running_times'];
    }

    /**
     * 输出日志
     *
     * @param     $msg
     * @param int $isSuccess
     */
    private function writeln($msg, bool $isSuccess = false)
    {
        if ($this->debug) {
            $workerId = $this->worker ? $this->worker->id : 0;
            echo 'worker:' . $workerId . ' [' . date('Y-m-d H:i:s') . '] ' . $msg . ($isSuccess == 0 ? " [Ok] " : " [Fail] ") . PHP_EOL;
        }
    }
}
