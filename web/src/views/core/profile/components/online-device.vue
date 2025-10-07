<script setup lang="ts">
import type { Recordable } from "#/components/core/types";

import type { VxeGridProps } from "#/adapter/vxe-table";

import { Popconfirm } from "ant-design-vue";

import { useVxeGrid } from "#/adapter/vxe-table";
import { SystemProfileApi } from "#/api/system/profile";
import { columns } from "./data";

const api = new SystemProfileApi();

const gridOptions: VxeGridProps = {
  columns,
  keepSource: true,
  pagerConfig: {
    enabled: true,
    pageSize:10
  },
  proxyConfig: {
    ajax: {
      query: async ({ page }, formValues = {} as any) => {
        return await api.onlineDevice({
          page: page.currentPage,
          limit: page.pageSize,
          ...formValues,
        });
      },
    },
  },
  rowConfig: {
    keyField: "id",
  },
  toolbarConfig: {
      custom: true,
      export: false,
      refresh: { code: "query" },
      zoom: true,
      slots: {
        buttons: "toolbar-buttons",
      },
    },
    id: 'system-profile-index',
};

const [BasicTable, tableApi] = useVxeGrid({ gridOptions });

async function handleForceOffline(row: Recordable<any>) {
  await api.kickoutByTokenValue(row.key);
  await tableApi.query();
}
</script>

<template>
  <div>
    <BasicTable :table-title="$t('system.user.profile.online_device.table.title')">
      <template #action="{ row }">
        <Popconfirm
          :title="$t('system.user.profile.online_device.table.columns.actions.force_offline.confirm',`${row.user_name}`)"
          placement="left"
          @confirm="handleForceOffline(row)"
        >
          <a-button danger size="small" type="link">{{$t('system.user.profile.online_device.table.columns.actions.force_offline.label')}}</a-button>
        </Popconfirm>
      </template>
    </BasicTable>
  </div>
</template>
