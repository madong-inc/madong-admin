import { defineConfig } from './.vite/config';

export default defineConfig(async () => {
  return {
    application: {},
    vite: {
      build: {
        rollupOptions: {
          output: {
            chunkFileNames: 'assets/js/[name]-[hash].js',
            entryFileNames: 'assets/js/[name]-[hash].js',
            assetFileNames: (assetInfo) => {
              const ext = assetInfo.name ? assetInfo.name.split('.').pop()?.toLowerCase() : '';
              if (!ext) {
                return 'assets/[name]-[hash][extname]';
              }
              if (['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'avif'].includes(ext)) {
                return 'assets/images/[name]-[hash][extname]';
              }
              if (['woff', 'woff2', 'eot', 'ttf', 'otf'].includes(ext)) {
                return 'assets/fonts/[name]-[hash][extname]';
              }
              if (ext === 'css') {
                return 'assets/css/[name]-[hash][extname]';
              }
              return 'assets/[name]-[hash][extname]';
            }
          }
        }
      },
      server: {
        proxy: {
          '/api': {
            changeOrigin: true,
            rewrite: (path) => path.replace(/^\/api/, ''),
            target: 'http://127.0.0.1:8899/',
            ws: true
          }
        },
        '/ws': {
          target: 'ws://127.0.0.1:3898',
          changeOrigin: true,
          ws: true,
          rewrite: (path: string) => path.replace(/^\/ws/, '')
        }
      },
    }
  }
});