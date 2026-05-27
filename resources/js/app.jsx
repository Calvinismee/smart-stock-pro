import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

createInertiaApp({
    title: (title) => title ? `${title} — SmartStock Pro` : 'SmartStock Pro',

    setup({ el, App, props }) {
        import('pusher-js').then(({ default: Pusher }) => {
            window.Pusher = Pusher;
            
            import('laravel-echo').then(({ default: Echo }) => {
                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: import.meta.env.VITE_REVERB_APP_KEY,
                    wsHost: import.meta.env.VITE_REVERB_HOST,
                    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
                    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
                    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
                    enabledTransports: ['ws', 'wss'],
                });
            });
        });
        
        createRoot(el).render(<App {...props} />);
    },
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        return pages[`./Pages/${name}.jsx`];
    },

    progress: {
        color: '#6366f1',
        showSpinner: true,
    },
});
