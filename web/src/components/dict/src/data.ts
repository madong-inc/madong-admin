import { h, VNode } from 'vue';
import { Tag } from 'ant-design-vue';

// 定义 TagType 接口 
interface TagType {
  [key: string]: { color: string; label: string };
}

// 定义标签类型
export const tagTypes: TagType = {
  processing: {
    color: 'processing',
    label: 'processing'
  },
  success: {
    color: 'success',
    label: 'success'
  },
  error: {
    color: 'error',
    label: 'error'
  },
  warning: {
    color: 'warning',
    label: 'warning'
  },
  magenta: {
    color: 'magenta',
    label: 'magenta'
  },
  red: {
    color: 'red',
    label: 'red'
  },
  volcano: {
    color: 'volcano',
    label: 'volcano'
  },
  orange: {
    color: 'orange',
    label: 'orange'
  },
  gold: {
    color: 'gold',
    label: 'gold'
  },
  lime: {
    color: 'lime',
    label: 'lime'
  },
  green: {
    color: 'green',
    label: 'green'
  },
  cyan: {
    color: 'cyan',
    label: 'cyan'
  },
  blue: {
    color: 'blue',
    label: 'blue'
  },
  geekblue: {
    color: 'geekblue',
    label: 'geekblue'
  },
  purple: {
    color: 'purple',
    label: 'purple'
  },
};

// Options 接口 
interface Options {
  label: string | VNode;
  value: string;
}

// 生成标签选择选项的函数 
export function tagSelectOptions() {
  const selectArray: Options[] = [];
  Object.keys(tagTypes).forEach((key) => {
    if (!tagTypes[key]) return;
    const { label, color } = tagTypes[key];
    const tagVNode = h(Tag, { color }, () => label);
    selectArray.push({
      label: tagVNode,
      value: key,
    });
  });
  return selectArray;
} 