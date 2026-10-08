{{--
    Nubi AI Chat Widget
    - Klik robot  → buka/tutup chat (popup)
    - Tahan + drag → pindah posisi robot
    - Ctrl+Shift+D → sembunyikan/tampilkan
    - ESC         → tutup chat
--}}

<div id="nubi-ai-widget"
     data-chat-url="{{ route('nubi-ai.chat') }}"
     data-user-photo="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff&size=32' }}"
     data-user-name="{{ auth()->user()->name }}"
     x-data="nubiAI()"
     x-init="init()">

    {{-- ── Backdrop overlay ── --}}
    <div x-show="open && !hidden"
         x-cloak
         x-on:click="closeChat()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position:fixed; inset:0; z-index:9989; background:rgba(0,0,0,0.25); backdrop-filter:blur(2px);"
         aria-hidden="true"></div>

    {{-- ── Chat Popup ── --}}
    <div id="nubi-modal"
         x-show="open && !hidden"
         x-cloak
         role="dialog"
         aria-modal="true"
         aria-label="Percakapan dengan Nubi AI"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         style="position:fixed; z-index:9990; bottom:148px; right:24px; width:360px; max-height:calc(100vh - 168px); transform-origin:bottom right;"
         class="bg-white dark:bg-navy-900 rounded-2xl shadow-[0_20px_60px_rgba(15,23,42,0.35)] border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-navy-800 to-navy-900 flex-shrink-0">
            <div class="w-9 h-9 rounded-full bg-white/10 border border-white/20 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img src="{{ asset('mascot/mascot-ai.png') }}" class="w-8 h-8 object-contain">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white leading-tight">Nubi AI</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse inline-block"></span>
                    <span class="text-[10px] text-slate-300">Asisten Aplikasi ICB CT</span>
                </div>
            </div>
            <button x-on:click="closeChat()"
                    type="button"
                    aria-label="Tutup percakapan"
                    class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div id="nubi-messages"
             x-ref="messages"
             style="flex:1; overflow-y:auto; padding:16px; min-height:0; max-height:340px; scroll-behavior:smooth;"
             class="space-y-3">

            <template x-if="messages.length === 0">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img src="{{ asset('mascot/mascot-ai.png') }}" class="w-6 h-6 object-contain">
                    </div>
                    <div class="bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]">
                        <p class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed">
                            Halo! Saya <strong>Nubi AI</strong> 👋<br>
                            Dibuat oleh <strong>Vexalyn Dev</strong> untuk membantu kamu menggunakan aplikasi <strong>Presensi Guru ICB CT</strong>.<br><br>
                            Ada yang bisa saya bantu? 😊
                        </p>
                    </div>
                </div>
            </template>

            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.role === 'user' ? 'flex items-end justify-end gap-2' : 'flex items-start gap-2.5'">
                    <template x-if="msg.role === 'assistant'">
                        <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <img src="{{ asset('mascot/mascot-ai.png') }}" class="w-6 h-6 object-contain">
                        </div>
                    </template>
                    <div :class="msg.role === 'user'
                            ? 'bg-navy-800 text-white rounded-2xl rounded-br-sm px-3.5 py-2.5 max-w-[85%]'
                            : (msg.failed
                                ? 'bg-red-50 dark:bg-red-900/20 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]'
                                : 'bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]')">
                        <p class="text-xs leading-relaxed"
                           :class="msg.role === 'user' ? 'text-white' : 'text-slate-700 dark:text-slate-200'"
                           x-html="fmt(msg.content)"></p>
                    </div>
                    <template x-if="msg.role === 'user'">
                        <div class="w-7 h-7 rounded-full bg-navy-800 border-2 border-navy-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <img :src="userPhoto" class="w-7 h-7 object-cover"
                                 x-on:error="$el.src='https://ui-avatars.com/api/?name='+encodeURIComponent(userName)+'&background=0F172A&color=fff&size=32'">
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="typing">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img src="{{ asset('mascot/mascot-ai.png') }}" class="w-6 h-6 object-contain">
                    </div>
                    <div class="bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-4 py-3">
                        <div class="flex gap-1.5 items-center">
                            <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Quick suggestions --}}
        <template x-if="messages.length === 0 && !typing">
            <div class="px-4 pb-2 flex flex-wrap gap-1.5 flex-shrink-0">
                <button type="button" x-on:click="suggest('Berapa guru yang hadir hari ini?')" class="nubi-chip">Kehadiran hari ini</button>
                <button type="button" x-on:click="suggest('Siapa saja guru yang belum presensi hari ini?')" class="nubi-chip">Belum presensi</button>
                <button type="button" x-on:click="suggest('Ada berapa pengajuan izin yang belum disetujui?')" class="nubi-chip">Izin pending</button>
                <button type="button" x-on:click="suggest('Bagaimana cara menyetujui pengajuan izin guru?')" class="nubi-chip">Setujui izin</button>
                <button type="button" x-on:click="suggest('Bagaimana cara export laporan presensi?')" class="nubi-chip">Export laporan</button>
            </div>
        </template>

        {{-- Input --}}
        <div class="px-3 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-navy-900/50 flex-shrink-0">
            <form x-on:submit.prevent="send()" class="flex gap-2 items-end">
                <textarea x-model="input"
                          x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"
                          :disabled="typing"
                          rows="1"
                          placeholder="Tanya sesuatu..."
                          x-ref="inp"
                          x-on:input="$el.style.height='auto'; $el.style.height=Math.min($el.scrollHeight,80)+'px'"
                          class="flex-1 text-xs bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-800/20 focus:border-navy-800 resize-none disabled:opacity-60"
                          style="max-height:80px; overflow-y:auto;"></textarea>
                <button type="submit" :disabled="typing || !input.trim()"
                        class="w-9 h-9 rounded-xl bg-navy-800 hover:bg-navy-700 disabled:opacity-40 flex items-center justify-center transition-all hover:scale-105 active:scale-95 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                </button>
            </form>
            <p class="text-[9px] text-slate-400 mt-1.5 text-center">Nubi AI · Dibuat oleh Vexalyn Dev</p>
        </div>
    </div>

    {{-- ── Robot Button (draggable, berdiri di layar) ── --}}
    <button id="nubi-btn"
            type="button"
            x-show="!hidden"
            x-cloak
            aria-label="Buka percakapan Nubi AI"
            aria-controls="nubi-modal"
            aria-keyshortcuts="Control+Shift+D"
            :aria-expanded="open"
            style="position:fixed; bottom:0; right:32px; z-index:9991; width:110px; height:130px; padding:0; border:0; background:transparent; touch-action:none; user-select:none; cursor:pointer; display:flex; align-items:flex-end; justify-content:center;">

        {{-- Ground shadow --}}
        <span class="nubi-shadow" aria-hidden="true"></span>

        {{-- Online ping dot --}}
        <span x-show="!open" class="nubi-ping-dot" aria-hidden="true">
            <span class="nubi-ping-ring"></span>
        </span>

        {{-- Robot image --}}
        <img id="nubi-img"
             src="{{ asset('mascot/mascot-ai.png') }}"
             alt="Nubi AI"
             draggable="false"
             class="nubi-robot-img select-none"
             style="width:110px; height:120px; object-fit:contain; object-position:bottom;">
    </button>

