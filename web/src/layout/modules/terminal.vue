<script setup lang="ts">
import {
  computed,
  h,
  nextTick,
  onMounted,
  reactive,
  ref,
  useTemplateRef,
  watch,
} from "vue";

import { useDrawer, useModal } from "#/components/common-ui";
import { $t } from "#/locale";
import { cloneDeep } from "#/components/common/utils";

import { useForm } from "#/adapter/form";

import { TerminalApi } from "#/api/dev/terminal";
import {
  Button,
  Space,
  Dropdown,
  Menu,
  MenuItem,
  Alert,
  Empty,
  Timeline,
  TimelineItem,
  Card,
  Tag
} from "ant-design-vue";
import {
  DownloadOutlined,
  ClearOutlined,
  SettingOutlined,
  CodeOutlined,
  TagOutlined,
  ClockCircleOutlined,
  ReloadOutlined,
  DeleteOutlined,
  DownOutlined,
  RollbackOutlined,
} from "@ant-design/icons-vue";
import { DictEnum, taskStatus } from "#/components/common/constants";
import { useTerminalStore } from "#/store";
import { confirm, getDictOptions } from "#/utils";

const emit = defineEmits(["retry", "delete", "toggle-log"]);

const isUpdate = ref<Boolean>(false);
const api = new TerminalApi();
const scrollContainer = ref<HTMLElement | null>(null);

const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-12",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 120,
  },
  schema: [
    {
      component: "Select",
      fieldName: "npm_registry",
      defaultValue: "npm",
      label: $t("dev.terminal.form.npm_registry"),
      componentProps: {
         placeholder: $t("dev.terminal.form.placeholder.npm_registry"),
        allowClear: true,
        options: [
          { label: "NPM", value: "npm" },
          { label: "Taobao", value: "taobao" },
          { label: "Tencent", value: "tencent" },
          { label: "未知", value: "unknown" },
        ],
        onChange:(value)=>{
          changeRegistry(value,'npm')
        }
      },
    },
    {
      component: "Select",
      fieldName: "composer_registry",
      defaultValue: "composer",
      label: $t("dev.terminal.form.composer_registry"),
      componentProps: {
        placeholder: $t("dev.terminal.form.placeholder.composer_registry"),
        allowClear: true,
        options: [
          { label: "Composer", value: "composer" },
          { label: "Huawei", value: "huawei" },
          { label: "Aliyun", value: "aliyun" },
          { label: "Tencent", value: "tencent" },
        ],
         onChange:(value)=>{
          changeRegistry(value,'composer')
        }
      },
    },
    {
      component: "Select",
      fieldName: "package_manager",
      label: $t("dev.terminal.form.package_manager"),
      defaultValue: "pnpm",
      componentProps: {
        placeholder: $t("dev.terminal.form.placeholder.package_manager"),
        allowClear: true,
        options: [
          { label: "NPM", value: "npm" },
          { label: "PNPM", value: "pnpm" },
          { label: "YARN", value: "yarn" },
        ],
         onChange:(value)=>{
          changePackageManager(value);
        }
      },
    },
    {
      fieldName: "automatic_cleanup_task",
      label: $t("dev.terminal.form.automatic_cleanup_task"),
      component: "RadioGroup",
      defaultValue: 1,
      componentProps: {
        buttonStyle: "solid",
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: "button",
         onChange:(value)=>{
          //调用接口
          console.log(value)

        }
      },
    },
  ],
  showDefaultActions: false,
  wrapperClass: "grid-cols-12",
});

const [Modal, modalApi] = useModal({
  destroyOnClose:true,
  draggable: true,
  fullscreenButton: false,
  async onOpenChange(isOpen) {
    if (isOpen) {
      terminal.state.showConfig=false;
    }
  },
});

const terminal = useTerminalStore();

type SourceType = "npm" | "composer";

const state = reactive({
  registryLoading: false,
  registryLoadingType: "pnpm",
  packageManagerLoading: false,
});

/**
 * 或者状态
 * @param status
 */
const getTaskStatus = (status: number) => {
  const statusMap: Record<number, { statusText: string; statusType: any }> = {
    [taskStatus.Waiting]: {
      statusText: $t("dev.terminal.status.waiting"),
      statusType: "purple",
    },
    [taskStatus.Connecting]: {
      statusText: $t("dev.terminal.status.connecting"),
      statusType: "orange",
    },
    [taskStatus.Executing]: {
      statusText: $t("dev.terminal.status.executing"),
      statusType: "orange",
    },
    [taskStatus.Success]: {
      statusText: $t("dev.terminal.status.success"),
      statusType: "green",
    },
    [taskStatus.Failed]: {
      statusText: $t("dev.terminal.status.failed"),
      statusType: "red",
    },
    [taskStatus.Unknown]: {
      statusText: $t("dev.terminal.status.unknown"),
      statusType: "blue",
    },
  };

  return (
    statusMap[status] || {
      statusText: $t("dev.terminal.status.unknown"),
      statusType: "blue",
    }
  );
};

