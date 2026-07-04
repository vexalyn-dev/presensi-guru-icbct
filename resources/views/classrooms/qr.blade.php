@extends('layouts.app')

@section('page-title', 'QR Code - ' . $classroom->name)

@section('content')
    <div class="fade-in max-w-2xl mx-auto space-y-6">

        <!-- Back Button -->
        <a href="{{ route('classrooms.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-navy-800 dark:hover:text-gold-400 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>

        <!-- QR Card -->
        <div class="card p-8 text-center">
            <div class="mb-6">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg mb-4">
                    <i data-lucide="school" class="w-10 h-10 text-white dark:text-navy-900"></i>
                </div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $classroom->name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kode: {{ $classroom->code }}</p>
                @if($classroom->building)
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $classroom->building }}
                        @if($classroom->floor)• Lantai {{ $classroom->floor }}@endif</p>
                @endif
            </div>

            <!-- QR Code -->
            <div class="bg-white p-6 rounded-2xl inline-block shadow-xl mb-6">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate($classroom->qr_data) !!}
            </div>

            <div class="space-y-3">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Cetak dan tempel QR Code ini di pintu kelas
                </p>
                <button onclick="window.print()" class="btn-primary inline-flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak QR Code
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .card,
            .card * {
                visibility: visible;
            }

            .card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            button {
                display: none !important;
            }
        }
    </style>
@endsection