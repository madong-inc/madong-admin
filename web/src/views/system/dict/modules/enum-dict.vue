<script lang="ts" setup>
import { reactive, ref } from 'vue';

import { useDrawer, useModal } from '#/components/core/effects/common-ui';

import { Col, Row ,Tag} from 'ant-design-vue';

import {
  useVxeGrid,
  type VxeGridListeners,
  type VxeGridProps,
} from '#/adapter';
import {SystemDictApi } from '#/api/system/dict';
import { VxeTable,VxeColumn } from 'vxe-table';
import { $t } from '#/locale';

const api = new SystemDictApi();

const type = ref('enum');
const dataSource = ref([]);

/**
 * 前端分页处理待优化添加搜索
 * @param params 
 */
const handlePage = (params: any) => {
  const { page, limit } = params;
  return Promise.resolve({
    total: dataSource.value.length,
    totalPage: Math.ceil(dataSource.value.length / limit),
    items: dataSource.value.slice((page - 1) * limit, page * limit),
  });
};

const gridOptions = reactive<VxeGridProps<any>>({
  toolbarConfig: {
    enabled: false,
  },
  expandConfig: {
    padding: true
  },
  columns: [
    { type: 'expand', width: 80, slots: { content: 'expandedRowRender' } },
    {
      field: 'dict_key',
      title: $t('system.dict.enum.columns.dict_key'),
      align: 'left'
    },
    {
      field: 'name',
      title: $t('system.dict.enum.columns.name'),
      align: 'left',
    },
  ],
  data: [],
  height: "auto",
  keepSource: true,
  pagerConfig: {
    pageSize: 20,
  },
  proxyConfig: {
    ajax: {
      query: async ({ page }, formValues) => {
        return await handlePage({
          page: page.currentPage,
          limit: page.pageSize,
          ...formValues,
        });
      },
    },
  },
});
const gridEvents: VxeGridListeners<any> = {};
const [Grid, gridApi] = useVxeGrid({
  gridOptions,
  gridEvents,
});

const [Drawer, drawerApi] = useDrawer({
  footer: false,
  onOpenChange(isOpen) {
    type.value = isOpen ? drawerApi.getData()?.type : 'enum';
    if (isOpen) {
      api.enumDictList({}).then((res) => {
        dataSource.value = res;
        gridApi.reload();
      });
    }
  },
});


defineExpose(drawerApi);
</script>
<template>
  <div>
    <Drawer :title="$t('system.dict.enum.title')" class="w-[60%]">
      <Grid>
        <template #expandedRowRender="{ row }">
          <div class="m-subtable">
            <!-- 标题 -->
            <div class="m-subtable-header-wrapper">
              <Row class="m-subtable-header m-dict-item">
                <Col :span="8">{{ $t('system.dict.enum.columns.case') }}</Col>
                <Col :span="8">{{ $t('system.dict.enum.columns.label') }}</Col>
                <Col :span="4">{{ $t('system.dict.enum.columns.value') }}</Col>
                <Col :span="4">{{ $t('system.dict.enum.columns.color') }}</Col>
              </Row>
            </div>

            <!-- 内容 -->
            <div class="m-subtable-body">
              <template v-for="(item, index) in row.items" :key="index">
                <Row class="m-dict-item" :class="{ 'has-key': item.dict_item_Key }">
                  <Col :span="8">{{ item.dict_item_key }}</Col>
                  <Col :span="8">{{ item.dict_item_label }}</Col>
                  <Col :span="4">
                    <span class="value-tag">{{ item.dict_item_value }}</span>
                  </Col>
                  <Col :span="4">
                    <span v-if="item.color !== ''"
                      ><Tag :color="item.color">{{ item.color }}</Tag></span
                    >
                    <span v-if="item.color == ''">N/A</span>
                  </Col>
                </Row>
              </template>
            </div>
          </div>
        </template>
      </Grid>
    </Drawer>
  </div>
</template>
<style lang="less" scoped>
.m-subtable-header {
  font-weight: 600; // 加粗
  color: #303133; // 深色增强可读性
  // .ant-col {
  //   padding-top: 10px;
  //   padding-bottom: 10px;
  // }
}
.m-dict-item {
  padding-left: 80px;
  line-height: 36px;
}

.m-subtable {
  //border-top: 1px solid #ebeef5; // 整体顶部边线

  // 首行数据上边线
  .m-dict-item:first-of-type:not(.m-subtable-header) {
    border-top: 1px solid #e4e7ed;
  }

  // 斑马纹+悬停交互
  .m-dict-item:not(.m-subtable-header) {
    &:nth-child(even) {
      background: #fafafa;
    }
    &:hover {
      background: #f5f7fa;
    }
  }
}
</style>
