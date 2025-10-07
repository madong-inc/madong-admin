<script lang="ts" setup>
import { Fallback } from '#/components/core/effects/common-ui';

defineOptions({ name: 'FallbackOffline' });
</script>

<template>
  <Fallback status="offline" />
</template>
