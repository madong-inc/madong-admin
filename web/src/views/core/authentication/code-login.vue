<script lang="ts" setup>
import type { BasicFormSchema } from "#/components/common-ui";

import { computed, ref } from "vue";

import { AuthenticationCodeLogin, z } from "#/components/common-ui";
import { $t } from "#/locale";

import { Alert, message } from "ant-design-vue";

import { useAuthStore } from "#/store";
import { sendSmsCode } from "#/api/core";

defineOptions({ name: "CodeLogin" });

const loading = ref(false);
const CODE_LENGTH = 6;

const formSchema = computed((): BasicFormSchema[] => {
  return [
    {
      component: "BasicInput",
      componentProps: {
        placeholder: $t("authentication.mobile"),
      },
      fieldName: "phoneNumber",
      label: $t("authentication.mobile"),
      rules: z
        .string()
        .min(1, { message: $t("authentication.mobileTip") })
        .refine((v) => /^\d{11}$/.test(v), {
          message: $t("authentication.mobileErrortip"),
        }),
    },
    {
      component: "BasicPinInput",
      fieldName:'code',
      componentProps: {
        codeLength: CODE_LENGTH,
        createText: (countdown: number) => {
          const text =
            countdown > 0
              ? $t("authentication.sendText", [countdown])
              : $t("authentication.sendCode");
          return text;
        },
        placeholder: $t("authentication.code"),
        componentProps(_values, form) {
          return {
            createText: (countdown: number) => {
              const text =
                countdown > 0
                  ? $t("authentication.sendText", [countdown])
                  : $t("authentication.sendCode");
              return text;
            },
            // 验证码长度 在这设置
            codeLength: 6,
            placeholder: $t("authentication.code"),
            handleSendCode: async () => {
              const { valid, value } = await form.validateField("phoneNumber");
              if (!valid) {
                throw new Error("未填写手机号");
              }
              // 调用接口发送
              await sendSmsCode({ mobile_phone: value });
              message.success("验证码发送成功");
            },
          };
        },
        fieldName: "code",
        label: $t("authentication.code"),
        rules: z.string().length(CODE_LENGTH, {
          message: $t("authentication.codeTip", [CODE_LENGTH]),
        }),
      },
    }
  ];
});

const authStore = useAuthStore();
async function handleLogin(data: any) {
  try {
    const requestParams: any = {
      mobile_phone: data.phoneNumber, //兼容后端对应KEY
      code: data.code,
      grant_type: "sms",
    };
    await authStore.authLogin(requestParams);
  } catch (error) {
    console.error(error);
  }
}
</script>

<template>
  <div>
    <AuthenticationCodeLogin
      :form-schema="formSchema"
      :loading="loading"
      @submit="handleLogin"
    />
  </div>
</template>
