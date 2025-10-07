import {defineOverridesPreferences, updatePreferences} from '#/components/core/@core/preferences';



/**
 * @description 项目配置文件
 * 只需要覆盖项目中的一部分配置，不需要的配置不用覆盖，会自动使用默认配置
 * !!! 更改配置后请清空缓存，否则可能不生效
 */
export const overridesPreferences = defineOverridesPreferences({
    // overrides
    app: {
        name: import.meta.env.VITE_APP_TITLE,
        accessMode: 'backend', // 默认值frontend|backend 默认值frontend可不填写
        loginExpiredMode:'page',//登录超时返回登录页面
    },
    logo: {
        source: '/logo.png'
    },
    theme: {
        mode: "light"
      }
});

