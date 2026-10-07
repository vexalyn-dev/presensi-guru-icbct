{{--
    Nubi AI Chat Widget
    Floating chat bot yang membantu guru menggunakan aplikasi ini.
    Diinjeksi ke semua layout (app.blade.php, teacher.blade.php, piket.blade.php)
--}}

{{-- ══ NUBI AI FLOATING WIDGET ══ --}}
<div id="nubi-ai-widget" 
     data-chat-url="{{ route('nubi-ai.chat') }}"
     data-user-photo="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff&size=32' }}"
     data-user-name="{{ urlencode(auth()->user()->name) }}"
     x-data="nubiAI()"
     x-init="init()"
     class="fixed bottom-6 right-6 z-[9990] select-none">

    {{-- ── Chat Modal ── --}}
    <div id="nubi-chat-modal"
         x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         x-cloak
         class="absolute bottom-20 right-0 w-[340px] sm:w-[380px] bg-white dark:bg-navy-900 rounded-2xl shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col"
         style="max-height: 520px;">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-navy-800 to-navy-900 flex-shrink-0">
            <div class="w-9 h-9 rounded-full bg-white/10 border border-white/20 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img src="{{ asset('images/Nubi-AI.gif') }}" alt="Nubi AI" class="w-8 h-8 object-contain rounded-full">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white leading-tight">Nubi AI</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse inline-block"></span>
                    <span class="text-[10px] text-slate-300">Asisten Aplikasi ICB CT</span>
                </div>
            </div>
            <button @click="open = false"
                    class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        {{-- Messages Area --}}
        <div id="nubi-messages"
             class="flex-1 overflow-y-auto px-4 py-4 space-y-3 scroll-smooth"
             style="min-height: 200px; max-height: 340px;"
             x-ref="messagesContainer">

            {{-- Welcome message --}}
            <template x-if="messages.length === 0">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                        <img src="{{ asset('images/Nubi-AI.gif') }}" alt="Nubi" class="w-6 h-6 object-contain rounded-full">
                    </div>
                    <div class="bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[80%]">
                        <p class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed">
                            Halo! Saya <strong>Nubi AI</strong> 👋<br>
                            Saya dibuat oleh <strong>Vexalyn Dev</strong> untuk membantu kamu menggunakan aplikasi <strong>Presensi Guru ICB CT</strong>.<br><br>
                            Ada yang bisa saya bantu? 😊
                        </p>
                    </div>
                </div>
            </template>

            {{-- Chat messages --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex items-end justify-end gap-2' : 'flex items-start gap-2.5'">

                    {{-- Bot avatar --}}
                    <template x-if="msg.role === 'assistant'">
                        <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                            <img src="{{ asset('images/Nubi-AI.gif') }}" alt="Nubi" class="w-6 h-6 object-contain rounded-full">
                        </div>
                    </template>

                    {{-- Message bubble --}}
                    <div :class="msg.role === 'user' 
                            ? 'bg-navy-800 text-white rounded-2xl rounded-br-sm px-3.5 py-2.5 max-w-[80%]' 
                            : 'bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[80%]'">
                        <p class="text-xs leading-relaxed whitespace-pre-wrap"
                           :class="msg.role === 'user' ? 'text-white' : 'text-slate-700 dark:text-slate-200'"
                           x-html="formatMessage(msg.content)"></p>
                    </div>

                    {{-- User avatar --}}
                    <template x-if="msg.role === 'user'">
                        <div class="w-7 h-7 rounded-full bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-navy-700">
                            <img :src="userPhoto" :alt="userName"
                                 class="w-7 h-7 object-cover"
                                 @error="$el.src='https://ui-avatars.com/api/?name='+userName+'&background=0F172A&color=fff&size=32'">
                        </div>
                    </template>
                </div>
            </template>

            {{-- Typing indicator --}}
            <template x-if="typing">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                        <img src="{{ asset('images/Nubi-AI.gif') }}" alt="Nubi" class="w-6 h-6 object-contain rounded-full">
                    </div>
                    <div class="bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-4 py-3">
                        <div class="flex gap-1.5 items-center">
                            <span class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Quick suggestions (only shown when no messages yet) --}}
        <template x-if="messages.length === 0 && !typing">
            <div class="px-4 pb-2 flex flex-wrap gap-1.5 flex-shrink-0">
                <button @click="sendSuggestion('Berapa guru yang hadir hari ini?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    📊 Kehadiran hari ini
                </button>
                <button @click="sendSuggestion('Siapa saja guru yang belum presensi hari ini?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    ⏳ Belum presensi
                </button>
                <button @click="sendSuggestion('Ada berapa pengajuan izin yang belum disetujui?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    📝 Izin pending
                </button>
                <button @click="sendSuggestion('Bagaimana cara menyetujui pengajuan izin guru?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    ✅ Cara approve izin
                </button>
                <button @click="sendSuggestion('Bagaimana cara export laporan presensi?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    � Export laporan
                </button>
            </div>
        </template>

        {{-- Input Area --}}
        <div class="px-3 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-navy-900/50 flex-shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2 items-end">
                <textarea
                    x-model="inputMessage"
                    @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                    :disabled="typing"
                    rows="1"
                    placeholder="Tanya sesuatu..."
                    class="flex-1 text-xs bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-800/20 dark:focus:ring-gold-400/20 focus:border-navy-800 dark:focus:border-gold-400 resize-none transition-colors disabled:opacity-60"
                    style="max-height: 80px; overflow-y: auto;"
                    x-ref="inputRef"
                    @input="autoResize($el)"></textarea>
                <button type="submit"
                        :disabled="typing || !inputMessage.trim()"
                        class="w-9 h-9 rounded-xl bg-navy-800 hover:bg-navy-700 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition-all flex-shrink-0 hover:scale-105 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                </button>
            </form>
            <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1.5 text-center">
                Nubi AI hanya membantu seputar penggunaan aplikasi ini
            </p>
        </div>
    </div>

    {{-- ── Floating Trigger Button ── --}}
    <div class="relative flex flex-col items-center">
        {{-- Tooltip "Sembunyikan Nubi AI" saat open --}}
        <div x-show="open" x-cloak
             class="absolute -top-8 right-0 whitespace-nowrap text-[10px] font-medium text-slate-500 dark:text-slate-400 bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded-full shadow-sm">
            Sembunyikan Nubi AI
        </div>

        {{-- Robot button --}}
        <button @click="toggleChat()"
                class="relative group flex items-center justify-center w-16 h-16 rounded-full shadow-[0_8px_24px_rgba(15,23,42,0.2)] hover:shadow-[0_12px_32px_rgba(15,23,42,0.3)] transition-all duration-300 hover:scale-110 active:scale-95 focus:outline-none"
                :class="open ? 'ring-2 ring-gold-400/60 ring-offset-2' : ''"
                title="Nubi AI — Asisten Penggunaan Aplikasi">
            
            {{-- Pulse ring saat closed --}}
            <span x-show="!open" class="absolute inset-0 rounded-full bg-navy-800/20 animate-ping" style="animation-duration: 2.5s;"></span>

            {{-- Robot GIF --}}
            <img src="{{ asset('images/Nubi-AI.gif') }}" 
                 alt="Nubi AI" 
                 class="w-16 h-16 object-contain drop-shadow-lg transition-transform duration-300"
                 :class="open ? 'scale-95' : 'group-hover:scale-105'">

            {{-- Unread badge (muncul saat ada pesan baru dari bot tapi modal tertutup) --}}
            <span x-show="unreadCount > 0 && !open" x-cloak
                  class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white dark:border-navy-900 rounded-full flex items-center justify-center">
                <span class="text-[9px] text-white font-bold" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            </span>
        </button>
    </div>
