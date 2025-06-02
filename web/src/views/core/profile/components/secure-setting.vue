<script setup lang="ts">
import type { UpdatePasswordParam } from "#/api/system/profile/model";

import { Modal } from "ant-design-vue";
import { omit } from "lodash-es";

import { useForm, z } from "#/adapter/form";
import { SystemProfileApi } from "#/api/system/profile";
import { useAuthStore } from "#/store";
import { $t } from "#/locale";

const api = new SystemProfileApi();

const [BasicForm, formApi] = useForm({
  actionWrapperClass: "text-left mb-[16px] ml-[96px]",
  commonConfig: {
    labelWidth: 90,
  },
  handleSubmit,
  resetButtonOptions: {
    show: false,
  },
  schema: [
    {
      component: "InputPassword",
      fieldName: "old_password",
      label: $t("system.user.profile.secure_setting.old_password"),
      rules: z
        .string({
          message: $t("system.user.profile.secure_setting.placeholder.old_password"),
        })
        .min(5, $t("system.user.profile.secure_setting.rules.min_password", [5]))
        .max(20, $t("system.user.profile.secure_setting.rules.max_password", [20])),
    },
    {
      component: "InputPassword",
      dependencies: {
        rules(values) {
          return z
            .string({
              message: $t("system.user.profile.secure_setting.placeholder.new_password"),
            })
            .min(5, $t("system.user.profile.secure_setting.rules.min_password", [5]))
            .max(20, $t("system.user.profile.secure_setting.rules.max_password", [20]))
            .refine((value) => value !== values.old_password, $t('system.user.profile.secure_setting.rules.degree_password'));
        },
        triggerFields: ["new_password", "old_password"],
      },
      fieldName: "new_password",
      label: $t("system.user.profile.secure_setting.new_password"),
      rules: "required",
    },
    {
      component: "InputPassword",
      dependencies: {
        rules(values) {
          return z
            .string({
              message: $t(
                "system.user.profile.secure_setting.placeholder.confirm_password"
              ),
            })
            .min(5, $t("system.user.profile.secure_setting.rules.min_password", [5]))
            .max(20, $t("system.user.profile.secure_setting.rules.max_password", [20]))
            .refine((value) => value === values.new_password, $t('system.user.profile.secure_setting.rules.differ_password'));
        },
        triggerFields: ["new_password", "confirm_password"],
      },
      fieldName: "confirm_password",
      label: $t("system.user.profile.secure_setting.confirm_password"),
      rules: "required",
    },
  ],
  submitButtonOptions: {
    content: $t("system.user.profile.secure_setting.button.submit"),
  },
});

function buttonLoading(loading: boolean) {
  formApi.setState((prev) => ({
    ...prev,
    submitButtonOptions: { ...prev.submitButtonOptions, loading },
  }));
}

const authStore = useAuthStore();
function handleSubmit(values: any) {
  Modal.confirm({
    content: "确认修改密码吗？",
    onOk: async () => {
      try {
        buttonLoading(true);
        const data = omit(values, ["confirm_password"]) as UpdatePasswordParam;
        await api.updatePwd(data);
        await authStore.logout(true);
      } catch (error) {
        console.error(error);
      } finally {
        buttonLoading(false);
      }
    },
    title: "提示",
  });
}
</script>

<template>
  <div class="mt-[16px] md:w-full lg:w-1/2 2xl:w-2/5">
    <BasicForm />
  </div>
</template>
