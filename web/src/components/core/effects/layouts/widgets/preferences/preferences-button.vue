<script lang="ts" setup>
import { Settings } from '#/components/core/icons';

import { BasicIconButton } from '#/components/core/@core/ui-kit/shadcn-ui';

import Preferences from './preferences.vue';

const emit = defineEmits<{ clearPreferencesAndLogout: [] }>();

function clearPreferencesAndLogout() {
  emit('clearPreferencesAndLogout');
}
</script>
<template>
  <Preferences @clear-preferences-and-logout="clearPreferencesAndLogout">
    <BasicIconButton>
      <Settings class="text-foreground size-4" />
    </BasicIconButton>
  </Preferences>
</template>
