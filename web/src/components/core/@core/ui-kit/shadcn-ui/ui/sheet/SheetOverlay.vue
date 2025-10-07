<script setup lang="ts">
import { inject } from 'vue';

import { useScrollLock } from '#/components/core/effects/hooks';

useScrollLock();
const id = inject('DISMISSABLE_DRAWER_ID');
</script>
<template>
  <div :data-dismissable-drawer="id" class="bg-overlay z-popup inset-0"></div>
</template>
