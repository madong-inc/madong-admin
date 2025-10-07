<script setup lang="ts">
import { computed, reactive, ref } from "vue";

import {  useDrawer } from "#/components/core/effects/common-ui";
import { $t } from "#/locale";
import { cloneDeep } from "#/components/core/utils";

import { useForm } from "#/adapter/form";

import { omit } from "lodash-es";

import type{ SystemMessageRow } from "#/api/system/message";
import { SystemMessageApi } from "#/api/system/message";
import Detail from "./detail.vue";
import dayjs from "dayjs";

const emit = defineEmits<{ reload: [] }>();

const api = new SystemMessageApi();
const record=ref<SystemMessageRow>();
const isRead= ref<Boolean>(false);

interface EmailMessage {
  title: string;
  content: string;
  sender: string;
  recipient: string;
  date: string | dayjs.Dayjs;
  salutation?: string;
  signature?: string;
}

const message = reactive<EmailMessage>({
  title: "",//标题
  content: "",//内容
  sender: "",//发送人
  recipient: "",//接收人
  date: "",//日期
  salutation: "",//招呼
  signature: ''//签名
});

/**
 * 弹窗
 */
const [Drawer, drawerApi] = useDrawer({
  header:false,
  footer:false,
  async onOpenChange(isOpen) {
    if (isOpen) {
      isRead.value=false;
      record.value = drawerApi.getData<Record<string, any>>().record;
      if (record.value.id) {
        const data = await api.get(record.value.id);
        message.title= data?.title||'';
        message.content=data?.content||'';
        message.sender=data?.sender?.real_name||'Administrator';
        message.recipient='';
        message.date=data.created_date||'';
        isRead.value=true;
        //渲染成功后标记已读状态
        if(record.value.status !== 'read'){
          api.updateRead(record.value.id);
          emit('reload');
        }
      }
    }
  }
});


</script>

<template>
  <Drawer class="w-[700px]">
    <Detail :message  v-if="isRead"/>
  </Drawer>
</template>
