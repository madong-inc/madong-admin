const BasicLayout = () => import('./basic.vue');
const AuthPageLayout = () => import('./auth.vue');

const IFrameView = () => import('../components/core/effects/layouts/iframe').then((m) => m.IFrameView);

export { AuthPageLayout, BasicLayout, IFrameView };