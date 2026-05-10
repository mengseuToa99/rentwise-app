import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;

if (reverbKey) {
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    // No Reverb key configured — provide a minimal stub so Livewire's
    // `wire:stream` / broadcast features don't log "Laravel Echo cannot be found".
    const noop = () => ({ listen: noop, stopListening: noop, leave: noop, leaving: noop, error: noop, here: noop, joining: noop, whisper: noop, listenForWhisper: noop });
    window.Echo = {
        socketId: () => null,
        channel: noop,
        private: noop,
        encryptedPrivate: noop,
        join: noop,
        leave: () => {},
        leaveChannel: () => {},
        connector: { pusher: { connection: { bind: () => {} } } },
    };
}
