@extends('layouts.app')

@section('page-title', 'Pesan CS (Support Guru)')

@section('content')
<div class="h-[calc(100vh-180px)] flex bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
    <!-- Chat List (Left Sidebar) -->
    <div class="w-80 border-r border-slate-200 dark:border-slate-800 flex flex-col">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-navy-800 dark:text-white">Percakapan</h3>
            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] rounded-full font-bold">
                {{ $users->count() }}
            </span>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @forelse($users as $user)
                <button onclick="selectChat({{ $user->id }}, '{{ $user->name }}')" 
                        id="user-btn-{{ $user->id }}"
                        class="w-full p-4 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-50 dark:border-slate-800/30 text-left user-chat-item">
                    <div class="relative">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-navy-100 dark:bg-navy-700 flex items-center justify-center text-navy-600 dark:text-navy-300 font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-navy-900 rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ $user->name }}</h4>
                            <span class="text-[10px] text-slate-400">{{ $user->messagesAsConversationUser->first()->created_at->format('H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                            {{ $user->messagesAsConversationUser->first()->message }}
                        </p>
                    </div>
                </button>
            @empty
                <div class="p-4 text-center text-slate-400 text-xs mt-10">Belum ada percakapan.</div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area (Right) -->
    <div class="flex-1 flex flex-col bg-slate-50/50 dark:bg-navy-950/20">
        <!-- Chat Area State: Empty -->
        <div id="chat-empty-state" class="flex-1 flex flex-col items-center justify-center text-center p-10">
            <div class="w-20 h-20 bg-slate-100 dark:bg-navy-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="message-square" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Pilih Percakapan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xs">Pilih salah satu guru di sebelah kiri untuk melihat riwayat pesan dan membalas dukungannya.</p>
        </div>

        <!-- Chat Area State: Active -->
        <div id="chat-active-state" class="hidden flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="p-4 bg-white dark:bg-navy-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="active-user-avatar" class="w-10 h-10 rounded-full bg-navy-800 flex items-center justify-center text-white font-bold">A</div>
                    <div>
                        <h4 id="active-user-name" class="text-sm font-bold text-navy-800 dark:text-white">Admin Support</h4>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            <span class="text-[10px] text-green-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="admin-message-container" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                <!-- Messages dynamicly here -->
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white dark:bg-navy-900 border-t border-slate-200 dark:border-slate-800">
                <form id="admin-chat-form" class="flex items-center gap-2" onsubmit="event.preventDefault(); sendAdminMessage();">
                    <input type="text" id="admin-chat-input" 
                           class="flex-1 bg-slate-100 dark:bg-navy-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-navy-600 dark:focus:ring-navy-400 placeholder:text-slate-400"
                           placeholder="Ketik balasan Anda di sini...">
                    <button type="submit" class="p-3 bg-navy-800 hover:bg-navy-700 text-white rounded-xl transition-all hover:scale-105 active:scale-95 shadow-lg shadow-navy-800/30">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }

    .user-chat-item.active {
        background-color: rgb(241 245 249);
    }
    .dark .user-chat-item.active {
        background-color: rgba(30, 41, 59, 0.5);
    }

    .message-bubble {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        position: relative;
    }
    .message-recieved {
        background-color: #ffffff;
        color: #1e293b;
        border-bottom-left-radius: 0.25rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .dark .message-recieved {
        background-color: #1e293b;
        color: #f8fafc;
    }
    .message-sent {
        background-color: #0F172A;
        color: #ffffff;
        border-bottom-right-radius: 0.25rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
</style>

<script>
    let activeChatUserId = null;
    let chatPollInterval = null;

    function selectChat(userId, userName) {
        if (activeChatUserId === userId) return;

        // Visual update
        document.querySelectorAll('.user-chat-item').forEach(item => item.classList.remove('active'));
        document.getElementById(`user-btn-${userId}`).classList.add('active');

        // UI State update
        document.getElementById('chat-empty-state').classList.add('hidden');
        document.getElementById('chat-active-state').classList.remove('hidden');
        document.getElementById('active-user-name').textContent = userName;
        document.getElementById('active-user-avatar').textContent = userName.charAt(0);

        activeChatUserId = userId;
        
        // Reset container and load
        document.getElementById('admin-message-container').innerHTML = '';
        loadAdminMessages();

        // Polling
        if (chatPollInterval) clearInterval(chatPollInterval);
        chatPollInterval = setInterval(loadAdminMessages, 5000);
    }

    async function loadAdminMessages() {
        if (!activeChatUserId) return;
        
        try {
            const response = await fetch(`/messages/${activeChatUserId}`);
            if (response.ok) {
                const messages = await response.json();
                renderAdminMessages(messages);
            }
        } catch (e) {
            console.error("Gagal memuat pesan", e);
        }
    }

    function renderAdminMessages(messages) {
        const container = document.getElementById('admin-message-container');
        const currentId = {{ Auth::id() }};
        
        // Detect if we need to scroll (if user was already at bottom)
        const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;

        container.innerHTML = messages.map(msg => {
            const isMe = msg.sender_id === currentId;
            return `
                <div class="flex ${isMe ? 'justify-end' : 'justify-start'} animate-fadeIn">
                    <div class="message-bubble ${isMe ? 'message-sent' : 'message-recieved'}">
                        <p>${msg.message.replace(/\n/g, '<br>')}</p>
                        <div class="text-[9px] mt-1 text-right opacity-60">
                            ${new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        if (isAtBottom) {
            container.scrollTop = container.scrollHeight;
        }
        
        // Refresh icons if needed
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    }

    async function sendAdminMessage() {
        const input = document.getElementById('admin-chat-input');
        const message = input.value.trim();
        if (!message || !activeChatUserId) return;

        input.value = '';
        
        try {
            const response = await fetch('/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    conversation_user_id: activeChatUserId
                })
            });

            if (response.ok) {
                loadAdminMessages();
            }
        } catch (e) {
            console.error("Gagal mengirim pesan", e);
        }
    }

    // Handle logout/cleanup
    window.addEventListener('beforeunload', () => {
        if (chatPollInterval) clearInterval(chatPollInterval);
    });
</script>
@endsection