</div>

<script>
function nubiAI() {
    return {
        open:      false,
        hidden:    false,
        typing:    false,
        input:     '',
        messages:  [],
        chatUrl:   '',
        userPhoto: '',
        userName:  '',

        init() {
            const w = document.getElementById('nubi-ai-widget');
            this.chatUrl   = w?.dataset.chatUrl   ?? '';
            this.userPhoto = w?.dataset.userPhoto ?? '';
            this.userName  = w?.dataset.userName  ?? '';

            // Restore chat history
            try {
                const saved = JSON.parse(sessionStorage.getItem('nubi_chat') ?? '[]');
                if (Array.isArray(saved)) {
                    this.messages = saved
                        .filter(m => m && ['user','assistant'].includes(m.role) && typeof m.content === 'string')
                        .map(m => ({ role: m.role, content: m.content, failed: m.failed === true }))
                        .slice(-30);
                }
            } catch(_) {}

            this._initDrag();

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.open) {
                    e.preventDefault();
                    this.closeChat();
                    return;
                }
                if (e.ctrlKey && e.shiftKey && !e.altKey && !e.metaKey && (e.key === 'D' || e.key === 'd')) {
                    e.preventDefault();
                    e.stopPropagation();
                    this._toggleHidden();
                }
            });
        },

        _toggleHidden() {
            this.hidden = !this.hidden;
            if (this.hidden && this.open) this.open = false;
        },

        _initDrag() {
            const btn = document.getElementById('nubi-btn');
            if (!btn) return;

            // Restore saved position
            try {
                const p = JSON.parse(localStorage.getItem('nubi_pos') ?? 'null');
                if (p && typeof p.right === 'number' && typeof p.bottom === 'number') {
                    const bW = 110, bH = 130;
                    btn.style.right  = Math.max(0, Math.min(window.innerWidth  - bW, p.right))  + 'px';
                    btn.style.bottom = Math.max(0, Math.min(window.innerHeight - bH, p.bottom)) + 'px';
                    btn.style.left   = 'auto';
                    btn.style.top    = 'auto';
                }
            } catch(_) {}

            let activePointer = null, dragging = false, didDrag = false;
            let startMX = 0, startMY = 0, startBL = 0, startBT = 0;

            btn.addEventListener('pointerdown', (e) => {
                if (e.pointerType === 'mouse' && e.button !== 0) return;
                activePointer = e.pointerId;
                dragging = didDrag = false;
                startMX = e.clientX; startMY = e.clientY;
                btn.setPointerCapture(e.pointerId);
                const r = btn.getBoundingClientRect();
                startBL = r.left; startBT = r.top;
                btn.style.left = startBL + 'px'; btn.style.top = startBT + 'px';
                btn.style.right = 'auto'; btn.style.bottom = 'auto';
                btn.style.cursor = 'grab';
            });

            btn.addEventListener('pointermove', (e) => {
                if (e.pointerId !== activePointer) return;
                const dx = e.clientX - startMX, dy = e.clientY - startMY;
                if (!dragging) {
                    if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return;
                    dragging = didDrag = true;
                    btn.classList.add('is-dragging');
                    document.body.style.cursor = 'grabbing';
                    btn.style.cursor = 'grabbing';
                }
                const bW = 110, bH = 130;
                btn.style.left = Math.max(0, Math.min(window.innerWidth  - bW, startBL + dx)) + 'px';
                btn.style.top  = Math.max(0, Math.min(window.innerHeight - bH, startBT + dy)) + 'px';
            });

            const finishDrag = (e) => {
                if (e.pointerId !== activePointer) return;
                activePointer = null;
                document.body.style.cursor = '';
                btn.classList.remove('is-dragging');
                btn.style.cursor = 'pointer';
                if (dragging) {
                    dragging = false;
                    const curL = parseFloat(btn.style.left) || 0;
                    const curT = parseFloat(btn.style.top)  || 0;
                    try {
                        localStorage.setItem('nubi_pos', JSON.stringify({
                            right:  window.innerWidth  - curL - 110,
                            bottom: window.innerHeight - curT - 130,
                        }));
                    } catch(_) {}
                }
            };
            btn.addEventListener('pointerup', finishDrag);
            btn.addEventListener('pointercancel', finishDrag);

            // Click — only if no drag
            btn.addEventListener('click', (e) => {
                if (didDrag) { e.preventDefault(); e.stopImmediatePropagation(); didDrag = false; return; }
                this.toggleChat();
            }, true);
        },

        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    this._scrollBottom();
                    if (this.$refs.inp) this.$refs.inp.focus();
                });
            }
        },

        closeChat() {
            this.open = false;
            this.$nextTick(() => {
                if (!this.hidden) document.getElementById('nubi-btn')?.focus();
            });
        },

        async send() {
            const msg = this.input.trim();
            if (!msg || this.typing) return;

            const history = this.messages
                .filter(m => ['user','assistant'].includes(m.role) && !m.failed)
                .slice(-10)
                .map(({ role, content }) => ({ role, content }));

            this.input = '';
            if (this.$refs.inp) this.$refs.inp.style.height = 'auto';

            this.messages.push({ role: 'user', content: msg });
            this._save();
            this._scrollBottom();
            this.typing = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!this.chatUrl || !csrfToken) {
                    throw new Error('Nubi AI belum siap. Muat ulang halaman dan coba lagi.');
                }

                const res = await fetch(this.chatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: msg, history }),
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    throw new Error(data.error || (res.status === 419
                        ? 'Sesi kamu sudah berakhir. Muat ulang halaman lalu coba lagi.'
                        : 'Nubi AI sedang tidak tersedia. Silakan coba lagi.'));
                }
                if (typeof data.reply !== 'string' || !data.reply.trim()) {
                    throw new Error(data.error || 'Nubi AI mengirim jawaban kosong. Silakan coba lagi.');
                }

                this.messages.push({ role: 'assistant', content: data.reply });

            } catch (error) {
                this.messages.push({
                    role: 'assistant',
                    content: error instanceof TypeError
                        ? 'Koneksi ke Nubi AI gagal. Periksa jaringan lalu coba lagi.'
                        : (error instanceof Error ? error.message : 'Nubi AI gagal memproses pesan. Silakan coba lagi.'),
                    failed: true,
                });
            } finally {
                this.typing = false;
                this._save();
                this.$nextTick(() => this._scrollBottom());
            }
        },

        suggest(t) { this.input = t; this.$nextTick(() => this.send()); },

        _scrollBottom() {
            this.$nextTick(() => {
                const c = this.$refs.messages;
                if (c) c.scrollTop = c.scrollHeight;
            });
        },

        _save() {
            try { sessionStorage.setItem('nubi_chat', JSON.stringify(this.messages.slice(-30))); } catch(_) {}
        },

        fmt(text) {
            if (typeof text !== 'string' || !text) return '';
            let s = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            s = s.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            s = s.replace(/\*(.+?)\*/g, '<em>$1</em>');
            s = s.replace(/\n/g, '<br>');
            return s;
        },
    };
}
</script>

