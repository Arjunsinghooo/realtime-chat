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
            const avatarElement = document.createElement('div');
            const contentElement = document.createElement('div');
            const senderElement = document.createElement('strong');
            const bubbleElement = document.createElement('div');
            const bodyElement = document.createElement('p');

            messageElement.className = 'group flex max-w-2xl items-end gap-3';
            avatarElement.className = 'grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-300 to-violet-300 text-xs font-extrabold text-violet-950';
            avatarElement.textContent = message.user.name.charAt(0).toUpperCase();
            senderElement.className = 'mb-1 block text-xs font-bold text-slate-400';
            senderElement.textContent = message.user.name;
            bubbleElement.className = 'rounded-2xl rounded-bl-md border border-white/8 bg-white/[0.06] px-4 py-3 shadow-[0_8px_22px_-12px_rgba(0,0,0,0.45)] backdrop-blur-sm';
            bodyElement.className = 'whitespace-pre-wrap break-words text-sm leading-6 text-slate-100';
            bodyElement.textContent = message.body;
            bubbleElement.append(bodyElement);
            contentElement.append(senderElement, bubbleElement);
            messageElement.append(avatarElement, contentElement);
            messagesContainer.appendChild(messageElement);
        });

    window.addEventListener('pagehide', () => {
        window.Echo.leave(channelName);
    }, { once: true });
}
