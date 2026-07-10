@extends('layouts.app')

@section('page-title', 'QR Code - ' . $classroom->name)

@section('content')
    <div class="fade-in min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 py-8">

        <div class="max-w-4xl mx-auto px-4">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('classrooms.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Data Kelas</span>
                </a>
            </div>

            <!-- Main Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                <!-- Header with Gradient -->
                <div class="relative bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:via-gold-500 dark:to-gold-600 px-8 py-10 text-center">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 left-0 w-full h-full opacity-10">
                        <div class="absolute top-4 left-4 w-20 h-20 border-2 border-white rounded-full"></div>
                        <div class="absolute bottom-4 right-4 w-32 h-32 border-2 border-white rounded-full"></div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 border border-white rounded-full"></div>
                    </div>

                    <div class="relative z-10">
                        <!-- Icon -->
                        <div class="w-20 h-20 mx-auto mb-4 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border-2 border-white/30 shadow-lg">
                            <i data-lucide="school" class="w-10 h-10 text-white dark:text-navy-900"></i>
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-white dark:text-navy-900 mb-2">{{ $classroom->name }}</h1>
                        <p class="text-sm text-white/80 dark:text-navy-900/80 font-medium">Kode: {{ $classroom->code }}</p>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="p-10">
                    <div class="max-w-md mx-auto">
                        <!-- QR Code Container -->
                        <div class="bg-white rounded-2xl p-8 shadow-xl border-4 border-slate-100 dark:border-slate-700 relative">
                            <!-- Corner Decorations -->
                            <div class="absolute top-2 left-2 w-8 h-8 border-t-4 border-l-4 border-navy-800 dark:border-gold-400 rounded-tl-lg"></div>
                            <div class="absolute top-2 right-2 w-8 h-8 border-t-4 border-r-4 border-navy-800 dark:border-gold-400 rounded-tr-lg"></div>
                            <div class="absolute bottom-2 left-2 w-8 h-8 border-b-4 border-l-4 border-navy-800 dark:border-gold-400 rounded-bl-lg"></div>
                            <div class="absolute bottom-2 right-2 w-8 h-8 border-b-4 border-r-4 border-navy-800 dark:border-gold-400 rounded-br-lg"></div>

                            <!-- QR Code -->
                            <div id="qr-code-container" class="flex items-center justify-center">
                                {!! QrCode::size(300)->generate($classroom->qr_data) !!}
                            </div>
                        </div>

                        <!-- Info Text -->
                        <div class="mt-8 text-center">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                <p class="text-sm text-blue-700 dark:text-blue-300 font-medium">
                                    Download dan tempel QR Code ini di pintu kelas
                                </p>
                            </div>
                        </div>

                        <!-- Download Button -->
                        <div class="mt-6 flex justify-center">
                            <button onclick="downloadQRCode()" 
                                    class="group inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-base font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-1 active:translate-y-0">
                                <i data-lucide="download" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                <span>Download QR Code</span>
                            </button>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="calendar-clock" class="w-4 h-4 text-navy-800 dark:text-gold-400"></i>
                                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Status</span>
                                </div>
                                <p class="text-sm font-bold text-navy-800 dark:text-white">
                                    {{ $classroom->is_active ? 'Aktif' : 'Nonaktif' }}
                                </p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="calendar-range" class="w-4 h-4 text-navy-800 dark:text-gold-400"></i>
                                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Jadwal</span>
                                </div>
                                <p class="text-sm font-bold text-navy-800 dark:text-white">
                                    {{ $classroom->teachingSchedules()->count() }} Kelas
                                </p>
                            </div>
                        </div>
                    </div>
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

    <script>
        async function downloadQRCode() {
            const qrContainer = document.getElementById('qr-code-container');
            const svg = qrContainer.querySelector('svg');

            if (!svg) {
                alert('QR Code tidak ditemukan!');
                return;
            }

            // Convert SVG to Canvas
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const svgData = new XMLSerializer().serializeToString(svg);
            const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(svgBlob);

            const img = new Image();
            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                URL.revokeObjectURL(url);

                // Download as PNG
                const pngUrl = canvas.toDataURL('image/png');
                const downloadLink = document.createElement('a');
                downloadLink.href = pngUrl;
                downloadLink.download = 'QR-Code-{{ $classroom->code }}.png';
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            };
            img.src = url;
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

        /* QR Code Styling */
        #qr-code-container svg {
            max-width: 100%;
            height: auto;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #qr-code-container, #qr-code-container * {
                visibility: visible;
            }
            #qr-code-container {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
@endsection