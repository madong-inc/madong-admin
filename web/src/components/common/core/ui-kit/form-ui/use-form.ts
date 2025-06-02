import type {
  BaseFormComponentType,
  ExtendedFormApi,
  FormProps,
} from './types';

import { defineComponent, h, isReactive, onBeforeUnmount, watch } from 'vue';

import {useStore} from '@tanstack/vue-store';

import { FormApi } from './form-api';
import UseForm from './use-form.vue';

export function useForm<
  T extends BaseFormComponentType = BaseFormComponentType,
>(options: FormProps<T>) {
  const IS_REACTIVE = isReactive(options);
  const api = new FormApi(options);
  const extendedApi: ExtendedFormApi = api as never;
  extendedApi.useStore = (selector) => {
    return useStore(api.store, selector);
  };

  const Form = defineComponent(
    (props: FormProps, { attrs, slots }) => {
      onBeforeUnmount(() => {
        api.unmount();
      });
      api.setState({ ...props, ...attrs });
      return () =>
        h(UseForm, { ...props, ...attrs, formApi: extendedApi }, slots);
    },
    {
      inheritAttrs: false,
      name: 'UseForm',
    },
  );
  // Add reactivity support
  if (IS_REACTIVE) {
    watch(
      () => options.schema,
      () => {
        api.setState({ schema: options.schema });
      },
      { immediate: true },
    );
  }

  return [Form, extendedApi] as const;
}
