import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react-swc'
import inspect from 'vite-plugin-inspect'


export default defineConfig(() => {
  const env = loadEnv('', process.cwd(), '')

  return {
    define: {
      APP_URL: JSON.stringify(env.APP_URL)
    },
    base: '/',
    root: './frontend',
    envDir: '../',
    resolve: {
      alias: {
        '@/': '/src/',
      },
    },
    plugins: [
      react(),
      inspect(),
    ],
  }
})
