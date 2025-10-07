interface NotificationItem {
  id?:string;
  uid?:string;
  type?:string;
  avatar: string;
  date: string;
  isRead?: boolean;
  message: string;
  title: string;
  send?:any;
  related_id?:string;
  channel?:string;
}

export type { NotificationItem };
