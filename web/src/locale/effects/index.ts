import {i18n, loadLocaleMessages, loadLocalesMap, loadLocalesMapFromDir, setupI18n,} from './i18n';

const $t = i18n.global.t;

export {
    $t,
    i18n,
    loadLocaleMessages,
    loadLocalesMap,
    loadLocalesMapFromDir,
    setupI18n,
};

export {
    type ImportLocaleFn,
    type LocaleSetupOptions,
    type SupportedLanguagesType,
} from './typing';

export {useI18n} from 'vue-i18n';

export type {Locale} from 'vue-i18n';
