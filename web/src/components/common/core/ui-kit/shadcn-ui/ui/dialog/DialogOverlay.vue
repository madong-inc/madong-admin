<script setup lang="ts">
import { inject } from 'vue';

import { useScrollLock } from '#/components/common/effects/hooks';

useScrollLock();
const id = inject('DISMISSABLE_MODAL_ID');
</script>
<template>
  <div :data-dismissable-modal="id" class="bg-overlay z-popup inset-0"></div>
</template>
