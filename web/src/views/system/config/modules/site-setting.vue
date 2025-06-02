<script lang="ts" setup>
import { useForm } from "#/adapter/form";
import { info, save } from "#/api/system/config/config";
import { Page } from "#/components/common-ui";
import { Button, message } from "ant-design-vue";
import { h, onMounted } from "vue";
import { transformData, convertStringNumbers } from "../data";
import { getDictOptions } from "#/utils";
import { DictEnum } from "#/components/common/constants";
import { $t } from "#/locale";

const fieldLink = {
  site_open: "站点开启",
  site_url: "网站地址",
  site_name: "站点名称",
  site_logo: "站点Logo",
  site_network_security: "网备案号",
  site_description: "网站描述",
  site_record_no: "网站ICP",
  site_icp_url: "ICP URL",
  site_network_security_url: "网安备案链接",
  site_keywords:"关键字"
};

const [BaseForm, formApi] = useForm({
  handleSubmit: onSubmit,
  // 垂直布局，label和input在不同行，值为vertical
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
      fieldName: "site_open",
      label: $t("system.config.site.open"),
      component: "RadioGroup",
      defaultValue: 1,
      componentProps: {
        optionType: "button",
        buttonStyle: "solid",
        options: getDictOptions(DictEnum.SYS_YES_NO),
      },
      rules: "selectRequired",
    },
    {
      fieldName: "site_url",
      label: $t("system.config.site.url"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.url"),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
      rules: "required",
    },
    {
      fieldName: "site_name",
      label: $t("system.config.site.name"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.name"),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
      rules: "required",
    },
    {
      fieldName: "site_logo",
      label: $t("system.config.site.logo"),
      component: "Avatar",
      componentProps: {
        placeholder: "",
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "site_network_security",
      label: $t("system.config.site.network_security"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.network_security"),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "site_keywords",
      label: $t("system.config.site.keywords"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.keywords"),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "site_description",
      label: $t("system.config.site.description"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.description"),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },

    {
      fieldName: "site_record_no",
      label: $t("system.config.site.record_no"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.record_no"),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "site_icp_url",
      label: $t("system.config.site.icp_url"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.icp_url"),
        allowClear: true,
      },
      formItemClass: "w-full md:w-1/2",
    },
    {
      fieldName: "site_network_security_url",
      label: $t("system.config.site.network_security_url"),
      component: "Input",
      componentProps: {
        placeholder: $t("system.config.site.placeholder.network_security_url"),
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
                return $t('system.config.site.button.submit');
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
      const data = transformData(
        values,
        ["btn"],
        [],
        { group_code: "site_setting" },
        fieldLink
      );
      save(data).then(() => {
        message.success($t("common.message.success"));
        retrieveData();
      });
    }
  });
}

/**
 * 获取表单数据
 */
const retrieveData = async () => {
  const record = await info({ group_code: "site_setting" });

  await formApi.setValues({ ...convertStringNumbers(record) });
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
