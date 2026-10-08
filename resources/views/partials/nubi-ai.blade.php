{{--
    Leo AI Chat Widget
    - Klik robot         → buka chat (popup)
    - Tahan + drag       → pindah posisi robot
    - Klik kanan robot   → context menu → Sembunyikan Robot
    - Tombol mata (pojok kanan bawah) → tampilkan kembali
    - ESC                → tutup chat
--}}

<div id="leo-ai-widget"
     data-chat-url="{{ route('nubi-ai.chat') }}"
     data-user-photo="{{ auth()->user()->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff&size=32' }}"
     data-user-name="{{ auth()->user()->name }}"
     x-data="leoAI()"
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
    <div id="leo-modal"
         x-show="open && !hidden"
         x-cloak
         role="dialog"
         aria-modal="true"
         aria-label="Percakapan dengan Leo AI"
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
                <img src="https://static.teamily.ai/sites/a9e19282-dc31-4d2f-aea8-ddf296a2b893/documents/lion_mascot_animation/lion_mascot_blink_loop.gif" class="w-8 h-8 object-contain">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white leading-tight">Leo AI</p>
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
        <div id="leo-messages"
             x-ref="messages"
             style="flex:1; overflow-y:auto; padding:16px; min-height:0; max-height:340px; scroll-behavior:smooth;"
             class="space-y-3">

            <template x-if="messages.length === 0">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img src="https://static.teamily.ai/sites/a9e19282-dc31-4d2f-aea8-ddf296a2b893/documents/lion_mascot_animation/lion_mascot_blink_loop.gif" class="w-6 h-6 object-contain">
                    </div>
                    <div class="bg-slate-100 dark:bg-navy-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]">
                        <p class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed">
                            Halo! Saya <strong>Leo AI</strong> 👋<br>
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
                            <img src="https://static.teamily.ai/sites/a9e19282-dc31-4d2f-aea8-ddf296a2b893/documents/lion_mascot_animation/lion_mascot_blink_loop.gif" class="w-6 h-6 object-contain">
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
                        <img src="https://static.teamily.ai/sites/a9e19282-dc31-4d2f-aea8-ddf296a2b893/documents/lion_mascot_animation/lion_mascot_blink_loop.gif" class="w-6 h-6 object-contain">
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
                <button type="button" x-on:click="suggest('Berapa guru yang hadir hari ini?')" class="leo-chip">Kehadiran hari ini</button>
                <button type="button" x-on:click="suggest('Siapa saja guru yang belum presensi hari ini?')" class="leo-chip">Belum presensi</button>
                <button type="button" x-on:click="suggest('Ada berapa pengajuan izin yang belum disetujui?')" class="leo-chip">Izin pending</button>
                <button type="button" x-on:click="suggest('Bagaimana cara menyetujui pengajuan izin guru?')" class="leo-chip">Setujui izin</button>
                <button type="button" x-on:click="suggest('Bagaimana cara export laporan presensi?')" class="leo-chip">Export laporan</button>
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
            <p class="text-[9px] text-slate-400 mt-1.5 text-center">Leo AI · Dibuat oleh Vexalyn Dev</p>
        </div>
    </div>

    {{-- ── Robot Button (draggable, berdiri di layar) ── --}}
    <button id="leo-btn"
            type="button"
            x-show="!hidden"
            x-cloak
            aria-label="Buka percakapan Leo AI"
            aria-controls="leo-modal"
            :aria-expanded="open"
            oncontextmenu="leoContextMenu(event)"
            style="position:fixed; bottom:0; right:32px; z-index:9991; width:110px; height:130px; padding:0; border:0; background:transparent; touch-action:none; user-select:none; cursor:pointer; display:flex; align-items:flex-end; justify-content:center;">

        {{-- Ground shadow --}}
        <span class="leo-shadow" aria-hidden="true"></span>

        {{-- Robot image --}}
        <img id="leo-img"
             src="https://static.teamily.ai/sites/a9e19282-dc31-4d2f-aea8-ddf296a2b893/documents/lion_mascot_animation/lion_mascot_blink_loop.gif"
             alt="Leo AI"
             draggable="false"
             class="leo-robot-img select-none"
             style="width:110px; height:120px; object-fit:contain; object-position:bottom;">
    </button>

</div>

{{-- ── Leo Context Menu (klik kanan robot) ── --}}
<div id="leo-context-menu"
     style="display:none; position:fixed; z-index:99999; min-width:180px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 8px 24px rgba(15,23,42,0.15); overflow:hidden;"
     class="dark:!bg-slate-800 dark:!border-slate-700">
    <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700">
        <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Leo AI</p>
    </div>
    <button onclick="leoHideWidget()"
            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        Sembunyikan Robot
    </button>
</div>

