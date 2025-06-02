import { ButtonProps } from 'ant-design-vue/es/button/buttonTypes';
import { TooltipProps } from 'ant-design-vue/es/tooltip/Tooltip';

export interface PopConfirm {
  title: string;
  okText?: string;
  cancelText?: string;
  confirm: Fn;
  cancel?: Fn;
  icon?: string;
  disabled?: boolean | (() => boolean); // 新增支持函数类型 
}
export interface ActionItem extends ButtonProps {
  onClick?: Fn;
  label?: string;
  color?: 'error' | 'success' | 'warning';
  icon?: string;
  popConfirm?: PopConfirm;
  disabled?: boolean | (() => boolean); // 新增支持函数类型 
  divider?: boolean;
  // 权限编码控制是否显示
  auth?: string[];
  // 业务控制是否显示
  show?: ((action: ActionItem) => boolean) | boolean;
  tooltip?: string | TooltipProps;
}
