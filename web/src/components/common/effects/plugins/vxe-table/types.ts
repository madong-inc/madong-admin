import type {
  VxeGridListeners,
  VxeGridPropTypes,
  VxeGridProps as VxeTableGridProps,
  VxeUIExport,
} from 'vxe-table';

import type { Ref } from 'vue';

import type { ClassType, DeepPartial } from '#/components/common/types';

import type { BasicFormProps as FormProps } from '#/components/common/core/ui-kit/form-ui';

import type { VxeGridApi } from './api';

import { useForm } from '#/components/common/core/ui-kit/form-ui';

export interface VxePaginationInfo {
  currentPage: number;
  pageSize: number;
  total: number;
}

interface ToolbarConfigOptions extends VxeGridPropTypes.ToolbarConfig {
  /** 是否显示切换搜索表单的按钮 */
  search?: boolean;
}

export interface VxeTableGridOptions<T = any> extends VxeTableGridProps<T> {
  /** 工具栏配置 */
  toolbarConfig?: ToolbarConfigOptions;
}

export interface VxeGridProps {
  /**
   * 标题
   */
  tableTitle?: string;
  /**
   * 标题帮助
   */
  tableTitleHelp?: string;
  /**
   * 组件class
   */
  class?: ClassType;
  /**
   * vxe-grid class
   */
  gridClass?: ClassType;
  /**
   * vxe-grid 配置
   */
  gridOptions?: DeepPartial<VxeTableGridOptions>;
  /**
   * vxe-grid 事件
   */
  gridEvents?: DeepPartial<VxeGridListeners>;
  /**
   * 表单配置
   */
  formOptions?: FormProps;
  /**
   * 显示搜索表单
   */
  showSearchForm?: boolean;
}

export type ExtendedVxeGridApi = VxeGridApi & {
  useStore: <T = NoInfer<VxeGridProps>>(
    selector?: (state: NoInfer<VxeGridProps>) => T,
  ) => Readonly<Ref<T>>;
};

export interface SetupVxeTable {
  configVxeTable: (ui: VxeUIExport) => void;
  useForm: typeof useForm;
}
