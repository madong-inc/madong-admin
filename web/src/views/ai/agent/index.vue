<script setup lang="ts">
import { Page } from '#/components/common-ui';

import Chat from '#/views/ai/agent/chat/index.vue';
</script>

<template>
  <Page title="实验功能-大模型聊天" class="wp-chat-view">
    <template #description> 孵化阶段,基于 互联网大模型 </template>
    <Chat />
  </Page>
</template>
