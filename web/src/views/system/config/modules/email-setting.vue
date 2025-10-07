<script lang="ts" setup>
import { Page } from "#/components/core/effects/common-ui";
import { Button, message, Tooltip } from "ant-design-vue";
import { useForm } from "#/adapter/form";
import { transformData } from "../data";
import { info, save } from "#/api/system/config/config";
import { h, onMounted } from "vue";
import { MailTwoTone } from "@ant-design/icons-vue";
import { $t } from "#/locale";

const fieldLink={
  Host:$t('system.config.email.Host'),
  Port:$t('system.config.email.Port'),
  Username:$t('system.config.email.Username'),
  Password:$t('system.config.email.Password'),
  SMTPSecure:$t('system.config.email.SMTPSecure'),
  From:$t('system.config.email.From'),
  FromName:$t('system.config.email.FromName'),
  email:$t('system.config.email.test'),
}

const [BaseForm, formApi] = useForm({
  handleSubmit: onSubmit,
  // 垂直布局，label和input在不同行，值为vertical
  layout: "horizontal",
  wrapperClass: "grid-cols-1 md:grid-cols-1 lg:grid-cols-1",
  commonConfig: {
    labelWidth: 120,
  },
  resetButtonOptions: {
    show: false,
  },
  submitButtonOptions: {
    show: false,
  },
  schema: [
    {
      fieldName: "Host",
      label: $t('system.config.email.Host'),
      component: "Input",
      componentProps: {
        placeholder:$t('system.config.email.placeholder.Host'),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
      rules: "required",
    },
    {
      fieldName: "Port",
      label: $t('system.config.email.Port'),
      component: "Input",
      componentProps: {
        placeholder: $t('system.config.email.placeholder.Port'),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
      rules: "required",
    },
    {
      fieldName: "Username",
      label: $t('system.config.email.Username'),
      component: "Input",
      componentProps: {
        placeholder:  $t('system.config.email.placeholder.Username'),
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "Password",
      label:$t('system.config.email.Password'),
      component: "Input",
      componentProps: {
        placeholder: $t('system.config.email.placeholder.Password'),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "SMTPSecure",
      label: $t('system.config.email.SMTPSecure'),
      component: "RadioGroup",
      defaultValue: "ssl",
      componentProps: {
        options: [
          {
            label: "ssl",
            value: "ssl",
          },
          {
            label: "tsl",
            value: "tsl",
          },
        ],
      },
      formItemClass: "w-full md:w-1/2",
      rules: "selectRequired",
    },

    {
      fieldName: "From",
      label: $t('system.config.email.From'),
      component: "Input",
      componentProps: {
        placeholder: $t('system.config.email.placeholder.From'),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "FromName",
      label:$t('system.config.email.FromName'),
      component: "Input",
      componentProps: {
        placeholder: $t('system.config.email.placeholder.FromName'),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "email",
      label: $t('system.config.email.test'),
      component: "Input",
      componentProps: {
        placeholder: $t('system.config.email.placeholder.test'),
        allowClear: true,
      },
      renderComponentContent: (_values: any) => {
        return {
          addonAfter: () => {
            return h(
              Tooltip,
              { title: $t('system.config.email.tip.test'), placement: "right" },
              {
                default: () =>
                  h(MailTwoTone, {
                    onClick() {
                      console.log("发起测试邮件");
                    },
                  }),
              }
            );
          },
        };
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "btn",
      label: "",
      component: () => {
        return h("div", { class: "flex space-x-2" }, [
          h(
            Button,
            {
              type: "primary",
              onClick: onSubmit.bind(null),
            },
            {
              default() {
                return $t('system.config.email.button.submit');
              },
            }
          ),
        ]);
      },
      componentProps: {},
      formItemClass: "col-span-1",
    },
  ],
});

function onSubmit() {
  formApi.validate().then(async (e: any) => {
    if (e.valid) {
      const values = await formApi.getValues();
      const data = transformData(values, ["btn", "email"], [], {
        group_code: "email_setting",
      },fieldLink);
      save(data).then(() => {
        message.success("保存成功");
        retrieveData();
      });
    }
  });
}

/**
 * 获取表单数据
 */
const retrieveData = async () => {
  const record = await info({ group_code: "email_setting" });
  await formApi.setValues({ ...record });
};

onMounted(async () => {
  retrieveData();
});
</script>

<template>
  <Page content-class="flex flex-col gap-4">
    <BaseForm />
  </Page>
</template>

<style lange="scss">
.label-left {
  display: flex; /* 确保使用 flexbox */
  justify-content: flex-start; /* 靠左对齐 */
  width: 100px;
  text-align: left;
}
</style>
