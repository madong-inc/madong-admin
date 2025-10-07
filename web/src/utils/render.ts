import type { Component as ComponentType } from 'vue';
import type { DictOptions} from '#/api/system/dict';
import { h } from 'vue';
import {
  IconifyIcon,
} from '#/components/core/icons';
import { Tag } from 'ant-design-vue';
import { DictTag } from '#/components/dict';
import { getDictOptions } from './dict';

/**
 * 渲染标签
 * @param text 文字
 * @param color 颜色
 * @returns render
 */
function renderTag(text: string, color?: string) {
  return h(Tag, { color }, text);
}

/**
 * 渲染多个标签
 * @param tags 标签list
 * @param wrap 是否换行显示
 * @param gap 间隔
 * @returns render
 */
export function renderTags(tags: string[], wrap = false, gap = 1) {
  return h('div', { class: ['flex', wrap ? 'flex-col' : 'flex-row'], style: { gap: `${gap}px` } }, 
    tags.map((tag, index) => h('div', { key: index }, renderTag(tag)))
  );
}

/**
 * 渲染图标
 * @param icon icon名称
 * @returns render
 */
export function renderIcon(icon: string) {
  return h(IconifyIcon, { icon });
}

/**
 * 渲染 HTTP 方法标签
 * @param type method类型
 * @returns render
 */
export function renderHttpMethodTag(type: string) {
  const method = type.toUpperCase();
  const colors: { [key: string]: string } = {
    DELETE: 'red',
    GET: 'green',
    POST: 'blue',
    PUT: 'orange',
  };

  const color = colors[method] ?? 'default';
  const title = `${method}请求`;

  return renderTag(title, color);
}

export function renderDictTag(value: number | string, dicts: DictOptions[]) {
  return h(DictTag, { dicts, value });
}

/**
 * 渲染多个字典标签
 * @param value key数组 string[]类型
 * @param dicts 字典数组
 * @param wrap 是否需要换行显示
 * @param gap 间隔
 * @returns render
 */
export function renderDictTags(
  value: string[],
  dicts: DictOptions[],
  wrap = true,
  gap = 1,
) {
  if (!Array.isArray(value)) {
    return h('div', value);
  }
  return h('div', { class: ['flex', wrap ? 'flex-col' : 'flex-row'], style: { gap: `${gap}px` } },
    value.map((item, index) => h('div', { key: index }, renderDictTag(item, dicts)))
  );
}

/**
 * 显示字典标签 一般是table使用
 * @param value 值
 * @param dictName dictName
 * @returns tag
 */
export function renderDict(value: number | string, dictName: string) {
  const dictInfo = getDictOptions(dictName);
  return renderDictTag(value, dictInfo);
}

/**
 * 渲染图标和文本
 * @param icon 组件
 * @param value 文本
 * @param center 是否居中
 * @param marginLeft 左边距
 * @returns render
 */
export function renderIconSpan(
  icon: ComponentType,
  value: string,
  center = false,
  marginLeft = '2px',
) {
  const justifyCenter = center ? 'justify-center' : '';

  return h('span', { class: ['flex', 'items-center', justifyCenter] },
    [h(icon), h('span', { style: { marginLeft } }, value)]
  );
}
