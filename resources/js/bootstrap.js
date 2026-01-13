import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';

// O Reverb usa o cliente Pusher (pusher-js) por baixo.
// Se não definires window.Pusher, o Echo rebenta com "Pusher client not found".
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const REVERB_KEY = import.meta.env.VITE_REVERB_APP_KEY;
const REVERB_HOST = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const REVERB_PORT = parseInt(import.meta.env.VITE_REVERB_PORT || '8080', 10);
const REVERB_SCHEME = import.meta.env.VITE_REVERB_SCHEME || 'http';
const REVERB_PATH = import.meta.env.VITE_REVERB_PATH || '/app';

if (REVERB_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: REVERB_KEY,

        wsHost: REVERB_HOST,
        wsPort: REVERB_PORT,
        wssPort: REVERB_PORT,
        wsPath: REVERB_PATH,

        forceTLS: REVERB_SCHEME === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    // Evitar “poluição” no console em produção.
    if (import.meta.env.DEV) {
        console.warn('[Echo] VITE_REVERB_APP_KEY não definido — websockets desativados neste ambiente.');
    }
}
