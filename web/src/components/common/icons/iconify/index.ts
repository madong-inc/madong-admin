export * from './create-icon';

export * from './lucide';

export type {IconifyIcon as IconifyIconStructure} from '@iconify/vue';

export {
    addCollection,
    addIcon,
    Icon as IconifyIcon,
    listIcons,
} from '@iconify/vue';

import {createIconifyIcon} from './create-icon';

export const MdiKeyboardEsc = createIconifyIcon('mdi:keyboard-esc');

export const MdiWechat = createIconifyIcon('mdi:wechat');

export const MdiGithub = createIconifyIcon('mdi:github');

export const MdiGoogle = createIconifyIcon('mdi:google');

export const MdiQqchat = createIconifyIcon('mdi:qqchat');
