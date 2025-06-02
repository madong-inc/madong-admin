<script lang="ts" setup>
import type { BasicFormSchema } from '#/components/common-ui';
import type { BasicOption } from '#/components/common/types';

import { computed, markRaw, onMounted, ref, useTemplateRef } from 'vue';

import { AuthenticationLogin, ImageCaptcha, z } from '#/components/common-ui';
import { $t } from '#/locale';

import { useAuthStore } from '#/store';
import { captcha, getCaptchaOpenFlag,getAccountSet, type AuthApi } from '#/api/core';

defineOptions({ name: 'Login' });

const loginFormRef = useTemplateRef('loginFormRef');
const authStore = useAuthStore();
const captchaFlag = ref(true);
const refreshCaptcha = ref(false);


const accountSetData =ref<any>({
  tenant_enabled: false,//是否开启账套模式
  list: [],//账套列表
});

async function loadAccountSet() {
  const result:AuthApi.AccountSetResponse  = await getAccountSet();
  accountSetData.value = result;
  if (result.tenant_enabled && result.list.length > 0) {
    const firstNumber = result.list[0]!.tenant_id;
    loginFormRef.value?.getFormApi().setFieldValue('tenant_id', firstNumber);
  }
}



// 判断是否启用图验证码
getCaptchaOpenFlag().then((res: any) => {
  captchaFlag.value = res.flag;
});

onMounted(async () => {
  await Promise.all([loadAccountSet()]);
});

const formSchema = computed((): BasicFormSchema[] => {
  // 公共字段
  const commonFields = [
    {
      component: 'BasicSelect',
      defaultValue: '',
      componentProps: {
        placeholder: '',
        options:accountSetData.value.list?.map((item: { name: any; tenant_id: any; }) => ({
          label: item.name,
          value: item.tenant_id,
        })),
      },
      fieldName: 'tenant_id',
      rules: z.string().min(1, { message: '请选择对应数据源' }),
      formItemClass: 'col-span-12',
      dependencies: {
        if: () => accountSetData.value.tenant_enabled,
        componentProps: (_values: { tenant_id: string}) => {
          return {};
        },
        triggerFields: ['', 'tenant_id'],
      },
    },
    {
      component: 'BasicInput',
      defaultValue: '',
      componentProps: {
        placeholder: $t('authentication.usernameTip'),
      },
      fieldName: 'user_name',
      label: $t('authentication.username'),
      rules: z.string().min(1, { message: $t('authentication.usernameTip') }),
      formItemClass: 'col-span-12',
    },
    {
      component: 'BasicInputPassword',
      defaultValue: '',
      componentProps: {
        placeholder: $t('authentication.password'),
      },
      fieldName: 'password',
      label: $t('authentication.password'),
      rules: z.string().min(1, { message: $t('authentication.passwordTip') }),
      formItemClass: 'col-span-12',
    },
  ];

  // 根据 captchaFlag 添加验证码相关字段
  if (captchaFlag.value) {
    return [
      ...commonFields,
      {
        component: 'BasicInput',
        defaultValue: '',
        componentProps: {
          placeholder: '请输入图片验证码',
        },
        formItemClass: 'col-span-8',
        fieldName: 'code',
        label: '验证码',
        rules: z.string().min(1, { message: '请输入图片验证码' }),
      },
      {
        component: markRaw(ImageCaptcha),
        fieldName: 'uuid',
        componentProps: {
          api: captcha,
          uuidField: 'uuid',
          base64Field: 'base64',
          refresh: refreshCaptcha,
          'onUpdate:refresh': () => {
            refreshCaptcha.value = false;
          },
        },
        formItemClass: 'col-span-4',
      },
    ];
  }

  return commonFields;
});


const handleSubmit = (data: any) => {
  authStore.authLogin(data).catch(() => {
    refreshCaptcha.value = true;
  });
};

</script>

<template>
  <AuthenticationLogin
    ref="loginFormRef"
    :form-schema="formSchema"
    :loading="authStore.loginLoading"
    :show-code-login="true"
    :show-forget-password="false"
    :show-qrcode-login="true"
    :show-register="false"
    :show-remember-me="false"
    :show-third-party-login="false"
    @submit="handleSubmit"
  />
</template>
