<script setup lang="ts">

import { onMounted, ref } from 'vue';

import { BasicRenderContent } from '#/components/core/@core/ui-kit/shadcn-ui';

import { RedisApi,DescriptionItem, RedisProps } from '#/api/monitor/redis';
import {Page} from '#/components/core/effects/common-ui';
import { labelMapping } from '#/views/monitor/redis/data';
import { $t } from '#/locale';

defineOptions({
  name: 'RedisInfo',
});


const api = new RedisApi();
const descriptionItems=ref<DescriptionItem[]> ([]);


/**
 * 转换格式
 * @param data 
 * @param labelMapping 
 */
function convertToDescriptionItems(
  data: Record<string, any>,
  labelMapping: Record<string, string>
): DescriptionItem[] {
  return Object.entries(data).reduce((acc: DescriptionItem[], [key, value]) => {
    // 1. 跳过未定义的标签
    if (!labelMapping[key]) return acc;
 
    // 2. 基础描述项
    const baseItem: DescriptionItem = {
      title: labelMapping[key],
      content: formatValue(value) // 使用值格式化函数
    };
 
    // 3. 添加特殊格式化规则
    if (key === 'used_memory_human' || key === 'used_memory_peak_human') {
      baseItem.content = formatMemory(value);
    }
 
    if (key === 'uptime_in_days') {
      baseItem.content = formatUptime(value);
    }
 
    return [...acc, baseItem];
  }, []);
}
 
// 值格式化辅助函数
function formatValue(value: any): any {
  if (typeof value === 'boolean') return value ? $t('monitors.redis.tip.yes') : $t('monitors.redis.tip.no');
  if (typeof value === 'number') return value.toLocaleString();
  return String(value);
}
 
// 内存格式化
function formatMemory(bytes: number): string {
  if (bytes > 1024 ** 3) return `${(bytes / 1024 ** 3).toFixed(2)} GB`;
  if (bytes > 1024 ** 2) return `${(bytes / 1024 ** 2).toFixed(2)} MB`;
  return `${(bytes / 1024).toFixed(2)} KB`;
}
 
// 运行时间格式化
function formatUptime(days: number): string {
  const years = Math.floor(days / 365);
  const remainingDays = days % 365;
  const parts: string[] = [];
  
  if (years > 0) parts.push(`${years}`+ $t('monitors.redis.tip.years'));
  if (remainingDays > 0) parts.push(`${remainingDays}`+ $t('monitors.redis.tip.day'));
 
  return parts.join('') ||  $t('monitors.redis.tip.less_day');
}

onMounted(async ()=>{
  const data=await api.list();
  const variable= data?.variable||{};
  descriptionItems.value = convertToDescriptionItems(variable, labelMapping());
})

</script>

<template>
  <Page>
    <div class="card-box p-5">
      <div>
        <h5 class="text-foreground text-lg"> {{$t('monitors.redis.title')}}</h5>
      </div>
      <div class="mt-4">
        <dl class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <template v-for="item in descriptionItems" :key="item.title">
            <div class="border-border border-t px-4 py-6 sm:col-span-1 sm:px-0">
              <dt class="text-foreground text-sm font-medium leading-6">
                {{ item.title }}
              </dt>
              <dd class="text-foreground mt-1 text-sm leading-6 sm:mt-2">
                <BasicRenderContent :content="item.content" />
              </dd>
            </div>
          </template>
        </dl>
      </div>
    </div>
  </Page>
</template>
