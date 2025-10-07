export type {
    AlertProps,
    BeforeCloseScope,
    IconType,
    PromptProps,
} from './alert';

export {useAlertContext} from './alert';

export {default as Alert} from './alert.vue';

export {
    alert,
    confirm,
    prompt,
    clearAllAlerts,
} from './builder';
