

/**
 * 职位接口
 */
export interface SystemPostRow {
  id: number | string;
  dept_id: string | number;
  code: string;
  name: string;
  sort: number;
  enabled: number;
  remark: string;
}



export interface Post {
  id: number | string;
  dept_id: string | number;
  code: string;
  name: string;
  sort: number;
  enabled: number;
  remark: string;
}
