<script setup lang="ts">
import { computed, h, ref } from "vue";

import { useDrawer } from "#/components/core/effects/common-ui";
import { $t } from "#/locale";
import { addFullName, cloneDeep, getPopupContainer } from "#/components/core/utils";

import { useForm } from "#/adapter/form";
import { PostItem, SystemPostApi } from "#/api/system/post";

import { drawerSchema } from "../data";
import { SystemDeptApi } from "#/api/system/dept";
import { SystemUserApi } from "#/api/system/user";
import { defaultFormValueGetter, useBeforeCloseDiff } from "#/utils/popup";
import { Role, SystemRoleApi } from "#/api/system/role";
import { Tag } from "ant-design-vue";
import { getDataScopeOptions } from "#/views/system/role/data";

const emit = defineEmits<{ reload: [] }>();

const api = new SystemUserApi();
const postApi = new SystemPostApi();
const deptApi = new SystemDeptApi();
const roleApi = new SystemRoleApi();

const isUpdate = ref(false);
const title = computed(() => {
  return isUpdate.value ? $t("common.edit") : $t("common.create");
});

const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-2",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 80,
  },
  schema: drawerSchema(),
  showDefaultActions: false,
  wrapperClass: "grid-cols-2",
});


const [Drawer, drawerApi] = useDrawer({
  onClosed: handleClosed,
  onConfirm: handleConfirm,
  async onOpenChange(isOpen) {
    if (!isOpen) {
      formApi.updateSchema([
        {
          componentProps: { options: [], placeholder: $t('system.user.form.placeholder.dept_name') },
          fieldName: "post_id_list",
        },
      ]);
      return null;
    }
    const { id } = drawerApi.getData() as { id?: number | string };
    isUpdate.value = !!id;
    /** update时 禁用用户名修改 不显示密码框 */
    formApi.updateSchema([
      { componentProps: { disabled: isUpdate.value }, fieldName: "user_name" },
      {
        dependencies: { show: () => !isUpdate.value, triggerFields: ["id"] },
        fieldName: "password",
      },
    ]);

    // 部门选择、初始密码及用户相关操作并行处理
    const promises = [fetchDeptTreeSelect(), loadDefaultPassword(isUpdate.value)];
    if (isUpdate.value) {
      const user = await api.get(id); //获取用户信息详情
      // const roleIds = (user?.roles ?? []).map((item) => item.id);
      // const postIds = (user?.posts ?? []).map((item) => item.id);
      promises.push(
        // 赋值基础信息
        formApi.setValues(user),
        // 添加角色
        // formApi.setFieldValue("role_id_list", roleIds),
        // // 添加角色
        // formApi.setFieldValue("post_id_list", postIds),
        // 初始化职位
        fetchPostOptions(user.dept_id)
      );
    }

    // 并行处理 重构后会带来10-50ms的优化
    await Promise.all(promises);
  },
});

/**
 * 默认密码
 */
const defaultPassword = ref("");
async function loadDefaultPassword(update: boolean) {
  if (!update && defaultPassword.value) {
    formApi.setFieldValue("password", defaultPassword.value);
  }
}

/**
 * 获取部门数据
 */
async function fetchDeptTreeSelect() {
  const deptTree = await deptApi.getTree();
  formApi.updateSchema([
    {
      componentProps: (formModel) => ({
        class: "w-full",
        fieldNames: {
          key: "id",
          value: "value",
          label:"name",
          children: "children",
        },
        getPopupContainer,
        async onSelect(dept_id: number | string) {
          /** 根据部门ID加载职位 */
          await fetchPostOptions(dept_id);
          /** 重新选择职位 */
          formModel.post_id_list = [];
        },
        placeholder: $t('system.user.form.placeholder.post_name'),
        showSearch: true,
        treeData: deptTree,
        treeDefaultExpandAll: true,
        treeLine: { showLeafIcon: false },
        // 筛选的字段
        treeNodeFilterProp: "name",
        // 选中后显示在输入框的值
        treeNodeLabelProp: "name",
      }),
      fieldName: "dept_id",
    },
  ]);
}

/**
 * 获取职位数据
 * @param deptId
 */
async function fetchPostOptions(deptId: number | string) {
  const options = await postApi.list({ dept_id: deptId,format:'select'});
  const placeholder = options.length > 0 ? "请选择" : "该部门下未设置职位";
  formApi.updateSchema([
    {
      componentProps: { options, placeholder },
      fieldName: "post_id_list",
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
    if(!isUpdate.value){
      delete data.id;
    }
    await (isUpdate.value ? api.update(data) : api.create(data));
    emit("reload");
    drawerApi.close();
  } catch (error) {
    console.error(error);
  } finally {
    drawerApi.lock(false);
  }
}

async function handleClosed() {
  await formApi.resetForm();
}
</script>

<template>
  <Drawer :title="title" class="w-[50%]">
    <Form />
  </Drawer>
</template>
