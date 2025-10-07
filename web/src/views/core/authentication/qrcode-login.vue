<script lang="ts" setup>
import { AuthenticationQrCodeLogin } from '#/components/core/effects/common-ui';
import { LOGIN_PATH } from '#/components/core/constants';

defineOptions({ name: 'QrCodeLogin' });
</script>

<template>
  <AuthenticationQrCodeLogin :login-path="LOGIN_PATH" />
</template>
