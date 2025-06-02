import type {UserConfig} from 'vite';
import {defineConfig, loadEnv, mergeConfig} from 'vite';

import type {DefineApplicationOptions} from '../typing';

import {loadApplicationPlugins} from '../plugins';
import {loadAndConvertEnv} from '../utils/env';
import {getCommonConfig} from './common';

function defineApplicationConfig(userConfigPromise?: DefineApplicationOptions) {
  return defineConfig(async (config) => {
    const options = await userConfigPromise?.(config);
    const { appTitle, base, port, ...envConfig } = await loadAndConvertEnv();
    const { command, mode } = config;
    const { application = {}, vite = {} } = options || {};
    const root = process.cwd();
    const isBuild = command === 'build';
    const env = loadEnv(mode, root);

    const plugins = await loadApplicationPlugins({
      archiver: true,
      archiverPluginOptions: {},
      compress: false,
      compressTypes: ['brotli', 'gzip'],
      devtools: true,
      env,
      html: true,
      injectAppLoading: true,
      isBuild,
      mode,
      vxeTableLazyImport: true,
      ...envConfig,
      ...application,
    });

    const applicationConfig: UserConfig = {
      base,
      build: {
        rollupOptions: {
          output: {
            assetFileNames: '[ext]/[name]-[hash].[ext]',
            chunkFileNames: 'js/[name]-[hash].js',
            entryFileNames: 'jse/index-[name]-[hash].js',
          },
        },
        target: 'es2015',
      },
      esbuild: {
        drop: isBuild ? ['debugger',] : [],
        legalComments: 'none',
      },
      plugins,
      server: {
        host: true,
        port,
        warmup: {
          // 预热文件
          clientFiles: [
            './index.html',
            './src/bootstrap.ts',
            './src/{views,layouts,router,stores,api}/*',
          ],
        },
      },
    };

    const mergedCommonConfig = mergeConfig(
      await getCommonConfig(),
      applicationConfig,
    );
    return mergeConfig(mergedCommonConfig, vite);
  });
}

export { defineApplicationConfig };
