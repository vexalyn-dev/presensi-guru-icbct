<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Bantuan - {{ $ticket->ticket_id ?? ('#' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT)) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #cbd5e1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        /* Garis putus-putus untuk area detail pertanyaan */
        .notebook-lines {
            background-image: repeating-linear-gradient(transparent, transparent 39px, #e5e7eb 39px, #e5e7eb 40px);
            background-attachment: local;
            line-height: 40px;
        }
        /* Efek miring di footer */
        .footer-slant {
            clip-path: polygon(0 0, 95% 0, 90% 100%, 0% 100%);
        }
    </style>
</head>
<body>

    <!-- Tombol Action untuk membuktikan bisa di-download jadi gambar -->
    <div class="mb-6 flex gap-4 no-print" data-html2canvas-ignore="true">
        <button onclick="downloadCard()" class="bg-[#112a64] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-900 transition-colors shadow-lg flex items-center gap-2">
            <i class="fa-solid fa-download"></i> Download sebagai PNG
        </button>
        <button onclick="window.close()" class="bg-white text-gray-700 px-6 py-2 rounded-xl font-bold hover:bg-gray-50 transition-colors shadow-lg flex items-center gap-2">
            <i class="fa-solid fa-times"></i> Tutup
        </button>
    </div>

    <!-- Container Utama yang akan dirubah jadi gambar -->
    <div id="ticket-card" class="w-full max-w-[1100px] bg-white rounded-3xl shadow-2xl border-4 border-slate-900/10 overflow-hidden relative">
        
        <!-- HEADER KARTU -->
        <div class="flex justify-between items-center px-10 pt-10 pb-6">
            <!-- Kiri: Logo & Judul -->
            <div class="flex items-center gap-6">
                <!-- TEMPAT LOGO (Ambil dari DB AppSettings) -->
                @php
                    $appSettings = \App\Models\Setting::first();
                @endphp
                <div class="w-24 h-24 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-200 p-2 shadow-sm">
                    @if($appSettings && $appSettings->app_logo)
                        <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo" class="w-full h-full object-contain">
                    @else
                        <span class="text-xs text-gray-400 text-center font-medium">Logo<br>Sekolah</span>
                    @endif
                </div>
                <!-- Teks Judul -->
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-10 bg-blue-900 rounded-full"></div>
                        <h1 class="text-[32px] font-extrabold text-[#112a64] tracking-tight">PERTANYAAN PUSAT BANTUAN</h1>
                    </div>
                    <p class="text-gray-500 text-lg ml-4 mt-1 font-medium">Pusat Bantuan • Sistem Informasi ICB Cinta Teknika</p>
                </div>
            </div>

            <!-- Kanan: ID Tiket -->
            <div class="flex items-stretch border-2 border-[#112a64] rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-[#112a64] text-white px-5 flex items-center justify-center">
                    <i class="fa-solid fa-ticket-alt text-3xl transform -rotate-45"></i>
                </div>
                <div class="bg-white px-6 py-2 flex flex-col justify-center">
                    <span class="text-gray-500 text-sm font-bold uppercase tracking-wider">ID TIKET</span>
                    <span class="text-[#112a64] font-extrabold text-2xl leading-none">{{ $ticket->ticket_id ?? ('#' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT)) }}</span>
                </div>
            </div>
        </div>

        <hr class="border-gray-200 mx-10 mb-8">

        <!-- BODY / ISI TIKET -->
        <div class="grid grid-cols-12 gap-8 px-10 pb-24">
            
            <!-- Kolom Kiri (Sidebar Info) -->
            <div class="col-span-4 flex flex-col space-y-6">
                
                <!-- Pelapor -->
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#f0f4f8] flex items-center justify-center text-[#112a64] flex-shrink-0">
                        <i class="fa-regular fa-user text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#112a64] font-extrabold text-sm mb-1 uppercase">PELAPOR</p>
                        <p class="text-gray-900 font-bold text-lg leading-none">{{ $ticket->user->name ?? 'Pengguna' }}</p>
                        <div class="border-b-2 border-dashed border-gray-300 my-2"></div>
                        @php
                            $roleName = match($ticket->user->role ?? '') {
                                'admin', 'operator' => 'Operator',
                                'guru_piket'        => 'Guru Piket',
                                'guru'              => 'Guru',
                                default             => ucfirst($ticket->user->role ?? 'Pengguna'),
                            };
                        @endphp
                        <p class="text-gray-500 font-medium">Role: <span class="text-gray-900 font-bold">{{ $roleName }}</span></p>
                    </div>
                </div>

                <!-- Subjek -->
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#f0f4f8] flex items-center justify-center text-[#112a64] flex-shrink-0">
                        <i class="fa-solid fa-graduation-cap text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#112a64] font-extrabold text-sm mb-1 uppercase">SUBJEK</p>
                        <p class="text-gray-900 font-bold text-base">{{ $ticket->title }}</p>
                        <div class="border-b-2 border-dashed border-gray-300 mt-3"></div>
                    </div>
                </div>

                <!-- Prioritas -->
                @php
                    $priorityColor = match($ticket->priority) {
                        'critical' => 'bg-[#ef4444]', // Merah
                        'high'     => 'bg-[#f97316]', // Orange
                        'medium'   => 'bg-[#eab308]', // Kuning
                        default    => 'bg-[#22c55e]', // Hijau
                    };
                    $priorityLabel = \App\Models\SupportTicket::priorityLabels()[$ticket->priority]['label'] ?? strtoupper($ticket->priority);
                @endphp
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#f0f4f8] flex items-center justify-center text-[#112a64] flex-shrink-0">
                        <i class="fa-regular fa-flag text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#112a64] font-extrabold text-sm mb-2 uppercase">PRIORITAS</p>
                        <span class="{{ $priorityColor }} text-white px-5 py-1.5 rounded-full text-sm font-bold shadow-sm uppercase">{{ $priorityLabel }}</span>
                        <div class="border-b-2 border-dashed border-gray-300 mt-4"></div>
                    </div>
                </div>

                <!-- Waktu Dibuat -->
                @php
                    $createdAt = $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->setTimezone('Asia/Jakarta') : now()->setTimezone('Asia/Jakarta');
                    $timeFormatted = $createdAt->locale('id')->isoFormat('D MMMM YYYY • HH:mm') . ' WIB';
                @endphp
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#f0f4f8] flex items-center justify-center text-[#112a64] flex-shrink-0">
                        <i class="fa-regular fa-calendar text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[#112a64] font-extrabold text-sm mb-1 uppercase">WAKTU DIBUAT</p>
                        <p class="text-gray-900 font-bold text-base">{{ $timeFormatted }}</p>
                        <div class="border-b-2 border-dashed border-gray-300 mt-3"></div>
                    </div>
                </div>

                <!-- Status -->
                @php
                    $statusLabel = \App\Models\SupportTicket::statusLabels()[$ticket->status]['label'] ?? ucfirst($ticket->status);
                @endphp
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#f0f4f8] flex items-center justify-center text-[#112a64] flex-shrink-0">
                        <i class="fa-regular fa-clock text-2xl"></i>
                    </div>
                    <div class="flex-1 pt-1">
                        <p class="text-[#112a64] font-extrabold text-sm mb-2 uppercase">STATUS</p>
                        <span class="bg-[#ffd166] text-gray-900 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm flex items-center inline-flex gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-600"></span>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan (Detail Pertanyaan) -->
            <div class="col-span-8 flex flex-col h-full">
                <div class="border-2 border-[#f0f4f8] rounded-3xl h-full flex flex-col bg-white overflow-hidden shadow-sm">
                    <!-- Judul Box -->
                    <div class="flex items-center gap-3 px-6 py-5 bg-white">
                        <i class="fa-regular fa-comment-dots text-3xl text-[#112a64]"></i>
                        <h2 class="text-xl font-bold text-[#112a64]">PERTANYAAN / DETAIL</h2>
                    </div>
                    <!-- Area Teks Bergaris -->
                    <div class="flex-1 px-8 pt-2 pb-8 bg-white/50 border-t border-gray-100 rounded-b-3xl">
                        <div class="notebook-lines h-full min-h-[350px] w-full relative pt-1">
                            <p class="text-gray-900 font-semibold text-[17px] leading-[40px] whitespace-pre-wrap">{{ $ticket->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- FOOTER BAWAH -->
        <div class="absolute bottom-0 w-full h-[60px] flex items-center z-10 bg-white">
            <hr class="absolute top-0 w-full border-gray-200">
            <!-- Bagian Kiri Biru Gelap -->
            <div class="bg-[#112a64] h-full w-[45%] flex items-center pl-8 footer-slant text-white z-20 shadow-lg relative">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-graduation-cap text-3xl"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">Pusat Bantuan</p>
                        <p class="text-xs text-gray-300">Sistem Informasi ICB Cinta Teknika</p>
                    </div>
                </div>
            </div>
            
            <!-- Ornamen Garis Miring Biru -->
            <div class="bg-[#244388] h-full w-[8%] -ml-12 footer-slant z-10"></div>
            <div class="bg-[#3a5ba8] h-full w-[4%] -ml-4 footer-slant z-0"></div>

            <!-- Bagian Kanan Putih -->
            <div class="flex-1 flex justify-end items-center pr-8 gap-6 h-full text-sm font-bold text-[#112a64]">
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-clock text-lg"></i>
                    <span>{{ $timeFormatted }}</span>
                </div>
                <div class="h-6 w-px bg-gray-300"></div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#ffd166]"></span>
                    <span>{{ $statusLabel }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Script HTML2Canvas -->
    <script>
        function downloadCard() {
            const card = document.getElementById('ticket-card');
            
            // Konfigurasi html2canvas agar font & shadow terbaca maksimal
            html2canvas(card, {
                scale: 2, // Kualitas HD
                useCORS: true, // Izinkan memuat gambar dari domain (logo)
                backgroundColor: null, // Transparan jika ada border radius
            }).then(canvas => {
                // Konversi canvas jadi Data URL
                const imgData = canvas.toDataURL('image/png');
                
                // Buat tag <a> secara dinamis
                const link = document.createElement('a');
                link.download = 'Tiket-{{ $ticket->ticket_id ?? "Preview" }}.png';
                link.href = imgData;
                link.click();
            });
        }
    </script>
</body>
</html>
