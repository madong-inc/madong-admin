<script lang="ts" setup>
import type { BasicFormSchema } from '#/components/common-ui';

import { computed, markRaw, onMounted, ref, useTemplateRef } from 'vue';

import { AuthenticationLogin, ImageCaptcha, z } from '#/components/common-ui';
import { $t } from '#/locale';

import { useAuthStore } from '#/store';
import { captcha, getCaptchaOpenFlag } from '#/api/core';

import { JSEncrypt } from 'jsencrypt';
defineOptions({ name: 'Login' });

const loginFormRef = useTemplateRef('loginFormRef');
const authStore = useAuthStore();
const captchaFlag = ref(true);
const refreshCaptcha = ref(false);
const captchaData = ref<any>(null);
const isCaptchaLoaded = ref(false); // 添加加载状态

// 公共字段（用户名和密码）
const commonFields = [
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

// 判断是否启用图验证码
onMounted(async () => {
  try {
    const res: any = await getCaptchaOpenFlag();
    captchaData.value = res;
    captchaFlag.value = res.flag;
    isCaptchaLoaded.value = true; // 标记加载完成
  } catch (error) {
    isCaptchaLoaded.value = true; // 即使失败也标记加载完成
  }
});

const formSchema = computed((): BasicFormSchema[] => {
  // 在配置加载完成前，先返回基础字段（用户名和密码）
  if (!isCaptchaLoaded.value) {
    return commonFields;
  }

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
  try {
    const currentCaptchaData = captchaData.value;
    const { public_key, key_id } = currentCaptchaData;
    if (public_key && key_id) {
      const encrypt = new JSEncrypt();
      encrypt.setPublicKey(public_key);
      const encryptedPassword = encrypt.encrypt(data.password);

      if (encryptedPassword) {
        const submitData = {
          user_name: data.user_name,
          password: encryptedPassword,
          key_id,
        };

        if (captchaFlag.value && data.code) submitData.code = data.code;
        if (captchaFlag.value && data.uuid) submitData.uuid = data.uuid;

        authStore.authLogin(submitData).catch(() => {
          refreshCaptcha.value = true;
        });
      } else {
        console.error('RSA encryption failed');
      }
    } else {
      console.error('Missing public key or key_id for encryption');
    }
  } catch (error) {
    console.error('Login process failed:', error);
  }
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
