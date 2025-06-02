/**
 * 系统字典键常量
 * 注：实际数据通过API动态获取
 */
export const DictEnum = {
  // 基础数据类 
  SYS_LOCKED_STATUS:'app.common.enum.system.LockedStatus',
  SYS_ENABLED_STATUS:'app.common.enum.system.EnabledStatus',
  SYS_YES_NO: 'app.common.enum.system.YesNoStatus',
  SYS_SEX: 'app.common.enum.system.Sex',

  //数据字典
  SYS_DICT_DATA_TYPE: 'app.common.enum.system.DictDataType',
  SYS_DICT_GROUP_CODE: 'sys_dict_group_code',

  // 菜单权限类 
  SYS_MENU_TYPE: 'app.common.enum.system.MenuType',
  SYS_MENU_OPEN_TYPE: 'app.common.enum.system.MenuOpenType',
  SYS_REQUEST_MODE: 'app.common.enum.system.RequestMethod',

  // 用户角色类 
  SYS_USER_ADMIN_TYPE: 'app.common.enum.system.UserAdminType',
  SYS_ROLE_TYPE: 'app.common.enum.system.RoleType',
  SYS_DATA_PERMISSION:'app.common.enum.system.DataPermission',

  // 定时任务类 
  SYS_MONITOR_CRONTAB_MODE: 'app.common.enum.system.TaskScheduleMode',//执行模式
  SYS_MONITOR_CRONTAB_TYPE: 'app.common.enum.system.TaskScheduleType',//任务类型
  SYS_MONITOR_CRONTAB_CYCLE: 'app.common.enum.system.TaskScheduleCycle',//任务规则
  SYS_MONITOR_CRONTAB_WEEK:'app.common.enum.system.Week',

  // 服务器监控类 
  SYS_MONITOR_SERVER_CPU: 'monitor_server_cpu',
  SYS_MONITOR_SERVER_MEMORY: 'monitor_server_memory',

  //通知公告
  SYS_NOTICE_TYPE: 'app.common.enum.system.NoticeType',
  //我的消息
  SYS_MESSAGE_TYPE: 'app.common.enum.system.MessageType',
  SYS_MESSAGE_STATUS: 'app.common.enum.system.MessageStatus',
  SYS_MESSAGE_PRIORITY: 'app.common.enum.system.MessagePriority',
  
  //系统设置
  SYS_CLOUD_STORAGE:'app.common.enum.system.CloudStorage',

  //操作结果
  SYS_OPERATION_RESULT:'app.common.enum.system.OperationResult',



} as const;

export type DictEnumKey = keyof typeof DictEnum;
