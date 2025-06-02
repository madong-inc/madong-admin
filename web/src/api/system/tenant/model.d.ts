
export interface SystemTenantRow {
  id?: number; // 主键ID
  tenant_id: string; // 租户ID（推荐使用UUID字符串）
  package_id: number; // 套餐ID
  
  // 联系人信息
  contact_user_name: string;
  contact_phone: string;
  company_name: string;
  
  // 资质信息
  license_number?: string; // 可选营业执照号
  address?: string;
  // 套餐信息
  intro?: string; // 套餐介绍
  domain?: string; // 绑定域名
  account_count: number; // 账号数量配额
  enabled: number; // 启用状态（0/1映射）
  // 时间戳（使用number类型存储时间戳）
  created_at?: number; // 创建时间
  updated_at?: Date; // 更新时间（推荐使用Date类型）
  expired_at?: number; // 到期时间
  deleted_at?: number; // 软删除时间戳
  // 操作元数据
  created_dept?: number; // 创建部门ID
  created_by: number; // 创建人ID
  updated_by?: number; // 更新人ID
  is_default?:number;

  // 备注信息
  remark?: string;
}

/**
 * 套餐字段
 */
export interface SystemTenantPackageRow {
  permissions: any;
  id: number; // 套餐唯一标识
  name: string; // 套餐名称（如：基础版、专业版）
  remark?: string; // 套餐说明（可选）
  permissions?:any;//授权菜单
  enabled?: number; // 启用状态（0/1映射）
  children?:SystemTenantPackageRow;
}
