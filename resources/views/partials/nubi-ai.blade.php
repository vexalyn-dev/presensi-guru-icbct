{{--
    Nubi AI Chat Widget
    - Draggable (tahan klik kiri + drag)
    - Ctrl+Del untuk sembunyikan/tampilkan
    - Chat modal muncul ke atas & menyesuaikan posisi
    - Hanya untuk: Admin, Operator, Guru Piket, Developer
--}}

{{-- ══ NUBI AI FLOATING WIDGET ══ --}}
<div id="nubi-ai-widget"
     data-chat-url="{{ route('nubi-ai.chat') }}"
     data-user-photo="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff&size=32' }}"
     data-user-name="{{ urlencode(auth()->user()->name) }}"
     x-data="nubiAI()"
     x-init="init()"
     x-show="!widgetHidden"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-75"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-75"
     x-transition:enter-end="opacity-100 scale-100"
     style="position: fixed; bottom: 24px; right: 24px; z-index: 9990; user-select: none;">

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
         :style="modalStyle"
         class="absolute w-[340px] sm:w-[380px] bg-white dark:bg-navy-900 rounded-2xl shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col"
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
            <button x-on:click="open = false"
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
                                 x-on:error="$el.src='https://ui-avatars.com/api/?name='+userName+'&background=0F172A&color=fff&size=32'">
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

        {{-- Quick suggestions --}}
        <template x-if="messages.length === 0 && !typing">
            <div class="px-4 pb-2 flex flex-wrap gap-1.5 flex-shrink-0">
                <button x-on:click="sendSuggestion('Berapa guru yang hadir hari ini?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    📊 Kehadiran hari ini
                </button>
                <button x-on:click="sendSuggestion('Siapa saja guru yang belum presensi hari ini?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    ⏳ Belum presensi
                </button>
                <button x-on:click="sendSuggestion('Ada berapa pengajuan izin yang belum disetujui?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    📝 Izin pending
                </button>
                <button x-on:click="sendSuggestion('Bagaimana cara menyetujui pengajuan izin guru?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    ✅ Cara approve izin
                </button>
                <button x-on:click="sendSuggestion('Bagaimana cara export laporan presensi?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-navy-800 dark:hover:bg-navy-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                    📈 Export laporan
                </button>
            </div>
        </template>

        {{-- Input Area --}}
        <div class="px-3 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-navy-900/50 flex-shrink-0">
            <form x-on:submit.prevent="sendMessage()" class="flex gap-2 items-end">
                <textarea
                    x-model="inputMessage"
                    x-on:keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                    :disabled="typing"
                    rows="1"
                    placeholder="Tanya sesuatu..."
                    class="flex-1 text-xs bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-800/20 dark:focus:ring-gold-400/20 focus:border-navy-800 dark:focus:border-gold-400 resize-none transition-colors disabled:opacity-60"
                    style="max-height: 80px; overflow-y: auto;"
                    x-ref="inputRef"
                    x-on:input="autoResize($el)"></textarea>
                <button type="submit"
                        :disabled="typing || !inputMessage.trim()"
                        class="w-9 h-9 rounded-xl bg-navy-800 hover:bg-navy-700 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition-all flex-shrink-0 hover:scale-105 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                </button>
            </form>
            <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1.5 text-center">
                Nubi AI · Dibuat oleh Vexalyn Dev
            </p>
        </div>
    </div>

    {{-- ── Floating Robot Button ── --}}
    <div class="relative flex flex-col items-center" style="width: 80px;">

        {{-- Tooltip Ctrl+Del --}}
        <div x-show="showTooltip" x-cloak
             style="position:absolute; bottom: 88px; right: 0; white-space:nowrap;"
             class="text-[10px] font-medium text-slate-500 dark:text-slate-400 bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded-full shadow-sm pointer-events-none">
            Ctrl+Del untuk sembunyikan
        </div>

        {{-- Robot button — draggable handle --}}
        <div id="nubi-drag-handle"
             x-on:mousedown="startDrag($event)"
             class="nubi-drag-handle relative flex items-center justify-center"
             style="width: 80px; height: 80px;">

            {{-- Pulse ring saat closed --}}
            <span x-show="!open" class="absolute inset-0 rounded-full bg-navy-800/15 animate-ping" style="animation-duration: 3s;"></span>

            {{-- Robot GIF — ukuran besar, setara robot headset --}}
            <img src="{{ asset('images/Nubi-AI.gif') }}"
                 alt="Nubi AI"
                 x-on:click="!hasDragged && toggleChat()"
                 class="w-20 h-20 object-contain drop-shadow-lg transition-transform duration-200 select-none"
                 :class="open ? 'scale-95' : 'hover:scale-105'"
                 draggable="false">

            {{-- Unread badge --}}
            <span x-show="unreadCount > 0 && !open" x-cloak
                  class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white dark:border-navy-900 rounded-full flex items-center justify-center pointer-events-none">
                <span class="text-[9px] text-white font-bold" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            </span>
        </div>
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
        showTooltip: false,
        widgetHidden: false,

        // Drag state
        dragging: false,
        dragStartX: 0,
        dragStartY: 0,
        widgetStartRight: 24,
        widgetStartBottom: 24,
        hasDragged: false,
        modalStyle: '',

        init() {
            const widget = document.getElementById('nubi-ai-widget');
            this.chatUrl   = widget ? widget.dataset.chatUrl   : '';
            this.userPhoto = widget ? widget.dataset.userPhoto : '';
            this.userName  = widget ? decodeURIComponent(widget.dataset.userName || '') : '';

            // Restore chat history
            try {
                const saved = sessionStorage.getItem('nubi_chat_history');
                if (saved) this.messages = JSON.parse(saved);
            } catch (e) {}

            // Restore posisi dari localStorage
            try {
                const pos = JSON.parse(localStorage.getItem('nubi_pos') || 'null');
                if (pos && widget) {
                    widget.style.right  = pos.right  + 'px';
                    widget.style.bottom = pos.bottom + 'px';
                    widget.style.left   = 'auto';
                    widget.style.top    = 'auto';
                }
            } catch (e) {}

            // Keyboard shortcut: Ctrl + Delete — toggle sembunyikan/tampilkan widget
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 'Delete') {
                    e.preventDefault();
                    this.widgetHidden = !this.widgetHidden;
                    if (this.widgetHidden) {
                        this.open = false;
                    }
                }
            });

            // Tooltip hint sekali
            setTimeout(() => {
                this.showTooltip = true;
                setTimeout(() => { this.showTooltip = false; }, 3000);
            }, 2000);

            // Bind drag events ke document
            document.addEventListener('mousemove', (e) => this.onDragMove(e));
            document.addEventListener('mouseup',   (e) => this.onDragEnd(e));
        },

        toggleChat() {
            this.open = !this.open;
            // Reset hasDragged setelah click diproses
            this.hasDragged = false;
            if (this.open) {
                this.unreadCount = 0;
                this.updateModalStyle();
                this.$nextTick(() => {
                    this.scrollToBottom();
                    if (this.$refs.inputRef) this.$refs.inputRef.focus();
                });
            }
        },

        // ── Drag logic ──
        startDrag(e) {
            if (e.button !== 0) return;
            this.hasDragged  = false;
            this.dragging    = false;
            this.dragStartX  = e.clientX;
            this.dragStartY  = e.clientY;

            // Simpan posisi widget sebagai left/top saat ini
            const widget = document.getElementById('nubi-ai-widget');
            const rect   = widget.getBoundingClientRect();
            this._initLeft = rect.left;
            this._initTop  = rect.top;

            // Cursor grab saat tahan klik (belum drag)
            const handle = document.getElementById('nubi-drag-handle');
            if (handle) handle.style.cursor = 'grab';

            // JANGAN preventDefault di sini agar click tetap bisa jalan
        },

        onDragMove(e) {
            if (this.dragStartX === undefined) return;

            const dx = e.clientX - this.dragStartX;
            const dy = e.clientY - this.dragStartY;

            // Aktifkan drag hanya setelah bergerak > 6px (threshold)
            if (!this.dragging) {
                if (Math.abs(dx) < 6 && Math.abs(dy) < 6) return;
                this.dragging   = true;
                this.hasDragged = true;
                // Cursor grabbing saat drag aktif — seluruh halaman
                document.body.classList.add('nubi-dragging');
            }

            const widget = document.getElementById('nubi-ai-widget');
            if (!widget) return;

            const wW = widget.offsetWidth  || 80;
            const wH = widget.offsetHeight || 80;

            let newLeft = this._initLeft + dx;
            let newTop  = this._initTop  + dy;

            newLeft = Math.max(8, Math.min(window.innerWidth  - wW - 8, newLeft));
            newTop  = Math.max(8, Math.min(window.innerHeight - wH - 8, newTop));

            widget.style.left   = newLeft + 'px';
            widget.style.top    = newTop  + 'px';
            widget.style.right  = 'auto';
            widget.style.bottom = 'auto';

            if (this.open) this.updateModalStyle();
        },

        onDragEnd(e) {
            const handle = document.getElementById('nubi-drag-handle');

            if (!this.dragging) {
                // Tidak drag → reset cursor ke pointer, state saja
                if (handle) handle.style.cursor = 'pointer';
                this.dragStartX = undefined;
                return;
            }

            // Selesai drag — kembalikan cursor
            document.body.classList.remove('nubi-dragging');
            if (handle) handle.style.cursor = 'pointer';
            this.dragging   = false;
            this.dragStartX = undefined;

            // Convert posisi left/top ke right/bottom lalu simpan
            const widget = document.getElementById('nubi-ai-widget');
            if (widget) {
                const rect      = widget.getBoundingClientRect();
                const newRight  = window.innerWidth  - rect.right;
                const newBottom = window.innerHeight - rect.bottom;

                widget.style.right  = Math.max(8, newRight)  + 'px';
                widget.style.bottom = Math.max(8, newBottom) + 'px';
                widget.style.left   = 'auto';
                widget.style.top    = 'auto';

                try {
                    localStorage.setItem('nubi_pos', JSON.stringify({
                        right:  Math.max(8, newRight),
                        bottom: Math.max(8, newBottom),
                    }));
                } catch (err) {}
            }
        },

        // Hitung posisi modal supaya selalu muncul ke atas & tidak kepotong
        updateModalStyle() {
            this.$nextTick(() => {
                const widget = document.getElementById('nubi-ai-widget');
                const modal  = document.getElementById('nubi-chat-modal');
                if (!widget || !modal) return;

                const wRect     = widget.getBoundingClientRect();
                const modalH    = 520;
                const modalW    = window.innerWidth >= 640 ? 380 : 340;
                const gap       = 8;
                const padding   = 8;

                // Default: muncul ke atas
                let bottom = wRect.height + gap;
                let right  = 0;

                // Cek apakah modal kepotong di atas
                if (wRect.top - modalH - gap < padding) {
                    // Muncul ke bawah kalau tidak muat ke atas
                    bottom = -(modalH + gap);
                }

                // Cek apakah modal kepotong di kiri
                if (wRect.left - modalW + wRect.width < padding) {
                    right = -(modalW - wRect.width);
                    right = Math.min(right, 0);
                }

                modal.style.bottom = bottom + 'px';
                modal.style.right  = right  + 'px';
                modal.style.top    = 'auto';
                modal.style.left   = 'auto';
            });
        },

        async sendMessage() {
            const msg = this.inputMessage.trim();
            if (!msg || this.typing) return;

            this.inputMessage = '';
            this.$nextTick(() => {
                if (this.$refs.inputRef) this.$refs.inputRef.style.height = 'auto';
            });

            this.messages.push({ role: 'user', content: msg });
            this.saveHistory();
            this.scrollToBottom();

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
                this.messages.push({
                    role: 'assistant',
                    content: data.error ? '❌ ' + data.error : data.reply
                });
                if (!this.open) this.unreadCount++;

            } catch (err) {
                this.messages.push({ role: 'assistant', content: '❌ Gagal terhubung ke Nubi AI. Periksa koneksi internet.' });
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
                const c = document.getElementById('nubi-messages');
                if (c) c.scrollTop = c.scrollHeight;
            });
        },

        saveHistory() {
            try {
                sessionStorage.setItem('nubi_chat_history', JSON.stringify(this.messages.slice(-30)));
            } catch (e) {}
        },

        formatMessage(text) {
            if (!text) return '';
            let s = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            s = s.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            s = s.replace(/\*(.+?)\*/g, '<em>$1</em>');
            s = s.replace(/\n/g, '<br>');
            return s;
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
    #nubi-ai-widget {
        touch-action: none;
    }
    /* Cursor default pointer di robot, JS yang urus grab/grabbing */
    .nubi-drag-handle {
        cursor: pointer;
    }
    /* Paksa grabbing cursor ke seluruh halaman saat drag aktif */
    body.nubi-dragging,
    body.nubi-dragging * {
        cursor: grabbing !important;
        user-select: none !important;
    }
    #nubi-messages {
        scroll-behavior: smooth;
    }
    #nubi-messages::-webkit-scrollbar { width: 4px; }
    #nubi-messages::-webkit-scrollbar-track { background: transparent; }
    #nubi-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .dark #nubi-messages::-webkit-scrollbar-thumb { background: #334155; }

    @@media (max-width: 400px) {
        #nubi-chat-modal {
            width: calc(100vw - 32px) !important;
        }
    }
</style>
