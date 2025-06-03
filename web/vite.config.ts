import { defineConfig } from './.vite/config';

export default defineConfig(async () => {
  return {
    application: {},
    vite: {
      server: {
        proxy: {
          '/api': {
            changeOrigin: true,
            rewrite: (path) => path.replace(/^\/api/, ''),
            target: 'http://127.0.0.1:8899/',
            ws: true,
          },
          '/ws': {
            target: 'ws://127.0.0.1:3898',
            changeOrigin: true,
            ws: true,
            rewrite: (path) => path.replace(/^\/ws/, '')
          }
        },
      },
    }
  }
});