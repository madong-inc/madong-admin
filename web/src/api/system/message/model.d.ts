/**
 * 系统消息
 */
export interface SystemMessageRow {
    id: string;
    title: string;
    type: number;
    content: any;
    enabled: number;
    sender_id:string;
    receiver_id:string;
    status:string;
    priority?:string;
    channel:string;
    related_id:string;
    related_type:string;
    action_url:string;
    action_params:string;
    created_by: string | null;
    created_at:string|null;
    created_date:string|null;
    expired_at: string | null;
    sender?:any;
}