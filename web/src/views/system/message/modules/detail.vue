<template>
  <a-card :bordered="false" class="email-card">
    <!-- 头部 -->
    <div class="email-header">
      <h1 class="email-title">{{ message.title }}</h1>

      <div class="email-meta">
        <span class="meta-item">
          <UserOutlined />
          <span class="sender">{{ message.sender }}</span>
        </span>
        <span class="meta-item">
          <ClockCircleOutlined />
          <span class="date">{{ formatDate(message.date) }}</span>
        </span>
        <span class="meta-item" v-if="message.recipient">
          <MailOutlined />
          <span class="to">给：{{ message.recipient }}</span>
        </span>
      </div>
    </div>

    <!-- 正文 -->
    <div class="email-content">
      <div class="content-block" v-if="message.salutation">
        <div class="salutation">{{ message.salutation }}</div>
      </div>

      <div class="content-block" v-html="formattedContent"></div>

      <div class="signature" v-if="message.signature">
        <div class="signature-line"></div>
        <div class="signature-content" v-html="message.signature"></div>
      </div>
    </div>

    <!-- 操作栏 -->
    <div class="email-actions" v-if="isActions">
      <a-button type="primary" @click="handleReply">
        <template #icon><SwapOutlined /></template>
        回复
      </a-button>
      <a-button @click="handleForward">
        <template #icon><ForwardOutlined /></template>
        转发
      </a-button>
      <a-dropdown :trigger="['hover']">
        <a-button>
          更多
          <DownOutlined />
        </a-button>
        <template #overlay>
          <a-menu @click="handleMoreActions">
            <a-menu-item key="print">
              <PrinterOutlined />
              打印
            </a-menu-item>
            <a-menu-item key="mark-read">
              <CheckCircleOutlined />
              标记已读
            </a-menu-item>
            <a-menu-item key="delete">
              <DeleteOutlined />
              删除
            </a-menu-item>
          </a-menu>
        </template>
      </a-dropdown>
    </div>
  </a-card>
</template>

<script setup lang="ts">
import { ref } from "vue";
import dayjs from "dayjs";
import {
  UserOutlined,
  ClockCircleOutlined,
  MailOutlined,
  SwapOutlined,
  ForwardOutlined,
  DownOutlined,
  PrinterOutlined,
  CheckCircleOutlined,
  DeleteOutlined,
} from "@ant-design/icons-vue";

interface EmailMessage {
  title: string;
  content: string;
  sender: string;
  recipient: string;
  date: string | dayjs.Dayjs;
  salutation?: string;
  signature?: string;
}

const props = defineProps({
  message: {
    type: Object as () => EmailMessage,
    required: true,
  },
});


const isActions = ref<Boolean>(false);

const formattedContent = ref<string>(
  props.message.content
    .replace(/\n\n/g, "</p><p>") // 双换行转段落
    .replace(/\n/g, "<br>") // 单换行转换行
    .replace(/^(.+)$/gm, "<p>$1</p>") // 自动段落包裹
);

const formatDate = (date: string | dayjs.Dayjs) => {
  return dayjs(date).format("YYYY年MM月DD日 HH:mm");
};

// 以下是模拟操作处理
const handleReply = () => {
  /* 回复逻辑 */
};
const handleForward = () => {
  /* 转发逻辑 */
};
const handleMoreActions = (e: { key: string }) => {
  console.log("执行操作:", e.key);
};
</script>

<style scoped>
.email-card {
  max-width: 900px;
  margin: 24px auto;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.email-header {
  padding: 24px;
  border-bottom: 1px solid #f0f0f0;
}

.email-title {
  margin: 0 0 12px 0;
  font-size: 24px;
  font-weight: 600;
  color: #1d2129;
}

.email-meta {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
  font-size: 14px;
  color: #86909c;
}

.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.email-content {
  padding: 24px;
  font-size: 15px;
  line-height: 1.8;
  color: #4e5969;
}

.content-block {
  margin-bottom: 24px;
  white-space: pre-wrap;
}

.salutation {
  font-weight: 500;
  margin-bottom: 16px;
}

.signature {
  margin-top: 32px;
  padding-top: 16px;
  border-top: 1px dashed #e4e7ed;
}

.signature-line {
  width: 60px;
  height: 2px;
  background: #c9cdd4;
  margin-bottom: 8px;
}

.email-actions {
  padding: 16px 24px;
  border-top: 1px solid #f0f0f0;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

:deep(.ant-btn) {
  border-radius: 4px;
}
</style>
