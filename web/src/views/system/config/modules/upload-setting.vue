<script lang="ts" setup>
import { Page, useDrawer } from "#/components/common-ui";
import { Button, message } from "ant-design-vue";
import { useForm } from "#/adapter/form";
import { h, onMounted, ref } from "vue";
import uploadSettingDrawer from "./upload-setting-drawer.vue";
import { info, save } from "#/api/system/config/config";
import { transformData } from "../data";
import { getDictOptions } from "#/utils";
import { DictEnum } from "#/components/common/constants";
import { $t } from "#/locale";


const fieldLink ={
  mode:'上传模式',
  single_limit:'上传大小',
  total_limit:'文件限制',
  nums:'数量限制',
  exclude:'不允许文件类型'

}


const [BaseForm, formApi] = useForm({
  handleSubmit: onSubmit,
  layout: "horizontal",
  wrapperClass: "grid-cols-1 md:grid-cols-1 lg:grid-cols-1",
  commonConfig: {
    labelClass: "mr-5",
  },
  resetButtonOptions: {
    show: false,
  },
  submitButtonOptions: {
    show: false,
  },
  schema: [
    {
      fieldName: "mode",
      label:$t('system.config.upload.mode'),
      component: "RadioGroup",
      defaultValue: "local",
      componentProps: {
        options: getDictOptions(DictEnum.SYS_CLOUD_STORAGE),
      },
      formItemClass: "w-full",
      rules: "selectRequired",
    },
    {
      component: "Input",
      componentProps: {
        placeholder: "1024",
      },
      fieldName: "single_limit",
      label:$t('system.config.upload.single_limit'),
      formItemClass: "w-full md:w-1/2",
    },
    {
      component: "Input",
      componentProps: {
        placeholder: "1024",
      },
      fieldName: "total_limit",
      label:$t('system.config.upload.total_limit'),
      formItemClass: "w-full md:w-1/2",
    },
    {
      component: "Input",
      componentProps: {
        placeholder: "1",
      },
      fieldName: "nums",
      label:$t('system.config.upload.nums'),
      formItemClass: "w-full md:w-1/2",
    },
    {
      component: "Input",
      componentProps: {
        placeholder: "",
      },
      fieldName: "exclude",
      label:$t('system.config.upload.exclude'),
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
                return $t('system.config.upload.button.submit');
              },
            }
          ),
          h(
            Button,
            {
              type: "primary",
              ghost: true,
              onClick: handleSetting.bind(null),
            },
            {
              default() {
                return $t('system.config.upload.button.extend');
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

const [Drawer, drawerApi] = useDrawer({
  connectedComponent: uploadSettingDrawer,
});

/**
 * 提交表单
 */
function onSubmit() {
  formApi.validate().then(async (e: any) => {
    if (e.valid) {
      const values = await formApi.getValues();
      const data = transformData(values, ["btn"], [], {
        group_code: "basic_upload_setting",
      },fieldLink);
      save(data).then(() => {
        message.success($t('common.message.success'));
        retrieveData();
      });
    }
  });
}

//更多设置
const handleSetting = () => {
  formApi.validate().then(async (e: any) => {
    if (e.valid) {
      const values = await formApi.getValues();
      drawerApi.setData(values);
      drawerApi.open();
    }
  });
};

/**
 * 获取表单数据
 */
const retrieveData = async () => {
  const record = await info({ group_code: "basic_upload_setting" });
  await formApi.setValues({ ...record });
};

onMounted(async () => {
  retrieveData();
});
</script>

<template>
  <Page content-class="flex flex-col gap-4">
    <BaseForm />
    <Drawer ref="uploadSettingDrawerRef" />
  </Page>
</template>
