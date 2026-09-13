import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws'],
});

const conversationId = document.body.dataset.conversationId;

if (conversationId) {
    const channelName = `chat.${conversationId}`;

    window.Echo.private(channelName)
        .listen('MessageSent', (message) => {
            const messagesContainer = document.getElementById('messages');

            if (!messagesContainer) {
                return;
            }

            document.getElementById('empty-messages')?.remove();

            const messageElement = document.createElement('div');
            const senderElement = document.createElement('strong');
            const bodyElement = document.createElement('p');

            messageElement.className = 'max-w-2xl rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm';
            senderElement.className = 'block text-sm text-indigo-700';
            senderElement.textContent = `${message.user.name}:`;
            bodyElement.className = 'mt-1 whitespace-pre-wrap break-words text-sm leading-6 text-slate-700';
            bodyElement.textContent = message.body;
            messageElement.append(senderElement, bodyElement);
            messagesContainer.appendChild(messageElement);
        });

    window.addEventListener('pagehide', () => {
        window.Echo.leave(channelName);
    }, { once: true });
}
