import { reactive } from 'vue';
import { defineStore } from 'pinia';

import { $t } from '#/locale';
import {  SystemConfigApi } from '#/api/system/config';
import type { SiteConfig } from '#/api/system/config';



export const useSiteConfigStore = defineStore('site-config', () => {
    const state = reactive<SiteConfig>({
        site_open: "1",
        site_url: "",
        site_name: "",
        site_logo: "",
        site_network_security: "",
        site_description: "",
        site_record_no: "",
        site_icp_url: "",
        site_network_security_url: "",
        cdn_url: "",
        cdn_url_params: "",
    });

    const api = new SystemConfigApi();

    // 从API获取配置
    async function fetchSiteConfig() {
        try {
            const config = await api.get({ group_code: "app-site_setting" });
            setSiteConfig(config);
        } catch (error) {
            console.error("Failed to fetch site config:", error);
            // 可以添加错误状态处理
        }
    }

    // 直接设置配置数据
    function setSiteConfig(config: Partial<SiteConfig>) {
        Object.assign(state, config);
    }

    function $reset() {
        setSiteConfig({
            site_open: "1",
            site_url: "",
            site_name: "",
            site_logo: "",
            site_network_security: "",
            site_description: "",
            site_record_no: "",
            site_icp_url: "",
            site_network_security_url: "",
            cdn_url: "",
            cdn_url_params: "",
        });
    }

    return {
        state,
        fetchSiteConfig,
        setSiteConfig,
        $reset,
    };
});