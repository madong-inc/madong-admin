
export interface SystemRoleRow {
  [key: string]: any;
  id: string;
  pid?: string;
  name: string
  code: string;
  is_super_admin: 0 | 1;
  role_type: number;
  data_scope: DataScopeEnum;
  enabled: 0 | 1;
  sort: number;
  permissions?: string[];
  remark?: string | null;
  created_by: number;
  updated_by: number;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  created_date: string;
  updated_date: string;
}


/**
 * 数据权限范围枚举
 */
export enum DataScopeEnum {
  ALL = 1,           // 全部数据权限
  CUSTOM = 2,        // 自定义权限 
  DEPT = 3,          // 本部门 
  DEPT_WITH_CHILD = 4, // 本部门及下级 
  SELF = 5           // 仅本人
}