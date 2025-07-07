<script setup lang="ts">
import { ref, h } from "vue";

import { useDrawer, Page } from "#/components/common-ui";

import GrantTenantIndex from "#/views/platform/tenant-subscription/modules/grant-tenant.vue";
import { Button, message, Space } from "ant-design-vue";
import { SaveOutlined } from "@ant-design/icons-vue";

import { useVxeGrid, isCheckboxChecked } from "#/adapter";
import { useGrantTenantColumns } from "../data";
import { $t } from "#/locale";
import { TenantApi } from "#/api/platform/tenant";
import type { TenantRow } from "#/api/platform/tenant";
import { TenantSubscriptionApi } from "#/api/platform/tenant-subscription";

const record = ref();
const subscriptions = ref([]);
const api = new TenantApi();
const tenantSubApi = new TenantSubscriptionApi();

const [Drawer, drawerApi] = useDrawer({
  async onOpenChange(isOpen) {
    record.value = isOpen ? drawerApi.getData()?.record : {};
    if (isOpen) {
      subscriptions.value = await api.tenantSubscriptionIds({
        tenant_id: record?.value?.id,
      });
    }
  },
});

const [Grid, gridApi] = useVxeGrid({
  // formOptions: {
  //   commonConfig: {
  //     labelWidth: 80,
  //     componentProps: {
  //       allowClear: true,
  //     },
  //   },
  //   collapsed: true,
  //   schema: querySchema(),
  //   submitOnChange: true,
  // },
  // showSearchForm: false,
  gridOptions: {
    checkboxConfig: {
      // 高亮
      highlight: true,
      // 翻页时保留选中状态
      reserve: true,
      trigger: "cell",
    },
    columns: useGrantTenantColumns(),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as TenantRow) => {
          const res = await tenantSubApi.list({
            page: page.currentPage,
            limit: page.pageSize,
            ...formValues,
          });

          // 数据加载后选中行
          setTimeout(() => {
            //@ts-ignore
            const data = res?.items || [];
            const rowsToSelect = data.filter((row) =>
              subscriptions?.value.includes(row.id)
            );
            gridApi.grid.setCheckboxRow(rowsToSelect, true);
          }, 0);

          return res;
        },
      },
    },
    rowConfig: {
      keyField: "id",
    },
    toolbarConfig: {
      custom: false,
      export: false,
      refresh: false,
      search: false,
      zoom: false,
      slots: {
        buttons: "toolbar-buttons",
      },
    },
    id: "platform-tenant_subscription-index",
  },
});

/**
 * 提交
 */
async function handleConfirm() {
  try {
    const rows = gridApi.grid.getCheckboxRecords();
    const data = {
      id: record?.value?.id,
      data: rows.map((row: TenantRow) => row.id),
    };
    await api.grantSubscription(data);
    drawerApi.close();
  } catch (error) {
    console.error(error);
  } finally {
    drawerApi.lock(false);
  }
}

/**
 * 标题
 */
function getTitle(): string {
  return $t("platform.tenant_subscription.grant_tenant.title");
}
</script>
<template>
  <Drawer :footer="false" class="w-[50%]" :title="getTitle()">
    <Page auto-content-height>
      <Grid>
        <template #toolbar-buttons>
          <Space>
            <Button
              type="primary"
              :icon="h(SaveOutlined)"
              v-access:code="['admin', 'platform:tenant_subscription:gran_tenant']"
              @click="handleConfirm()"
            >
              {{ $t("platform.tenant_subscription.grant_tenant.table.toolbar.save") }}
            </Button>
          </Space>
        </template>
      </Grid>
    </Page>
  </Drawer>
</template>
<style lang="less" scoped></style>
