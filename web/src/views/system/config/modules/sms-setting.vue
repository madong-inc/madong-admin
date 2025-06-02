<script lang="ts" setup>
import { Page } from "#/components/common-ui";
import { Button, message } from "ant-design-vue";
import { useForm } from "#/adapter/form";
import { convertStringNumbers, transformData } from "../data";
import { h, onMounted } from "vue";
import { info, save } from "#/api/system/config/config";
import { $t } from "#/locale";
import { getDictOptions } from "#/utils";
import { DictEnum } from "#/components/common/constants";

const fieldLink={
  access_key_id:$t('system.config.sms.access_key_id'),
  access_key_secret:$t('system.config.sms.access_key_secret'),
  enable:$t('system.config.sms.enable'),
  sign_name:$t('system.config.sms.sign_name'),

}
const [BaseForm, formApi] = useForm({
  handleSubmit: onSubmit,
  layout: "horizontal",
  wrapperClass: "grid-cols-1 md:grid-cols-1 lg:grid-cols-1",
  commonConfig: {},
  resetButtonOptions: {
    show: false,
  },
  submitButtonOptions: {
    show: false,
  },
  schema: [
    {
      fieldName: "access_key_id",
      label: $t('system.config.sms.access_key_id'),
      component: "Input",
      componentProps: {
        placeholder:$t('system.config.sms.placeholder.access_key_id'),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
      rules: "required",
    },
    {
      fieldName: "access_key_secret",
      label: $t('system.config.sms.access_key_secret'),
      component: "Input",
      componentProps: {
        placeholder:$t('system.config.sms.placeholder.access_key_secret'),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
      rules: "required",
    },
    {
      fieldName: "enable",
      label: $t('system.config.sms.enable'),
      component: "RadioGroup",
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      rules: "selectRequired",
    },
    {
      fieldName: "sign_name",
      label: $t('system.config.sms.sign_name'),
      component: "Textarea",
      componentProps: {
        autosize: { minRows: 5, maxRows: 10 },
        placeholder: $t('system.config.sms.placeholder.sign_name'),
        allowClear: true,
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
                return $t('system.config.sms.button.submit')
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
      const data = transformData(values, ["btn"], [], { group_code: "sms_setting" },fieldLink);
      save(data).then(() => {
        message.success($t('common.message.success'));
        retrieveData();
      });
    }
  });
}

/**
 * 获取表单数据
 */
const retrieveData = async () => {
  const record = await info({ group_code: "sms_setting" });
  await formApi.setValues({...convertStringNumbers(record)});
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
