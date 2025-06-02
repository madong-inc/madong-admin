export type HttpMethod = "get" | "post" | "put" | "delete" | "patch";

/**
 * 路由列表
 */
export interface RouteItem {
    name: string;
    method: Uppercase<HttpMethod>;
    path: string;
    title?: string;
    type?: number;
  }