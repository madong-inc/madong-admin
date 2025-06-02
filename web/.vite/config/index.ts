import type {DefineConfig} from '../typing';

import {defineApplicationConfig} from './application';

export * from './application';

function defineConfig(userConfigPromise?: DefineConfig) {
  return defineApplicationConfig(userConfigPromise);
}

export { defineConfig };
