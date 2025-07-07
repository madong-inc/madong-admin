import { TenantRow } from "#/api/platform/tenant";
import { BasicUserInfo } from "#/components/common/types";
import { Dept } from "../dept";

/**
 * 用户
 */
export interface User extends BasicUserInfo {
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
    tenant?:any;
    login_date?: string;
}

