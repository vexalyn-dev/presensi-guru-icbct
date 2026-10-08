{{--
    Nubi AI Chat Widget
    - Klik robot  → buka/tutup chat
    - Tahan + drag → pindah posisi
    - Ctrl+Shift+D → sembunyikan/tampilkan
--}}

{{-- Widget wrapper — Alpine scope --}}
<div id="nubi-ai-widget"
     data-chat-url="{{ route('nubi-ai.chat') }}"
     data-user-photo="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff&size=32' }}"
     data-user-name="{{ auth()->user()->name }}"
     x-data="nubiAI()"
     x-init="init()">

    {{-- ── Chat Modal (posisi fixed, dihitung JS) ── --}}
    <div id="nubi-modal"
         x-show="open && !hidden"
         x-cloak
         role="dialog"
         aria-modal="true"
         aria-label="Percakapan dengan Nubi AI"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="position:fixed; z-index:9991; width:360px; max-height:calc(100vh - 16px); max-height:calc(100dvh - 16px); transform-origin:bottom right;"
         class="bg-white dark:bg-navy-900 rounded-2xl shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-navy-800 to-navy-900 flex-shrink-0">
            <div class="w-9 h-9 rounded-full bg-white/10 border border-white/20 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img src="{{ asset('images/Nubi-AI.gif') }}" class="w-8 h-8 object-contain rounded-full">
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
             style="flex:1; overflow-y:auto; padding:16px; min-height:0; max-height:310px; scroll-behavior:smooth;"
             class="space-y-3">

            <template x-if="messages.length === 0">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img src="{{ asset('images/Nubi-AI.gif') }}" class="w-6 h-6 object-contain rounded-full">
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
                            <img src="{{ asset('images/Nubi-AI.gif') }}" class="w-6 h-6 object-contain rounded-full">
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
                        <img src="{{ asset('images/Nubi-AI.gif') }}" class="w-6 h-6 object-contain rounded-full">
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

    {{-- ── Robot Button (posisi fixed, diatur JS) ── --}}
    <button id="nubi-btn"
         type="button"
         x-show="!hidden"
         aria-label="Buka percakapan Nubi AI"
         aria-controls="nubi-modal"
         aria-keyshortcuts="Control+Shift+D"
         :aria-expanded="open"
         style="position:fixed; bottom:24px; right:24px; z-index:9990; width:80px; height:80px; cursor:pointer; padding:0; border:0; background:transparent; touch-action:none; user-select:none;">
        <span x-show="!open"
              class="absolute inset-0 rounded-full bg-navy-800/10 animate-ping pointer-events-none"
              style="animation-duration:3s;"></span>
        <img id="nubi-img"
             src="{{ asset('images/Nubi-AI.gif') }}"
             alt="Nubi AI"
             class="w-20 h-20 object-contain drop-shadow-lg select-none"
             draggable="false"
             style="transition:transform 0.15s ease;">
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
            // ── Ambil config dari data-* ──
            const w = document.getElementById('nubi-ai-widget');
            this.chatUrl   = w?.dataset.chatUrl   ?? '';
            this.userPhoto = w?.dataset.userPhoto ?? '';
            this.userName  = w?.dataset.userName ?? '';

            // ── Restore chat history ──
            try {
                const s = sessionStorage.getItem('nubi_chat');
                const saved = s ? JSON.parse(s) : [];
                if (Array.isArray(saved)) {
                    this.messages = saved.filter(message =>
                        message
                        && ['user', 'assistant'].includes(message.role)
                        && typeof message.content === 'string'
                    ).map(message => ({
                        role: message.role,
                        content: message.content,
                        failed: message.failed === true,
                    })).slice(-30);
                }
            } catch (_) {}

            // ── Setup drag & click di robot button ──
            this._initDrag();

            // ── Ctrl+Shift+D: sembunyikan/tampilkan ──
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.open) {
                    e.preventDefault();
                    this.closeChat();
                    return;
                }

                if (e.ctrlKey && e.shiftKey && !e.altKey && !e.metaKey && e.key.toLowerCase() === 'd') {
                    e.preventDefault();
                    this.hidden = !this.hidden;
                    if (this.hidden) this.closeChat();
                }
            });
            window.addEventListener('resize', () => {
                if (this.open) this.$nextTick(() => this._posModal());
            });
        },

        _initDrag() {
            const btn = document.getElementById('nubi-btn');
            const img = document.getElementById('nubi-img');
            if (!btn || !img) return;

            // ── Restore posisi tersimpan ──
            try {
                const p = JSON.parse(localStorage.getItem('nubi_pos') ?? 'null');
                if (p && typeof p.left === 'number' && typeof p.top === 'number') {
                    const bW = 80, bH = 80;
                    const left = Math.max(8, Math.min(window.innerWidth  - bW - 8, p.left));
                    const top  = Math.max(8, Math.min(window.innerHeight - bH - 8, p.top));
                    btn.style.left   = left + 'px';
                    btn.style.top    = top  + 'px';
                    btn.style.right  = 'auto';
                    btn.style.bottom = 'auto';
                }
            } catch (_) {}

            let activePointer = null;
            let dragging   = false;
            let didDrag    = false;
            let startMX = 0, startMY = 0;
            let startBL = 0, startBT = 0;

            // ── click: toggle chat (hanya kalau tidak drag) ──
            btn.addEventListener('click', (e) => {
                if (didDrag) {
                    didDrag = false; // reset untuk gesture berikutnya
                    return;
                }
                this.toggleChat();
            });

            // ── Pointer events support mouse, pen, and touch ──
            btn.addEventListener('pointerdown', (e) => {
                if (e.pointerType === 'mouse' && e.button !== 0) return;
                activePointer = e.pointerId;
                dragging  = false;
                didDrag   = false;
                startMX   = e.clientX;
                startMY   = e.clientY;
                btn.setPointerCapture(e.pointerId);

                // Posisi btn saat ini dalam koordinat left/top
                const r  = btn.getBoundingClientRect();
                startBL  = r.left;
                startBT  = r.top;

                // Pastikan btn pakai left/top (bukan right/bottom)
                btn.style.left   = startBL + 'px';
                btn.style.top    = startBT + 'px';
                btn.style.right  = 'auto';
                btn.style.bottom = 'auto';

                // Cursor: grab (tanda siap drag)
                btn.style.cursor = 'grab';
            });

            btn.addEventListener('pointermove', (e) => {
                if (e.pointerId !== activePointer) return;

                const dx = e.clientX - startMX;
                const dy = e.clientY - startMY;

                if (!dragging) {
                    if (Math.abs(dx) < 6 && Math.abs(dy) < 6) return;
                    // Threshold terlewati → mulai drag
                    dragging               = true;
                    didDrag                = true;
                    img.style.transform    = 'scale(0.92)';
                    // Cursor: grabbing di seluruh halaman
                    document.body.style.cursor = 'grabbing';
                    btn.style.cursor           = 'grabbing';
                }

                // Hitung posisi baru
                const bW = btn.offsetWidth  || 80;
                const bH = btn.offsetHeight || 80;
                let newL = Math.max(8, Math.min(window.innerWidth  - bW - 8, startBL + dx));
                let newT = Math.max(8, Math.min(window.innerHeight - bH - 8, startBT + dy));

                btn.style.left = newL + 'px';
                btn.style.top  = newT + 'px';

                // Reposisi modal kalau sedang terbuka
                if (this.open) this._posModal();
            });

            const finishDrag = (e) => {
                if (e.pointerId !== activePointer) return;
                activePointer = null;
                // Reset cursor & transform
                document.body.style.cursor = '';
                btn.style.cursor           = 'pointer';
                img.style.transform        = '';

                if (dragging) {
                    dragging = false;
                    // Simpan posisi
                    try {
                        localStorage.setItem('nubi_pos', JSON.stringify({
                            left: parseFloat(btn.style.left) || 0,
                            top:  parseFloat(btn.style.top)  || 0,
                        }));
                    } catch (_) {}
                }
                // Klik ditangani oleh 'click' event di atas, bukan di sini
            };
            btn.addEventListener('pointerup', finishDrag);
            btn.addEventListener('pointercancel', finishDrag);
        },

        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    this._posModal();
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

        // Hitung posisi modal agar tidak keluar viewport
        _posModal() {
            const btn   = document.getElementById('nubi-btn');
            const modal = document.getElementById('nubi-modal');
            if (!btn || !modal) return;

            const br  = btn.getBoundingClientRect();
            const vW  = window.innerWidth;
            const vH  = window.innerHeight;
            const gap = 10;
            const pad = 8;
            const mW  = Math.min(360, vW - pad * 2);
            modal.style.width = mW + 'px';
            const mH  = modal.getBoundingClientRect().height || Math.min(520, vH - pad * 2);

            // Horizontal: rata kanan dengan robot, geser kalau kepotong
            let left = br.right - mW;
            if (left < pad)          left = pad;
            if (left + mW > vW - pad) left = vW - mW - pad;

            // Vertikal: tampil di atas robot
            let top = br.top - mH - gap;
            if (top < pad) {
                // Tidak muat di atas → tampil di bawah
                top = br.bottom + gap;
            }
            if (top + mH > vH - pad) top = Math.max(pad, vH - mH - pad);

            modal.style.left = left + 'px';
            modal.style.top  = top  + 'px';
        },

        async send() {
            const msg = this.input.trim();
            if (!msg || this.typing) return;
            const history = this.messages
                .filter(message => ['user', 'assistant'].includes(message.role) && !message.failed)
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

                const res  = await fetch(this.chatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: msg,
                        history,
                    }),
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

                this.messages.push({
                    role: 'assistant',
                    content: data.reply,
                });
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
            try { sessionStorage.setItem('nubi_chat', JSON.stringify(this.messages.slice(-30))); } catch (_) {}
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
    #nubi-messages::-webkit-scrollbar { width: 4px; }
    #nubi-messages::-webkit-scrollbar-track { background: transparent; }
    #nubi-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .dark #nubi-messages::-webkit-scrollbar-thumb { background: #334155; }
    .nubi-chip {
        font-size: 11px; padding: 3px 10px; border-radius: 9999px;
        background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569;
        cursor: pointer; transition: background 0.15s;
    }
    .nubi-chip:hover { background: #e2e8f0; }
    .dark .nubi-chip { background: #1e293b; border-color: #334155; color: #94a3b8; }
    .dark .nubi-chip:hover { background: #334155; }
    #nubi-ai-widget button:focus-visible,
    #nubi-ai-widget textarea:focus-visible {
        outline: 2px solid #0f172a;
        outline-offset: 2px;
        box-shadow: 0 0 0 5px #fff;
    }
    @@media (max-width: 400px) {
        #nubi-modal { width: calc(100vw - 24px) !important; }
    }
</style>
