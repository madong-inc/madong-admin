import type {
  BasicFormSchema,
  BasicFormProps as FormProps,
} from '#/components/core/effects/common-ui';

import type { ComponentType } from './component';

import { setupForm, useForm as useBasicForm, z } from '#/components/core/effects/common-ui';
import { $t } from '#/locale';

setupForm<ComponentType>({
  config: {
    // ant design vue组件库默认都是 v-model:value
    baseModelPropName: 'value',
    // 一些组件是 v-model:checked 或者 v-model:fileList
    modelPropNameMap: {
      Checkbox: 'checked',
      Radio: 'checked',
      Switch: 'checked',
      Upload: 'fileList',
    },
  },
  defineRules: {
    // 输入项目必填国际化适配
    required: (value, _params, ctx) => {
      if (value === undefined || value === null || value.length === 0) {
        return $t('ui.formRules.required', [ctx.label]);
      }
      return true;
    },
    // 选择项目必填国际化适配
    selectRequired: (value, _params, ctx) => {
      if (value === undefined || value === null) {
        return $t('ui.formRules.selectRequired', [ctx.label]);
      }
      return true;
    },
  },
});

const useForm = useBasicForm<ComponentType>;

export { useForm, z };
export type FormSchema = BasicFormSchema<ComponentType>;
export type { FormProps };
export type FormSchemaGetter = () => FormSchema[];