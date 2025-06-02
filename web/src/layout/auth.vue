<script lang="ts" setup>
import { computed } from 'vue';

import { AuthPageLayout } from '../components/common/effects/layouts/authentication';
import { preferences } from '#/components/common/core/preferences';

import { $t } from '#/locale';

const appName = computed(() => preferences.app.name);
const logo = computed(() => preferences.logo.source);
const clickLogo = () => {};
</script>

<template>
  <AuthPageLayout
    :app-name="appName"
    :logo="logo"
    :page-description="$t('authentication.pageDesc')"
    :page-title="$t('authentication.pageTitle')"
    :click-logo="clickLogo"
  >
    <!-- 自定义工具栏 -->
    <!-- <template #toolbar></template> -->
  </AuthPageLayout>
</template>
