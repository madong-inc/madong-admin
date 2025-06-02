import { Modal } from 'ant-design-vue';
 
type ConfirmConfig = {
  content: string;
  title: string;
  okText?: string;
  cancelText?: string;
  danger?: boolean;
};
 

export function confirm(
  arg1: string | ConfirmConfig,
  arg2?: string 
): Promise<boolean> {
  const finalConfig: ConfirmConfig = typeof arg1 === 'string' 
    ? { content: arg1, title: arg2 || '系统提示' } 
    : arg1;
 
  return new Promise((resolve, reject) => {
    Modal.confirm({ 
      content: finalConfig.content, 
      title: finalConfig.title, 
      okText: finalConfig.okText, 
      okButtonProps: { danger: finalConfig.danger  },
      onOk: () => resolve(true),
      onCancel: () => reject(new Error('Operation canceled'))
    });
  });
}