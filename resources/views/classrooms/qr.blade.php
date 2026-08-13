@extends('layouts.app')

@section('page-title', 'QR Code - ' . $classroom->name)

@section('content')
    <div class="fade-in min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 py-8">

        <div class="max-w-xl mx-auto px-4">
            <!-- Back Button & Download Button Container -->
            <div class="mb-6 flex items-center justify-between gap-4">
                <a href="{{ route('classrooms.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>

                <button onclick="downloadCardAsImage()" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-navy-800 hover:bg-navy-900 dark:bg-gold-400 dark:hover:bg-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Download Card QR</span>
                </button>
            </div>

            <!-- Main Card to Capture -->
            <div id="qr-card-to-download" class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                <!-- Header with Gradient -->
                <div class="relative bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-navy-900 dark:via-navy-950 dark:to-slate-900 px-8 py-10 text-center">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 left-0 w-full h-full opacity-10">
                        <div class="absolute top-4 left-4 w-20 h-20 border-2 border-white rounded-full"></div>
                        <div class="absolute bottom-4 right-4 w-32 h-32 border-2 border-white rounded-full"></div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 border border-white rounded-full"></div>
                    </div>

                    <div class="relative z-10">
                        <!-- Icon -->
                        <div class="w-20 h-20 mx-auto mb-4 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border-2 border-white/30 shadow-lg">
                            <i data-lucide="school" class="w-10 h-10 text-white"></i>
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $classroom->name }}</h1>
                        <p class="text-sm text-white/80 font-medium tracking-wide">Kode: {{ $classroom->code }}</p>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="p-8 sm:p-10">
                    <div class="max-w-md mx-auto">
                        <!-- QR Code Container -->
                        <div class="bg-white rounded-2xl p-8 shadow-xl border-4 border-slate-100 dark:border-slate-700 relative">
                            <!-- Corner Decorations -->
                            <div class="absolute top-2 left-2 w-8 h-8 border-t-4 border-l-4 border-navy-800 dark:border-navy-900 rounded-tl-lg"></div>
                            <div class="absolute top-2 right-2 w-8 h-8 border-t-4 border-r-4 border-navy-800 dark:border-navy-900 rounded-tr-lg"></div>
                            <div class="absolute bottom-2 left-2 w-8 h-8 border-b-4 border-l-4 border-navy-800 dark:border-navy-900 rounded-bl-lg"></div>
                            <div class="absolute bottom-2 right-2 w-8 h-8 border-b-4 border-r-4 border-navy-800 dark:border-navy-900 rounded-br-lg"></div>

                            <!-- QR Code -->
                            <div id="qr-code-container" class="flex items-center justify-center">
                                {!! QrCode::size(300)->generate($classroom->qr_data) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer branding on Card -->
                <div class="py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/50 text-center">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 tracking-wider uppercase">
                        Sistem Presensi Kelas • {{ config('app.name', 'ICB CT') }}
                    </p>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="mt-6 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    QR Code ini digunakan untuk presensi kelas. Pastikan QR Code terlihat jelas dan tidak rusak.
                </p>
            </div>
        </div>
    </div>

    <!-- Include html2canvas for capturing full card -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        async function downloadCardAsImage() {
            const cardElement = document.getElementById('qr-card-to-download');
            if (!cardElement) return;

            try {
                const canvas = await html2canvas(cardElement, {
                    scale: 3, // High quality
                    useCORS: true,
                    backgroundColor: null,
                    logging: false
                });

                const link = document.createElement('a');
                link.download = 'QR-Card-{{ $classroom->code }}.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (err) {
                console.error('Download card error:', err);
                alert('Gagal mendownload card QR. Silakan coba lagi.');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #qr-code-container svg {
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection