import type {PluginOption} from 'vite';

import type {ApplicationPluginOptions, CommonPluginOptions, ConditionPlugin,} from '../typing';

import viteVue from '@vitejs/plugin-vue';
import {createHtmlPlugin as viteHtmlPlugin} from 'vite-plugin-html';
import viteCompressPlugin from 'vite-plugin-compression';
import viteVueDevTools from 'vite-plugin-vue-devtools';

import {viteInjectAppLoadingPlugin} from './inject-app-loading';
import {viteVxeTableImportsPlugin} from './vxe-table';
import { viteArchiverPlugin } from './archiver';

/**
 * 获取条件成立的 vite 插件
 * @param conditionPlugins
 */
async function loadConditionPlugins(conditionPlugins: ConditionPlugin[]) {
    const plugins: PluginOption[] = [];
    for (const conditionPlugin of conditionPlugins) {
        if (conditionPlugin.condition) {
            const realPlugins = await conditionPlugin.plugins();
            plugins.push(...realPlugins);
        }
    }
    return plugins.flat();
}

/**
 * 根据条件获取通用的vite插件
 */
async function loadCommonPlugins(options: CommonPluginOptions,): Promise<ConditionPlugin[]> {
    const {devtools, isBuild, } = options;
    return [
        {
            condition: true,
            plugins: () => [
                viteVue({
                    script: {
                        defineModel: true,
                        // propsDestructure: true,
                    },
                }),
            ],
        },
        {
            condition: !isBuild && devtools,
            plugins: () => [viteVueDevTools()],
        },
    ];
}

/**
 * 根据条件获取应用类型的vite插件
 */
async function loadApplicationPlugins(
    options: ApplicationPluginOptions,
): Promise<PluginOption[]> {
    // 单独取，否则commonOptions拿不到
    const isBuild = options.isBuild;
    const env = options.env;

    const {
        archiver,
        archiverPluginOptions,
        compress,
        compressTypes,
        html,
        injectAppLoading,
        vxeTableLazyImport,
        ...commonOptions
    } = options;

    const commonPlugins = await loadCommonPlugins(commonOptions);

    return await loadConditionPlugins([
        ...commonPlugins,
        {
            condition: vxeTableLazyImport,
            plugins: async () => {
                return [await viteVxeTableImportsPlugin()];
            },
        },
        {
            condition: injectAppLoading,
            plugins: async () => [await viteInjectAppLoadingPlugin(!!isBuild, env)],
        },
        {
            condition: isBuild && !!compress,
            plugins: () => {
                const compressPlugins: PluginOption[] = [];
                if (compressTypes?.includes('brotli')) {
                    compressPlugins.push(
                        viteCompressPlugin({ deleteOriginFile: false, ext: '.br' }),
                    );
                }
                if (compressTypes?.includes('gzip')) {
                    compressPlugins.push(
                        viteCompressPlugin({ deleteOriginFile: false, ext: '.gz' }),
                    );
                }
                return compressPlugins;
            },
        },
        {
            condition: !!html,
            plugins: () => [viteHtmlPlugin({minify: true})],
        },
        {
            condition: archiver,
            plugins: async () => {
                return [await viteArchiverPlugin(archiverPluginOptions)];
            },
        },
    ]);
}

export {
    loadApplicationPlugins,
    viteArchiverPlugin,
    viteCompressPlugin,
    viteHtmlPlugin,
    viteVxeTableImportsPlugin,
};
