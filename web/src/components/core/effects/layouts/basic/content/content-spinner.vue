<script lang="ts" setup>
import { BasicSpinner } from '#/components/core/@core/ui-kit/shadcn-ui';

import { useContentSpinner } from './use-content-spinner';

defineOptions({ name: 'LayoutContentSpinner' });

const { spinning } = useContentSpinner();
</script>
<template>
  <BasicSpinner :spinning="spinning" />
</template>
