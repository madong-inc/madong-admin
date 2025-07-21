/**
 * 系统字典键常量
 * 注：实际数据通过API动态获取
 */
export const DictEnum = {
  // 基础数据类 
  SYS_LOCKED_STATUS:'core.enum.system.LockedStatus',
  SYS_ENABLED_STATUS:'core.enum.system.EnabledStatus',
  SYS_YES_NO: 'core.enum.system.YesNoStatus',
  SYS_SEX: 'core.enum.system.Sex',

  //数据字典
  SYS_DICT_DATA_TYPE: 'core.enum.system.DictDataType',
  SYS_DICT_GROUP_CODE: 'sys_dict_group_code',

  // 菜单权限类 
  SYS_MENU_TYPE: 'core.enum.system.MenuType',
  SYS_MENU_OPEN_TYPE: 'core.enum.system.MenuOpenType',
  SYS_REQUEST_MODE: 'core.enum.system.RequestMethod',

  // 用户角色类 
  SYS_USER_ADMIN_TYPE: 'core.enum.system.UserAdminType',
  SYS_ROLE_TYPE: 'core.enum.system.RoleType',
  SYS_DATA_PERMISSION:'core.enum.system.DataPermission',

  // 定时任务类 
  SYS_MONITOR_CRONTAB_MODE: 'core.enum.system.TaskScheduleMode',//执行模式
  SYS_MONITOR_CRONTAB_TYPE: 'core.enum.system.TaskScheduleType',//任务类型
  SYS_MONITOR_CRONTAB_CYCLE: 'core.enum.system.TaskScheduleCycle',//任务规则
  SYS_MONITOR_CRONTAB_WEEK:'core.enum.system.Week',

  // 服务器监控类 
  SYS_MONITOR_SERVER_CPU: 'monitor_server_cpu',
  SYS_MONITOR_SERVER_MEMORY: 'monitor_server_memory',

  //通知公告
  SYS_NOTICE_TYPE: 'core.enum.system.NoticeType',
  //我的消息
  SYS_MESSAGE_TYPE: 'core.enum.system.MessageType',
  SYS_MESSAGE_STATUS: 'core.enum.system.MessageStatus',
  SYS_MESSAGE_PRIORITY: 'core.enum.system.MessagePriority',
  
  //系统设置
  SYS_CLOUD_STORAGE:'core.enum.system.CloudStorage',

  //操作结果
  SYS_OPERATION_RESULT:'core.enum.system.OperationResult',

  //平台管理
  SYS_DB_TYPE:'core.enum.system.DbType',
  SYS_ISOLATION_MODE:'core.enum.platform.IsolationMode',
  SYS_TENANT_ADMIN_TYPE:'core.enum.system.TenantAdminType'



} as const;

export type DictEnumKey = keyof typeof DictEnum;
