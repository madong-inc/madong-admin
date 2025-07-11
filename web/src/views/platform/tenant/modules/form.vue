<script setup lang="ts">
import { computed, reactive, ref } from "vue";

import { useDrawer, useModal } from "#/components/common-ui";
import { $t } from "#/locale";
import { cloneDeep } from "#/components/common/utils";

import { useForm } from "#/adapter/form";

import { omit } from "lodash-es";

import { formSchemas } from "../data";
import type { TenantRow } from "#/api/platform/tenant";
import { TenantApi } from "#/api/platform/tenant";
import { DbSettingApi } from "#/api/platform/db";
import { debounce } from "lodash-es";
import { TenantMemberApi } from "#/api/platform/tenant-member";
import { message, Alert, Select, Spin } from "ant-design-vue";

const emit = defineEmits<{ reload: [] }>();

const api = new TenantApi();
const dbApi = new DbSettingApi();
const mumberApi= new TenantMemberApi();

const record = ref<TenantRow>();
const isUpdate = ref<Boolean>(false);
const isRead = ref(false);

const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-2",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 110,
  },
  schema: formSchemas(),
  showDefaultActions: false,
  wrapperClass: "grid-cols-2",
});

const [Modal, drawerApi] = useDrawer({
  async onOpenChange(isOpen) {
    if (isOpen) {
      isUpdate.value = false;
      isRead.value = false;
      formApi.resetForm();
      const data = drawerApi.getData();
      //编辑
      if (data?.id) {
        isUpdate.value = true;
        const response = await api.get(data.id);
        record.value = {
          ...response,
          expired_at:response?.expired_date||null,
          gran_subscription: response?.packages?.map((pkg) => pkg.id) || [],
        };
        
        await formApi.setValues(record.value);
      }

      await initialDbSelect();
    }
  },
  onConfirm: handleConfirm,
});

async function initialDbSelect() {
  const options = await dbApi.list({
    format: "select",
  });
  formApi.updateSchema([
    {
      componentProps: {
        options,
        showSearch: true,
      },
      fieldName: "db_name",
    },
  ]);
}

/**
 * 表单提交
 */
async function handleConfirm() {
  try {
    drawerApi.lock(true);
    const { valid } = await formApi.validate();
    if (!valid) {
      return;
    }
    const data = cloneDeep(await formApi.getValues());
    await (isUpdate.value ? api.update(data) : api.create(omit(data, ["id"])));
    emit("reload");
    drawerApi.close();
  } catch (error) {
    console.error(error);
  } finally {
    drawerApi.lock(false);
  }
}

const title = computed(() => {
  const EDIT_KEY = "common.edit";
  const CREATE_KEY = "common.create";
  // 缓存响应式值
  const isUpdateVal = isUpdate.value;
  // 条件判断逻辑
  if (isUpdateVal) {
    return $t(EDIT_KEY);
  } else {
    return $t(CREATE_KEY);
  }
});


let lastFetchId = 0;
const selectType = ref("default"); // 默认多选，可以动态改为"default"切换单选

const state = reactive({
  data: [],
  fetching: false,
});

// 统一的搜索处理函数
const handleSearch = debounce((value) => {
  fetchUser(value);
}, 300);

// 聚焦时自动加载一些数据（可选）
const handleFocus = () => {
  if (state.data.length === 0) {
    fetchUser('');
  }
};

// 获取成员列表
const fetchUser = (value, fieldName = 'LIKE_real_name') => {
  lastFetchId += 1;
  const fetchId = lastFetchId;
  state.data = [];
  state.fetching = true;
  
  mumberApi.list({ [fieldName]: value, format: "select",page:1,limit:5}).then((response) => {
    if (fetchId !== lastFetchId) {
      return;
    }
    state.data = response;
    state.fetching = false;
  });
};

</script>

<template>
  <Modal :title="title" class="w-[800px]">
    <Form>
      <template #admin_id="slotProps">
          <Select
            v-bind="slotProps"
           :mode="selectType"
            :label-in-value="false"
            placeholder="请选择站点管理员"
            style="width: 100%"
            :filter-option="false"
            :show-search="true"
            :allowClear="true"
            :not-found-content="state.fetching ? undefined : null"
            :options="state.data"
            @search="fetchUser"
            @focus="handleFocus"
          >
            <template v-if="state.fetching" #notFoundContent>
              <Spin size="small" />
            </template>
          </Select>
        </template>
    </Form>
  </Modal>
</template>
<style scoped lang="less">
@import "#/assets/styles/common-form.less";
</style>
