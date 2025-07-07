
export interface TenantSubscriptionRow {
  id: string | number;
  tenant_id: string | number;
  name: string;
  description: string;
  sort: number;
  start_time?: string;
  end_time?: string;
  enabled?: number;
  remark: string;
}
