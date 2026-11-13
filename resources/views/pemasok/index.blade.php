@extends('main.index')

@section('title', 'Data Pemasok')

@section('content')
<div class="space-y-5">

    <!-- Header Halaman & Aksi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-two/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-6 rounded-full bg-three inline-block"></span>
                <h1 class="text-xl font-bold text-four tracking-tight">Data Pemasok</h1>
            </div>
            <p class="text-xs text-four/60 mt-1 pl-4">
                Kelola informasi pemasok, kontak, email, dan alamat untuk kelancaran pengadaan stok.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Tab Pemasok Aktif & Tong Sampah (Tong Sampah khusus Superadmin & Admin) -->
            <div class="inline-flex p-1 rounded-lg bg-two/80 border border-four/10 text-xs">
                <button id="tabActiveSuppliers"
                        type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-three text-four font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pemasok Aktif</span>
                </button>
                <button id="tabTrashedSuppliers"
                        type="button"
                        data-roles="superadmin,admin"
                        class="hidden flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-four/70 hover:text-four transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Tong Sampah</span>
                </button>
            </div>

            <!-- Tombol Tambah Pemasok (Khusus Superadmin, Admin, & Staff) -->
            <button id="btnOpenCreateSupplierModal"
                    type="button"
                    data-roles="superadmin,admin,staff"
                    class="hidden group flex items-center gap-2 px-4 py-2 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Pemasok</span>
            </button>
        </div>
    </div>

    <!-- Kartu Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Total Pemasok</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
            </div>
            <p id="statTotalSuppliers" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Ditampilkan</span>
                <span class="p-2 rounded-lg bg-one text-four transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </span>
            </div>
            <p id="statPageSuppliers" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Kontak Lengkap</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statCompleteSuppliers" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Data Belum Lengkap</span>
                <span class="p-2 rounded-lg bg-one text-four/60 transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statIncompleteSuppliers" class="text-2xl font-bold text-four/70 mt-2">0</p>
        </div>
    </div>

    <!-- Bar Filter & Pencarian Tabel -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-two/40 p-3 rounded-lg border border-two/80">
        <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Input Filter Pemasok -->
            <div class="group flex items-center flex-1 max-w-md bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200 shadow-inner">
                <svg class="w-4 h-4 text-three mr-2.5 shrink-0 transition-transform duration-300 group-focus-within:scale-110 group-focus-within:rotate-12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 10.5a7.5 7.5 0 0013.15 6.15z" />
                </svg>
                <input id="filterSearchSupplier"
                       type="text"
                       autocomplete="off"
                       placeholder="Filter nama, kontak, email, atau alamat pemasok..."
                       class="w-full bg-transparent text-sm text-four placeholder-four/50 focus:outline-none">
            </div>

            <!-- Custom Dropdown Filter Kelengkapan Data (Menyatu dengan Toggle) -->
            <div id="dropdownFilterStatus" class="relative w-full sm:w-56 select-none">
                <input type="hidden" id="filterStatusSupplier" value="">
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
                    <button type="button"
                            data-value=""
                            data-label="Semua Status"
                            class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-2.5 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                            <span class="truncate">Semua Status</span>
                        </div>
                    </button>
                    <button type="button"
                            data-value="has_email"
                            data-label="Memiliki Email"
                            class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-2.5 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                            <span class="truncate">Memiliki Email</span>
                        </div>
                    </button>
                    <button type="button"
                            data-value="has_kontak"
                            data-label="Memiliki Kontak"
                            class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-2.5 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                            <span class="truncate">Memiliki Kontak</span>
                        </div>
                    </button>
                    <button type="button"
                            data-value="incomplete"
                            data-label="Data Belum Lengkap"
                            class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-2.5 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                            <span class="truncate">Data Belum Lengkap</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tombol Segarkan -->
        <button id="btnRefreshSuppliers"
                type="button"
                title="Muat Ulang Data"
                class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:bg-two hover:border-three/70 text-sm text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer shadow-sm">
            <svg class="w-4 h-4 text-three transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Segarkan</span>
        </button>
    </div>

    <!-- Tabel Data Pemasok -->
    <div class="rounded-lg border border-two/80 bg-one overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-two/70 border-b border-two text-xs font-semibold uppercase tracking-wider text-four/60">
                        <th class="px-4 py-3.5 w-44">ID Pemasok</th>
                        <th class="px-4 py-3.5">Nama Pemasok</th>
                        <th class="px-4 py-3.5">Kontak & Email</th>
                        <th class="px-4 py-3.5">Alamat</th>
                        <th class="px-4 py-3.5 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody id="supplierTableBody" class="text-sm divide-y divide-two/60">
                    <!-- Diisi secara dinamis oleh public/js/pemasok.js -->
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

<!-- Modal Form Tambah / Ubah Pemasok -->
<div id="supplierFormModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-supplier-form-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="supplierFormCard"
         class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10">

        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two sticky top-0 z-30">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 id="supplierFormTitle" class="text-base font-bold text-four">Tambah Pemasok Baru</h2>
            </div>
            <button type="button" class="btn-close-supplier-form-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="supplierForm" class="p-6 space-y-4 text-sm">
            <input type="hidden" id="supplier_id">

            <!-- Pesan Validasi -->
            <div id="supplierFormErrorBox" class="hidden p-3.5 rounded-lg bg-three/20 border border-three text-four text-xs">
                <div class="flex items-center gap-2 font-semibold mb-1 text-three">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Periksa kembali data yang dimasukkan:</span>
                </div>
                <ul id="supplierFormErrorList" class="space-y-0.5 pl-6"></ul>
            </div>

            <div>
                <label for="nama_supplier" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Nama Pemasok <span class="text-three">*</span>
                </label>
                <input type="text" id="nama_supplier" required maxlength="255" placeholder="Contoh: PT Sumber Makmur Jaya"
                       class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kontak" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Nomor Kontak
                    </label>
                    <input type="text" id="kontak" maxlength="50" placeholder="Contoh: 081234567890"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Alamat Email
                    </label>
                    <input type="email" id="email" maxlength="255" placeholder="Contoh: kontak@pemasok.co.id"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Alamat Lengkap
                </label>
                <textarea id="alamat" rows="3" placeholder="Alamat gudang atau kantor pemasok..."
                          class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-two">
                <button type="button"
                        class="btn-close-supplier-form-modal px-4 py-2 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two active:scale-95 transition-all duration-200 cursor-pointer">
                    Batal
                </button>
                <button id="btnSubmitSupplierForm"
                        type="submit"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg bg-three border border-four/20 text-four font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Pemasok</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Pemasok -->
<div id="supplierDetailModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-supplier-detail-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="supplierDetailCard"
         class="relative w-full max-w-lg rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 class="text-base font-bold text-four">Rincian Data Pemasok</h2>
            </div>
            <button type="button" class="btn-close-supplier-detail-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="supplierDetailContent" class="p-6">
            <!-- Diisi secara dinamis oleh public/js/pemasok.js -->
        </div>

        <div class="px-6 py-3.5 bg-two/40 border-t border-two flex justify-end">
            <button type="button"
                    class="btn-close-supplier-detail-modal px-4 py-2 rounded-lg bg-two border border-four/10 text-four text-sm font-medium hover:bg-three hover:border-three active:scale-95 transition-all duration-200 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Container Toast Notifikasi -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pemasok.js') }}"></script>
@endpush
