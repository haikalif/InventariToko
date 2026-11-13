@extends('main.index')

@section('title', 'Kasir / Penjualan')

@section('content')
<div class="flex flex-col gap-5 lg:h-full lg:overflow-hidden">

    <!-- Header Halaman -->
    <div class="shrink-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-two/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-6 rounded-full bg-three inline-block"></span>
                <h1 class="text-xl font-bold text-four tracking-tight">Kasir / Penjualan</h1>
            </div>
            <p class="text-xs text-four/60 mt-1 pl-4">
                Pilih produk atau pindai barcode/SKU, atur jumlah keranjang, dan selesaikan transaksi penjualan.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button id="btnRefreshPosProducts"
                    type="button"
                    title="Muat Ulang Katalog Produk"
                    class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:bg-two hover:border-three/70 text-sm text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer shadow-sm">
                <svg class="w-4 h-4 text-three transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Segarkan Produk</span>
            </button>

            <button id="btnResetPosCart"
                    type="button"
                    class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:bg-three hover:border-three text-sm text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer shadow-sm">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Kosongkan Keranjang</span>
            </button>
        </div>
    </div>

    <!-- Layout Utama Kasir (2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start lg:items-stretch lg:flex-1 lg:min-h-0">

        <!-- Kolom Kiri: Katalog & Pencarian Produk -->
        <div class="lg:col-span-7 flex flex-col gap-4 lg:h-full lg:min-h-0">
            <!-- Bar Pencarian & Filter Kategori -->
            <div class="shrink-0 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-two/40 p-3 rounded-lg border border-two/80">
                <div class="group flex items-center flex-1 bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200 shadow-inner">
                    <svg class="w-4 h-4 text-three mr-2.5 shrink-0 transition-transform duration-300 group-focus-within:scale-110 group-focus-within:rotate-12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 10.5a7.5 7.5 0 0013.15 6.15z" />
                    </svg>
                    <input id="searchPosProduct"
                           type="text"
                           autocomplete="off"
                           placeholder="Cari nama produk, SKU, atau scan barcode (Enter)..."
                           class="w-full bg-transparent text-sm text-four placeholder-four/50 focus:outline-none">
                </div>

                <!-- Custom Dropdown Filter Kategori -->
                <div id="dropdownPosCategory" class="relative w-full sm:w-52 select-none">
                    <input type="hidden" id="filterPosCategory" value="">
                    <button type="button"
                            data-dropdown-toggle
                            class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                        <span data-dropdown-label class="truncate">Semua Kategori</span>
                        <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="posCategoryMenu"
                         data-dropdown-menu
                         class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-60 z-40">
                        <button type="button"
                                data-value=""
                                data-label="Semua Kategori"
                                class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                                <span class="truncate">Semua Kategori</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grid Katalog Produk -->
            <div class="rounded-lg border border-two/80 bg-one p-3.5 shadow-md flex flex-col lg:flex-1 lg:min-h-0">
                <div class="shrink-0 flex items-center justify-between mb-3 px-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Katalog Produk Tersedia</span>
                    <span id="posProductCount" class="text-xs text-four/60">Memuat produk...</span>
                </div>

                <div id="posProductGrid"
                     class="flex flex-col gap-2.5 max-h-[62vh] lg:max-h-none lg:flex-1 lg:min-h-0 overflow-y-auto pr-1">
                    <!-- Diisi secara dinamis oleh public/js/salesCreate.js -->
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Keranjang & Pembayaran (Diam / Tidak Ikut Scroll Katalog) -->
        <div class="lg:col-span-5 flex flex-col gap-3.5 lg:h-full lg:overflow-y-auto pr-0.5">

            <!-- Detail Transaksi -->
            <div class="rounded-lg border border-two/80 bg-one p-4 shadow-md space-y-3.5">
                <div class="flex items-center justify-between border-b border-two pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-three"></span>
                        <h2 class="text-sm font-bold text-four">Informasi Transaksi</h2>
                    </div>
                    <button id="btnGenerateTrxNumber"
                            type="button"
                            title="Buat Nomor Transaksi Baru"
                            class="text-xs text-three hover:underline cursor-pointer">
                        Buat Nomor Baru
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <label for="nomor_transaksi" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                            No. Transaksi <span class="text-three">*</span>
                        </label>
                        <input type="text"
                               id="nomor_transaksi"
                               maxlength="25"
                               required
                               class="w-full px-3 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four font-mono text-xs focus:outline-none transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                            Tanggal <span class="text-three">*</span>
                        </label>
                        <div id="customDatePicker" class="relative select-none">
                            <input type="hidden" id="tanggal" required>
                            <button type="button"
                                    id="datePickerToggle"
                                    data-dropdown-toggle
                                    class="w-full flex items-center justify-between gap-2 bg-two px-3 py-2 rounded-lg border border-four/10 hover:border-three/70 text-xs text-four transition-all duration-200 cursor-pointer shadow-sm">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-4 h-4 text-three shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="datePickerLabel" class="font-medium truncate">Pilih Tanggal</span>
                                </div>
                                <svg data-dropdown-icon class="w-3.5 h-3.5 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div id="datePickerMenu"
                                 data-dropdown-menu
                                 class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute right-0 top-full w-68 bg-two border border-three rounded-b-lg rounded-tl-lg shadow-2xl p-3 z-50">
                                <!-- Header Bulan & Tahun -->
                                <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-four/10">
                                    <button type="button" id="datePickerPrevMonth"
                                            class="p-1.5 rounded-lg bg-one/60 border border-four/10 text-four/80 hover:text-four hover:border-three active:scale-95 transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <span id="datePickerMonthYear" class="text-xs font-bold text-four tracking-wide"></span>
                                    <button type="button" id="datePickerNextMonth"
                                            class="p-1.5 rounded-lg bg-one/60 border border-four/10 text-four/80 hover:text-four hover:border-three active:scale-95 transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Nama Hari -->
                                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-semibold uppercase text-four/50 mb-1.5">
                                    <span>Min</span>
                                    <span>Sen</span>
                                    <span>Sel</span>
                                    <span>Rab</span>
                                    <span>Kam</span>
                                    <span>Jum</span>
                                    <span>Sab</span>
                                </div>

                                <!-- Grid Tanggal -->
                                <div id="datePickerDays" class="grid grid-cols-7 gap-1 text-xs"></div>

                                <!-- Footer Hari Ini -->
                                <div class="mt-2.5 pt-2 border-t border-four/10 flex justify-between items-center">
                                    <span class="text-[11px] text-four/50">Pilih tanggal transaksi</span>
                                    <button type="button" id="datePickerTodayBtn"
                                            class="px-2.5 py-1 rounded-lg bg-one border border-four/10 hover:border-three text-[11px] font-semibold text-three hover:bg-three hover:text-four active:scale-95 transition-all cursor-pointer">
                                        Hari Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Dropdown Metode Pembayaran -->
                <div class="text-sm">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Metode Pembayaran
                    </label>
                    <div id="dropdownPaymentMethod" class="relative select-none">
                        <input type="hidden" id="metode_pembayaran" value="Tunai">
                        <button type="button"
                                data-dropdown-toggle
                                class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                            <span data-dropdown-label class="truncate">Tunai</span>
                            <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div data-dropdown-menu
                             class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                            @foreach (['Tunai', 'QRIS', 'Transfer Bank', 'Kartu Debit', 'Kartu Kredit'] as $metode)
                                <button type="button"
                                        data-value="{{ $metode }}"
                                        data-label="{{ $metode }}"
                                        class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                                    <div class="flex items-center gap-2.5 truncate">
                                        <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                                        <span class="truncate">{{ $metode }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keranjang Belanja -->
            <div class="rounded-lg border border-two/80 bg-one p-4 shadow-md space-y-3">
                <div class="flex items-center justify-between border-b border-two pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-three"></span>
                        <h2 class="text-sm font-bold text-four">Keranjang Belanja</h2>
                    </div>
                    <span id="posCartBadge" class="px-2 py-0.5 rounded-lg bg-two text-xs font-semibold text-four/80">
                        0 item
                    </span>
                </div>

                <div id="posCartList" class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                    <!-- Diisi secara dinamis oleh public/js/salesCreate.js -->
                </div>
            </div>

            <!-- Ringkasan & Pembayaran -->
            <div class="rounded-lg border border-two/80 bg-one p-4 shadow-md space-y-3.5">
                <!-- Pesan Error Validasi -->
                <div id="posErrorBox" class="hidden p-3 rounded-lg bg-three/20 border border-three text-four text-xs">
                    <div class="flex items-center gap-2 font-semibold mb-1 text-three">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Periksa kembali transaksi:</span>
                    </div>
                    <ul id="posErrorList" class="space-y-0.5 pl-6"></ul>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between text-four/70">
                        <span>Total Kuantitas</span>
                        <span id="posTotalQty" class="font-semibold text-four">0 pcs</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-two">
                        <span class="text-base font-bold text-four">Total Tagihan</span>
                        <span id="posGrandTotal" class="text-xl font-bold text-three">Rp 0</span>
                    </div>
                </div>

                <!-- Input Nominal Pembayaran (Wajib Format Ribuan Otomatis) -->
                <div class="space-y-2 pt-1">
                    <label for="inputCashReceived" class="block text-xs font-semibold uppercase tracking-wider text-four/60">
                        Uang Diterima
                    </label>
                    <div class="flex items-center bg-two rounded-lg border border-four/10 focus-within:border-three transition-colors overflow-hidden">
                        <span class="px-3 py-2 text-sm font-semibold text-four/60 bg-one/40 border-r border-four/10">Rp</span>
                        <input type="text"
                               id="inputCashReceived"
                               inputmode="numeric"
                               data-rupiah
                               value="0"
                               placeholder="0"
                               class="w-full px-3 py-2 bg-transparent text-sm font-semibold text-four placeholder-four/40 focus:outline-none">
                    </div>

                    <!-- Tombol Cepat Nominal -->
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" data-quick-cash="exact"
                                class="px-2.5 py-1 rounded-lg bg-two/80 border border-four/10 hover:border-three text-xs text-four/80 hover:text-four active:scale-95 transition-all duration-150 cursor-pointer">
                            Uang Pas
                        </button>
                        <button type="button" data-quick-cash="10000"
                                class="px-2.5 py-1 rounded-lg bg-two/80 border border-four/10 hover:border-three text-xs text-four/80 hover:text-four active:scale-95 transition-all duration-150 cursor-pointer">
                            +10.000
                        </button>
                        <button type="button" data-quick-cash="20000"
                                class="px-2.5 py-1 rounded-lg bg-two/80 border border-four/10 hover:border-three text-xs text-four/80 hover:text-four active:scale-95 transition-all duration-150 cursor-pointer">
                            +20.000
                        </button>
                        <button type="button" data-quick-cash="50000"
                                class="px-2.5 py-1 rounded-lg bg-two/80 border border-four/10 hover:border-three text-xs text-four/80 hover:text-four active:scale-95 transition-all duration-150 cursor-pointer">
                            +50.000
                        </button>
                        <button type="button" data-quick-cash="100000"
                                class="px-2.5 py-1 rounded-lg bg-two/80 border border-four/10 hover:border-three text-xs text-four/80 hover:text-four active:scale-95 transition-all duration-150 cursor-pointer">
                            +100.000
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-two text-sm">
                        <span class="text-four/70">Kembalian</span>
                        <span id="posChangeAmount" class="font-bold text-four">Rp 0</span>
                    </div>
                </div>

                <!-- Tombol Aksi Transaksi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                    <button id="btnSavePendingSale"
                            type="button"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-two border border-four/15 text-four text-sm font-semibold hover:bg-two/80 hover:border-three/70 active:scale-95 transition-all duration-200 cursor-pointer">
                        <svg class="w-4 h-4 text-three" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Simpan Pending</span>
                    </button>

                    <button id="btnCompleteSale"
                            type="button"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Selesaikan Transaksi</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Ringkasan / Struk Transaksi -->
<div id="saleReceiptModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-receipt-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="saleReceiptCard"
         class="relative w-full max-w-md max-h-[90vh] overflow-y-auto rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10">
        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two sticky top-0 z-20">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 class="text-base font-bold text-four">Ringkasan Transaksi</h2>
            </div>
            <button type="button" class="btn-close-receipt-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="saleReceiptContent" class="p-6 text-sm space-y-4">
            <!-- Diisi secara dinamis oleh public/js/salesCreate.js -->
        </div>

        <div class="px-6 py-3.5 bg-two/40 border-t border-two flex items-center justify-end gap-2.5">
            <button type="button"
                    class="btn-close-receipt-modal px-4 py-2 rounded-lg bg-two border border-four/10 text-four text-sm font-medium hover:bg-two/80 active:scale-95 transition-all duration-200 cursor-pointer">
                Tutup
            </button>
            <button id="btnNewTransactionAfterReceipt"
                    type="button"
                    class="px-4 py-2 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 active:scale-95 transition-all duration-200 cursor-pointer">
                Transaksi Baru
            </button>
        </div>
    </div>
</div>

<!-- Container Toast Notifikasi -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/salesCreate.js') }}"></script>
@endpush
