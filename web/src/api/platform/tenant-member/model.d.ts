
export interface TenantMemberRow {
    id: string;
    user_name: string;
    real_name: string;
    nick_name: string;
    is_super?: number;
    mobile_phone: string;
    email: string;
    avatar: string;
    signed: string;
    dashboard: string;
    dept_id: string;
    enabled: number;
    login_ip: string;
    login_time: string;
    backend_setting: string;
    created_by: string;
    updated_by: string;
    created_at: string;
    updated_at: string;
    deleted_at: string;
    sex: string;
    remark: string;
    birthday: string;
    tel: string;
    is_locked?: string;
    depts?: Dept;
    posts?: Post[];
    roles?: Role[];
    login_date?: string;
}


/**
 * 用户租户接口
 */
export interface TenantMemberAdminRow {
    id: string;
    admin_id: string;
    tenant_id: string;
    is_super: number;
    is_default: number;
    priority: number;
}