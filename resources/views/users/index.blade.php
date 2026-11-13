@extends('main.index')

@section('title', 'Kelola Karyawan')

@section('content')
<div class="space-y-5">

    <!-- Header Halaman & Aksi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-two/80 pb-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-6 rounded-full bg-three inline-block"></span>
                <h1 class="text-xl font-bold text-four tracking-tight">Kelola Karyawan</h1>
            </div>
            <p class="text-xs text-four/60 mt-1 pl-4">
                Kelola daftar akun karyawan, hak akses peran (role), dan pemulihan akun terhapus.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Tab Karyawan Aktif & Tong Sampah -->
            <div class="inline-flex p-1 rounded-lg bg-two/80 border border-four/10 text-xs">
                <button id="tabActiveUsers"
                        type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-three text-four font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Karyawan Aktif</span>
                </button>
                <button id="tabTrashedUsers"
                        type="button"
                        data-roles="superadmin,admin"
                        class="hidden flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-four/70 hover:text-four transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Tong Sampah</span>
                </button>
            </div>

            <!-- Tombol Tambah Karyawan -->
            <button id="btnOpenCreateUserModal"
                    type="button"
                    data-roles="superadmin,admin"
                    class="hidden group flex items-center gap-2 px-4 py-2 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Karyawan</span>
            </button>
        </div>
    </div>

    <!-- Kartu Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Total Akun</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
            </div>
            <p id="statTotalUsers" class="text-2xl font-bold text-four mt-2">0</p>
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
            <p id="statPageUsers" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Admin & Super</span>
                <span class="p-2 rounded-lg bg-one text-three transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
            </div>
            <p id="statAdminUsers" class="text-2xl font-bold text-four mt-2">0</p>
        </div>

        <div class="group p-4 rounded-lg bg-two/60 border border-two/80 hover:border-three/60 hover:bg-two/80 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-four/60">Staff & Kasir</span>
                <span class="p-2 rounded-lg bg-one text-four/80 transition-transform duration-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
            </div>
            <p id="statStaffKasirUsers" class="text-2xl font-bold text-four mt-2">0</p>
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
                <input id="filterSearchUser"
                       type="text"
                       autocomplete="off"
                       placeholder="Filter nama, email, atau ID karyawan..."
                       class="w-full bg-transparent text-sm text-four placeholder-four/50 focus:outline-none">
            </div>

            <!-- Custom Dropdown Filter Role -->
            <div id="dropdownFilterUserRole" class="relative w-full sm:w-52 select-none">
                <input type="hidden" id="filterRoleUser" value="">
                <button type="button"
                        data-dropdown-toggle
                        class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                    <span data-dropdown-label class="truncate">Semua Peran</span>
                    <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div data-dropdown-menu
                     class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                    @foreach ([
                        '' => 'Semua Peran',
                        'superadmin' => 'Superadmin',
                        'admin' => 'Admin',
                        'staff' => 'Staff',
                        'kasir' => 'Kasir',
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
        <button id="btnRefreshUsers"
                type="button"
                title="Muat Ulang Data"
                class="group inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-two/80 border border-four/10 hover:bg-two hover:border-three/70 text-sm text-four/80 hover:text-four active:scale-95 transition-all duration-200 cursor-pointer shadow-sm">
            <svg class="w-4 h-4 text-three transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Segarkan</span>
        </button>
    </div>

    <!-- Tabel Data Karyawan -->
    <div class="rounded-lg border border-two/80 bg-one overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-two/70 border-b border-two text-xs font-semibold uppercase tracking-wider text-four/60">
                        <th class="px-4 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5">Email</th>
                        <th class="px-4 py-3.5">Peran</th>
                        <th class="px-4 py-3.5">Terdaftar</th>
                        <th class="px-4 py-3.5 text-right w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="text-sm divide-y divide-two/60">
                    <!-- Diisi secara dinamis oleh public/js/users.js -->
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

<!-- Modal Form Tambah / Ubah Karyawan -->
<div id="userFormModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-user-form-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="userFormCard"
         class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10">

        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two sticky top-0 z-30">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 id="userFormTitle" class="text-base font-bold text-four">Tambah Karyawan Baru</h2>
            </div>
            <button type="button" class="btn-close-user-form-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="userForm" class="p-6 space-y-4 text-sm">
            <input type="hidden" id="user_id">

            <!-- Pesan Validasi -->
            <div id="userFormErrorBox" class="hidden p-3.5 rounded-lg bg-three/20 border border-three text-four text-xs">
                <div class="flex items-center gap-2 font-semibold mb-1 text-three">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Periksa kembali data yang dimasukkan:</span>
                </div>
                <ul id="userFormErrorList" class="space-y-0.5 pl-6"></ul>
            </div>

            <div>
                <label for="user_name" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Nama Lengkap <span class="text-three">*</span>
                </label>
                <input type="text" id="user_name" required maxlength="255" placeholder="Contoh: Budi Santoso"
                       class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
            </div>

            <div>
                <label for="user_email" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Alamat Email <span class="text-three">*</span>
                </label>
                <input type="email" id="user_email" required maxlength="255" placeholder="Contoh: budi@toko.com"
                       class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
            </div>

            <!-- Custom Dropdown Peran (Role) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                    Peran (Role) <span class="text-three">*</span>
                </label>
                <div id="dropdownUserRoleForm" class="relative select-none">
                    <input type="hidden" id="user_role" value="kasir">
                    <button type="button"
                            data-dropdown-toggle
                            class="w-full flex items-center justify-between gap-2 bg-two px-3.5 py-2 rounded-lg border border-four/10 hover:border-three/70 text-sm text-four transition-all duration-200 cursor-pointer shadow-sm">
                        <span data-dropdown-label class="truncate">Kasir</span>
                        <svg data-dropdown-icon class="w-4 h-4 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div data-dropdown-menu
                         class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-56 z-40">
                        @foreach ([
                            'kasir' => 'Kasir',
                            'staff' => 'Staff',
                            'admin' => 'Admin',
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="user_password" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Kata Sandi <span id="passwordRequiredStar" class="text-three">*</span>
                    </label>
                    <input type="password" id="user_password" minlength="8" placeholder="Minimal 8 karakter"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
                </div>

                <div>
                    <label for="user_password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-four/60 mb-1.5">
                        Konfirmasi Sandi <span id="passwordConfirmStar" class="text-three">*</span>
                    </label>
                    <input type="password" id="user_password_confirmation" minlength="8" placeholder="Ulangi kata sandi"
                           class="w-full px-3.5 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-four placeholder-four/40 focus:outline-none transition-colors">
                </div>
            </div>
            <p id="passwordEditHint" class="hidden text-xs text-four/50 -mt-2">
                Kosongkan kata sandi jika tidak ingin mengubahnya.
            </p>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-two">
                <button type="button"
                        class="btn-close-user-form-modal px-4 py-2 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two active:scale-95 transition-all duration-200 cursor-pointer">
                    Batal
                </button>
                <button id="btnSubmitUserForm"
                        type="submit"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg bg-three border border-four/20 text-four font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Karyawan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Karyawan -->
<div id="userDetailModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-user-detail-modal absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

    <div id="userDetailCard"
         class="relative w-full max-w-lg rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-two/70 border-b border-two">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 rounded-full bg-three"></span>
                <h2 class="text-base font-bold text-four">Rincian Akun Karyawan</h2>
            </div>
            <button type="button" class="btn-close-user-detail-modal p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="userDetailContent" class="p-6">
            <!-- Diisi secara dinamis oleh public/js/users.js -->
        </div>

        <div class="px-6 py-3.5 bg-two/40 border-t border-two flex justify-end">
            <button type="button"
                    class="btn-close-user-detail-modal px-4 py-2 rounded-lg bg-two border border-four/10 text-four text-sm font-medium hover:bg-three hover:border-three active:scale-95 transition-all duration-200 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Container Toast Notifikasi -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

<!-- Modal Preview Foto Profil Lebih Detail -->
<div id="userAvatarPreviewModal"
     class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200">
    <div class="btn-close-avatar-preview absolute inset-0 bg-canvas/85 backdrop-blur-xs"></div>

    <div id="userAvatarPreviewCard"
         class="relative w-full max-w-md rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 bg-two/70 border-b border-two">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-2 h-5 rounded-full bg-three shrink-0"></span>
                <h3 id="userAvatarPreviewTitle" class="text-sm font-bold text-four truncate">Foto Profil Karyawan</h3>
            </div>
            <button type="button" class="btn-close-avatar-preview p-1.5 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-5 flex flex-col items-center justify-center bg-canvas/40">
            <img id="userAvatarPreviewImg"
                 src=""
                 alt="Preview Foto Profil"
                 class="w-full max-h-[70vh] rounded-lg object-contain border border-two bg-one shadow-lg">
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/users.js') }}"></script>
@endpush
