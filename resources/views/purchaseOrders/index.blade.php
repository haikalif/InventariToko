@extends('main.index')

@section('title', 'Pesanan Pembelian (Stok Ulang)')

@section('content')
<div class="space-y-5">

    <!-- Header Halaman & Aksi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-two/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-6 rounded-full bg-three inline-block"></span>
                <h1 class="text-xl font-bold text-four tracking-tight">Pesanan Pembelian (Stok Ulang)</h1>
            </div>
            <p class="text-xs text-four/60 mt-1 pl-4">
                Kelola pengadaan barang ke pemasok, atur daftar item pesanan, dan catat penerimaan stok masuk.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button id="btnOpenCreatePoModal"
                    type="button"
                    data-roles="superadmin,admin,staff"
                    class="hidden group flex items-center gap-2 px-4 py-2 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat PO Baru</span>
            </button>
        </div>
    </div>

    <!-- Kartu Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Total PO</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
            </div>
            <p id="statTotalPo" class="text-2xl font-bold text-four mt-2">0</p>
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
            <p id="statPagePo" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Draft & Dipesan</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statPendingPo" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Sudah Diterima</span>
                <span class="p-2 rounded-lg bg-one text-four transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p id="statReceivedPo" class="text-2xl font-bold text-four mt-2">0</p>
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
                <input id="filterSearchPo"
                       type="text"
                       autocomplete="off"
                       placeholder="Filter nomor PO, pemasok, atau catatan..."
                       class="w-full bg-transparent text-sm text-four placeholder-four/50 focus:outline-none">
            </div>

            <!-- Custom Dropdown Filter Status PO -->
            <div id="dropdownFilterPoStatus" class="relative w-full sm:w-56 select-none">
                <input type="hidden" id="filterStatusPo" value="">
                <button type="button"
                        data-dropdown-toggle
                        class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                    <span data-dropdown-label class="truncate">Semua Status</span>
                    <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div data-dropdown-menu
                     class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-60 z-40">
                    @foreach ([
                        '' => 'Semua Status',
                        'draft' => 'Draft',
                        'dipesan' => 'Dipesan',
                        'diterima_sebagian' => 'Diterima Sebagian',
                        'diterima' => 'Diterima',
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
        </div>

        <!-- Tombol Segarkan -->
        <button id="btnRefreshPo"
                type="button"
                title="Muat Ulang Data"
                class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:bg-two hover:border-three/70 text-sm text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer shadow-sm">
            <svg class="w-4 h-4 text-three transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Segarkan</span>
        </button>
    </div>

    <!-- Tabel Data Purchase Orders -->
    <div class="rounded-lg border border-two/80 bg-one overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-two/70 border-b border-two text-xs font-semibold uppercase tracking-wider text-four/60">
                        <th class="px-4 py-3.5">Nomor PO & Tanggal</th>
                        <th class="px-4 py-3.5">Pemasok</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Item</th>
                        <th class="px-4 py-3.5">Total Nilai</th>
                        <th class="px-4 py-3.5 text-right w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody id="poTableBody" class="text-sm divide-y divide-two/60">
                    <!-- Diisi secara dinamis oleh public/js/purchaseOrders.js -->
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

<!-- Modal Form Tambah / Ubah Purchase Order -->
<div id="poFormModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-po-form-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="poFormCard"
         class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10">

        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two sticky top-0 z-30">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 id="poFormTitle" class="text-base font-bold text-four">Buat Purchase Order Baru</h2>
            </div>
            <button type="button" class="btn-close-po-form-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="poForm" class="p-6 space-y-4 text-sm">
            <input type="hidden" id="po_id">

            <!-- Pesan Validasi -->
            <div id="poFormErrorBox" class="hidden p-3.5 rounded-lg bg-three/20 border border-three text-four text-xs">
                <div class="flex items-center gap-2 font-semibold mb-1 text-three">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Periksa kembali data yang dimasukkan:</span>
                </div>
                <ul id="poFormErrorList" class="space-y-0.5 pl-6"></ul>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nomor_po" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Nomor PO <span class="text-three">*</span>
                    </label>
                    <input type="text" id="nomor_po" required maxlength="25" placeholder="PO-20261113-1234"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four font-mono text-xs placeholder-four/40 focus:outline-none transition-colors">
                </div>

                <!-- Custom Date Picker -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Tanggal <span class="text-three">*</span>
                    </label>
                    <div id="poDatePicker" class="relative select-none">
                        <input type="hidden" id="po_tanggal" required>
                        <button type="button"
                                id="poDatePickerToggle"
                                data-dropdown-toggle
                                class="w-full flex items-center justify-between gap-2 bg-two px-3 py-2 rounded-lg border border-four/10 hover:border-three/70 text-xs text-four transition-all duration-200 cursor-pointer shadow-sm">
                            <div class="flex items-center gap-2 truncate">
                                <svg class="w-4 h-4 text-three shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span id="poDatePickerLabel" class="font-medium truncate">Pilih Tanggal</span>
                            </div>
                            <svg data-dropdown-icon class="w-3.5 h-3.5 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="poDatePickerMenu"
                             data-dropdown-menu
                             class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute right-0 top-full w-68 bg-two border border-three rounded-b-lg rounded-tl-lg shadow-2xl p-3 z-50">
                            <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-four/10">
                                <button type="button" id="poDatePrevMonth"
                                        class="p-1.5 rounded-lg bg-one/60 border border-four/10 text-four/80 hover:text-four hover:border-three active:scale-95 transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <span id="poDateMonthYear" class="text-xs font-bold text-four tracking-wide"></span>
                                <button type="button" id="poDateNextMonth"
                                        class="p-1.5 rounded-lg bg-one/60 border border-four/10 text-four/80 hover:text-four hover:border-three active:scale-95 transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-semibold uppercase text-four/50 mb-1.5">
                                <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                            </div>
                            <div id="poDateDays" class="grid grid-cols-7 gap-1 text-xs"></div>
                            <div class="mt-2.5 pt-2 border-t border-four/10 flex justify-between items-center">
                                <span class="text-[11px] text-four/50">Pilih tanggal PO</span>
                                <button type="button" id="poDateTodayBtn"
                                        class="px-2.5 py-1 rounded-lg bg-one border border-four/10 hover:border-three text-[11px] font-semibold text-three hover:bg-three hover:text-four active:scale-95 transition-all cursor-pointer">
                                    Hari Ini
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Dropdown Pemasok -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Pemasok <span class="text-three">*</span>
                </label>
                <div id="dropdownPoSupplier" class="relative select-none">
                    <input type="hidden" id="po_supplier_id" required>
                    <button type="button"
                            data-dropdown-toggle
                            class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                        <span data-dropdown-label class="truncate">Pilih Pemasok</span>
                        <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="poSupplierMenu"
                         data-dropdown-menu
                         class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                    </div>
                </div>
            </div>

            <!-- Custom Dropdown Status PO -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Status Pesanan
                </label>
                <div id="dropdownPoStatus" class="relative select-none">
                    <input type="hidden" id="po_status" value="draft">
                    <button type="button"
                            data-dropdown-toggle
                            class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                        <span data-dropdown-label class="truncate">Draft</span>
                        <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div data-dropdown-menu
                         class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                        @foreach ([
                            'draft' => 'Draft',
                            'dipesan' => 'Dipesan',
                            'diterima_sebagian' => 'Diterima Sebagian',
                            'diterima' => 'Diterima',
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
            </div>

            <div>
                <label for="po_catatan" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Catatan
                </label>
                <textarea id="po_catatan" rows="3" placeholder="Catatan pengiriman atau keterangan tambahan..."
                          class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-two">
                <button type="button"
                        class="btn-close-po-form-modal px-4 py-2 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two active:scale-95 transition-all duration-200 cursor-pointer">
                    Batal
                </button>
                <button id="btnSubmitPoForm"
                        type="submit"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg bg-three border border-four/20 text-four font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan PO</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail & Kelola Item Purchase Order -->
<div id="poDetailModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-po-detail-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="poDetailCard"
         class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10">
        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two sticky top-0 z-30">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 class="text-base font-bold text-four">Rincian & Item Purchase Order</h2>
            </div>
            <button type="button" class="btn-close-po-detail-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="poDetailContent" class="p-6 space-y-5">
            <!-- Diisi secara dinamis oleh public/js/purchaseOrders.js -->
        </div>

        <div class="px-6 py-3.5 bg-two/40 border-t border-two flex justify-end">
            <button type="button"
                    class="btn-close-po-detail-modal px-4 py-2 rounded-lg bg-two border border-four/10 text-four text-sm font-medium hover:bg-three hover:border-three active:scale-95 transition-all duration-200 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Container Toast Notifikasi -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/purchaseOrders.js') }}"></script>
@endpush
