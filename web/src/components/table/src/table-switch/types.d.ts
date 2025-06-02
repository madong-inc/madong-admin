export type CheckedType = boolean | number | string;

export interface Props {
  /**
   * 选中的文本 
   * @default i18n 启用 
   */
  checkedText?: string;
  /**
   * 未选中的文本 
   * @default i18n 禁用 
   */
  unCheckedText?: string;
  checkedValue?: CheckedType;
  unCheckedValue?: CheckedType;
  disabled?: boolean;
  /**
   * 需要自己在内部处理更新的逻辑 因为status已经双向绑定了 可以直接获取 
   */
  api: () => PromiseLike<void>;
  /**
   * 更新前是否弹窗确认 
   * @default false 
   */
  confirm?: boolean;
  /**
   * 对应的提示内容 
   * @param checked 选中的值(更新后的值)
   * @default string '确认要更新状态吗？'
   */
  confirmText?: (checked: CheckedType) => string;
}