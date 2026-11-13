@extends('main.index')

@section('title', 'Data Produk')

@section('content')
<div class="space-y-5">

    <!-- Header Halaman & Aksi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-two/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-6 rounded-full bg-three inline-block"></span>
                <h1 class="text-xl font-bold text-four tracking-tight">Data Produk</h1>
            </div>
            <p class="text-xs text-four/60 mt-1 pl-4">
                Kelola daftar inventaris barang, harga beli dan jual, serta batas stok minimum toko.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Tab Produk Aktif & Tong Sampah (Tong Sampah khusus Superadmin & Admin) -->
            <div class="inline-flex p-1 rounded-lg bg-two/80 border border-four/10 text-xs">
                <button id="tabActiveProducts"
                        type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-three text-four font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Produk Aktif</span>
                </button>
                <button id="tabTrashedProducts"
                        type="button"
                        data-roles="superadmin,admin"
                        class="hidden flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-four/70 hover:text-four transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Tong Sampah</span>
                </button>
            </div>

            <!-- Tombol Tambah Produk (Khusus Superadmin, Admin, & Staff) -->
            <button id="btnOpenCreateModal"
                    type="button"
                    data-roles="superadmin,admin,staff"
                    class="hidden group flex items-center gap-2 px-4 py-2 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Produk</span>
            </button>
        </div>
    </div>

    <!-- Kartu Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Total Produk</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
            </div>
            <p id="statTotalProducts" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Stok Aman</span>
                <span class="p-2 rounded-lg bg-one text-four transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statSafeStock" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Stok Menipis</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
            </div>
            <p id="statLowStock" class="text-2xl font-bold text-three mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Nonaktif</span>
                <span class="p-2 rounded-lg bg-one text-four/60 transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
            </div>
            <p id="statInactiveProducts" class="text-2xl font-bold text-four/70 mt-2">0</p>
        </div>
    </div>

    <!-- Bar Filter & Pencarian Tabel -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-two/40 p-3 rounded-lg border border-two/80">
        <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Input Filter Produk -->
            <div class="group flex items-center flex-1 max-w-md bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200 shadow-inner">
                <svg class="w-4 h-4 text-three mr-2.5 shrink-0 transition-transform duration-300 group-focus-within:scale-110 group-focus-within:rotate-12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 10.5a7.5 7.5 0 0013.15 6.15z" />
                </svg>
                <input id="filterSearchProduct"
                       type="text"
                       autocomplete="off"
                       placeholder="Filter nama produk, SKU, atau barcode..."
                       class="w-full bg-transparent text-sm text-four placeholder-four/50 focus:outline-none">
            </div>

            <!-- Custom Dropdown Filter Kategori (Menyatu dengan Toggle) -->
            <div id="dropdownFilterCategory" class="relative w-full sm:w-60 select-none">
                <input type="hidden" id="filterCategoryProduct" value="">
                <button type="button"
                        data-dropdown-toggle
                        class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                    <span data-dropdown-label class="truncate">Semua Kategori</span>
                    <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div data-dropdown-menu
                     class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                    <!-- Opsi diisi otomatis oleh public/js/product.js -->
                </div>
            </div>
        </div>

        <!-- Tombol Segarkan -->
        <button id="btnRefreshProducts"
                type="button"
                title="Muat Ulang Data"
                class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:bg-two hover:border-three/70 text-sm text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer shadow-sm">
            <svg class="w-4 h-4 text-three transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Segarkan</span>
        </button>
    </div>

    <!-- Tabel Data Produk -->
    <div class="rounded-lg border border-two/80 bg-one overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-two/70 border-b border-two text-xs font-semibold uppercase tracking-wider text-four/60">
                        <th class="px-4 py-3.5">SKU</th>
                        <th class="px-4 py-3.5">Nama Produk</th>
                        <th class="px-4 py-3.5">Kategori & Pemasok</th>
                        <th class="px-4 py-3.5">Harga Jual / Beli</th>
                        <th class="px-4 py-3.5">Stok</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTableBody" class="text-sm divide-y divide-two/60">
                    <!-- Diisi secara dinamis oleh public/js/product.js -->
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

<!-- Modal Form Tambah / Ubah Produk -->
<div id="productFormModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div id="productFormBackdrop" class="btn-close-form-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="productFormCard"
         class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10">

        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two sticky top-0 z-30">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 id="productFormTitle" class="text-base font-bold text-four">Tambah Produk Baru</h2>
            </div>
            <button type="button" class="btn-close-form-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="productForm" class="p-6 space-y-4 text-sm">
            <input type="hidden" id="product_id">

            <!-- Pesan Validasi -->
            <div id="productFormErrorBox" class="hidden p-3.5 rounded-lg bg-three/20 border border-three text-four text-xs">
                <div class="flex items-center gap-2 font-semibold mb-1 text-three">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Periksa kembali data yang dimasukkan:</span>
                </div>
                <ul id="productFormErrorList" class="space-y-0.5 pl-6"></ul>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Kode SKU <span class="text-three">*</span>
                    </label>
                    <input type="text" id="sku" required placeholder="Contoh: PRD-001"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
                </div>

                <div>
                    <label for="barcode" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Barcode
                    </label>
                    <input type="text" id="barcode" placeholder="Contoh: 8991234567890"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
                </div>

                <div class="sm:col-span-2">
                    <label for="nama_produk" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Nama Produk <span class="text-three">*</span>
                    </label>
                    <input type="text" id="nama_produk" required placeholder="Masukkan nama lengkap produk..."
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
                </div>

                <!-- Custom Dropdown Kategori (Menyatu dengan Toggle) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Kategori Produk
                    </label>
                    <div id="dropdownFormCategory" class="relative select-none">
                        <input type="hidden" id="category_id" value="">
                        <button type="button"
                                data-dropdown-toggle
                                class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer">
                            <span data-dropdown-label class="truncate">Pilih Kategori</span>
                            <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div data-dropdown-menu
                             class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-48 z-50">
                        </div>
                    </div>
                </div>

                <!-- Custom Dropdown Pemasok (Menyatu dengan Toggle) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Pemasok Barang
                    </label>
                    <div id="dropdownFormSupplier" class="relative select-none">
                        <input type="hidden" id="supplier_id" value="">
                        <button type="button"
                                data-dropdown-toggle
                                class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer">
                            <span data-dropdown-label class="truncate">Pilih Pemasok</span>
                            <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div data-dropdown-menu
                             class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-48 z-50">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="harga_beli" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Harga Beli
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-xs font-semibold text-four/50 pointer-events-none select-none">Rp</span>
                        <input type="text" id="harga_beli" inputmode="numeric" data-rupiah value="0" placeholder="0"
                               class="w-full pl-9 pr-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four focus:outline-none transition-colors">
                    </div>
                </div>

                <div>
                    <label for="harga_jual" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Harga Jual
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-xs font-semibold text-four/50 pointer-events-none select-none">Rp</span>
                        <input type="text" id="harga_jual" inputmode="numeric" data-rupiah value="0" placeholder="0"
                               class="w-full pl-9 pr-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four focus:outline-none transition-colors">
                    </div>
                </div>

                <div>
                    <label for="stok" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Stok Saat Ini
                    </label>
                    <input type="number" id="stok" min="0" step="1" value="0"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four focus:outline-none transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label for="stok_minimum" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                            Stok Minimum
                        </label>
                        <input type="number" id="stok_minimum" min="0" step="1" value="5"
                               class="w-full px-3 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label for="satuan" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                            Satuan
                        </label>
                        <input type="text" id="satuan" placeholder="pcs / box" value="pcs"
                               class="w-full px-3 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four focus:outline-none transition-colors">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Deskripsi Produk
                    </label>
                    <textarea id="deskripsi" rows="2" placeholder="Catatan atau spesifikasi singkat produk..."
                              class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors"></textarea>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2.5 pt-1">
                    <input type="checkbox" id="aktif" checked
                           class="w-4 h-4 rounded accent-three cursor-pointer">
                    <label for="aktif" class="text-sm text-four/90 cursor-pointer select-none">
                        Produk berstatus <span class="font-semibold text-four">Aktif</span> dan siap ditransaksikan
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-two">
                <button type="button"
                        class="btn-close-form-modal px-4 py-2 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two active:scale-95 transition-all duration-200 cursor-pointer">
                    Batal
                </button>
                <button id="btnSubmitProductForm"
                        type="submit"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg bg-three border border-four/20 text-four font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Produk</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Produk -->
<div id="productDetailModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-detail-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="productDetailCard"
         class="relative w-full max-w-lg rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 class="text-base font-bold text-four">Rincian Informasi Produk</h2>
            </div>
            <button type="button" class="btn-close-detail-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="productDetailContent" class="p-6">
            <!-- Diisi secara dinamis oleh public/js/product.js -->
        </div>

        <div class="px-6 py-3.5 bg-two/40 border-t border-two flex justify-end">
            <button type="button"
                    class="btn-close-detail-modal px-4 py-2 rounded-lg bg-two border border-four/10 text-four text-sm font-medium hover:bg-three hover:border-three active:scale-95 transition-all duration-200 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Container Toast Notifikasi -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/product.js') }}"></script>
@endpush
