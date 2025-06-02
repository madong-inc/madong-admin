import type { Preferences } from './effects';
import type { DeepPartial } from '#/components/common/core/typings';

/**
 * 如果你想修改默认偏好设置，可以在 src/overrides.ts 中使用 defineOverridesPreferences 函数
 * 而不是去修改 src/preferences/xxx 中的默认偏好设置
 * @param preferences
 * @returns
 */
function defineOverridesPreferences(preferences: DeepPartial<Preferences>) {
  return preferences;
}

export { defineOverridesPreferences };
export * from './effects';