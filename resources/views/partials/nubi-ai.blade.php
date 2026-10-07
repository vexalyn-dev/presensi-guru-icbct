{{--
    Nubi AI Chat Widget
    - Klik robot → buka/tutup chat
    - Tahan klik + drag → pindah posisi
    - Ctrl+Del → sembunyikan/tampilkan
--}}

<div id="nubi-ai-widget"
     data-chat-url="{{ route('nubi-ai.chat') }}"
     data-user-photo="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff&size=32' }}"
     data-user-name="{{ urlencode(auth()->user()->name) }}"
     x-data="nubiAI()"
     x-init="init()">

    {{-- ── Chat Modal ── --}}
    <div id="nubi-chat-modal"
         x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="nubi-modal bg-white dark:bg-navy-900 rounded-2xl shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col"
         style="width:360px; max-height:520px; position:fixed; z-index:9991;">

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

        {{-- Messages --}}
        <div id="nubi-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3" style="min-height:200px; max-height:310px; scroll-behavior:smooth;">

            <template x-if="messages.length === 0">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                        <img src="{{ asset('images/Nubi-AI.gif') }}" class="w-6 h-6 object-contain rounded-full">
                    </div>
                    <div class="bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]">
                        <p class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed">
                            Halo! Saya <strong>Nubi AI</strong> 👋<br>
                            Saya dibuat oleh <strong>Vexalyn Dev</strong> untuk membantu kamu menggunakan aplikasi <strong>Presensi Guru ICB CT</strong>.<br><br>
                            Ada yang bisa saya bantu? 😊
                        </p>
                    </div>
                </div>
            </template>

            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.role === 'user' ? 'flex items-end justify-end gap-2' : 'flex items-start gap-2.5'">
                    <template x-if="msg.role === 'assistant'">
                        <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                            <img src="{{ asset('images/Nubi-AI.gif') }}" class="w-6 h-6 object-contain rounded-full">
                        </div>
                    </template>
                    <div :class="msg.role === 'user'
                            ? 'bg-navy-800 text-white rounded-2xl rounded-br-sm px-3.5 py-2.5 max-w-[85%]'
                            : 'bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]'">
                        <p class="text-xs leading-relaxed"
                           :class="msg.role==='user' ? 'text-white' : 'text-slate-700 dark:text-slate-200'"
                           x-html="fmt(msg.content)"></p>
                    </div>
                    <template x-if="msg.role === 'user'">
                        <div class="w-7 h-7 rounded-full bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border-2 border-navy-700">
                            <img :src="userPhoto" class="w-7 h-7 object-cover"
                                 x-on:error="$el.src='https://ui-avatars.com/api/?name='+userName+'&background=0F172A&color=fff&size=32'">
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="typing">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                        <img src="{{ asset('images/Nubi-AI.gif') }}" class="w-6 h-6 object-contain rounded-full">
                    </div>
                    <div class="bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-4 py-3">
                        <div class="flex gap-1.5 items-center">
                            <span class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Suggestions --}}
        <template x-if="messages.length === 0 && !typing">
            <div class="px-4 pb-2 flex flex-wrap gap-1.5 flex-shrink-0">
                <button x-on:click="suggest('Berapa guru yang hadir hari ini?')" class="nubi-chip">📊 Kehadiran hari ini</button>
                <button x-on:click="suggest('Siapa saja guru yang belum presensi hari ini?')" class="nubi-chip">⏳ Belum presensi</button>
                <button x-on:click="suggest('Ada berapa pengajuan izin yang belum disetujui?')" class="nubi-chip">📝 Izin pending</button>
                <button x-on:click="suggest('Bagaimana cara menyetujui pengajuan izin guru?')" class="nubi-chip">✅ Approve izin</button>
                <button x-on:click="suggest('Bagaimana cara export laporan presensi?')" class="nubi-chip">📈 Export laporan</button>
            </div>
        </template>

        {{-- Input --}}
        <div class="px-3 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-navy-900/50 flex-shrink-0">
            <form x-on:submit.prevent="send()" class="flex gap-2 items-end">
                <textarea x-model="input"
                          x-on:keydown.enter.prevent="if(!$event.shiftKey) send()"
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

    {{-- ── Robot Button ── --}}
    <div id="nubi-btn"
         class="nubi-robot-btn"
         style="position:fixed; bottom:24px; right:24px; z-index:9990; width:80px; height:80px;">

        {{-- Pulse ring --}}
        <span x-show="!open"
              class="absolute inset-0 rounded-full bg-navy-800/10 animate-ping pointer-events-none"
              style="animation-duration:3s;"></span>

        {{-- Robot GIF --}}
        <img id="nubi-img"
             src="{{ asset('images/Nubi-AI.gif') }}"
             alt="Nubi AI"
             class="w-20 h-20 object-contain drop-shadow-lg select-none"
             draggable="false"
             style="transition: transform 0.15s ease;">

        {{-- Unread badge --}}
        <span x-show="unread > 0 && !open" x-cloak
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white dark:border-navy-900 rounded-full flex items-center justify-center pointer-events-none">
            <span class="text-[9px] text-white font-bold" x-text="unread > 9 ? '9+' : unread"></span>
        </span>
    </div>
