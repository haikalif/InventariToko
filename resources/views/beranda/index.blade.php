@extends('main.index')

@section('title', 'Beranda - MyInventaris')

@section('content')
<div class="space-y-6">

    <!-- TAMPILAN PENGENALAN APLIKASI (SAAT BELUM LOGIN / GUEST) -->
    <section id="berandaGuestSection" class="hidden space-y-6">
        <div class="relative overflow-hidden rounded-lg bg-two/50 border border-two p-6 sm:p-8 shadow-md">
            <div class="max-w-2xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-one border border-three/40 text-xs font-semibold text-three">
                    <span class="w-2 h-2 rounded-full bg-three animate-pulse"></span>
                    <span>Sistem Manajemen Inventaris & Kasir Toko</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-four tracking-tight leading-snug">
                    Kelola Stok Barang, Pemasok, dan Penjualan Kasir dalam Satu Tempat
                </h1>

                <p class="text-sm text-four/75 leading-relaxed">
                    <strong class="text-four">MyInventaris</strong> membantu operasional toko Anda tetap tertib — mulai dari pencatatan produk dan kategori, pemesanan stok ulang ke pemasok (Purchase Order), transaksi kasir harian, hingga log audit pergerakan stok secara real-time.
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="/login"
                       class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 shadow-md">
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Masuk ke Aplikasi</span>
                    </a>
                    <a href="/register"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-one border border-four/15 text-four/90 text-sm font-medium hover:bg-two hover:border-three/70 active:scale-95 transition-all duration-200">
                        <span>Daftar Akun Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kartu Fitur Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-lg bg-two/50 border border-two/80 hover:border-three/60 transition-all duration-200 space-y-2">
                <div class="w-9 h-9 rounded-lg bg-one text-three flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-four">Kasir / POS Cepat</h3>
                <p class="text-xs text-four/65 leading-relaxed">
                    Dukungan pencarian SKU/barcode, hitung kembalian otomatis, dan pencatatan transaksi instan.
                </p>
            </div>

            <div class="p-4 rounded-lg bg-two/50 border border-two/80 hover:border-three/60 transition-all duration-200 space-y-2">
                <div class="w-9 h-9 rounded-lg bg-one text-three flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-four">Master Produk & Kategori</h3>
                <p class="text-xs text-four/65 leading-relaxed">
                    Pantau harga beli, harga jual, batas stok minimum, serta pengelompokan kategori barang.
                </p>
            </div>

            <div class="p-4 rounded-lg bg-two/50 border border-two/80 hover:border-three/60 transition-all duration-200 space-y-2">
                <div class="w-9 h-9 rounded-lg bg-one text-three flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-four">Purchase Order & Pemasok</h3>
                <p class="text-xs text-four/65 leading-relaxed">
                    Kelola data pemasok dan pesanan stok ulang hingga barang diterima otomatis ke gudang.
                </p>
            </div>

            <div class="p-4 rounded-lg bg-two/50 border border-two/80 hover:border-three/60 transition-all duration-200 space-y-2">
                <div class="w-9 h-9 rounded-lg bg-one text-three flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-four">Audit Mutasi Stok</h3>
                <p class="text-xs text-four/65 leading-relaxed">
                    Setiap stok masuk, keluar, dan retur tercatat rapi dengan jejak referensi dan penanggung jawab.
                </p>
            </div>
        </div>
    </section>

    <!-- TAMPILAN DASHBOARD & GRAFIK (SAAT SUDAH LOGIN) -->
    <section id="berandaAuthSection" class="hidden space-y-5">

        <!-- Header Sambutan & Pintasan Cepat -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-two/80 pb-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-6 rounded-full bg-three inline-block"></span>
                    <h1 id="dashGreetingTitle" class="text-xl font-bold text-four tracking-tight">Ringkasan Toko</h1>
                </div>
                <p id="dashGreetingSubtitle" class="text-xs text-four/60 mt-1 pl-4">
                    Pantau performa penjualan, kondisi stok barang, dan aktivitas inventaris terbaru.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="/sales/create"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-three border border-four/20 text-four text-xs font-semibold hover:brightness-110 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Buka Kasir</span>
                </a>
                <a href="/products"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-two border border-four/10 text-four/90 text-xs font-medium hover:border-three/70 active:scale-95 transition-all duration-200">
                    <span>Data Produk</span>
                </a>
                <button id="btnRefreshDashboard"
                        type="button"
                        class="group inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:border-three/70 text-xs text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-three transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Segarkan</span>
                </button>
            </div>
        </div>

        <!-- 4 Kartu KPI Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Total Produk</span>
                    <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                </div>
                <p id="dashTotalProducts" class="text-2xl font-bold text-four mt-2">0</p>
                <p id="dashLowStockHint" class="text-xs text-three mt-1">Memeriksa stok...</p>
            </div>

            <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Kategori Produk</span>
                    <span class="p-2 rounded-lg bg-one text-four transition-transform duration-200 group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </span>
                </div>
                <p id="dashTotalCategories" class="text-2xl font-bold text-four mt-2">0</p>
                <p class="text-xs text-four/55 mt-1">Kelompok barang aktif</p>
            </div>

            <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Total Penjualan</span>
                    <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p id="dashTotalRevenue" class="text-xl font-bold text-four mt-2 truncate">Rp 0</p>
                <p id="dashTotalSalesCount" class="text-xs text-four/55 mt-1">0 transaksi tercatat</p>
            </div>

            <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Mutasi Stok</span>
                    <span class="p-2 rounded-lg bg-one text-four transition-transform duration-200 group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </span>
                </div>
                <p id="dashTotalMovements" class="text-2xl font-bold text-four mt-2">0</p>
                <p class="text-xs text-four/55 mt-1">Riwayat keluar & masuk</p>
            </div>
        </div>

        <!-- Baris Grafik Data -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 w-full">
            <!-- Grafik Batang Penjualan 7 Hari Terakhir -->
            <div class="w-full lg:col-span-8 rounded-lg border border-two/80 bg-two/30 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-four">Tren Penjualan 7 Hari Terakhir</h2>
                        <p class="text-xs text-four/55">Akumulasi nominal transaksi non-batal per hari</p>
                    </div>
                    <a href="/sales" class="text-xs text-three hover:underline">Lihat Semua</a>
                </div>
                <div id="dashSalesChart" class="w-full h-52 flex items-end gap-2 sm:gap-3 pt-4">
                    <!-- Dirender oleh public/js/beranda.js -->
                </div>
            </div>

            <!-- Ringkasan Distribusi Mutasi Stok -->
            <div class="w-full lg:col-span-4 rounded-lg border border-two/80 bg-two/30 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-four">Proporsi Mutasi Stok</h2>
                        <p class="text-xs text-four/55">Berdasarkan catatan mutasi terbaru</p>
                    </div>
                    <a href="/stock-movements" class="text-xs text-three hover:underline">Log</a>
                </div>
                <div id="dashMovementChart" class="w-full space-y-4 my-auto">
                    <!-- Dirender oleh public/js/beranda.js -->
                </div>
            </div>
        </div>

        <!-- Baris Daftar Ringkas: Stok Menipis & Transaksi Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-lg border border-two/80 bg-two/30 p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-two pb-2.5">
                    <h2 class="text-sm font-bold text-four">Peringatan Stok Menipis</h2>
                    <a href="/products" class="text-xs text-three hover:underline">Kelola Produk</a>
                </div>
                <div id="dashLowStockList" class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    <!-- Dirender oleh public/js/beranda.js -->
                </div>
            </div>

            <div class="rounded-lg border border-two/80 bg-two/30 p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-two pb-2.5">
                    <h2 class="text-sm font-bold text-four">Transaksi Penjualan Terbaru</h2>
                    <a href="/sales" class="text-xs text-three hover:underline">Riwayat Penjualan</a>
                </div>
                <div id="dashRecentSalesList" class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    <!-- Dirender oleh public/js/beranda.js -->
                </div>
            </div>
        </div>

    </section>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/beranda.js') }}"></script>
@endpush
