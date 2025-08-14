<script lang="ts" setup>
import type { BasicFormSchema } from '#/components/common-ui';

import { computed, nextTick, markRaw, onMounted, ref, useTemplateRef, watch } from 'vue';

import { AuthenticationLogin, ImageCaptcha, z } from '#/components/common-ui';
import { $t } from '#/locale';

import { useAuthStore } from '#/store';
import { captcha, getCaptchaOpenFlag, type AuthApi } from '#/api/core';
import { JSEncrypt } from 'jsencrypt';
defineOptions({ name: 'Login' });

const loginFormRef = useTemplateRef('loginFormRef');
const authStore = useAuthStore();
const captchaFlag = ref(true);
const refreshCaptcha = ref(false);
const formSchemaRef = ref<BasicFormSchema[]>([]); // 新增：用于存储最新的表单结构

// 更新表单结构的函数
const updateFormSchema = (flag: boolean) => {
  // 公共字段
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

  // 根据 flag 添加验证码相关字段
  if (flag) {
    formSchemaRef.value = [
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
  } else {
    formSchemaRef.value = commonFields;
  }
};

// 判断是否启用图验证码
getCaptchaOpenFlag().then((res: any) => {
  captchaFlag.value = res.flag;
  // 使用 nextTick 确保 DOM 更新
  nextTick(() => {
    updateFormSchema(res.flag);
  });
});

// 监听 captchaFlag 变化
watch(captchaFlag, (newFlag) => {
  updateFormSchema(newFlag);
});

const handleSubmit = async (data: any) => {
  try {
      // 获取加密相关信息（包括公钥和key_id）
      const captchaRes: any = await getCaptchaOpenFlag();

      // 检查是否返回了公钥和key_id
      if (captchaRes.public_key && captchaRes.key_id) {
        // 创建 JSEncrypt 实例
        const encrypt = new JSEncrypt();
        // 设置公钥
        encrypt.setPublicKey(captchaRes.public_key);

        // 对密码进行 RSA 加密
        const encryptedPassword = encrypt.encrypt(data.password);

        if (encryptedPassword) {
          // 修改提交数据，添加 key_id 并替换密码为加密后的密码
          const submitData = {
            user_name: data.user_name,
            password: encryptedPassword, // 使用加密后的密码
            key_id: captchaRes.key_id    // 添加 key_id
          };

          // 如果有验证码相关字段，也添加进去
          if (data.code) {
            submitData.code = data.code;
          }
          if (data.uuid) {
            submitData.uuid = data.uuid;
          }

          await authStore.authLogin(submitData).catch(() => {
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
      :form-schema="formSchemaRef"
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