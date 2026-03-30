<x-main-layout>
    @php
        $me = auth()->user();
        $conversationPayload = $conversations->map(function ($conversation) use ($me) {
            $other = $conversation->buyer_id === $me->id ? $conversation->seller : $conversation->buyer;

            return [
                'id' => $conversation->id,
                'title' => $conversation->advert?->title ?? 'Watch Listing',
                'price' => $conversation->advert?->price,
                'advert_image' => $conversation->advert?->mainImageUrl(),
                'other_name' => $other?->name ?? 'User',
                'last_message' => $conversation->latestMessage?->message ?? '',
                'last_time' => optional($conversation->latestMessage?->created_at)?->diffForHumans(),
            ];
        })->values();
    @endphp

    <section
        class="max-w-[1280px] mx-auto px-4 lg:px-6 py-4 sm:py-6"
        x-data="messagesPage({
            meId: {{ $me->id }},
            initialConversations: @js($conversationPayload),
            initialConversationId: {{ $activeConversation?->id ?? 'null' }},
            listUrl: '{{ route('messages.list') }}',
            itemUrlTemplate: '{{ route('messages.items', ['conversation' => '__CID__']) }}'
        })"
        x-init="init()"
    >
        <div class="border border-[#ddd] rounded-2xl overflow-hidden bg-white min-h-[70vh] lg:h-[80vh] lg:min-h-[640px]">
            <div class="grid grid-cols-1 lg:grid-cols-[360px_1fr] h-full">
                <aside
                    class="h-full flex flex-col border-b border-[#e5e5e5] lg:border-b-0 lg:border-r"
                    :class="mobilePanel === 'chat' ? 'hidden lg:flex' : 'flex'"
                >
                    <div class="px-4 py-4 border-b border-[#ececec]">
                        <div class="flex items-center justify-between gap-3">
                            <h1 class="text-[28px] sm:text-[36px] font-semibold text-[#111]">Messages</h1>

                            <template x-if="activeConversationId">
                                <button
                                    type="button"
                                    class="lg:hidden h-10 px-4 rounded-xl border border-[#ddd] text-[14px] font-medium text-[#222]"
                                    @click="mobilePanel = 'chat'"
                                >
                                    Open Chat
                                </button>
                            </template>
                        </div>

                        <div class="mt-3">
                            <input
                                type="text"
                                x-model="search"
                                placeholder="Search conversations..."
                                class="w-full h-11 border border-[#ddd] rounded-lg px-3 text-[14px]"
                            >
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <template x-for="conversation in filteredConversations()" :key="conversation.id">
                            <a
                                :href="`{{ route('messages.index') }}?conversation=${conversation.id}`"
                                @click.prevent="openConversation(conversation.id)"
                                class="flex items-start gap-3 px-4 py-3 border-b border-[#f0f0f0] no-underline"
                                :class="activeConversationId === conversation.id ? 'bg-[#f7f7f7]' : 'bg-white hover:bg-[#fbfbfb]'"
                            >
                                <img :src="conversation.advert_image || '{{ asset('images/logo.webp') }}'" alt="" class="w-14 h-14 rounded-lg object-cover border border-[#e1e1e1]">

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="text-[18px] text-[#111] font-medium truncate" x-text="conversation.other_name"></div>
                                        <div class="text-[12px] text-[#9aa0ac] whitespace-nowrap" x-text="conversation.last_time || ''"></div>
                                    </div>

                                    <div class="text-[15px] text-[#c8a95a] truncate" x-text="conversation.title"></div>
                                    <div class="text-[14px] text-[#666] truncate" x-text="conversation.last_message"></div>
                                </div>
                            </a>
                        </template>

                        <template x-if="filteredConversations().length === 0">
                            <div class="px-4 py-10 text-[14px] text-[#888]">No conversations found.</div>
                        </template>
                    </div>
                </aside>

                <main class="h-full flex flex-col" :class="mobilePanel === 'list' ? 'hidden lg:flex' : 'flex'">
                    <template x-if="activeConversation">
                        <div class="h-full flex flex-col">
                            <div class="px-4 py-3 border-b border-[#ececec] flex items-center gap-3">
                                <button
                                    type="button"
                                    class="lg:hidden inline-flex w-10 h-10 rounded-xl border border-[#ddd] items-center justify-center text-[#222]"
                                    @click="showConversationList()"
                                    aria-label="Back to conversations"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>

                                <img :src="activeConversation.advert_image || '{{ asset('images/logo.webp') }}'" alt="" class="w-12 h-12 rounded-lg object-cover border border-[#e1e1e1]">

                                <div class="min-w-0">
                                    <div class="text-[20px] sm:text-[28px] text-[#111] font-medium truncate" x-text="activeConversation.title"></div>
                                    <div class="text-[14px] text-[#c8a95a] font-semibold" x-text="activeConversation.price ? `\u00A3${Number(activeConversation.price).toLocaleString()}` : ''"></div>
                                </div>
                            </div>

                            <div id="chat-scroll" class="flex-1 overflow-y-auto p-4 sm:p-5 bg-[#fff]">
                                <div class="max-w-[420px] mx-auto mb-6 rounded-lg border border-[#f1d7a0] bg-[#fff8e9] text-[#a06a18] text-[14px] px-4 py-3">
                                    Safety Reminder: Always meet in a public place or verified jeweller. Never send money before viewing the watch in person.
                                </div>

                                <template x-for="message in messages" :key="message.id">
                                    <div class="mb-3 flex" :class="message.is_mine ? 'justify-end' : 'justify-start'">
                                        <div
                                            class="max-w-[85%] sm:max-w-[70%] rounded-xl px-3 py-2 text-[14px]"
                                            :class="message.is_mine ? 'bg-black text-white' : 'bg-[#f1f1f1] text-[#222]'"
                                        >
                                            <div x-text="message.message"></div>
                                            <div class="text-[11px] mt-1 opacity-70" x-text="message.time || ''"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="border-t border-[#ececec] p-4">
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                    <input
                                        type="text"
                                        x-model="newMessage"
                                        @keydown.enter.prevent="sendMessage()"
                                        placeholder="Type your message..."
                                        class="flex-1 h-11 border border-[#ddd] rounded-lg px-3 text-[14px]"
                                    >

                                    <button
                                        type="button"
                                        @click="sendMessage()"
                                        class="h-11 px-5 rounded-lg bg-black text-white text-[14px] font-semibold disabled:opacity-60"
                                        :disabled="newMessage.trim().length === 0 || sending"
                                    >
                                        <span x-text="sending ? 'Sending...' : 'Send'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!activeConversation">
                        <div class="h-full flex items-center justify-center text-center p-6">
                            <div>
                                <h3 class="text-[24px] text-[#9aa0ac] font-medium">Select a conversation</h3>
                                <p class="text-[16px] text-[#9aa0ac] mt-2">Choose from your existing conversations</p>
                            </div>
                        </div>
                    </template>
                </main>
            </div>
        </div>
    </section>

    <script>
        function messagesPage(config) {
            return {
                meId: config.meId,
                conversations: config.initialConversations || [],
                activeConversationId: config.initialConversationId,
                activeConversation: null,
                mobilePanel: config.initialConversationId ? 'chat' : 'list',
                messages: [],
                newMessage: '',
                sending: false,
                search: '',
                listUrl: config.listUrl,
                itemUrlTemplate: config.itemUrlTemplate,
                listPoller: null,
                messagePoller: null,

                init() {
                    this.syncActiveConversation();
                    if (this.activeConversationId) {
                        this.loadMessages();
                    }

                    this.listPoller = setInterval(() => this.refreshConversations(), 5000);
                    this.messagePoller = setInterval(() => {
                        if (this.activeConversationId) {
                            this.loadMessages(false);
                        }
                    }, 2500);
                },

                filteredConversations() {
                    const needle = this.search.trim().toLowerCase();
                    if (!needle) {
                        return this.conversations;
                    }

                    return this.conversations.filter((conversation) => {
                        return (
                            String(conversation.other_name || '').toLowerCase().includes(needle) ||
                            String(conversation.title || '').toLowerCase().includes(needle) ||
                            String(conversation.last_message || '').toLowerCase().includes(needle)
                        );
                    });
                },

                syncActiveConversation() {
                    this.activeConversation = this.conversations.find((conversation) => conversation.id === this.activeConversationId) || null;
                },

                showConversationList() {
                    this.mobilePanel = 'list';
                },

                openConversation(conversationId) {
                    this.activeConversationId = conversationId;
                    this.syncActiveConversation();
                    this.mobilePanel = 'chat';

                    const url = new URL(window.location.href);
                    url.searchParams.set('conversation', conversationId);
                    window.history.replaceState({}, '', url);

                    this.loadMessages();
                },

                conversationItemUrl(id) {
                    return this.itemUrlTemplate.replace('__CID__', String(id));
                },

                async refreshConversations() {
                    try {
                        const response = await fetch(this.listUrl, { headers: { Accept: 'application/json' } });
                        const payload = await response.json();

                        if (!response.ok || !payload.ok) return;

                        this.conversations = payload.conversations || [];
                        this.syncActiveConversation();
                    } catch (error) {
                    }
                },

                async loadMessages(scrollToBottom = true) {
                    if (!this.activeConversationId) return;

                    try {
                        const response = await fetch(this.conversationItemUrl(this.activeConversationId), { headers: { Accept: 'application/json' } });
                        const payload = await response.json();

                        if (!response.ok || !payload.ok) return;

                        this.messages = payload.messages || [];

                        if (scrollToBottom) {
                            this.$nextTick(() => {
                                const box = document.getElementById('chat-scroll');
                                if (box) box.scrollTop = box.scrollHeight;
                            });
                        }
                    } catch (error) {
                    }
                },

                async sendMessage() {
                    const message = this.newMessage.trim();
                    if (!message || this.sending || !this.activeConversationId) return;

                    this.sending = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch(this.conversationItemUrl(this.activeConversationId), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': token || '',
                            },
                            body: JSON.stringify({ message }),
                        });
                        const payload = await response.json();

                        if (!response.ok || !payload.ok) {
                            throw new Error(payload.message || 'Unable to send message.');
                        }

                        this.newMessage = '';
                        await this.loadMessages();
                        await this.refreshConversations();
                    } catch (error) {
                        alert(error.message || 'Unable to send message.');
                    } finally {
                        this.sending = false;
                    }
                },
            };
        }
    </script>
</x-main-layout>