<style>
    /* ── Scrollbar ── */
    #nubi-messages::-webkit-scrollbar { width: 4px; }
    #nubi-messages::-webkit-scrollbar-track { background: transparent; }
    #nubi-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .dark #nubi-messages::-webkit-scrollbar-thumb { background: #334155; }

    /* ── Chip buttons ── */
    .nubi-chip {
        font-size: 11px; padding: 3px 10px; border-radius: 9999px;
        background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569;
        cursor: pointer; transition: background 0.15s;
    }
    .nubi-chip:hover { background: #e2e8f0; }
    .dark .nubi-chip { background: #1e293b; border-color: #334155; color: #94a3b8; }
    .dark .nubi-chip:hover { background: #334155; }

    /* ── Focus ── */
    #nubi-ai-widget button:focus-visible,
    #nubi-ai-widget textarea:focus-visible {
        outline: 2px solid #0f172a;
        outline-offset: 2px;
    }

    /* ── Robot idle float ── */
    .nubi-robot-img {
        filter: drop-shadow(0 10px 20px rgba(15,23,42,0.2));
        transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), filter 0.2s ease;
        animation: nubi-float 3.5s ease-in-out infinite;
    }
    @@keyframes nubi-float {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-8px); }
    }

    /* ── Hover / active ── */
    #nubi-btn:hover .nubi-robot-img {
        animation: none;
        transform: translateY(-10px) scale(1.05);
        filter: drop-shadow(0 14px 28px rgba(15,23,42,0.28));
    }
    #nubi-btn:active .nubi-robot-img {
        animation: none;
        transform: scale(0.95) !important;
    }
    #nubi-btn.is-dragging .nubi-robot-img {
        animation: none !important;
        transform: scale(0.92) rotate(3deg) !important;
        filter: drop-shadow(0 6px 12px rgba(15,23,42,0.3)) !important;
    }

    /* ── Ground shadow ── */
    .nubi-shadow {
        position: absolute;
        bottom: 2px; left: 50%;
        transform: translateX(-50%);
        width: 64px; height: 10px;
        background: rgba(0,0,0,0.12);
        border-radius: 50%;
        filter: blur(5px);
        pointer-events: none;
        animation: nubi-shadow-pulse 3.5s ease-in-out infinite;
    }
    @@keyframes nubi-shadow-pulse {
        0%, 100% { width: 64px; opacity: 0.6; }
        50%       { width: 44px; opacity: 0.3; }
    }
    #nubi-btn.is-dragging .nubi-shadow { animation: none; width: 50px; opacity: 0.4; }

    /* ── Online ping dot ── */
    .nubi-ping-dot {
        position: absolute;
        top: 14px; right: 8px;
        width: 12px; height: 12px;
        background: #34d399;
        border: 2px solid white;
        border-radius: 50%;
        pointer-events: none;
        z-index: 1;
    }
    .nubi-ping-ring {
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        background: #34d399;
        opacity: 0.6;
        animation: nubi-ping 2s cubic-bezier(0,0,0.2,1) infinite;
    }
    @@keyframes nubi-ping {
        0%   { transform: scale(1); opacity: 0.6; }
        75%, 100% { transform: scale(2.2); opacity: 0; }
    }

    /* ── Responsive ── */
    @@media (max-width: 480px) {
        #nubi-modal {
            right: 8px !important;
            left: 8px !important;
            width: auto !important;
            bottom: 145px !important;
        }
        #nubi-btn {
            right: 8px !important;
            width: 90px !important;
            height: 108px !important;
        }
        #nubi-btn .nubi-robot-img {
            width: 90px !important;
            height: 100px !important;
        }
    }
</style>
