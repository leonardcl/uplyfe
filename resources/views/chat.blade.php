<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Uplyfe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:wght@100..900&family=Fira+Code:wght@300..700&family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <style type="text/tailwindcss">
        @import "tailwindcss";
      @theme inline {
        --color-background: var(--background);
        --color-foreground: var(--foreground);
        --color-primary: var(--primary);
        --color-primary-foreground: var(--primary-foreground);
        --color-secondary: var(--secondary);
        --color-secondary-foreground: var(--secondary-foreground);
        --color-tertiary: var(--tertiary);
        --color-muted: var(--muted);
        --color-muted-foreground: var(--muted-foreground);
        --color-accent: var(--accent);
        --color-destructive: var(--destructive);
        --color-card: var(--card);
        --color-card-foreground: var(--card-foreground);
        --color-border: var(--border);
        --color-input: var(--input);
        --color-ring: var(--ring);
        --radius-sm: calc(var(--radius) - 4px);
        --radius-md: calc(var(--radius) - 2px);
        --radius-lg: var(--radius);
        --font-family-sans: var(--font-sans);
        --font-family-heading: var(--font-heading);
      }
      :root { --border: #e2e8f0;
        --accent: #f1f5f9;
        --input: #e2e8f0;
        --card-foreground: #0f172a;
        --ring: #90ee90;
        --muted-foreground: #64748b;
        --primary-foreground: #0f172a;
        --tertiary: #10b981;
        --foreground: #0f172a;
        --muted: #f1f5f9;
        --background: #f8fafc;
        --secondary-foreground: #0f172a;
        --font-heading: Inter, sans-serif;
        --card: #ffffff;
        --font-sans: Inter, sans-serif;
        --primary: #90ee90;
        --destructive: #ef4444;
        --radius: 1rem;
        --secondary: #e2e8f0; }
    </style>
</head>

<body>
    <div class="min-h-screen w-full bg-background flex flex-col md:flex-row relative font-sans text-foreground">
        <!-- Sidebar Navigation -->
        <aside id="mobile-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 lg:w-72 -translate-x-full md:relative md:translate-x-0 md:flex bg-card border-r border-border flex-shrink-0 flex-col transition-transform duration-300 shadow-xl md:shadow-none">
            <div class="h-16 flex items-center px-6 border-b border-border">
                <div class="flex items-center gap-2 cursor-pointer group">
                    <div
                        class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground shadow-sm">
                        <iconify-icon icon="lucide:leaf" class="text-lg"></iconify-icon>
                    </div>
                    <span class="text-xl font-heading font-bold tracking-tight">Uplyfe</span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2">
                <p class="px-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Main Menu</p>

                <a href="/"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:layout-dashboard" class="text-lg"></iconify-icon>
                    Dashboard
                </a>

                <a href="/health-check"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:file-text" class="text-lg"></iconify-icon>
                    Health Checkup
                </a>

                <a href="/recipe"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:apple" class="text-lg"></iconify-icon>
                    Nutrition & Recipes
                </a>

                <a href="/exercise"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:dumbbell" class="text-lg"></iconify-icon>
                    Exercise Routine
                </a>

                <a href="/chat"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-primary text-primary-foreground shadow-sm transition-all">
                    <iconify-icon icon="lucide:bot" class="text-lg"></iconify-icon>
                    AI Assistant
                </a>
            </div>

            <div class="p-4 border-t border-border">
                <div class="flex items-center gap-3 px-2 py-2">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
                        class="w-10 h-10 rounded-full border border-border">
                    <div class="flex-1 min-w-0">
                        <p id="sidebar-user-name" class="text-sm font-bold truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                    </div>
                    <a href="/profile"
                        class="text-muted-foreground hover:text-foreground p-1 rounded-md hover:bg-muted transition-colors inline-flex items-center justify-center">
                        <iconify-icon icon="lucide:settings" class="text-lg"></iconify-icon>
                    </a>
                </div>
            </div>
        </aside>
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm hidden md:hidden"
            onclick="toggleSidebar(false)"></div>

        <!-- Main Content (Chat Interface) -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">

            <!-- Topbar -->
            <header
                class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-button" class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                    </button>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div
                                class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary border border-primary/30">
                                <iconify-icon icon="lucide:bot" class="text-xl"></iconify-icon>
                            </div>
                            <span
                                class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-tertiary border-2 border-card"></span>
                        </div>
                        <div>
                            <h1 class="text-base font-heading font-bold leading-none">Uplyfe AI</h1>
                            <p class="text-xs text-tertiary font-medium mt-1">Online & ready</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 relative">
                    <button id="new-chat-button" title="Start a new chat"
                        class="px-3 py-1.5 rounded-full border border-primary/30 bg-primary/5 text-primary text-xs font-semibold hover:bg-primary/10 transition-colors flex items-center gap-1">
                        <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                        <span class="hidden sm:inline">New chat</span>
                    </button>
                    <button id="history-toggle" title="Conversation history"
                        class="p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:history" class="text-xl"></iconify-icon>
                    </button>
                    <div id="history-dropdown"
                        class="hidden absolute right-0 top-12 w-80 max-h-96 overflow-y-auto bg-card border border-border rounded-xl shadow-lg z-50 p-2">
                        <p class="text-xs font-bold text-muted-foreground px-3 py-2 sticky top-0 bg-card">Recent conversations</p>
                        <div id="history-list" class="flex flex-col gap-1">
                            <p class="text-xs text-muted-foreground px-3 py-4 text-center">Loading…</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Chat Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 sm:p-6 flex flex-col gap-6 scroll-smooth">
                <div id="chat-empty-state" class="m-auto text-center max-w-md py-12">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mx-auto mb-4">
                        <iconify-icon icon="lucide:sparkles" class="text-3xl"></iconify-icon>
                    </div>
                    <h2 class="text-lg font-heading font-bold">Hi {{ $user->first_name ?? 'there' }} — how can I help today?</h2>
                    <p class="text-sm text-muted-foreground mt-2">Ask about your habits, diet, recipes, or workouts. Conversations are saved automatically.</p>
                </div>
                <div id="chat-spacer" class="h-20"></div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-background border-t border-border mt-auto">
                <div
                    class="max-w-4xl mx-auto relative flex items-end gap-2 bg-card border border-border rounded-3xl p-2 shadow-sm focus-within:ring-2 focus-within:ring-primary focus-within:border-transparent transition-all">
                    <button class="p-3 text-muted-foreground hover:text-primary transition-colors flex-shrink-0">
                        <iconify-icon icon="lucide:plus-circle" class="text-xl"></iconify-icon>
                    </button>

                    <textarea placeholder="Ask anything about your health, diet, or workouts..."
                        class="flex-1 bg-transparent border-none outline-none resize-none max-h-32 min-h-[44px] py-3 text-sm text-foreground placeholder:text-muted-foreground"
                        rows="1"></textarea>

                    <button id="send-button"
                        class="w-11 h-11 rounded-full bg-primary flex items-center justify-center text-primary-foreground shadow-md hover:shadow-lg hover:scale-105 transition-all flex-shrink-0 mb-0.5 mr-0.5">
                        <iconify-icon icon="lucide:send" class="text-lg ml-0.5"></iconify-icon>
                    </button>
                </div>
                <p class="text-center text-[10px] text-muted-foreground mt-2">
                    Uplyfe AI can make mistakes. Always consult your doctor for serious medical advice.
                </p>
            </div>

        </main>
    </div>

    <script>
        const chatHistory = [];
        // Server-assigned conversation id. Persisted in localStorage so it
        // survives page navigation — when the user comes back to /chat, we
        // resume the same thread rather than starting fresh.
        const CID_KEY = 'uplyfe.chat.conversationId';
        let conversationId = (() => {
            const raw = localStorage.getItem(CID_KEY);
            return raw ? Number(raw) : null;
        })();

        function setConversationId(id) {
            conversationId = id;
            if (id) {
                localStorage.setItem(CID_KEY, String(id));
            } else {
                localStorage.removeItem(CID_KEY);
            }
        }

        function clearMessageArea() {
            const chatMessages = document.getElementById('chat-messages');
            // Keep the empty state + spacer, drop every other child.
            [...chatMessages.children].forEach((child) => {
                if (child.id !== 'chat-empty-state' && child.id !== 'chat-spacer') {
                    child.remove();
                }
            });
        }

        function hideEmptyState() {
            const el = document.getElementById('chat-empty-state');
            if (el) el.classList.add('hidden');
        }

        function showEmptyState() {
            const el = document.getElementById('chat-empty-state');
            if (el) el.classList.remove('hidden');
        }

        function escapeHtml(s) {
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderUserMessage(message) {
            return `
                <div class="flex gap-4 max-w-3xl ml-auto flex-row-reverse animate-[fadeIn_0.3s_ease-out]">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User" class="w-8 h-8 rounded-full border border-border flex-shrink-0 mt-1">
                    <div class="flex flex-col gap-1 items-end">
                        <span class="text-xs font-bold text-muted-foreground mr-1">You</span>
                        <div class="bg-primary text-primary-foreground rounded-2xl rounded-tr-none p-4 shadow-sm text-sm leading-relaxed">
                            ${escapeHtml(message).replace(/\n/g, '<br>')}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderBotMessage(content, opts = {}) {
            const id = opts.id ? `id="${opts.id}"` : '';
            const body = opts.isHtml ? content : escapeHtml(content).replace(/\n/g, '<br>');
            return `
                <div ${id} class="flex gap-4 max-w-3xl animate-[fadeIn_0.3s_ease-out]">
                    <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary flex-shrink-0 mt-1">
                        <iconify-icon icon="lucide:bot" class="text-sm"></iconify-icon>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold text-muted-foreground ml-1">Uplyfe AI</span>
                        <div class="bg-card border border-border rounded-2xl rounded-tl-none p-4 shadow-sm text-sm leading-relaxed">
                            ${body}
                        </div>
                    </div>
                </div>
            `;
        }

        async function sendChat() {
            const textarea = document.querySelector('textarea');
            const sendBtn = document.getElementById('send-button');
            const message = textarea.value.trim();
            if (!message) return;

            hideEmptyState();
            const chatMessages = document.getElementById('chat-messages');
            const spacer = document.getElementById('chat-spacer');
            const insertBefore = (html) => {
                if (spacer) {
                    spacer.insertAdjacentHTML('beforebegin', html);
                } else {
                    chatMessages.insertAdjacentHTML('beforeend', html);
                }
            };
            insertBefore(renderUserMessage(message));

            const pendingId = 'pending-' + Date.now();
            const pendingHtml = `
                <span class="inline-flex gap-1 items-center text-muted-foreground">
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:300ms"></span>
                </span>
            `;
            insertBefore(renderBotMessage(pendingHtml, { id: pendingId, isHtml: true }));

            textarea.value = '';
            textarea.style.height = 'auto';
            chatMessages.scrollTop = chatMessages.scrollHeight;
            sendBtn.disabled = true;
            sendBtn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                const res = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        message,
                        conversation_id: conversationId,
                        history: chatHistory.slice(-10),
                    }),
                });

                let reply;
                if (!res.ok) {
                    let detail = '';
                    try {
                        const j = await res.json();
                        detail = j.error || j.message || '';
                    } catch (_) { /* ignore */ }
                    reply = `Sorry — something went wrong (HTTP ${res.status})${detail ? `: ${detail}` : ''}.`;
                } else {
                    const data = await res.json();
                    reply = data.reply || 'I had no response. Try again.';
                    if (data.conversation_id) {
                        setConversationId(data.conversation_id);
                        // Refresh history dropdown so the new conversation
                        // (and its auto-generated title) shows up immediately.
                        loadConversationList();
                    }
                    chatHistory.push({ role: 'user', content: message });
                    chatHistory.push({ role: 'assistant', content: reply });
                }

                const pending = document.getElementById(pendingId);
                if (pending) pending.outerHTML = renderBotMessage(reply);
            } catch (err) {
                const pending = document.getElementById(pendingId);
                const msg = `Network error: ${err?.message || err}`;
                if (pending) pending.outerHTML = renderBotMessage(msg);
            } finally {
                sendBtn.disabled = false;
                sendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        document.getElementById('send-button').addEventListener('click', sendChat);
        document.querySelector('textarea').addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendChat();
            }
        });

        // Auto-resize textarea
        const textarea = document.querySelector('textarea');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        function toggleSidebar(open) {
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileBackdrop = document.getElementById('mobile-sidebar-backdrop');
            const isOpen = mobileSidebar.classList.contains('translate-x-0');
            const shouldOpen = typeof open === 'boolean' ? open : !isOpen;

            if (shouldOpen) {
                mobileSidebar.classList.remove('-translate-x-full');
                mobileSidebar.classList.add('translate-x-0');
                mobileBackdrop.classList.remove('hidden');
            } else {
                mobileSidebar.classList.remove('translate-x-0');
                mobileSidebar.classList.add('-translate-x-full');
                mobileBackdrop.classList.add('hidden');
            }
        }

        document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());

        // ---------- Conversation history wiring ----------
        // Tracks an active poll loop so we don't start a second one when the
        // user clicks around. Cleared when the assistant reply arrives.
        let pollHandle = null;
        let lastSeenMessageId = 0;

        function clearPoll() {
            if (pollHandle) {
                clearTimeout(pollHandle);
                pollHandle = null;
            }
        }

        function ensurePendingBubble() {
            if (document.getElementById('pending-poll')) return;
            hideEmptyState();
            const spacer = document.getElementById('chat-spacer');
            const html = `
                <span class="inline-flex gap-1 items-center text-muted-foreground">
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:300ms"></span>
                </span>
            `;
            const bubble = renderBotMessage(html, { id: 'pending-poll', isHtml: true });
            if (spacer) spacer.insertAdjacentHTML('beforebegin', bubble);
        }

        async function pollForAssistantReply(cid) {
            // Stop polling when the user navigates away.
            if (document.hidden) {
                pollHandle = setTimeout(() => pollForAssistantReply(cid), 4000);
                return;
            }
            try {
                const res = await fetch(`/api/chat-conversations/${cid}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!res.ok) {
                    clearPoll();
                    return;
                }
                const data = await res.json();
                const msgs = data.messages || [];
                // Append any messages we haven't seen yet (could be the
                // assistant reply, or a user message sent from another tab).
                const newOnes = msgs.filter(m => m.id > lastSeenMessageId);
                if (newOnes.length > 0) {
                    const spacer = document.getElementById('chat-spacer');
                    const insertBefore = (html) =>
                        spacer ? spacer.insertAdjacentHTML('beforebegin', html) : null;
                    for (const m of newOnes) {
                        // Don't double-render messages that the synchronous
                        // sendChat() path already inserted into the DOM —
                        // those are tracked in chatHistory by content.
                        const alreadyShown = chatHistory.some(h => h.role === m.role && h.content === m.content);
                        if (!alreadyShown) {
                            insertBefore(m.role === 'user'
                                ? renderUserMessage(m.content)
                                : renderBotMessage(m.content));
                            chatHistory.push({ role: m.role, content: m.content });
                        }
                        lastSeenMessageId = Math.max(lastSeenMessageId, m.id);
                    }
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
                // If the tail is now an assistant message, we're done.
                const tail = msgs[msgs.length - 1];
                if (tail && tail.role === 'assistant') {
                    const pending = document.getElementById('pending-poll');
                    if (pending) pending.remove();
                    clearPoll();
                    return;
                }
            } catch (e) {
                // Network blip — keep polling.
            }
            pollHandle = setTimeout(() => pollForAssistantReply(cid), 2000);
        }

        async function loadConversation(id) {
            try {
                const res = await fetch(`/api/chat-conversations/${id}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) {
                    // Stored id may point to a conversation that was deleted
                    // or that belongs to a different user (after logout/login).
                    if (res.status === 404) setConversationId(null);
                    return;
                }
                const data = await res.json();
                setConversationId(data.id);
                clearMessageArea();
                clearPoll();
                lastSeenMessageId = 0;
                if (!data.messages || data.messages.length === 0) {
                    showEmptyState();
                    return;
                }
                hideEmptyState();
                const spacer = document.getElementById('chat-spacer');
                const insertBefore = (html) =>
                    spacer ? spacer.insertAdjacentHTML('beforebegin', html) : null;
                for (const m of data.messages) {
                    insertBefore(m.role === 'user'
                        ? renderUserMessage(m.content)
                        : renderBotMessage(m.content));
                    chatHistory.push({ role: m.role, content: m.content });
                    lastSeenMessageId = Math.max(lastSeenMessageId, m.id);
                }
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
                // If the last message is the user's, the assistant reply is
                // still in flight on the server — start polling so it appears
                // automatically when ready.
                const tail = data.messages[data.messages.length - 1];
                if (tail && tail.role === 'user') {
                    ensurePendingBubble();
                    pollForAssistantReply(data.id);
                }
            } catch (e) {
                console.warn('loadConversation failed:', e);
            }
        }

        async function loadConversationList() {
            const listEl = document.getElementById('history-list');
            try {
                const res = await fetch('/api/chat-conversations', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const data = res.ok ? await res.json() : { conversations: [] };
                const items = data.conversations || [];
                if (items.length === 0) {
                    listEl.innerHTML = '<p class="text-xs text-muted-foreground px-3 py-4 text-center">No conversations yet.</p>';
                    return;
                }
                listEl.innerHTML = items.map(c => {
                    const isActive = c.id === conversationId;
                    const title = escapeHtml(c.title || 'Untitled chat');
                    const when = c.last_message_at
                        ? new Date(c.last_message_at).toLocaleString()
                        : '';
                    return `
                        <button data-cid="${c.id}"
                            class="conv-item text-left px-3 py-2 rounded-lg hover:bg-muted transition-colors ${isActive ? 'bg-muted' : ''}">
                            <p class="text-sm font-medium truncate">${title}</p>
                            <p class="text-[10px] text-muted-foreground">${escapeHtml(when)}</p>
                        </button>
                    `;
                }).join('');
                listEl.querySelectorAll('.conv-item').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const cid = Number(btn.dataset.cid);
                        document.getElementById('history-dropdown').classList.add('hidden');
                        chatHistory.length = 0;
                        await loadConversation(cid);
                    });
                });
            } catch (e) {
                listEl.innerHTML = '<p class="text-xs text-destructive px-3 py-4 text-center">Failed to load.</p>';
            }
        }

        document.getElementById('new-chat-button').addEventListener('click', () => {
            setConversationId(null);
            chatHistory.length = 0;
            clearMessageArea();
            showEmptyState();
            document.getElementById('history-dropdown').classList.add('hidden');
        });

        document.getElementById('history-toggle').addEventListener('click', (e) => {
            e.stopPropagation();
            const dd = document.getElementById('history-dropdown');
            const willOpen = dd.classList.contains('hidden');
            dd.classList.toggle('hidden');
            if (willOpen) loadConversationList();
        });

        document.addEventListener('click', (e) => {
            const dd = document.getElementById('history-dropdown');
            const toggle = document.getElementById('history-toggle');
            if (!dd.classList.contains('hidden') && !dd.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });

        // On page open: resume the saved conversation if there is one.
        if (conversationId) {
            loadConversation(conversationId);
        }
        // Pre-load the dropdown list in the background so it opens instantly.
        loadConversationList();
    </script>
</body>

</html>