<script setup lang="ts">
import { MdiGithub, MdiGoogle, MdiQqchat, MdiWechat } from '#/components/common/icons';
import { $t } from '#/locale';

import { BasicIconButton } from '#/components/common/core/ui-kit/shadcn-ui';

defineOptions({
  name: 'ThirdPartyLogin',
});
</script>

<template>
  <div class="w-full sm:mx-auto md:max-w-md">
    <div class="mt-4 flex items-center justify-between">
      <span class="border-input w-[35%] border-b dark:border-gray-600"></span>
      <span class="text-muted-foreground text-center text-xs uppercase">
        {{ $t('authentication.thirdPartyLogin') }}
      </span>
      <span class="border-input w-[35%] border-b dark:border-gray-600"></span>
    </div>

    <div class="mt-4 flex flex-wrap justify-center">
      <BasicIconButton class="mb-3">
        <MdiWechat />
      </BasicIconButton>
      <BasicIconButton class="mb-3">
        <MdiQqchat />
      </BasicIconButton>
      <BasicIconButton class="mb-3">
        <MdiGithub />
      </BasicIconButton>
      <BasicIconButton class="mb-3">
        <MdiGoogle />
      </BasicIconButton>
    </div>
  </div>
</template>
