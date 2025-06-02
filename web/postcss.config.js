export default {
  plugins: {
    ...(process.env.NODE_ENV === 'production' ? { cssnano: {} } : {}),
    // 解决 element-ui 和 ant-design-vue 的样式和tailwindcss冲突问题
    'postcss-antd-fixes': { prefixes: ['ant', 'el'] },
    tailwindcss: {},
    autoprefixer: {},
    'postcss-import': {},
    'postcss-preset-env': {},
    'tailwindcss/nesting': {},
  },
}