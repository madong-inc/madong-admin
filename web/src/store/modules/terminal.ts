import { nextTick, reactive } from 'vue';
import { defineStore } from 'pinia';

import { Terminal, TerminalApi } from '#/api/dev/terminal';
import { taskStatus } from '#/components/common/constants';
import { timestampFormat, uuid } from '#/utils';
import { $t } from '#/locale';
import { notification } from 'ant-design-vue';


const api = new TerminalApi();

export const useTerminalStore = defineStore(
  'app-terminal',
  () => {
    const state: Terminal = reactive({
      // 显示终端窗口
      show: true,
      // 在后台终端按钮上显示一个红点
      showDot: false,
      // 任务列表
      taskList: [],
      // 包管理器
      packageManager: 'npm',
      // 显示包管理器切换窗口
      showPackageManagerDialog: false,
      // 显示终端设置窗口
      showConfig: false,
      // 开始任务时自动清理已完成任务
      automaticCleanupTask: '0',
      // npm源
      npmRegistry: 'unknown',
      // composer源
      composerRegistry: 'unknown',
    })


    /**
      * 初始化终端状态 
      * - 遍历任务列表，将处于连接中(Connecting)或执行中(Executing)的任务状态重置为未知(Unknown)
      * - 用于应用启动时恢复中断的任务状态 
      */
    function init() {
      for (const key in state.taskList) {
        if (state.taskList[key].status == taskStatus.Connecting || state.taskList[key].status == taskStatus.Executing) {
          state.taskList[key].status = taskStatus.Unknown
        }
      }
    }


    /**
     * 切换终端窗口显示状态 
     * @param val - 可选参数，指定显示/隐藏状态（默认取反当前状态）
     * - 显示窗口时会自动清除后台红点提示 
     */
    function toggle(val = !state.show) {
      state.show = val
      if (val) {
        toggleDot(false)
      }
    }

    /**
   * 切换后台红点提示状态
   * @param val - 可选参数，指定红点显示/隐藏（默认取反当前状态）
   * - 当有新任务添加且终端未打开时触发 
   */
    function toggleDot(val = !state.showDot) {
      state.showDot = val
    }

    /**
    * 切换终端设置弹窗显示
    * @param val - 可选参数，指定显示/隐藏（默认取反当前状态）
    * - 打开设置时会自动关闭主终端窗口 
    */
    function toggleConfigDialog(val = !state.showConfig) {
      toggle(!val)
      state.showConfig = val
    }


    /**
   * 切换包管理器选择弹窗 
   * @param val - 可选参数，指定显示/隐藏（默认取反当前状态）
   * - 打开弹窗时会自动关闭主终端窗口
   */
    function togglePackageManagerDialog(val = !state.showPackageManagerDialog) {
      toggle(!val)
      state.showPackageManagerDialog = val
    }


    /**
     * 设置数据源
     * @param val 
     * @param type 
     */
    function changeRegistry(val: string, type: 'npm' | 'composer') {
      state[type == 'npm' ? 'npmRegistry' : 'composerRegistry'] = val
    }


    /**
     * 修改包管理器类型
     * @param val - 新的包管理器名称 (如 'npm'/'yarn'/'pnpm')
     */
    function changePackageManager(val: string) {
      state.packageManager = val
    }



    /**
     * 修改任务自动清理策略
     * @param val - '0' 禁用 / '1' 启用
     * - 启用时添加新任务会自动清理已完成任务 
     */
    function changeAutomaticCleanupTask(val: '0' | '1') {
      state.automaticCleanupTask = val
    }



    /**
     * 更新任务状态
     * @param idx - 任务索引 
     * @param status - 新状态（参考 taskStatus 枚举）
     * - 若任务标记为失败且配置了阻塞，自动展开日志面板 
     */
    function setTaskStatus(idx: number, status: number) {
      state.taskList[idx].status = status
      if ((status == taskStatus.Failed || status == taskStatus.Unknown) && state.taskList[idx].blockOnFailure) {
        setTaskShowMessage(idx, true)
      }
    }


    /**
     * 任务完成回调处理
     * @param idx - 任务索引 
     * - 根据成功/失败状态调用任务的 callback 函数
     */
    function taskCompleted(idx: number) {
      if (typeof state.taskList[idx].callback != 'function') {
        return
      }
      const status = state.taskList[idx].status
      if (status == taskStatus.Failed || status == taskStatus.Unknown) {
        state.taskList[idx].callback(taskStatus.Failed)
      } else if (status == taskStatus.Success) {
        state.taskList[idx].callback(taskStatus.Success)
      }
    }

    /**
     * 切换任务日志面板显示
     * @param idx - 任务索引 
     * @param val - 可选参数，指定显示/隐藏（默认取反当前状态）
     */
    function setTaskShowMessage(idx: number, val = !state.taskList[idx].showMessage) {
      state.taskList[idx].showMessage = val
    }


    /**
     * 追加任务日志内容 
     * @param idx - 任务索引
     * @param message - 要追加的日志字符串
     * - 自动滚动日志面板到底部
     * - 若终端未打开则触发红点提示 
     */
    function addTaskMessage(idx: number, message: string) {
      if (!state.show) toggleDot(true)
      state.taskList[idx].message = state.taskList[idx].message.concat(message)
      nextTick(() => {
        execMessageScrollbarKeep(state.taskList[idx].uuid)
      })
    }


    /**
     * 添加普通终端任务 
     * @param command - 待执行的命令字符串
     * @param blockOnFailure - 任务失败时是否阻塞后续任务（默认 true）
     * @param extend - 附加参数 
     * @param callback - 任务完成回调函数
     * - 自动触发后台红点提示
     * - 根据配置自动清理已完成任务
     */
    function addTask(command: string, blockOnFailure = true, extend = '', callback: Function = () => { }) {
      if (!state.show) {
        toggleDot(true)
      }
      state.taskList = state.taskList.concat({
        uuid: uuid(),
        date: timestampFormat(),
        status: taskStatus.Waiting,
        command: command,
        message: [],
        showMessage: false,
        blockOnFailure: blockOnFailure,
        extend: extend,
        callback: callback,
      })

      // 清理任务列表
      if (parseInt(state.automaticCleanupTask) === 1) {
        clearSuccessTask()
      }

      // 检查是否有已经失败的任务
      if (state.show === false) {
        for (const key in state.taskList) {
          if (state.taskList[key].status == taskStatus.Failed || state.taskList[key].status == taskStatus.Unknown) {
            notification.error({
              message: '失败任务提醒',
              duration: 5,
              description: $t('dev.terminal.task_blocking'),
            });
            break
          }
        }
      }
      startTask()
    }

    /**
     * 添加包管理器任务（自动拼接当前包管理器类型）
     * @example addTaskPM('install') → 执行 'install.pnpm' 
     */
    function addTaskPM(command: string, blockOnFailure = true, extend = '', callback: Function = () => { }) {
      addTask(command + '.' + state.packageManager, blockOnFailure, extend, callback)
    }

    /**
     * 删除指定任务 
     * @param idx - 任务索引
     * - 仅允许删除非运行状态的任务
     * - 删除后自动触发任务调度
     */
    function delTask(idx: number) {
      if (state.taskList[idx].status != taskStatus.Connecting && state.taskList[idx].status != taskStatus.Executing) {
        state.taskList.splice(idx, 1)
      }
      startTask()
    }


    /**
     * 启动任务调度
     * - 按顺序检查等待中(Wating)任务 
     * - 遇到运行中或阻塞性失败任务时停止调度 
     * - 找到可执行任务后触发 EventSource 连接
     */
    function startTask() {
      let taskKey = null

      // 捕获可以开始执行的命令
      for (const key in state.taskList) {
        if (state.taskList[key].status == taskStatus.Waiting) {
          taskKey = parseInt(key)
          break
        }
        if (state.taskList[key].status == taskStatus.Connecting || state.taskList[key].status == taskStatus.Executing) {
          break
        }
        if (state.taskList[key].status == taskStatus.Success) {
          continue
        }
        if (state.taskList[key].status == taskStatus.Failed || state.taskList[key].status == taskStatus.Unknown) {
          if (state.taskList[key].blockOnFailure) {
            break
          } else {
            continue
          }
        }
      }
      if (taskKey !== null) {
        setTaskStatus(taskKey, taskStatus.Connecting)
        startEventSource(taskKey)
      }
    }


    function startEventSource(taskKey: number) {
      const { command, uuid, extend } = state.taskList[taskKey];
      const url = api.command(command, uuid, extend);

      // 1. 创建 EventSource 实例
      const eventSource = new EventSource(url);

      // 2. message 事件（兼容旧格式）
      eventSource.onmessage = (event) => {
        try {
          const parsed = JSON.parse(event.data);
          console.log('Default message:', parsed);

          if (!parsed?.data) return;
          const taskIdx = findTaskIdxFromUuid(parsed.uuid);
          if (taskIdx === false) return;

          // 如果没有 event 字段，默认当作普通消息处理
          addTaskMessage(taskIdx, parsed.data);
        } catch (error) {
          console.error('Error processing default message:', error);
        }
      };

      // 3. 自定义事件-command-success
      eventSource.addEventListener('command-success', (event) => {
        try {
          const parsed = JSON.parse(event.data);
          const taskIdx = findTaskIdxFromUuid(parsed.uuid);
          if (taskIdx === false) return;

          setTaskStatus(taskIdx, taskStatus.Success);
          closeEventSource(eventSource); // 关闭连接
          taskCompleted(taskIdx);
          startTask();
        } catch (error) {
          console.error('Error processing command-success:', error);
        }
      });

      // 4. 监听事件-command-error
      eventSource.addEventListener('command-error', (event) => {
        try {
          const parsed = JSON.parse(event.data);
          const taskIdx = findTaskIdxFromUuid(parsed.uuid);
          if (taskIdx === false) return;

          setTaskStatus(taskIdx, taskStatus.Failed);
          closeEventSource(eventSource);
          taskCompleted(taskIdx);
          startTask();
        } catch (error) {
          console.error('Error processing command-error:', error);
        }
      });

      /**
       * 监听事件-command-completed
       */
      eventSource.addEventListener('command-completed', (event) => {
        try {
          const parsed = JSON.parse(event.data);
          const taskIdx = findTaskIdxFromUuid(parsed.uuid);
          if (taskIdx === false) return;

          closeEventSource(eventSource);
          if (state.taskList[taskIdx].status != taskStatus.Success) {
            setTaskStatus(taskIdx, taskStatus.Failed);
          }
          taskCompleted(taskIdx);
          startTask();
        } catch (error) {
          console.error('Error processing command-completed:', error);
        }
      });

      eventSource.addEventListener('command-link', (event) => {
        try {
          const parsed = JSON.parse(event.data);
          const taskIdx = findTaskIdxFromUuid(parsed.uuid);
          if (taskIdx === false) return;

          setTaskStatus(taskIdx, taskStatus.Executing);
        } catch (error) {
          console.error('Error processing command-link:', error);
        }
      });

      // 4. 关闭连接的函数
      const closeEventSource = (es: EventSource) => {
        es.close();
      };

    }


    /**
     * 重试失败任务 
     * @param idx - 任务索引
     * - 清空原有日志，状态重置为等待中(Waiting)
     * - 重新加入任务调度队列
     */
    function retryTask(idx: number) {
      state.taskList[idx].message = []
      setTaskStatus(idx, taskStatus.Waiting)
      startTask()
    }

    /**
     * 清理所有成功完成的任务
     * - 根据 state.automaticCleanupTask  配置自动触发 
     */
    function clearSuccessTask() {
      state.taskList = state.taskList.filter((item) => item.status != taskStatus.Success)
    }


    /**
     * 通过UUID查找任务索引
     * @returns 索引值 (找不到返回 false)
     */
    function findTaskIdxFromUuid(uuid: string) {
      for (const key in state.taskList) {
        if (state.taskList[key].uuid == uuid) {
          return parseInt(key)
        }
      }
      return false
    }

    /**
     * 智能猜测当前运行任务索引 
     * - 当索引失效时，自动查找状态为 Connecting/Executing 的任务
     * @returns 索引值 (找不到返回 false)
     */
    function findTaskIdxFromGuess(idx: number) {
      if (!state.taskList[idx]) {
        let taskKey = -1
        for (const key in state.taskList) {
          if (state.taskList[key].status == taskStatus.Connecting || state.taskList[key].status == taskStatus.Executing) {
            taskKey = parseInt(key)
          }
        }
        return taskKey === -1 ? false : taskKey
      } else {
        return idx
      }
    }

    /**
     * 保持任务日志滚动条在底部
     * @param uuid - 任务唯一ID 
     * - 通过DOM操作实现自动滚动
     */
    function execMessageScrollbarKeep(uuid: string) {
      const execMessageEl = document.querySelector('.exec-message-' + uuid)
      if (execMessageEl && execMessageEl.scrollHeight) {
        execMessageEl.scrollTop = execMessageEl.scrollHeight
      }
    }

    /**
     * 退出清空数据
     */
    function $reset() {
      state.taskList = [];
    }

    return {
      $reset,
      state,
      init,
      toggle,
      toggleDot,
      setTaskStatus,
      setTaskShowMessage,
      addTaskMessage,
      addTask,
      addTaskPM,
      delTask,
      startTask,
      retryTask,
      clearSuccessTask,
      togglePackageManagerDialog,
      toggleConfigDialog,
      changePackageManager,
      changeRegistry,
      changeAutomaticCleanupTask,
    }
  },
  {
    persist: {
      pick: ['state.showDot', 'state.taskList', 'state.automaticCleanupTask', 'state.npmRegistry', 'state.composerRegistry'],
    }
  });