{{-- ── Tombol tampilkan kembali (saat robot hidden) ── --}}
<button id="leo-show-btn"
        title="Tampilkan Leo AI"
        style="display:none; position:fixed; bottom:0; right:32px; z-index:99998; width:80px; height:80px; border:none; background:transparent; cursor:pointer; padding:0; touch-action:none; user-select:none;">
    <img src="{{ asset('mascot/floating-leo.jpg') }}"
         alt="Tampilkan Leo AI"
         draggable="false"
         style="width:80px; height:80px; object-fit:contain; filter:drop-shadow(0 4px 12px rgba(15,23,42,0.25)); pointer-events:none; transition:transform 0.2s ease;">
</button>

<script>
function leoAI() {
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
            const w = document.getElementById('leo-ai-widget');
            this.chatUrl   = w?.dataset.chatUrl   ?? '';
            this.userPhoto = w?.dataset.userPhoto ?? '';
            this.userName  = w?.dataset.userName  ?? '';

            // Restore chat history
            try {
                const saved = JSON.parse(sessionStorage.getItem('leo_chat') ?? '[]');
                if (Array.isArray(saved)) {
                    this.messages = saved
                        .filter(m => m && ['user','assistant'].includes(m.role) && typeof m.content === 'string')
                        .map(m => ({ role: m.role, content: m.content, failed: m.failed === true }))
                        .slice(-30);
                }
            } catch(_) {}

            this._initDrag();

            // ESC → tutup chat
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.open) {
                    e.preventDefault();
                    this.closeChat();
                }
            });
        },

        _initDrag() {
            const btn = document.getElementById('leo-btn');
            if (!btn) return;

            // Restore saved position
            try {
                const p = JSON.parse(localStorage.getItem('leo_pos') ?? 'null');
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
                        localStorage.setItem('leo_pos', JSON.stringify({
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
            // Klik robot = SELALU buka, tidak toggle tutup
            // Tutup hanya lewat tombol X, ESC, atau backdrop
            if (!this.open) {
                this.open = true;
                this.$nextTick(() => {
                    this._scrollBottom();
                    if (this.$refs.inp) this.$refs.inp.focus();
                });
            }
        },

        closeChat() {
            this.open = false;
            this.$nextTick(() => {
                if (!this.hidden) document.getElementById('leo-btn')?.focus();
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
                    throw new Error('Leo AI belum siap. Muat ulang halaman dan coba lagi.');
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
                        : 'Leo AI sedang tidak tersedia. Silakan coba lagi.'));
                }
                if (typeof data.reply !== 'string' || !data.reply.trim()) {
                    throw new Error(data.error || 'Leo AI mengirim jawaban kosong. Silakan coba lagi.');
                }

                this.messages.push({ role: 'assistant', content: data.reply });

            } catch (error) {
                this.messages.push({
                    role: 'assistant',
                    content: error instanceof TypeError
                        ? 'Koneksi ke Leo AI gagal. Periksa jaringan lalu coba lagi.'
                        : (error instanceof Error ? error.message : 'Leo AI gagal memproses pesan. Silakan coba lagi.'),
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
            try { sessionStorage.setItem('leo_chat', JSON.stringify(this.messages.slice(-30))); } catch(_) {}
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

// ── Context menu klik kanan robot ──
function leoContextMenu(e) {
    e.preventDefault();
    e.stopPropagation();
    const menu = document.getElementById('leo-context-menu');
    if (!menu) return;
    menu.style.display = 'block';
    let x = e.clientX, y = e.clientY;
    requestAnimationFrame(() => {
        const mW = menu.offsetWidth  || 180;
        const mH = menu.offsetHeight || 80;
        if (x + mW > window.innerWidth)  x = window.innerWidth  - mW - 8;
        if (y + mH > window.innerHeight) y = window.innerHeight - mH - 8;
        menu.style.left = x + 'px';
        menu.style.top  = y + 'px';
    });
}

function leoHideWidget() {
    document.getElementById('leo-context-menu').style.display = 'none';
    const widget = document.getElementById('leo-ai-widget');
    if (widget && widget._x_dataStack) {
        const data = widget._x_dataStack[0];
        if (data) { data.hidden = true; data.open = false; }
    } else {
        const btn = document.getElementById('leo-btn');
        const modal = document.getElementById('leo-modal');
        if (btn)   btn.style.display   = 'none';
        if (modal) modal.style.display = 'none';
    }
    const showBtn = document.getElementById('leo-show-btn');
    if (showBtn) {
        showBtn.style.display = 'block';
        _leoInitShowBtnDrag(showBtn);
    }
}

function leoShowWidget() {
    const widget = document.getElementById('leo-ai-widget');
    if (widget && widget._x_dataStack) {
        const data = widget._x_dataStack[0];
        if (data) data.hidden = false;
    } else {
        const btn = document.getElementById('leo-btn');
        if (btn) btn.style.display = '';
    }
    const showBtn = document.getElementById('leo-show-btn');
    if (showBtn) showBtn.style.display = 'none';
}

// Drag untuk floating show button
function _leoInitShowBtnDrag(btn) {
    if (btn._leoDragInit) return; // init sekali saja
    btn._leoDragInit = true;

    const img = btn.querySelector('img');
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
            document.body.style.cursor = 'grabbing';
            btn.style.cursor = 'grabbing';
            if (img) img.style.transform = 'scale(0.92) rotate(3deg)';
        }
        const bW = 80, bH = 80;
        btn.style.left = Math.max(0, Math.min(window.innerWidth  - bW, startBL + dx)) + 'px';
        btn.style.top  = Math.max(0, Math.min(window.innerHeight - bH, startBT + dy)) + 'px';
    });

    const finish = (e) => {
        if (e.pointerId !== activePointer) return;
        activePointer = null;
        document.body.style.cursor = '';
        btn.style.cursor = 'pointer';
        if (img) img.style.transform = '';
        if (dragging) {
            dragging = false;
            try {
                localStorage.setItem('leo_show_pos', JSON.stringify({
                    right:  window.innerWidth  - (parseFloat(btn.style.left) || 0) - 80,
                    bottom: window.innerHeight - (parseFloat(btn.style.top)  || 0) - 80,
                }));
            } catch(_) {}
        }
    };
    btn.addEventListener('pointerup', finish);
    btn.addEventListener('pointercancel', finish);

    // Click — hanya kalau bukan drag
    btn.addEventListener('click', (e) => {
        if (didDrag) { e.preventDefault(); e.stopImmediatePropagation(); didDrag = false; return; }
        leoShowWidget();
    }, true);

    // Restore posisi tersimpan
    try {
        const p = JSON.parse(localStorage.getItem('leo_show_pos') ?? 'null');
        if (p && typeof p.right === 'number' && typeof p.bottom === 'number') {
            const right  = Math.max(0, Math.min(window.innerWidth  - 80, p.right));
            const bottom = Math.max(0, Math.min(window.innerHeight - 80, p.bottom));
            btn.style.right  = right  + 'px';
            btn.style.bottom = bottom + 'px';
            btn.style.left   = 'auto';
            btn.style.top    = 'auto';
        }
    } catch(_) {}
}

// Tutup context menu saat klik di luar atau ESC
document.addEventListener('click', (e) => {
    const menu = document.getElementById('leo-context-menu');
    if (menu && !menu.contains(e.target)) menu.style.display = 'none';
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const menu = document.getElementById('leo-context-menu');
        if (menu) menu.style.display = 'none';
    }
});
</script>

<style>
    /* ── Scrollbar ── */
    #leo-messages::-webkit-scrollbar { width: 4px; }
    #leo-messages::-webkit-scrollbar-track { background: transparent; }
    #leo-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    .dark #leo-messages::-webkit-scrollbar-thumb { background: #334155; }

    /* ── Chip buttons ── */
    .leo-chip {
        font-size: 11px; padding: 3px 10px; border-radius: 9999px;
        background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569;
        cursor: pointer; transition: background 0.15s;
    }
    .leo-chip:hover { background: #e2e8f0; }
    .dark .leo-chip { background: #1e293b; border-color: #334155; color: #94a3b8; }
    .dark .leo-chip:hover { background: #334155; }

    /* ── Focus ── */
    #leo-ai-widget button:focus-visible,
    #leo-ai-widget textarea:focus-visible {
        outline: 2px solid #0f172a;
        outline-offset: 2px;
    }

    /* ── Robot — diam, no float animation ── */
    .leo-robot-img {
        filter: drop-shadow(0 10px 20px rgba(15,23,42,0.2));
        transition: transform 0.2s ease, filter 0.2s ease;
    }

    /* ── Hover / active ── */
    #leo-btn:hover .leo-robot-img {
        transform: scale(1.05);
        filter: drop-shadow(0 14px 28px rgba(15,23,42,0.28));
    }
    #leo-btn:active .leo-robot-img {
        transform: scale(0.95);
    }
    #leo-btn.is-dragging .leo-robot-img {
        transform: scale(0.92) rotate(3deg) !important;
        filter: drop-shadow(0 6px 12px rgba(15,23,42,0.3)) !important;
    }

    /* ── Ground shadow ── */
    .leo-shadow {
        position: absolute;
        bottom: 2px; left: 50%;
        transform: translateX(-50%);
        width: 64px; height: 10px;
        background: rgba(0,0,0,0.12);
        border-radius: 50%;
        filter: blur(5px);
        pointer-events: none;
    }

    /* ── Responsive ── */
    @@media (max-width: 480px) {
        #leo-modal {
            right: 8px !important;
            left: 8px !important;
            width: auto !important;
            bottom: 145px !important;
        }
        #leo-btn {
            right: 8px !important;
            width: 90px !important;
            height: 108px !important;
        }
        #leo-btn .leo-robot-img {
            width: 90px !important;
            height: 100px !important;
        }
    }
</style>
