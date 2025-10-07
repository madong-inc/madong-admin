<script lang="ts" setup>
import type { DropdownMenuProps } from '#/components/core/@core/ui-kit/shadcn-ui';

import { ChevronDown } from '#/components/core/icons';
import { BasicDropdownMenu } from '#/components/core/@core/ui-kit/shadcn-ui';

defineProps<DropdownMenuProps>();
</script>

<template>
  <BasicDropdownMenu :menus="menus" :modal="false">
    <div
      class="flex-center hover:bg-muted hover:text-foreground text-muted-foreground border-border h-full cursor-pointer border-l px-2 text-lg font-semibold"
    >
      <ChevronDown class="size-4" />
    </div>
  </BasicDropdownMenu>
</template>
