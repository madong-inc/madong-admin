<script lang="ts" setup>
import { ref, reactive } from "vue";
import { IconifyIcon } from "#/components/common/icons";
import { HttpMethod, RouteItem, SystemRuleApi } from "#/api/system/rule";
import { useModal, ColPage } from '#/components/common-ui';
import { SystemMenuApi } from '#/api/system/menu';
import { $t } from '#/locale';
import {
  Button,
  Card,
  Tooltip,
  Input,
  Tree,
  message,
  Row,
  Col,
} from "ant-design-vue";

const api = new SystemMenuApi();
const routeApi = new SystemRuleApi();

const emit = defineEmits<{
  success: [];
}>();

const props = reactive({
  leftCollapsedWidth: 5,
  leftCollapsible: true,
  leftMaxWidth: 50,
  leftMinWidth: 20,
  leftWidth: 30,
  resizable: true,
  rightWidth: 70,
  splitHandle: false,
  splitLine: false,
});

const record = ref();
const gridApi = ref();
const keyword = ref('');
const treeData = ref<any>([]);
const expandedKeys = ref<(string | number)[]>();
const autoExpandParent = ref<boolean>(true);
const defaultSelectedKeys = ref<(string | number)[]>([])
const selectedRoute = ref<Pick<RouteItem, "path" | "method">[]>([]);
const routeList = ref<RouteItem[]>([]);

/**
 * 模态框
 */
const [Modal, modalApi] = useModal({
  draggable: true,
  async onOpenChange(isOpen) {
    record.value = isOpen ? modalApi.getData()?.record || {} : {};
    if (isOpen) {
      //1.0 获取左侧分组数据
      getTree()
      //2.0 获取右侧默认数据
      loadData(defaultSelectedKeys.value[0] || null, null);
    }
  },
  onConfirm() {
    if (selectedRoute.value.length !== 0) {
      modalApi.setState({ loading: true, confirmLoading: true });
      const data = mergePropertiesToRoutes(selectedRoute.value, { pid: record.value.id });
      api.batchStore({ menus: data })
        .then(() => {
          message.success($t('common.message.success'));
          emit('success');
          modalApi.close();
        })
        .finally(() => {
          modalApi.setState({ loading: false, confirmLoading: false });
        });
    }
  },
});

/**
 * 加载Tree数据
 */
const getTree = async () => {
  //1.0清空选中路由列表
  selectedRoute.value = [];
  //2.0获取tree数据
  const result = await routeApi.cate({ page: 1, limit: 9999, format: 'tree' });
  treeData.value = result || [];
  //3.0将第一个子级第一个节点设为默认节点
  defaultSelectedKeys.value = getFirstLeafKeys(treeData.value);
}

/**
 * 加载列表数据
 * @param cateId
 * @param name
 */
const loadData = async (cateId: any, name: any) => {
  const result = await routeApi.list({ page: 1, limit: 9999, cate_id: cateId, LIKE_name: name });
  //@ts-ignore
  routeList.value = result?.items || [];
}

/**
 * 搜索
 */
const handleSearch = () => {
  loadData(defaultSelectedKeys.value[0] || null, keyword.value);
}

/**
 * 路由同步
 */
const handleSync = () => {
  modalApi.setState({ loading: true, confirmLoading: true });
  routeApi.sync({}).then(() => {
    getTree();
    message.success($t('common.message.success'));
  })
    .finally(() => {
      modalApi.setState({ loading: false, confirmLoading: false });
    });
}

/**
 * 获取tree第一个子节点
 * @param tree
 */
const getFirstLeafKeys = (tree: any) => {
  //@ts-ignore
  const findLeafKeys = (nodes: any) => {
    const keys = [];
    for (const node of nodes) {
      if (node.children && node.children.length > 0) {
        // 如果有子节点，递归查找
        keys.push(...findLeafKeys(node.children));
      } else {
        // 如果没有子节点，是叶子节点，加入 keys
        keys.push(node.id);
      }
    }
    return keys;
  };
  const allLeafKeys = findLeafKeys(tree);
  return allLeafKeys.length > 0 ? [allLeafKeys[0]] : [];
}

/**
 * 展开/收起节事件
 * @param keys
 */
const onExpand = (keys: string[]) => {
  expandedKeys.value = keys;
  autoExpandParent.value = true;
};

/**
 * 树节点点击事件
 * @param selectedKeys
 * @param e
 */
const handleSelect = (selectedKeys: any, e: any) => {
  loadData(selectedKeys[0] || null, null)
};

// 统一方法类型格式
const normalizeMethod = (method: string): HttpMethod => {
  const lowerMethod = method.toLowerCase() as HttpMethod;
  if (!["get", "post", "put", "delete", "patch"].includes(lowerMethod)) {
    throw new Error(`非标准HTTP方法: ${method}`);
  }
  return lowerMethod;
};

