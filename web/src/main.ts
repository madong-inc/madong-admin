import { initPreferences } from '#/components/common/core/preferences';
import { unmountGlobalLoading } from '#/components/common/utils';

import { overridesPreferences } from './preferences';

/**
 * 应用初始化完成之后再进行页面加载渲染
 */
async function initApplication() {
    // name用于指定项目唯一标识
    // 用于区分不同项目的偏好设置以及存储数据的key前缀以及其他一些需要隔离的数据
    const env = import.meta.env.PROD ? 'prod' : 'dev';
    const namespace = `${import.meta.env.VITE_APP_NAMESPACE}-${env}`;

    // app偏好设置初始化
    await initPreferences({
        namespace,
        overrides: overridesPreferences,
    });

    // 启动应用并挂载
    // vue应用主要逻辑及视图
    const { bootstrap } = await import('./bootstrap');
    await bootstrap(namespace);

    // 移除并销毁loading
    unmountGlobalLoading();
}

initApplication();
