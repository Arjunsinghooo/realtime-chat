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

            senderElement.textContent = `${message.user.name}:`;
            messageElement.append(senderElement, ` ${message.body}`);
            messagesContainer.appendChild(messageElement);
        });

    window.addEventListener('pagehide', () => {
        window.Echo.leave(channelName);
    }, { once: true });
}
