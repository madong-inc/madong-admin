<script lang="ts" setup>
import { useDrawer, useForm } from "#/components/core/effects/common-ui";
import { ref } from "vue";
import { drawerFormSchamas, templateField, transformData ,fieldLink} from "../data";
import { info, save } from "#/api/system/config/config";
import { message } from "ant-design-vue";
import { $t } from "#/locale";

const data = ref();

const [SettingForm, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-3",
    labelWidth: 110,
  },
  wrapperClass: "grid-cols-3",
  showDefaultActions: false,
  schema: drawerFormSchamas.schema,
});

const [Drawer, drawerApi] = useDrawer({
  onCancel() {
    drawerApi.close();
  },
  async onConfirm() {
    const values = await formApi.getValues();
    const field = templateField(data.value.mode);
    let datas = transformData(values, ["btn"], field, { group_code: data.value.mode },fieldLink);
    if (field.length == 0) {
      datas = [];
    }
    save(datas).then(() => {
      message.success($t('common.message.success'));
      retrieveData();
    });
  },

  async onOpenChange(isOpen: boolean) {
    if (!isOpen) {
      return null;
    }
    retrieveData();
  },
});

/**
 * 获取配置信息
 */
const retrieveData = async function () {
  data.value = drawerApi.getData<Record<string, any>>();
  const record = await info({ group_code: data.value.mode });
  await formApi.setValues({ ...record, mode: data.value.mode });
};
</script>
<template>
  <Drawer title="更多设置" class="w-[650px]">
    <SettingForm />
  </Drawer>
</template>