const addTerminalTask = (
  command: string,
  pm: boolean,
  blockOnFailure = true,
  extend = "",
  callback: Function = () => {}
) => {
  if (pm) {
    terminal.addTaskPM(command, blockOnFailure, extend, callback);
  } else {
    terminal.addTask(command, blockOnFailure, extend, callback);
  }

  // 任务列表滚动条滚动到底部
  nextTick(() => {
    if (scrollContainer.value) {
      scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight;
    }
  });
};

/**
 * 重新发布
 */
const buildPackaging = async () => {
  await confirm($t("dev.terminal.form.confirm.packaging_build"), $t("dev.terminal.form.confirm.title"));
  addTerminalTask("cmdr-build", true, true, "", null);
};

/**
 * 更改包管理
 * @param val 
 */
const changePackageManager = (val: string) => {
  state.packageManagerLoading = true;
  api.config({ manager: val })
    .then((res) => {
      if (res.code == 0) {
        terminal.changePackageManager(val);
      }
    })
    .finally(() => {
      state.packageManagerLoading = false;
    });
};

/**
 * 更改源
 * @param val 
 * @param type 
 */
const changeRegistry = (val: string, type: SourceType) => {
  const oldVal = type == 'npm' ? terminal.state.npmRegistry : terminal.state.composerRegistry
  terminal.changeRegistry(val, type)
  state.registryLoading = true
  state.registryLoadingType = type
  terminal.addTask(`set-${type}-registry.${val}`, false, '', async (res: taskStatus) => {
      state.registryLoading = false
      if (res == taskStatus.Failed || res == taskStatus.Unknown) {
          await confirm($t('dev.terminal.form.confirm.source_command'), $t('dev.terminal.form.confirm.title'));
          terminal.changeRegistry(oldVal, type)
          const field= type== "npm"?"npm_registry":"composer_registry"
          formApi.setFieldValue(field,oldVal)
      }
  })
};


/**
 * 是否显示配置
 */
const isConfigVisible = computed(() => {
  return terminal.state.showConfig;
});

onMounted(() => {
  terminal.init();
});

// 空状态图像
const simpleImage = Empty.PRESENTED_IMAGE_SIMPLE;

const title = computed(() => {
  return terminal.state.showConfig ? $t('dev.terminal.form.title') : $t('dev.terminal.title');
});
</script>

