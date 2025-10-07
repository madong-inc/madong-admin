<script lang="ts" setup>
import type { BasicFormSchema } from '#/components/core/effects/common-ui';
import type { Recordable } from '#/components/core/types';

import { computed, ref } from 'vue';

import { AuthenticationForgetPassword, z } from '#/components/core/effects/common-ui';
import { $t } from '#/locale';

defineOptions({ name: 'ForgetPassword' });

const loading = ref(false);

const formSchema = computed((): BasicFormSchema[] => {
  return [
    {
      component: 'BasicInput',
      componentProps: {
        placeholder: 'example@example.com',
      },
      fieldName: 'email',
      label: $t('authentication.email'),
      rules: z
        .string()
        .min(1, { message: $t('authentication.emailTip') })
        .email($t('authentication.emailValidErrorTip')),
    },
  ];
});

function handleSubmit(value: Recordable<any>) {
  // eslint-disable-next-line no-console
  console.log('reset email:', value);
}
</script>

<template>
  <AuthenticationForgetPassword
    :form-schema="formSchema"
    :loading="loading"
    @submit="handleSubmit"
  />
</template>
