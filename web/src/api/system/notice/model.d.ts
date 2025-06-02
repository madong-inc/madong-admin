/**
 * 通知公告
 */
export interface SystemNoticeRow {
    id: string;
    message_id?: string|null;
    title: string;
    type: number;
    content: any;
    enabled: number;
    created_by: string | null;
    updated_by: string | null;
    created_date: string; 
    updated_date: string|null; 
    created_dept: string|null; 
    remark: string | null;
}