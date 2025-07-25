import type {Locale} from './messages';
import {getMessages} from './messages';

import {computed, ref} from 'vue';

import {createSharedComposable} from '@vueuse/core';

export const useSimpleLocale = createSharedComposable(() => {
    const currentLocale = ref<Locale>('zh-CN');

    const setSimpleLocale = (locale: Locale) => {
        currentLocale.value = locale;
    };

    const $t = computed(() => {
        const localeMessages = getMessages(currentLocale.value);
        return (key: string) => {
            return localeMessages[key] || key;
        };
    });
    return {
        $t,
        currentLocale,
        setSimpleLocale,
    };
});