/**
 * 路由列表选中判断
 * @param item
 */
const isSelected = (item: RouteItem): boolean => {
  return selectedRoute.value.some(
    (selected) =>
      selected.path === item.path &&
      normalizeMethod(selected.method) === normalizeMethod(item.method)
  );
};

/**
 * 卡片点击事件
 * @param item
 */
const handleCardClick = (item: RouteItem): void => {
  const normalizedMethod = normalizeMethod(item.method);
  //@ts-ignore
  const target: Pick<RouteItem, "path" | "method" | "title" | "type" | "methods"> = {
    path: item.path,
    method: item.method,
    methods: item.method,
    title: item.name,
    type: 4, //接口
  };

  const existIndex = selectedRoute.value.findIndex(
    (selected) =>
      selected.path === target.path &&
      normalizeMethod(selected.method) === normalizedMethod
  );

  existIndex > -1
    ? selectedRoute.value.splice(existIndex, 1)
    : selectedRoute.value.push(target);
};

/**
 * 为二维数组中的每个对象添加键值对
 * @param matrix 二维数组（每个元素必须是对象数组）
 * @param keyValuePairs 需合并的键值对
 */
const mergePropertiesToRoutes = (routes: any, propertiesToMerge: any): RouteItem[] => {
  //@ts-ignore
  return routes.map((route) => ({
    ...route,
    ...propertiesToMerge, // 批量合并属性
  }));
};
</script>

<template>
  <Modal :title="$t('system.menu.api.title')" class="w-[60%]">
    <div class="common-form">
      <ColPage v-bind="props" style="height: 500px;">
        <template #description>
          <p class="mr-2">1.接口可多选，可重复添加；</p>
          <p class="mr-2">2.开发者按照后端路由规则进行添加路由定义；</p>
          <p class="mr-2">3.添加后即可在此选择对应的接口；</p>
          <Input class="mt-2" style="width: 250px" v-model:value="keyword"></Input>
          <Button class="ml-2" type="primary" @click="handleSearch">{{ $t('system.menu.api.search') }}</Button>
          <Button class="ml-2" @click="handleSync">{{ $t('system.menu.api.sync') }}</Button>
        </template>
        <template #left="{ isCollapsed, expand }">
          <div v-if="isCollapsed" @click="expand">
            <Tooltip title="点击展开左侧">
              <Button shape="circle" type="primary">
                <template #icon>
                  <IconifyIcon class="text-2xl" icon="bi:arrow-right" />
                </template>
              </Button>
            </Tooltip>
          </div>
          <div
            v-else
            :style="{ minWidth: '200px' }"
            class="border-border bg-card mr-2 rounded-[var(--radius)] border p-2"
          >
            <!-- 树表 -->
            <Tree
              :expanded-keys="expandedKeys"
              :auto-expand-parent="autoExpandParent"
              :tree-data="treeData"
              :fieldNames="{title: 'name', key: 'id', children: 'children' }"
              v-model:selected-keys="defaultSelectedKeys"
              @expand="onExpand"
              @select="handleSelect"
            ></Tree>
          </div>
        </template>
        <Row :gutter="{ xs: 8, sm: 16, md: 24 }">
          <Col
            v-for="(item, index) in routeList"
            :key="index"
            :xs="{ span: 24 }"
            :md="{ span: 12 }"
            :lg="{ span: 12, offset: 0 }"
          >
            <Card
              size="small"
              class="mb-2 cursor-pointer transition-all duration-300"
              :class="{
                'selected-route-card': isSelected(item),
                'hover:shadow-md': !isSelected(item),
              }"
              @click.stop="handleCardClick(item)"
            >
              <p class="text-inherit">接口名称：{{ item.name }}</p>
              <p class="text-inherit">接口方式：{{ item.method }}</p>
              <p class="text-inherit">接口地址：{{ item.path }}</p>
            </Card>
          </Col>
        </Row>
      </ColPage>
    </div>
  </Modal>
</template>

<style scoped lang="less">
/* 修复黑暗模式选中问题 */
.selected-route-card {
  background-color: rgba(24, 144, 255, 0.1);
  border-color: rgba(24, 144, 255, 0.5);
  transition: all 0.3s ease;
  
  /* 黑暗模式适配 */
  .dark & {
    background-color: rgba(24, 144, 255, 0.2);
    border-color: rgba(24, 144, 255, 0.7);
    color: rgba(255, 255, 255, 0.85);
  }
}

/* 确保文字在黑暗模式下可读 */
:deep(.ant-card-body) {
  .dark & {
    color: rgba(255, 255, 255, 0.85);
    
    p {
      color: inherit;
    }
  }
}

/* 悬停效果优化 */
:deep(.ant-card):not(.selected-route-card):hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  
  .dark & {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  }
}

/* 页面容器样式 */
.page-container {
  overflow-y: auto;
  display: flex;
  height: 100%;
}
</style>