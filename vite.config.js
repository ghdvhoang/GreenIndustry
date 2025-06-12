import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
        server: {
        host: '0.0.0.0',     // Cho phép mọi thiết bị truy cập
        port: 5173,          // Port mặc định của Vite (có thể đổi)
        strictPort: true,    // Nếu 5173 bị chiếm, không tự đổi
    },

    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