</div>

<script>
(function () {
    // ── Tunggu Alpine init ──
    document.addEventListener('alpine:init', () => {
        Alpine.data('nubiAI', () => ({
            open:    false,
            typing:  false,
            input:   '',
            messages: [],
            unread:  0,
            chatUrl:   '',
            userPhoto: '',
            userName:  '',

            init() {
                const w = document.getElementById('nubi-ai-widget');
                this.chatUrl   = w?.dataset.chatUrl   ?? '';
                this.userPhoto = w?.dataset.userPhoto ?? '';
                this.userName  = w ? decodeURIComponent(w.dataset.userName ?? '') : '';

                // Restore history
                try {
                    const s = sessionStorage.getItem('nubi_chat');
                    if (s) this.messages = JSON.parse(s);
                } catch (_) {}

                // Setup drag + click di robot button
                this._setupRobot();

                // Ctrl+Del
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.key === 'Delete') {
                        e.preventDefault();
                        const btn = document.getElementById('nubi-btn');
                        const modal = document.getElementById('nubi-chat-modal');
                        if (!btn) return;
                        const hidden = btn.style.display === 'none';
                        btn.style.display    = hidden ? '' : 'none';
                        if (modal) modal.style.display = hidden ? '' : 'none';
                        if (!hidden) this.open = false;
                    }
                });
            },

            _setupRobot() {
                const btn = document.getElementById('nubi-btn');
                const img = document.getElementById('nubi-img');
                if (!btn || !img) return;

                let isDragging   = false;
                let didDrag      = false;
                let startMouseX  = 0;
                let startMouseY  = 0;
                let startBtnLeft = 0;
                let startBtnTop  = 0;

                // Posisi awal: bottom-right — convert ke left/top absolute
                const initPos = () => {
                    const r = btn.getBoundingClientRect();
                    btn.style.left   = r.left + 'px';
                    btn.style.top    = r.top  + 'px';
                    btn.style.right  = 'auto';
                    btn.style.bottom = 'auto';
                };

                // Restore posisi tersimpan
                try {
                    const p = JSON.parse(localStorage.getItem('nubi_pos') ?? 'null');
                    if (p) {
                        btn.style.left   = p.left + 'px';
                        btn.style.top    = p.top  + 'px';
                        btn.style.right  = 'auto';
                        btn.style.bottom = 'auto';
                    } else {
                        initPos();
                    }
                } catch (_) { initPos(); }

                btn.addEventListener('mousedown', (e) => {
                    if (e.button !== 0) return;

                    isDragging  = false;
                    didDrag     = false;
                    startMouseX = e.clientX;
                    startMouseY = e.clientY;

                    const r      = btn.getBoundingClientRect();
                    startBtnLeft = r.left;
                    startBtnTop  = r.top;

                    // Pastikan posisi pakai left/top
                    btn.style.left   = startBtnLeft + 'px';
                    btn.style.top    = startBtnTop  + 'px';
                    btn.style.right  = 'auto';
                    btn.style.bottom = 'auto';

                    // Cursor: grab saat tahan sebelum drag
                    btn.style.cursor = 'grab';

                    e.preventDefault(); // cegah text selection saat drag
                });

                document.addEventListener('mousemove', (e) => {
                    if (e.buttons !== 1 || startMouseX === 0) return;

                    const dx = e.clientX - startMouseX;
                    const dy = e.clientY - startMouseY;

                    // Threshold 6px sebelum drag aktif
                    if (!isDragging) {
                        if (Math.abs(dx) < 6 && Math.abs(dy) < 6) return;
                        isDragging = true;
                        didDrag    = true;
                        // Cursor: grabbing saat drag
                        document.body.style.cursor = 'grabbing';
                        btn.style.cursor           = 'grabbing';
                        img.style.transform        = 'scale(0.95)';
                    }

                    const bW = btn.offsetWidth  || 80;
                    const bH = btn.offsetHeight || 80;

                    let newLeft = startBtnLeft + dx;
                    let newTop  = startBtnTop  + dy;

                    // Clamp
                    newLeft = Math.max(8, Math.min(window.innerWidth  - bW - 8, newLeft));
                    newTop  = Math.max(8, Math.min(window.innerHeight - bH - 8, newTop));

                    btn.style.left = newLeft + 'px';
                    btn.style.top  = newTop  + 'px';

                    // Update posisi modal jika terbuka
                    if (this.open) this._positionModal();
                });

                document.addEventListener('mouseup', (e) => {
                    if (e.button !== 0 || startMouseX === 0) return;

                    // Reset cursor
                    document.body.style.cursor = '';
                    btn.style.cursor           = 'pointer';
                    img.style.transform        = '';

                    if (isDragging) {
                        // Selesai drag — simpan posisi
                        isDragging  = false;
                        startMouseX = 0;
                        try {
                            localStorage.setItem('nubi_pos', JSON.stringify({
                                left: parseFloat(btn.style.left) || 0,
                                top:  parseFloat(btn.style.top)  || 0,
                            }));
                        } catch (_) {}
                    } else if (!didDrag) {
                        // Klik biasa → toggle chat
                        startMouseX = 0;
                        this.toggleChat();
                    } else {
                        startMouseX = 0;
                    }
                });

                // Cursor pointer default di robot
                btn.style.cursor = 'pointer';
            },

            toggleChat() {
                this.open = !this.open;
                if (this.open) {
                    this.unread = 0;
                    this.$nextTick(() => {
                        this._positionModal();
                        this.scrollBottom();
                        if (this.$refs.inp) this.$refs.inp.focus();
                    });
                }
            },

            // Posisikan modal supaya tidak keluar layar
            _positionModal() {
                const btn   = document.getElementById('nubi-btn');
                const modal = document.getElementById('nubi-chat-modal');
                if (!btn || !modal) return;

                const br      = btn.getBoundingClientRect();
                const mH      = 520;
                const mW      = 360;
                const gap     = 8;
                const pad     = 8;
                const vw      = window.innerWidth;
                const vh      = window.innerHeight;

                // Posisi horizontal: ratakan ke kanan robot, geser ke kiri kalau kepotong
                let left = br.left + br.width - mW;
                if (left < pad) left = pad;
                if (left + mW > vw - pad) left = vw - mW - pad;

                // Posisi vertikal: default di atas robot
                let top = br.top - mH - gap;
                if (top < pad) {
                    // Tidak muat di atas → taruh di bawah
                    top = br.bottom + gap;
                }
                // Kalau di bawah juga kepotong, snap ke atas layar
                if (top + mH > vh - pad) top = pad;

                modal.style.left = left + 'px';
                modal.style.top  = top  + 'px';
            },

            async send() {
                const msg = this.input.trim();
                if (!msg || this.typing) return;
                this.input = '';
                if (this.$refs.inp) this.$refs.inp.style.height = 'auto';

                this.messages.push({ role: 'user', content: msg });
                this._save();
                this.scrollBottom();
                this.typing = true;

                try {
                    const res  = await fetch(this.chatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            message: msg,
                            history: this.messages.slice(-10).filter(m => m.role !== 'error'),
                        }),
                    });
                    const data = await res.json();
                    this.messages.push({ role: 'assistant', content: data.error ? '❌ ' + data.error : data.reply });
                    if (!this.open) this.unread++;
                } catch (_) {
                    this.messages.push({ role: 'assistant', content: '❌ Gagal terhubung ke Nubi AI.' });
                } finally {
                    this.typing = false;
                    this._save();
                    this.$nextTick(() => this.scrollBottom());
                }
            },

            suggest(t) { this.input = t; this.$nextTick(() => this.send()); },

            scrollBottom() {
                this.$nextTick(() => {
                    const c = document.getElementById('nubi-messages');
                    if (c) c.scrollTop = c.scrollHeight;
                });
            },

            _save() {
                try { sessionStorage.setItem('nubi_chat', JSON.stringify(this.messages.slice(-30))); } catch (_) {}
            },

            fmt(text) {
                if (!text) return '';
                let s = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                s = s.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                s = s.replace(/\*(.+?)\*/g, '<em>$1</em>');
                s = s.replace(/\n/g, '<br>');
                return s;
            },
        }));
    });
})();
</script>

<style>
    .nubi-modal { transform-origin: bottom right; }
    .nubi-chip {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 9999px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        transition: background 0.15s;
        cursor: pointer;
    }
    .nubi-chip:hover { background: #e2e8f0; }
    .dark .nubi-chip { background: #1e293b; border-color: #334155; color: #94a3b8; }
    .dark .nubi-chip:hover { background: #334155; }

    #nubi-messages::-webkit-scrollbar { width: 4px; }
    #nubi-messages::-webkit-scrollbar-track { background: transparent; }
    #nubi-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .dark #nubi-messages::-webkit-scrollbar-thumb { background: #334155; }

    @@media (max-width: 400px) {
        .nubi-modal { width: calc(100vw - 24px) !important; }
    }
</style>
