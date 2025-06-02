/**
 * 菜单项基础接口
 */
export interface MenuItemBase {
  /** 菜单项唯一标识 */
  id: string;
  /** 父级菜单ID */
  pid: number;
  /** 所属应用标识 */
  app?: string | null;
  /** 菜单显示标题 */
  title: string;
  /** 菜单编码（唯一） */
  code: string;
  /** 菜单层级（未使用） */
  level?: null;
  /** 菜单类型：1-目录 2-页面 */
  type: 1 | 2 | 3 | 4 | 5 | 6;
  /** 排序值 */
  sort: number;
  /** 路由路径 */
  path?: string;
  /** 组件路径 */
  component?: string;
  /** 重定向路径 */
  redirect?: null;
  /** 菜单图标 */
  icon?: string;
  /** 是否显示 */
  is_show: 0 | 1;
  /** 是否为外链 */
  is_link?: 0 | 1;
  /** 外链地址 */
  link_url?: null;
  /** 是否启用 */
  enabled: 0 | 1;
  /** 打开方式 */
  open_type?: 0;
  /** 是否缓存 */
  is_cache?: 0 | 1;
  /** 是否同步 */
  is_sync?: 0 | 1;
  /** 是否固定 */
  is_affix?: 0 | 1;
  /** 动态参数 */
  variable?: null;
}

/**
 * 时间字段扩展接口
 */
export interface TimeFields {
  /** 创建时间 */
  created_at?: string;
  /** 创建人 */
  created_by?: null;
  /** 更新时间 */
  updated_at?: string;
  /** 更新人 */
  updated_by?: null;
  /** 删除时间 */
  deleted_at?: null;
}

/**
 * 完整菜单项接口
 */
export interface SystemMenuRow extends MenuItemBase, TimeFields {
  label: any;
  /** HTTP方法 */
  methods: string;
  /** 创建时间（格式化） */
  created_date: string;
  /** 更新时间（格式化） */
  updated_date: string;
  /** 子菜单项 */
  children?: SystemMenuRow[];
}

/**
 * 根菜单项接口（可选子菜单）
 */
export interface RootMenuItem extends Omit<MenuItem, 'children'> {
  children?: MenuItem[];
}

