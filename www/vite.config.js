import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  plugins: [],
  root: 'resources',
  base: '/build/',
  build: {
    outDir: '../public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'resources/js/app.ts'),
        style: resolve(__dirname, 'resources/sass/app.scss')
      }
    }
  },
  server: {
    strictPort: true,
    port: 5173,
    host: '0.0.0.0',
    origin: 'http://localhost:5173',
  }
});