<template>
  <Modal
    :title="title"
    class="w-[700px]"
    :footerClass="{
      'flex m-2': true,
      'justify-end': isConfigVisible,
      'justify-center': !isConfigVisible,
    }"
  >
    <div v-show="!terminal.state.showConfig">
      <!-- <Alert message="演示环境已禁用命令执行" type="warning" closable /> -->
      <!-- 任务执行内容 -->
      <div ref="scrollContainer" class="scroll-content pt-5 pl-5 pr-5" :min-height="500">
        <!-- 任务时间线 -->
        <Timeline v-if="terminal.state.taskList.length" mode="left">
          <TimelineItem
            v-for="(item, idx) in terminal.state.taskList"
            :key="idx"
            :color="getTaskStatus(item.status)['statusType']"
            class="task-item"
            :class="'task-status-' + item.status"
          >
            <!-- dot小圆点 -->
            <template #dot><ClockCircleOutlined style="font-size: 16px" /></template>
            <!-- 时间标签 -->
            <h3 class="task-title">{{ item.date }}</h3>
            <!-- 任务卡片 -->
            <Card>
              <div class="task-header">
                <!-- 左侧内容区域 -->
                <div class="header-left">
                  <!-- 状态标签 -->
                  <Tag :color="getTaskStatus(item.status)['statusType']">
                    {{ getTaskStatus(item.status).statusText }}
                  </Tag>

                  <!-- 失败且阻塞的任务标签 -->
                  <Tag
                    class="block-on-failure-tag"
                    v-if="
                      (item.status == taskStatus.Failed ||
                        item.status == taskStatus.Unknown) &&
                      item.blockOnFailure
                    "
                    color="orange"
                  >
                    {{ $t("dev.terminal.block_on_failure") }}
                  </Tag>

                  <!-- 执行中或连接中的任务标签 -->
                  <Tag
                    class="block-on-failure-tag"
                    v-if="
                      item.status == taskStatus.Executing ||
                      item.status == taskStatus.Connecting
                    "
                    color="red"
                  >
                    {{ $t("dev.terminal.not_refresh_browser") }}
                  </Tag>

                  <!-- 命令文本 -->
                  <span class="command">{{ item.command }}</span>
                </div>

                <!-- 右侧操作按钮 -->
                <div class="header-right">
                  <Button
                    :title="$t('dev.terminal.actions.retry')"
                    size="small"
                    shape="circle"
                    type="primary"
                    class="warning-button"
                    @click="terminal.retryTask(idx)"
                  >
                    <template #icon
                      ><ReloadOutlined
                        style="line-height: 10px; font-size: 10px; display: block"
                    /></template>
                  </Button>

                  <Button
                    :title="$t('dev.terminal.actions.delete')"
                    size="small"
                    shape="circle"
                    type="primary"
                    danger
                    @click="terminal.delTask(idx)"
                  >
                    <template #icon
                      ><DeleteOutlined
                        style="line-height: 10px; font-size: 10px; display: block"
                    /></template>
                  </Button>
                </div>
              </div>

              <!-- 执行日志 -->
              <template v-if="item.status != taskStatus.Waiting">
                <div
                  v-if="
                    item.status != taskStatus.Connecting &&
                    item.status != taskStatus.Executing
                  "
                  @click="terminal.setTaskShowMessage(idx)"
                  class="toggle-message-display"
                >
                  <span>{{ $t("dev.terminal.actions.command_run_log") }}</span>
                  <DownOutlined
                    :style="{ fontSize: '14px' }"
                    :class="{ 'arrow-up': item.showMessage }"
                  />
                </div>

                <div
                  v-if="
                    item.status == taskStatus.Connecting ||
                    item.status == taskStatus.Executing ||
                    (item.status > taskStatus.Executing && item.showMessage)
                  "
                  class="exec-message"
                  :class="'exec-message-' + item.uuid"
                >
                  <pre
                    v-for="(msg, index) in item.message"
                    :key="index"
                    class="message-item"
                    >{{ msg }}</pre
                  >
                </div>
              </template>
            </Card>
          </TimelineItem>
        </Timeline>

        <!-- 空状态 -->
        <Empty
          v-else
          :image="simpleImage"
          :description="$t('dev.terminal.empty')"
          class="empty-state"
        />
      </div>
    </div>

    <div v-if="isConfigVisible">
      <Form></Form>
    </div>

    <!-- 底部按钮 -->
    <template #footer>
      <div v-show="!isConfigVisible">
        <Space wrap>
          <Button v-blur @click="addTerminalTask('test', true, false)">
            <template #icon>
              <CodeOutlined />
            </template>
            {{ $t("dev.terminal.actions.test") }}
          </Button>
          <Dropdown arrow placement="topLeft">
            <Button>
              <template #icon>
                <DownloadOutlined />
              </template>
              {{ $t("dev.terminal.actions.install.title") }}
            </Button>
            <template #overlay>
              <Menu>
                <MenuItem>
                  <a href="javascript:;" @click="addTerminalTask('cmdr-install', true)">
                    {{ $t("dev.terminal.actions.install.pnpm_install") }}
                  </a>
                </MenuItem>
                <MenuItem>
                  <a
                    href="javascript:;"
                    @click="addTerminalTask('composer.update', false)"
                  >
                    {{ $t("dev.terminal.actions.install.composer_update") }}</a
                  >
                </MenuItem>
              </Menu>
            </template>
          </Dropdown>

          <Button v-blur @click="buildPackaging()">
            <template #icon>
              <TagOutlined />
            </template>
            {{ $t("dev.terminal.actions.republish") }}</Button
          >
          <Button v-blur @click="terminal.clearSuccessTask()">
            <template #icon>
              <ClearOutlined />
            </template>
            {{ $t("dev.terminal.actions.empty_task") }}
          </Button>
          <Button v-blur @click="terminal.toggleConfigDialog()">
            <template #icon>
              <SettingOutlined />
            </template>
            {{ $t("dev.terminal.actions.setting") }}
          </Button>
        </Space>
      </div>
      <div v-show="isConfigVisible">
        <Button v-blur @click="terminal.toggleConfigDialog()">
          <template #icon>
            <RollbackOutlined />
          </template>
          {{ $t("dev.terminal.actions.return_terminal") }}
        </Button>
      </div>
    </template>
  </Modal>
</template>

<style scoped>
/* 关键布局 - 卡片头部 */
.task-header {
  display: flex;
  justify-content: space-between; /* 左右两端对齐 */
  align-items: center;
  width: 100%;
}

/* 左侧内容区域 */
.header-content {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

/* 右侧按钮容器 */
.header-right {
  display: flex;
  gap: 4px;
  margin-left: auto; /* 按钮靠右 */
}

/* 消息切换按钮 - 居中显示 */
.toggle-message-display {
  cursor: pointer;
  display: flex;
  justify-content: center; /* 水平居中 */
  align-items: center;
  margin-top: 8px;
  color: #727277;
  gap: 4px; /* 文本和图标间距 */
}

/* 箭头旋转效果 */
.arrow-up {
  transform: rotate(180deg);
}

/* 其他样式... */
.block-on-failure-tag {
  margin-left: 8px;
}

.command {
  word-break: break-all;
}

/* 消息内容区域 */
.exec-message {
  margin-top: 8px;
  padding: 8px;
  background-color: #f5f5f5;
  border-radius: 4px;
  max-height: 200px;
  overflow-y: auto;
}

.message-item {
  margin: 0;
  white-space: pre-wrap;
  color: darkgray;
}

/* 警告按钮样式 */
.warning-button {
  background-color: #fffbe6;
  border-color: #ffe58f;
  color: #d48806;
}

.warning-button:hover {
  background-color: #ffe58f;
  border-color: #ffd591;
  color: #ad6800;
}
</style>
