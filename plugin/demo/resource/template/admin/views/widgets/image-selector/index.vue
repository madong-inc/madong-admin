<template>
  <div class="test-page p-5">
    <h1 class="text-2xl font-bold mb-5">图片选择器组件测试</h1>
    
    <!-- 单选测试 -->
    <div class="mb-8 p-5 bg-white rounded-lg shadow-sm">
      <h2 class="text-xl font-semibold mb-3 text-g-800">1. 单选模式</h2>
      <div class="flex items-center gap-5">
        <ImageSelector
          v-model="singleImage"
          :multiple="false"
          shape="circle"
          size="medium"
          @confirm="handleSingleConfirm"
        />
        <div class="text-sm">
          <div class="font-medium">已选择:</div>
          <div class="text-g-600 mt-1 break-all">{{ singleImage || '未选择' }}</div>
        </div>
      </div>
    </div>

    <!-- 多选测试 -->
    <div class="mb-8 p-5 bg-white rounded-lg shadow-sm">
      <h2 class="text-xl font-semibold mb-3 text-g-800">2. 多选模式</h2>
      <ImageSelector
        v-model="multipleImages"
        :multiple="true"
        shape="square"
        size="small"
        @confirm="handleMultipleConfirm"
      />
      <div class="mt-3 text-sm">
        <div class="font-medium">已选择 ({{ multipleImages.length }} 张):</div>
        <div class="text-g-600 mt-1 break-all">{{ multipleImages.length > 0 ? multipleImages.join(', ') : '未选择' }}</div>
      </div>
    </div>

    <!-- 大尺寸单选 -->
    <div class="mb-8 p-5 bg-white rounded-lg shadow-sm">
      <h2 class="text-xl font-semibold mb-3 text-g-800">3. 大尺寸单选（带默认值）</h2>
      <div class="flex items-center gap-5">
        <ImageSelector
          v-model="largeImage"
          :multiple="false"
          shape="square"
          size="large"
          :default-selected="'/upload/default-avatar.webp'"
          @confirm="handleLargeConfirm"
        />
        <div class="text-sm">
          <div class="font-medium">已选择:</div>
          <div class="text-g-600 mt-1 break-all">{{ largeImage || '未选择' }}</div>
        </div>
      </div>
    </div>

    <!-- 禁用状态 -->
    <div class="mb-8 p-5 bg-white rounded-lg shadow-sm">
      <h2 class="text-xl font-semibold mb-3 text-g-800">4. 禁用状态</h2>
      <ImageSelector
        v-model="disabledImage"
        :multiple="false"
        shape="circle"
        size="medium"
        :disabled="true"
      />
      <div class="mt-2 text-sm text-g-600">
        禁用状态下的组件不可点击
      </div>
    </div>

    <!-- 代码示例 -->
    <div class="mb-8 p-5 bg-white rounded-lg shadow-sm">
      <h2 class="text-xl font-semibold mb-3 text-g-800">5. 代码示例</h2>
      <ElTabs>
        <ElTabPane label="单选">
          <pre class="bg-g-100 p-3 rounded text-sm overflow-auto">{{ singleCode }}</pre>
        </ElTabPane>
        <ElTabPane label="多选">
          <pre class="bg-g-100 p-3 rounded text-sm overflow-auto">{{ multipleCode }}</pre>
        </ElTabPane>
      </ElTabs>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ImageSelector from '@/components/form/components/image-selector/index.vue'
import { ElMessage } from 'element-plus'
import { $t } from '@/locales'

// 测试数据
const singleImage = ref<string>('')
const multipleImages = ref<string[]>([])
const largeImage = ref<string>('/upload/default-avatar.webp')
const disabledImage = ref<string>('/upload/default-avatar.webp')

// 事件处理
const handleSingleConfirm = (image: string) => {
  ElMessage.success(`单选确认: ${image}`)
}

const handleMultipleConfirm = (images: string[]) => {
  ElMessage.success(`多选确认: 选择了 ${images.length} 张图片`)
}

const handleLargeConfirm = (image: string) => {
  ElMessage.success(`大尺寸确认: ${image}`)
}

// 代码示例
const singleCode = `<ImageSelector
  v-model="singleImage"
  :multiple="false"
  shape="circle"
  size="medium"
  @confirm="handleConfirm"
/>`

const multipleCode = `<ImageSelector
  v-model="multipleImages"
  :multiple="true"
  shape="square"
  size="small"
  @confirm="handleConfirm"
/>`
</script>

<style lang="scss" scoped>
.test-page {
  max-width: 1200px;
  margin: 0 auto;
}
</style>
