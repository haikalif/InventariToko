@extends('main.index')

@section('title', 'Riwayat Penjualan')

@section('content')
<div class="space-y-5">

    <!-- Header Halaman & Aksi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-two/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-6 rounded-full bg-three inline-block"></span>
                <h1 class="text-xl font-bold text-four tracking-tight">Riwayat Penjualan</h1>
            </div>
            <p class="text-xs text-four/60 mt-1 pl-4">
                Pantau daftar transaksi penjualan, kelola transaksi pending, dan lihat rincian item terjual.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="/sales/create"
               class="group flex items-center gap-2 px-4 py-2 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:scale-95 transition-all duration-200 shadow-md">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Transaksi Kasir Baru</span>
            </a>
        </div>
    </div>

    <!-- Kartu Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Total Transaksi</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <p id="statTotalSales" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Selesai (Halaman)</span>
                <span class="p-2 rounded-lg bg-one text-four transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statCompletedSales" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Pending (Halaman)</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statPendingSales" class="text-2xl font-bold text-three mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Omzet Halaman Ini</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statPageRevenue" class="text-xl font-bold text-four mt-2 truncate">Rp 0</p>
        </div>
    </div>

    <!-- Bar Filter & Pencarian -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-two/40 p-3 rounded-lg border border-two/80">
        <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Input Pencarian -->
            <div class="group flex items-center flex-1 max-w-md bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200 shadow-inner">
                <svg class="w-4 h-4 text-three mr-2.5 shrink-0 transition-transform duration-300 group-focus-within:scale-110 group-focus-within:rotate-12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 10.5a7.5 7.5 0 0013.15 6.15z" />
                </svg>
                <input id="filterSearchSale"
                       type="text"
                       autocomplete="off"
                       placeholder="Filter nomor transaksi, kasir, atau metode..."
                       class="w-full bg-transparent text-sm text-four placeholder-four/50 focus:outline-none">
            </div>

            <!-- Custom Dropdown Filter Status -->
            <div id="dropdownFilterSaleStatus" class="relative w-full sm:w-44 select-none">
                <input type="hidden" id="filterStatusSale" value="">
                <button type="button"
                        data-dropdown-toggle
                        class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                    <span data-dropdown-label class="truncate">Semua Status</span>
                    <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div data-dropdown-menu
                     class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                    @foreach ([
                        '' => 'Semua Status',
                        'pending' => 'Pending',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ] as $val => $label)
                        <button type="button"
                                data-value="{{ $val }}"
                                data-label="{{ $label }}"
                                class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                                <span class="truncate">{{ $label }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Custom Dropdown Filter Metode Pembayaran -->
            <div id="dropdownFilterSaleMethod" class="relative w-full sm:w-48 select-none">
                <input type="hidden" id="filterMethodSale" value="">
                <button type="button"
                        data-dropdown-toggle
                        class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                    <span data-dropdown-label class="truncate">Semua Metode</span>
                    <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div data-dropdown-menu
                     class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                    @foreach ([
                        '' => 'Semua Metode',
                        'Tunai' => 'Tunai',
                        'QRIS' => 'QRIS',
                        'Transfer Bank' => 'Transfer Bank',
                        'Kartu Debit' => 'Kartu Debit',
                        'Kartu Kredit' => 'Kartu Kredit',
                    ] as $val => $label)
                        <button type="button"
                                data-value="{{ $val }}"
                                data-label="{{ $label }}"
                                class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                                <span class="truncate">{{ $label }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tombol Segarkan -->
        <button id="btnRefreshSales"
                type="button"
                title="Muat Ulang Data"
                class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:bg-two hover:border-three/70 text-sm text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer shadow-sm">
            <svg class="w-4 h-4 text-three transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Segarkan</span>
        </button>
    </div>

    <!-- Tabel Data Penjualan -->
    <div class="rounded-lg border border-two/80 bg-one overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-two/70 border-b border-two text-xs font-semibold uppercase tracking-wider text-four/60">
                        <th class="px-4 py-3.5">No. Transaksi & Tanggal</th>
                        <th class="px-4 py-3.5">Kasir</th>
                        <th class="px-4 py-3.5">Metode</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Item</th>
                        <th class="px-4 py-3.5">Total</th>
                        <th class="px-4 py-3.5 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody id="saleTableBody" class="text-sm divide-y divide-two/60">
                    <!-- Diisi secara dinamis oleh public/js/sales.js -->
                </tbody>
            </table>
        </div>

        <!-- Navigasi Halaman (Pagination) -->
        <div class="px-4 py-3 bg-two/40 border-t border-two/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-four/70">
            <span id="paginationInfo">Memuat informasi halaman...</span>
            <div class="flex items-center gap-2">
                <button id="btnPrevPage"
                        type="button"
                        disabled
                        class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-two/80 border border-four/10 text-four hover:bg-two hover:border-three/70 disabled:opacity-40 disabled:pointer-events-none active:scale-95 transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-three transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Sebelumnya</span>
                </button>
                <button id="btnNextPage"
                        type="button"
                        disabled
                        class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-two/80 border border-four/10 text-four hover:bg-two hover:border-three/70 disabled:opacity-40 disabled:pointer-events-none active:scale-95 transition-all duration-200 cursor-pointer">
                    <span>Berikutnya</span>
                    <svg class="w-3.5 h-3.5 text-three transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Modal Detail & Kelola Transaksi Penjualan -->
<div id="saleDetailModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-sale-detail-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="saleDetailCard"
         class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10">
        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two sticky top-0 z-30">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 class="text-base font-bold text-four">Rincian Transaksi Penjualan</h2>
            </div>
            <button type="button" class="btn-close-sale-detail-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="saleDetailContent" class="p-6 space-y-5">
            <!-- Diisi secara dinamis oleh public/js/sales.js -->
        </div>

        <div class="px-6 py-3.5 bg-two/40 border-t border-two flex justify-end">
            <button type="button"
                    class="btn-close-sale-detail-modal px-4 py-2 rounded-lg bg-two border border-four/10 text-four text-sm font-medium hover:bg-three hover:border-three active:scale-95 transition-all duration-200 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Container Toast Notifikasi -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/sales.js') }}"></script>
@endpush
