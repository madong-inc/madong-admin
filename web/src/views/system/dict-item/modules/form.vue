<script lang="ts" setup>
import { computed, ref } from "vue";

import { useModal } from "#/components/common-ui";

import { message } from "ant-design-vue";

import { useForm } from "#/adapter";
import { SystemDictItemApi, SystemDictApi } from "#/api/system/dict";
import type{ SystemDictItemRow } from "#/api/system/dict";


import { formSchamas } from "../data";
import { tagTypes } from "#/components/dict";
import TagStylePicker from "./tag-style-picker.vue";
import { $t } from "#/locale";

const emit = defineEmits<{ success: [] }>();
const dictApi = new SystemDictApi();
const dictItemApi = new SystemDictItemApi();

const record = ref();
const isUpdate = ref(false);
/**
 * 标签样式选择器
 * default: 预设标签样式
 * custom: 自定义标签样式
 */
const selectType = ref("default");

/**
 * 根据标签样式判断是自定义还是默认
 * @param color 标签样式
 */
function setupSelectType(_class: string) {
  const isDefault = Reflect.has(tagTypes, _class);
  selectType.value = isDefault ? "default" : "custom";
}

const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-2",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 110,
  },
  schema: formSchamas(),
  showDefaultActions: false,
  wrapperClass: "grid-cols-2",
});

const [Modal, modalApi] = useModal({
  draggable: true,
  async onOpenChange(isOpen) {
    if (!isOpen) {
      return null;
    }
    isUpdate.value=false;
    const result = modalApi.getData();
    isUpdate.value = result?.is_update||false;
    await formApi.setFieldValue("code", result.dict_code);
    await formApi.setFieldValue("dict_id", result.dict_id);
    if (isUpdate.value) {
      record.value = result.record;
      dictItemApi.get(record.value?.id).then((data) => {
        formApi.setValues(data);
        setupSelectType(data.color || "");
      });
    }
  },
  onConfirm() {
    formApi.validate().then(async (e: any) => {
      if (e.valid) {
        const values = (await formApi.getValues()) as SystemDictItemRow;
        modalApi.setState({ loading: true, confirmLoading: true });
        const isUpdate = Boolean(record.value?.id);
        if (isUpdate) {
          dictItemApi
            .update(values)
            .then(() => {
              message.success($t('system.dict_item.message.success'));
              emit("success");
              modalApi.close();
            })
            .finally(() => {
              modalApi.setState({ loading: false, confirmLoading: false });
            });
        } else {
          dictItemApi
            .create(values)
            .then(() => {
             message.success($t('system.dict_item.message.success'));
              emit("success");
              modalApi.close();
            })
            .finally(() => {
              modalApi.setState({ loading: false, confirmLoading: false });
            });
        }
      }
    });
  },
});


const title = computed(() => {
  return isUpdate.value ? $t("common.edit") : $t("common.create");
});


/**
 * 取消标签选中 必须设置为undefined才行
 */
async function handleDeleteSelect() {
  await formApi.setFieldValue("echo_class", undefined);
}
</script>
<template>
  <Modal :title="title" class="w-[40%]">
    <div class="common-form">
      <Form>
        <template #color="slotProps">
          <TagStylePicker
            v-bind="slotProps"
            v-model:select-type="selectType"
            @deselect="handleDeleteSelect"
          />
        </template>
      </Form>
    </div>
  </Modal>
</template>
