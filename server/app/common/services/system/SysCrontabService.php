<?php

namespace app\common\services\system;

use app\common\dao\system\SysCrontabDao;
use core\abstract\BaseService;
use core\enum\system\OperationResult;
use core\enum\system\TaskScheduleCycle;
use core\exception\handler\AdminException;
use core\scheduler\Client;
use core\scheduler\event\EventBootstrap;
use support\Container;

/**
 * 定时任务
 *
 * @author Mr.April
 * @since  1.0
 */
class SysCrontabService extends BaseService
{
    public function __construct()
    {
        $this->dao = Container::make(SysCrontabDao::class);
    }

    /**
     * 添加定时任务
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Throwable
     */
    public function save(array $data): mixed
    {
        try {
            return $this->transaction(function () use ($data) {
                $title      = $data['title'] ?? '';
                $type       = $data['type'] ?? '';
                $target     = $data['target'] ?? '';
                $status     = $data['enabled'] ?? 1;
                $singleton  = $data['singleton'] ?? 1;
                $task_cycle = TaskScheduleCycle::from($data['task_cycle'] ?? 1);
                $month      = $data['month'] ?? '';
                $week       = $data['week'] ?? '';
                $day        = $data['day'] ?? '';
                $hour       = $data['hour'] ?? '';
                $minute     = $data['minute'] ?? '';
                $second     = $data['second'] ?? '';
                $remark     = $data['remark'] ?? '';

                //验证传入参数
                $this->validateTaskData($task_cycle, $minute, $hour, $day, $week, $month, $second);

                //生成表达式规则
                $rule = $this->generateCronRule($task_cycle, $minute, $hour, $day, $week, $month, $second);

                //更新到数据库
                $insertData = [
                    'title'      => $title,
                    'type'       => $type,
                    'rule'       => $rule,
                    'target'     => $target,
                    'enabled'    => $status,
                    'singleton'  => $singleton,
                    'task_cycle' => $task_cycle,
                    'cycle_rule' => [
                        'month'  => $month,
                        'week'   => $week,
                        'day'    => $day,
                        'hour'   => $hour,
                        'minute' => $minute,
                        'second' => $second,
                    ],
                    'remark'     => $remark,
                ];
                $model      = $this->dao->save($insertData);
                //添加定时任务重启服务
                if (!empty($model)) {
                    $pk = $model->getPk();
                    $this->requestData($model->getData($pk));
                }
                return $model;
            });
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 更新定时任务
     *
     * @param       $id
     * @param array $data
     *
     * @throws \Throwable
     */
    public function update($id, array $data): void
    {
        try {
            $this->transaction(function () use ($id, $data) {
                $title      = $data['title'] ?? '';
                $type       = $data['type'] ?? '';
                $target     = $data['target'] ?? '';
                $status     = $data['enabled'] ?? 1;
                $singleton  = $data['singleton'] ?? 1;
                $task_cycle = TaskScheduleCycle::from($data['task_cycle'] ?? 1);
                $month      = $data['month'] ?? '';
                $week       = $data['week'] ?? '';
                $day        = $data['day'] ?? '';
                $hour       = $data['hour'] ?? '';
                $minute     = $data['minute'] ?? '';
                $second     = $data['second'] ?? '';
                $remark     = $data['remark'] ?? '';
                //验证传入参数
                $this->validateTaskData($task_cycle, $minute, $hour, $day, $week, $month, $second);

                //生成表达式规则
                $rule = $this->generateCronRule($task_cycle, $minute, $hour, $day, $week, $month, $second);

                // 更新到数据库
                $this->dao->update($id, [
                    'title'      => $title,
                    'type'       => $type,
                    'rule'       => $rule,
                    'target'     => $target,
                    'enabled'    => $status,
                    'singleton'  => $singleton,
                    'task_cycle' => $task_cycle->value,
                    'cycle_rule' => [
                        'month'  => $month,
                        'week'   => $week,
                        'day'    => $day,
                        'hour'   => $hour,
                        'minute' => $minute,
                        'second' => $second,
                    ],
                    'remark'     => $remark,
                ]);

                // 更新之后重启服务
                $this->requestData($id);
            });
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 定时任务删除
     *
     * @param array|int|string $id
     *
     * @return bool
     * @throws \Throwable
     */
    public function destroy(array|int|string $id): bool
    {
        try {
            return $this->transaction(function () use ($id) {
                //1.0 先关闭再删除,避免删了后直接连不上服务的情况出现
                $this->dao->update([['id', 'in', $id]], ['enabled' => 0]);
                //2.0 重启任务
                $this->requestData($id);
                //3.0 删除定时任务跟日志数据
                $this->dao->destroy($id);
                $systemCrontabLogService = Container::make(SysCrontabLogService::class);
                $systemCrontabLogService->delete(['crontab_id' => $id]);
                return true;
            });
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 恢复任务
     *
     * @param string|int|array $data
     *
     * @throws \Throwable
     */
    public function resumeCrontab(string|int|array $data): void
    {
        try {
            $this->transaction(function () use ($data) {
                $this->dao->update([['id', 'in', $data]], ['enabled' => 1]);//更改启用
                $result = $this->requestData($data);
                if (!$result) {
                    throw new AdminException('恢复失败');
                }
            });
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     *暂停任务
     *
     * @param string|int|array $data
     *
     * @throws \Throwable
     */
    public function pauseCrontab(string|int|array $data): void
    {
        try {
            $this->transaction(function () use ($data) {
                $this->dao->update([['id', 'in', $data]], ['enabled' => 0]);//更改禁用
                $result = $this->requestData($data);
                if (!$result) {
                    throw new AdminException('重启失败');
                }
            });
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 获取所有任务
     *
     * @param string $field
     *
     * @return array
     */
    public function getTaskAll(string $field = '*'): array
    {
        return $this->dao->selectList(['enabled' => 1], $field, 0, 0, '', [], true)->toArray();
    }

    /**
     * 传入ID 获取指定的一条任务
     *
     * @param $id
     *
     * @return mixed
     */
    public function getTask($id): mixed
    {
        return $this->dao->get($id);
    }

    /**
     * 运行单个任务
     *
     * @param $id
     *
     * @return array
     */
    public function runOneTask($id): array
    {
        /**  @var $task_handle EventBootstrap[] */
        $task_handle = config('core.scheduler.app.task_handle', []);
        $crontab     = $this->dao->get($id);
        $start_time  = microtime(true);
        try {
            if (empty($crontab)) {
                throw new \Exception('执行任务失败, 任务不存在');
            }
            if (!isset($task_handle[$crontab->getData('type')])) {
                throw new \Exception('执行任务失败, 任务类型错误: ');
            }

            $result_data = $task_handle[$crontab['type']]::parse($crontab);
            // 记录执行信息
            $crontab->last_running_time = $start_time;
            $crontab->increment('running_times');
            if ($crontab->singleton == 0) {
                // 单次任务 直接停用
                $crontab->enabled = 0;
            }
            if (!$crontab->save()) {
                throw new \Exception('记录保存失败');
            }
            $end_time = microtime(true);
            if (strlen($result_data['log']) > 300) {
                $result_data['log'] = mb_substr($result_data['log'], 0, 300) . '...';
            }
            // 写入执行日志
            $installData       = [
                'crontab_id'   => $crontab['id'] ?? '',
                'target'       => $crontab['target'] ?? '',
                'log'          => $result_data['log'] ?? '--',
                'return_code'  => $result_data['code'] ?? OperationResult::FAILURE->value,
                'running_time' => round($end_time - $start_time, 6),
                'created_at'   => $start_time,
            ];
            $crontabLogService = Container::make(SysCrontabLogService::class);
            $crontabLogModel   = $crontabLogService->dao->save($installData);
            if (empty($crontabLogModel)) {
                throw new \Exception('日志记录保存失败');
            }
        } catch (\Exception $e) {
            return ['code' => 1, 'log' => $e->getMessage() . '---任务id: ' . $id];
        }
        return [
            'code'       => $installData['return_code'],
            'log'        => $installData['log'],
            'crontab_id' => $installData['crontab_id'],
        ];
    }

    /**
     * 重启任务
     *
     * @param int|string|array $id_str int|string 需要重启的任务id,多个id用，拼接，例：1,2,3,4,5
     *
     * @return bool
     * @throws \Exception
     */
    public function requestData(int|string|array $id_str): bool
    {
        // 如果是数组，使用 implode 连接为字符串；如果是单个值，直接使用
        $ids = is_array($id_str) ? implode(',', $id_str) : $id_str;
        // 重启任务
        $param = ['method' => 'crontabReload', 'args' => ['id' => $ids]];
        return Client::request($param);
    }

    /**
     * 验证器
     *
     * @param \core\enum\system\TaskScheduleCycle       $task_cycle
     * @param                                           $minute
     * @param                                           $hour
     * @param                                           $day
     * @param                                           $week
     * @param                                           $month
     * @param                                           $second
     *
     * @throws \Exception
     */
    private function validateTaskData(TaskScheduleCycle $task_cycle, $minute, $hour, $day, $week, $month, $second): void
    {
        switch ($task_cycle) {
            case TaskScheduleCycle::DAILY:
                $this->validateInteger($hour, "请输入执行小时", 23);
                $this->validateInteger($minute, "请输入执行分钟", 59);
                break;
            case TaskScheduleCycle::HOURLY:
                $this->validateInteger($minute, "请输入执行分钟", 59);
                break;
            case TaskScheduleCycle::WEEKLY:
                $this->validateInteger($week, "请输入星期几执行", 6);
                $this->validateInteger($hour, "请输入执行小时", 23);
                $this->validateInteger($minute, "请输入执行分钟", 59);
                break;
            case TaskScheduleCycle::MONTHLY:
                $this->validateInteger($day, "请输入执行天数", 31);
                $this->validateInteger($hour, "请输入执行小时", 23);
                $this->validateInteger($minute, "请输入执行分钟", 59);
                break;
            case TaskScheduleCycle::YEARLY:
                $this->validateInteger($month, "请输入执行月份", 12);
                $this->validateInteger($day, "请输入执行天数", 31);
                $this->validateInteger($hour, "请输入执行小时", 23);
                $this->validateInteger($minute, "请输入执行分钟", 59);
                break;
            case TaskScheduleCycle::N_HOURS:
                $this->validateInteger($hour, "请输入N小时", 23);
                break;
            case TaskScheduleCycle::N_MINUTES:
                $this->validateInteger($minute, "请输入N分钟", 59);
                break;
            case TaskScheduleCycle::N_SECONDS:
                $this->validateInteger($second, "请输入N秒数", 59);
                if (60 % (int)$second !== 0) {
                    throw new \Exception('秒级任务必须是60的因数');
                }
                break;
            default:
                throw new \Exception("任务周期不正确");
        }
    }

    /**
     * 定义验证规则器
     *
     * @throws \Exception
     */
    private function validateInteger($value, $message, $max): void
    {
        if (!is_numeric($value) || (int)$value < 0 || (int)$value > $max) {
            throw new \Exception($message);
        }
    }

    /**
     * 生成任务表达式
     *
     * @param \core\enum\system\TaskScheduleCycle       $task_cycle
     * @param                                           $minute
     * @param                                           $hour
     * @param                                           $day
     * @param                                           $week
     * @param                                           $month
     * @param                                           $second
     *
     * @return string
     */
    private function generateCronRule(TaskScheduleCycle $task_cycle, $minute, $hour, $day, $week, $month, $second): string
    {
        return match ($task_cycle) {
            TaskScheduleCycle::DAILY => "0 {$minute} {$hour} * * *",
            TaskScheduleCycle::HOURLY => "0 {$minute} * * * *",
            TaskScheduleCycle::WEEKLY => "0 {$minute} {$hour} * * {$week}",
            TaskScheduleCycle::MONTHLY => "0 {$minute} {$hour} {$day} * *",
            TaskScheduleCycle::YEARLY => "0 {$minute} {$hour} {$day} {$month} *",
            TaskScheduleCycle::N_HOURS => "0 {$minute} */{$hour} * * *",
            TaskScheduleCycle::N_MINUTES => "0 */{$minute} * * * *", // N分钟
            TaskScheduleCycle::N_SECONDS => "*/{$second} * * * * *", // N秒
        };
    }
}
