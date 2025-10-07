<script setup lang="ts">
import type { Recordable } from '#/components/core/types';


import { onMounted } from 'vue';

import { DictEnum } from '#/components/core/constants';
import { useUserStore } from '#/components/core/stores';

import { pick } from 'lodash-es';

import { useForm, z } from '#/adapter/form';
import { SystemProfileApi } from '#/api/system/profile';
import { useAuthStore } from '#/store';
import { getDictOptions } from '#/utils/dict';

import { emitter } from '../mitt';
import type { User } from '#/api/system/user';
import { UpdateUserInfoParam } from '#/api/system/profile/model';
import { $t } from '#/locale';

const props = defineProps<{ profile: User }>();

const userStore = useUserStore();
const authStore = useAuthStore();
const api = new SystemProfileApi();

const [BasicForm, formApi] = useForm({
  actionWrapperClass: 'text-left ml-[68px] mb-[16px]',
  commonConfig: {
    labelWidth: 60,
  },
  handleSubmit,
  resetButtonOptions: {
    show: false,
  },
  schema: [
    {
      component: 'Input',
      dependencies: {
        show: () => false,
        triggerFields: [''],
      },
      fieldName: 'id',
      label: $t('system.user.profile.base_setting.id'),
      rules: 'required',
    },
    {
      component: 'Input',
      fieldName: 'real_name',
      label: $t('system.user.profile.base_setting.real_name'),
      rules: 'required',
    },
    {
      component: 'Input',
      fieldName: 'email',
      label: $t('system.user.profile.base_setting.email'),
      rules: z.string().email('请输入正确的邮箱'),
    },
    {
      component: 'RadioGroup',
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_SEX),
        optionType: 'button',
      },
      defaultValue: '0',
      fieldName: 'sex',
      label: $t('system.user.profile.base_setting.sex'),
      rules: 'required',
    },
    {
      component: 'Input',
      fieldName: 'mobile_phone',
      label: $t('system.user.profile.base_setting.mobile_phone'),
      rules: z.string().regex(/^1[3-9]\d{9}$/, '请输入正确的电话'),
    },
  ],
  submitButtonOptions: {
    content: $t('system.user.profile.base_setting.button.submit'),
  },
});

function buttonLoading(loading: boolean) {
  formApi.setState((prev) => ({
    ...prev,
    submitButtonOptions: { ...prev.submitButtonOptions, loading },
  }));
}

/**
 * 表单提交
 * @param values 
 */
async function handleSubmit(values: Recordable<any>) {
  try {
    buttonLoading(true);
    await api.updateInfo(values as UpdateUserInfoParam);
    // 更新store
    const userInfo = await authStore.fetchUserInfo();
    userStore.setUserInfo(userInfo);
    // 左边reload
    emitter.emit('updateProfile');
  } catch (error) {
    console.error(error);
  } finally {
    buttonLoading(false);
  }
}

onMounted(() => {
  //按需导入对应的key
  const data = pick(props.profile, [
    'id',
    'real_name',
    'email',
    'mobile_phone',
    'sex',
  ]);
  formApi.setValues(data);
});
</script>

<template>
  <div class="mt-[16px] md:w-full lg:w-1/2 2xl:w-2/5">
    <BasicForm />
  </div>
</template>