</div>

{{-- ══ NUBI AI ALPINE COMPONENT ══ --}}
<script>
function nubiAI() {
    return {
        open: false,
        typing: false,
        inputMessage: '',
        messages: [],
        unreadCount: 0,
        chatUrl: '',
        userPhoto: '',
        userName: '',

        init() {
            // Baca config dari data-* attribute (hindari Blade syntax di dalam JS)
            const widget = document.getElementById('nubi-ai-widget');
            this.chatUrl   = widget ? widget.dataset.chatUrl   : '';
            this.userPhoto = widget ? widget.dataset.userPhoto : '';
            this.userName  = widget ? decodeURIComponent(widget.dataset.userName || '') : '';

            // Restore chat history from sessionStorage
            try {
                const saved = sessionStorage.getItem('nubi_chat_history');
                if (saved) this.messages = JSON.parse(saved);
            } catch (e) {}
        },

        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.unreadCount = 0;
                this.$nextTick(() => {
                    this.scrollToBottom();
                    if (this.$refs.inputRef) this.$refs.inputRef.focus();
                });
            }
        },

        async sendMessage() {
            const msg = this.inputMessage.trim();
            if (!msg || this.typing) return;

            this.inputMessage = '';
            this.$nextTick(() => {
                if (this.$refs.inputRef) {
                    this.$refs.inputRef.style.height = 'auto';
                }
            });

            // Add user message
            this.messages.push({ role: 'user', content: msg });
            this.saveHistory();
            this.scrollToBottom();

            // Show typing
            this.typing = true;
            this.scrollToBottom();

            try {
                const response = await fetch(this.chatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: msg,
                        history: this.messages.slice(-10).filter(m => m.role !== 'error'),
                    }),
                });

                const data = await response.json();

                if (data.error) {
                    this.messages.push({ role: 'assistant', content: '❌ ' + data.error });
                } else {
                    this.messages.push({ role: 'assistant', content: data.reply });
                    if (!this.open) this.unreadCount++;
                }

            } catch (err) {
                this.messages.push({ role: 'assistant', content: '❌ Gagal terhubung ke Nubi AI. Periksa koneksi internet kamu.' });
            } finally {
                this.typing = false;
                this.saveHistory();
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        sendSuggestion(text) {
            this.inputMessage = text;
            this.$nextTick(() => this.sendMessage());
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('nubi-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        saveHistory() {
            try {
                // Simpan maks 30 pesan di sessionStorage
                const toSave = this.messages.slice(-30);
                sessionStorage.setItem('nubi_chat_history', JSON.stringify(toSave));
            } catch (e) {}
        },

        formatMessage(text) {
            if (!text) return '';
            // Escape HTML
            let safe = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Bold **text** atau *text*
            safe = safe.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            safe = safe.replace(/\*(.+?)\*/g, '<em>$1</em>');

            // Newlines
            safe = safe.replace(/\n/g, '<br>');

            return safe;
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 80) + 'px';
        },
    };
}
</script>

{{-- ══ NUBI AI STYLES ══ --}}
<style>
    /* Pastikan widget tidak ketutup sidebar/header */
    #nubi-ai-widget {
        z-index: 9990;
    }

    /* Smooth scroll di message container */
    #nubi-messages {
        scroll-behavior: smooth;
    }

    /* Custom scrollbar di chat */
    #nubi-messages::-webkit-scrollbar { width: 4px; }
    #nubi-messages::-webkit-scrollbar-track { background: transparent; }
    #nubi-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .dark #nubi-messages::-webkit-scrollbar-thumb { background: #334155; }

    /* Mobile: lebih kecil dan posisi lebih ke tengah */
    @@media (max-width: 400px) {
        #nubi-ai-widget {
            right: 12px;
            bottom: 12px;
        }
        #nubi-chat-modal {
            width: calc(100vw - 24px) !important;
            right: -4px !important;
        }
    }
</style>
