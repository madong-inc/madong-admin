<script setup lang="ts">

import { computed } from 'vue';

import { preferences } from '#/components/core/@core/preferences';

import {
  Card,
  Descriptions,
  DescriptionsItem,
  Tag,
  Tooltip,
} from 'ant-design-vue';

import { SystemProfileApi } from '#/api/system/profile';
import UpdateAvatar from "./components/update-avatar.vue";
import type { User } from '#/api/system/user';
import { fullUrl } from '#/utils';


const props = defineProps<{ profile?: User }>();

const api =new SystemProfileApi();

defineEmits<{
  uploadFinish: [];
}>();

const avatar = computed(
  () => fullUrl(props.profile?.avatar) || preferences.app.defaultAvatar,
);

</script>

<template>
  <Card :loading="!profile" class="h-full lg:w-1/3">
    <div v-if="profile" class="flex flex-col items-center gap-[24px]">
      <div class="flex flex-col items-center gap-[20px]">
        <Tooltip title="点击上传头像">
            <UpdateAvatar v-model:avatar="avatar"   @refresh="$emit('uploadFinish')"/>
        </Tooltip>
        <div class="flex flex-col items-center gap-[8px]">
          <span class="text-foreground text-xl font-bold">
            {{ profile.real_name ?? '未知' }}
          </span>
        </div>
      </div>
      <div class="px-[24px]">
        <Descriptions :column="1">
          <DescriptionsItem :label="$t('system.user.form.modal.user_name')">
            {{ profile.user_name }}
          </DescriptionsItem>
          <DescriptionsItem :label="$t('system.user.form.modal.mobile_phone')">
            {{ profile?.mobile_phone || '未绑定手机号' }}
          </DescriptionsItem>
          <DescriptionsItem :label="$t('system.user.form.modal.email')">
            {{ profile.email || '未绑定邮箱' }}
          </DescriptionsItem>
          <DescriptionsItem :label="$t('system.user.form.modal.dept_name')">
            <Tag color="processing">
              {{ profile?.depts?.name ?? '未分配部门' }}
            </Tag>
          </DescriptionsItem>
          <DescriptionsItem :label="$t('system.user.form.modal.login_date')">
            {{ profile?.login_date }}
          </DescriptionsItem>
        </Descriptions>
      </div>
    </div>
  </Card>
</template>
