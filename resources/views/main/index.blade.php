<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
    <title>@yield('title', 'Beranda') | MyInventaris</title>
    <style>
        .app-shell-body {
            height: 100vh;
            height: 100dvh;
            padding-top: max(0.5rem, env(safe-area-inset-top));
            padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
        }
        @media (max-width: 1023px) {
            #sidebar {
                height: auto;
                top: max(0.5rem, env(safe-area-inset-top));
                bottom: max(0.5rem, env(safe-area-inset-bottom));
            }
            #navbutton {
                padding-bottom: max(1.25rem, calc(1rem + env(safe-area-inset-bottom)));
            }
        }
        @media (max-width: 767px) {
            main {
                padding-bottom: max(2.25rem, calc(1.5rem + env(safe-area-inset-bottom)));
            }
        }
        [id$="Modal"] {
            padding-top: max(1rem, env(safe-area-inset-top));
            padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }
        [id$="Modal"] > div.overflow-y-auto {
            max-height: calc(100dvh - 2rem - env(safe-area-inset-top) - env(safe-area-inset-bottom));
        }
        #toastContainer {
            bottom: max(1.25rem, calc(1.25rem + env(safe-area-inset-bottom)));
        }
    </style>
</head>
<body class="bg-canvas app-shell-body flex h-dvh overflow-hidden pt-2 pb-2">

    <div id="sidebarBackdrop" class="fixed inset-0 bg-canvas/75 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity duration-300"></div>

    <nav id="sidebar" class="bg-one border-y border-r border-two/80 h-full w-70 shrink-0 flex flex-col justify-between select-none shadow-xl rounded-r-lg transition-all duration-300 ease-in-out overflow-hidden fixed inset-y-2 left-0 z-50 -translate-x-full lg:static lg:inset-y-auto lg:z-auto lg:translate-x-0">
        <div class="overflow-y-auto w-70">
            <div id="logo" class="group/logo rounded-tr-lg flex items-center justify-center pt-4 pb-4 bg-two/70 border-b border-two cursor-pointer transition-colors duration-300 hover:bg-two relative">
                <h1 class="text-three font-semibold text-2xl transition-transform duration-300 group-hover/logo:-translate-y-0.5">My</h1>
                <h1 class="text-four font-bold text-2xl transition-transform duration-300 group-hover/logo:-translate-y-0.5">Inventaris</h1>
                <svg class="ml-1.5 animate-float transition-transform duration-300 group-hover/logo:scale-110 group-hover/logo:rotate-6" width="38" height="38" viewBox="0 0 32 32" fill="none">
                    <path d="M 4 14 C 4 6, 15 2, 23 6" class="stroke-three" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3" />
                    <path d="M 16 7 L 6 12 L 16 17 L 26 12 Z" class="fill-four" />
                    <path d="M 11 9.5 L 21 14.5 L 23 13.5 L 13 8.5 Z" class="fill-three" />
                    <path d="M 6 12 V 23 L 16 28 V 17 Z" class="fill-two" />
                    <path d="M 16 17 V 28 L 26 23 V 12 Z" class="fill-one" />
                    <path d="M 3 19 C 5 29, 20 31, 28 20" class="stroke-three" stroke-width="2.5" stroke-linecap="round" />
                    <path d="M 24 19 L 28.5 19.5 L 29 24" class="stroke-three" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>


            <div id="navbutton" class="p-4 space-y-1.5 text-sm">


                <p class="px-3 pt-2 pb-1 text-xs font-semibold uppercase tracking-wider text-four/50">Menu Utama</p>


                <a href="/"
                   class="group/link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('/') ? 'bg-three text-four font-semibold shadow-sm' : 'text-four/80 hover:bg-two hover:text-four' }}">
                    <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover/link:scale-110 {{ request()->is('/') ? '' : 'group-hover/link:text-three' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Beranda</span>
                </a>


                <a href="/sales/create"
                   class="group/link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('sales/create') ? 'bg-three text-four font-semibold shadow-sm' : 'text-four/80 hover:bg-two hover:text-four' }}">
                    <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover/link:scale-110 {{ request()->is('sales/create') ? '' : 'group-hover/link:text-three' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Kasir / Penjualan</span>
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-four/50">Inventaris & Data</p>

                @php
                    $isMasterActive = request()->is('products*', 'categories*', 'suppliers*');
                @endphp
                <details class="group sidebar-accordion" {{ $isMasterActive ? 'open' : '' }}>
                    <summary class="group/summary flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer list-none transition-all duration-200 ease-out hover:translate-x-1 {{ $isMasterActive ? 'bg-two text-three font-semibold' : 'text-four/80 hover:bg-two hover:text-four' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover/summary:scale-110 group-hover/summary:text-three" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Master Barang</span>
                        </div>

                        <svg class="accordion-icon w-4 h-4 transition-transform duration-300 ease-in-out group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>


                    <div class="accordion-content mt-1 ml-5 flex flex-col overflow-hidden">
                        <div class="relative pl-4 py-0.5 before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:border-l-2 before:border-two last:before:hidden after:content-[''] after:absolute after:left-0 after:top-0 after:w-3 after:h-1/2 after:border-l-2 after:border-b-2 after:border-two after:rounded-bl-md">
                            <a href="/products"
                               class="block px-3 py-2 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('products*') ? 'bg-three text-four font-medium' : 'text-four/70 hover:bg-two hover:text-four' }}">
                                Data Produk
                            </a>
                        </div>
                        <div class="relative pl-4 py-0.5 before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:border-l-2 before:border-two last:before:hidden after:content-[''] after:absolute after:left-0 after:top-0 after:w-3 after:h-1/2 after:border-l-2 after:border-b-2 after:border-two after:rounded-bl-md">
                            <a href="/categories"
                               class="block px-3 py-2 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('categories*') ? 'bg-three text-four font-medium' : 'text-four/70 hover:bg-two hover:text-four' }}">
                                Kategori Produk
                            </a>
                        </div>
                        <div data-roles="superadmin,admin,staff"
                             class="relative pl-4 py-0.5 before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:border-l-2 before:border-two last:before:hidden after:content-[''] after:absolute after:left-0 after:top-0 after:w-3 after:h-1/2 after:border-l-2 after:border-b-2 after:border-two after:rounded-bl-md">
                            <a href="/suppliers"
                               class="block px-3 py-2 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('suppliers*') ? 'bg-three text-four font-medium' : 'text-four/70 hover:bg-two hover:text-four' }}">
                                Data Pemasok
                            </a>
                        </div>
                    </div>
                </details>


                @php
                    $isTransaksiActive = request()->is('purchase-orders*', 'sales') || (request()->is('sales*') && !request()->is('sales/create'));
                @endphp
                <details class="group sidebar-accordion" {{ $isTransaksiActive ? 'open' : '' }}>
                    <summary class="group/summary flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer list-none transition-all duration-200 ease-out hover:translate-x-1 {{ $isTransaksiActive ? 'bg-two text-three font-semibold' : 'text-four/80 hover:bg-two hover:text-four' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover/summary:scale-110 group-hover/summary:text-three" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Transaksi & Pembelian</span>
                        </div>
                        <svg class="accordion-icon w-4 h-4 transition-transform duration-300 ease-in-out group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>

                    <div class="accordion-content mt-1 ml-5 flex flex-col overflow-hidden">
                        <div data-roles="superadmin,admin,staff"
                             class="relative pl-4 py-0.5 before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:border-l-2 before:border-two last:before:hidden after:content-[''] after:absolute after:left-0 after:top-0 after:w-3 after:h-1/2 after:border-l-2 after:border-b-2 after:border-two after:rounded-bl-md">
                            <a href="/purchase-orders"
                               class="block px-3 py-2 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('purchase-orders*') ? 'bg-three text-four font-medium' : 'text-four/70 hover:bg-two hover:text-four' }}">
                                Pesanan Pembelian (Stok Ulang)
                            </a>
                        </div>
                        <div class="relative pl-4 py-0.5 before:content-[''] before:absolute before:left-0 before:top-0 before:bottom-0 before:border-l-2 before:border-two last:before:hidden after:content-[''] after:absolute after:left-0 after:top-0 after:w-3 after:h-1/2 after:border-l-2 after:border-b-2 after:border-two after:rounded-bl-md">
                            <a href="/sales"
                               class="block px-3 py-2 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('sales') ? 'bg-three text-four font-medium' : 'text-four/70 hover:bg-two hover:text-four' }}">
                                Riwayat Penjualan
                            </a>
                        </div>
                    </div>
                </details>


                <a href="/stock-movements"
                   class="group/link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('stock-movements*') ? 'bg-three text-four font-semibold shadow-sm' : 'text-four/80 hover:bg-two hover:text-four' }}">
                    <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover/link:scale-110 {{ request()->is('stock-movements*') ? '' : 'group-hover/link:text-three' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    <span>Mutasi / Riwayat Stok</span>
                </a>

                <!-- MENU KHUSUS ADMIN & SUPERADMIN: KELOLA KARYAWAN & ROLE -->
                @php
                    $isAdminOrSuper = auth()->check() && in_array(auth()->user()->role, ['superadmin', 'admin']);
                @endphp
                <div data-roles="superadmin,admin" class="{{ $isAdminOrSuper ? '' : 'hidden' }} pt-3 space-y-1.5">
                    <p class="px-3 pt-1 pb-1 text-xs font-semibold uppercase tracking-wider text-four/50">Administrasi</p>

                    <a href="/users"
                       class="group/link flex items-center justify-between px-3 py-2.5 rounded-lg transition-all duration-200 ease-out hover:translate-x-1 {{ request()->is('users*') ? 'bg-three text-four font-semibold shadow-sm' : 'text-four/80 hover:bg-two hover:text-four' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover/link:scale-110 {{ request()->is('users*') ? '' : 'group-hover/link:text-three' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Kelola Karyawan</span>
                        </div>
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-two text-three border border-three/40 group-hover/link:bg-one/60 group-hover/link:text-four transition-colors">
                            Role
                        </span>
                    </a>
                </div>

            </div>
        </div>
    </nav>

    <div class="flex-1 min-w-0 flex flex-col h-full overflow-y-auto px-2 sm:px-3 transition-all duration-300 ease-in-out">
        <nav id="upperNav" class="bg-one border border-two/80 min-h-16 md:h-18 w-full px-3 sm:px-5 py-2.5 md:py-0 flex flex-wrap md:flex-nowrap items-center justify-between gap-2.5 md:gap-3 rounded-lg shadow-md relative z-30 shrink-0">

            <!-- 1. BAGIAN KIRI: TOMBOL SIDEBAR & BRAND MOBILE -->
            <div class="order-1 flex items-center gap-2.5 shrink-0">
                <!-- Tombol Buka/Tutup Sidebar -->
                <button id="toggleSidebar"
                        type="button"
                        title="Buka/Tutup Menu Samping"
                        aria-expanded="true"
                        class="shrink-0 group/btn p-2 sm:p-2.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:shadow-md active:scale-90 transition-all duration-200 cursor-pointer shadow-sm">
                    <svg id="toggleSidebarIcon" class="w-5 h-5 transition-transform duration-300 group-hover/btn:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h16" />
                    </svg>
                </button>

                <a href="/" class="md:hidden font-bold text-base tracking-tight select-none">
                    <span class="text-three">My</span><span class="text-four">Inventaris</span>
                </a>
            </div>

            <!-- 2. KOLOM PENCARIAN + DROPDOWN MENYATU (Baris 2 Full-Width di Mobile, Sebelah Kiri di Desktop) -->
            <div class="order-3 md:order-2 w-full md:w-80 md:focus-within:w-96 relative group transition-all duration-300 ease-out">
                <!-- Input & Ikon Pencarian (Sudut bawah otomatis rata 'rounded-b-none' saat dropdown muncul) -->
                <div class="flex items-center bg-two px-3 sm:px-3.5 py-2 rounded-lg group-focus-within:rounded-b-none border border-four/10 group-focus-within:border-three group-focus-within:border-b-one/50 transition-all duration-200 shadow-inner">
                    <svg class="w-4 h-4 text-three mr-2.5 shrink-0 transition-transform duration-300 group-focus-within:scale-110 group-focus-within:rotate-12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 10.5a7.5 7.5 0 0013.15 6.15z" />
                    </svg>
                    <input id="searchbar"
                           type="text"
                           autocomplete="off"
                           placeholder="Cari produk atau menu..."
                           class="w-full min-w-0 bg-transparent text-xs sm:text-sm text-four placeholder-four/50 focus:outline-none">
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-one/70 text-four/50 border border-four/10 transition-colors duration-200 group-focus-within:border-three/50 group-focus-within:text-three">
                        Cari
                    </span>
                </div>

                <!-- Dropdown Hasil Pencarian (Menyatu langsung dengan animasi slide & fade) -->
                <div id="searchDropdown"
                     class="max-h-[70vh] overflow-y-auto opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none group-focus-within:opacity-100 group-focus-within:translate-y-0 group-focus-within:scale-y-100 group-focus-within:pointer-events-auto transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl z-50">
                    <div class="px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-four/50 bg-one/35">
                        Hasil Pencarian Cepat
                    </div>

                    <!-- Contoh Item Hasil Pencarian (Nanti diisi dinamis pakai JS) -->
                    <a href="/products" class="group/item flex items-center justify-between px-4 py-2.5 text-sm text-four hover:bg-three hover:pl-5 transition-all duration-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-three group-hover/item:bg-four group-hover/item:scale-125 transition-all duration-200"></span>
                            <span>Data Produk</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded bg-one/50 text-four/70 group-hover/item:bg-one/70 group-hover/item:text-four transition-colors">Master Barang</span>
                    </a>

                    <a href="/sales/create" class="group/item flex items-center justify-between px-4 py-2.5 text-sm text-four hover:bg-three hover:pl-5 transition-all duration-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-three group-hover/item:bg-four group-hover/item:scale-125 transition-all duration-200"></span>
                            <span>Kasir / Transaksi Baru</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded bg-one/50 text-four/70 group-hover/item:bg-one/70 group-hover/item:text-four transition-colors">Menu Utama</span>
                    </a>

                    <a href="/stock-movements" class="group/item flex items-center justify-between px-4 py-2.5 text-sm text-four hover:bg-three hover:pl-5 transition-all duration-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-three group-hover/item:bg-four group-hover/item:scale-125 transition-all duration-200"></span>
                            <span>Mutasi / Riwayat Stok</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded bg-one/50 text-four/70 group-hover/item:bg-one/70 group-hover/item:text-four transition-colors">Inventaris</span>
                    </a>

                    <a href="/users" data-roles="superadmin,admin" class="{{ $isAdminOrSuper ? '' : 'hidden' }} group/item flex items-center justify-between px-4 py-2.5 text-sm text-four hover:bg-three hover:pl-5 transition-all duration-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-three group-hover/item:bg-four group-hover/item:scale-125 transition-all duration-200"></span>
                            <span>Kelola Karyawan & Role</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded bg-one/50 text-four/70 group-hover/item:bg-one/70 group-hover/item:text-four transition-colors">Administrasi</span>
                    </a>
                </div>
            </div>

            <!-- 3. BAGIAN KANAN: PROFIL PENGGUNA / AUTENTIKASI -->
            <div class="order-2 md:order-3 ml-auto flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Tampilan Jika Sudah Login (via Session atau Sanctum Token) -->
                <div id="navAuthProfile" class="{{ auth()->check() ? '' : 'hidden' }} flex items-center gap-2 sm:gap-2.5">
                    <!-- Kotak Dropdown Info Akun (Terpisah dari Foto) -->
                    <details id="navAuthDropdown" class="relative w-36 sm:w-48 group sidebar-accordion">
                        <summary class="flex items-center justify-between gap-2.5 cursor-pointer list-none px-3.5 py-1.5 rounded-lg bg-two border border-four/10 hover:border-three/70 group-open:rounded-b-none group-open:border-three group-open:border-b-one/50 transition-all duration-200 select-none shadow-inner">
                            <div class="min-w-0 text-left">
                                <p id="navUserName" class="text-xs font-semibold text-four truncate leading-snug">
                                    {{ auth()->user()->name ?? 'Pengguna' }}
                                </p>
                                <p id="navUserRole" class="text-[11px] text-three font-medium capitalize truncate leading-tight">
                                    {{ auth()->user()->role ?? 'staff' }}
                                </p>
                            </div>

                            <svg class="accordion-icon w-4 h-4 text-four/60 shrink-0 transition-transform duration-300 group-open:rotate-180 group-open:text-three" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>

                        <!-- Dropdown Menu Profil & Keluar (Menyatu presisi dengan kotak nama) -->
                        <div class="accordion-content absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl z-50 overflow-hidden">
                            <div class="px-3.5 py-2 bg-one/40 border-b border-one/60">
                                <p class="text-[10px] uppercase tracking-wider text-four/45">Email Akun</p>
                                <p id="navUserEmail" class="text-[11px] text-four/80 truncate mt-0.5">
                                    {{ auth()->user()->email ?? 'akun@inventaris.com' }}
                                </p>
                            </div>

                            <div class="py-1">
                                <a href="/profile"
                                   class="group/item flex items-center justify-between px-3.5 py-2 text-xs text-four hover:bg-three hover:pl-4.5 transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-three group-hover/item:text-four transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Pengaturan Akun</span>
                                    </div>
                                </a>

                                <a href="/users" data-roles="superadmin,admin"
                                   class="{{ $isAdminOrSuper ? '' : 'hidden' }} group/item flex items-center justify-between px-3.5 py-2 text-xs text-four hover:bg-three hover:pl-4.5 transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-three group-hover/item:text-four transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Kelola Karyawan</span>
                                    </div>
                                </a>
                            </div>

                            <div class="border-t border-one/60">
                                <a href="/logout" id="btnLogoutAction"
                                   class="group/item flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-medium text-three hover:bg-three hover:text-four hover:pl-4.5 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar Sesi</span>
                                </a>
                            </div>
                        </div>
                    </details>

                    <!-- Foto Profil Terpisah di Sebelah Kanan -->
                    <a href="/profile"
                       title="Lihat Profil"
                       class="shrink-0 p-1 rounded-lg bg-two border border-four/10 hover:border-three hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 shadow-sm">
                        <img id="navUserAvatar"
                             src="{{ auth()->check() && auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('image/blankFoto.png') }}"
                             alt="Foto Profil"
                             class="w-8 h-8 rounded-md object-cover bg-one">
                    </a>
                </div>

                <!-- Tampilan Jika Belum Masuk -->
                <div id="navGuestActions" class="{{ auth()->check() ? 'hidden' : '' }} flex items-center gap-1.5 sm:gap-2.5 text-xs sm:text-sm">
                    <a href="/login"
                       class="group/login flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg bg-two/80 border border-four/15 text-four hover:bg-two hover:border-three/70 hover:-translate-y-0.5 hover:shadow-md active:translate-y-0 active:scale-95 transition-all duration-200 font-medium shadow-sm">
                        <svg class="w-4 h-4 text-three transition-transform duration-200 group-hover/login:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Masuk</span>
                    </a>

                    <a href="/register"
                       class="group/reg flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg bg-three border border-four/20 text-four hover:brightness-110 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:scale-95 transition-all duration-200 font-semibold shadow-md">
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover/reg:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Daftar</span>
                    </a>
                </div>
            </div>

        </nav>

        <main class="flex-1 bg-one border border-two/80 mt-2 rounded-lg p-3 sm:p-5 lg:p-6 overflow-y-auto text-four shadow-md animate-fade-in">
            @yield('content')
        </main>
    </div>

    <!-- MODAL ALERT & KONFIRMASI KUSTOM GLOBAL -->
    <div id="globalDialogModal"
         class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200 select-none">
        <div id="globalDialogBackdrop" class="absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

        <div id="globalDialogCard"
             class="relative w-full max-w-md rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 bg-two/70 border-b border-two">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-5 rounded-full bg-three"></span>
                    <h3 id="globalDialogTitle" class="text-sm font-bold text-four">Konfirmasi Tindakan</h3>
                </div>
                <button type="button" id="globalDialogCloseBtn" class="p-1 rounded-lg bg-one/50 border border-four/10 text-four/70 hover:text-four hover:border-three/60 transition-all duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 flex items-start gap-3.5">
                <div class="p-2.5 rounded-lg bg-two border border-three/40 text-three shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <p id="globalDialogMessage" class="text-sm text-four/85 leading-relaxed pt-0.5"></p>
            </div>

            <div class="px-5 py-3.5 bg-two/40 border-t border-two flex items-center justify-end gap-2.5 text-sm">
                <button type="button" id="globalDialogCancelBtn"
                        class="px-4 py-2 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two active:scale-95 transition-all duration-200 cursor-pointer">
                    Batal
                </button>
                <button type="button" id="globalDialogConfirmBtn"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-three border border-four/20 text-four font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="globalDialogConfirmText">Ya, Lanjutkan</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // 1. Animasi Buka/Tutup Sidebar Responsif (Drawer di Mobile/Tablet, Collapsible di Desktop)
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const toggleSidebarBtn = document.getElementById('toggleSidebar');
        const toggleSidebarIcon = document.getElementById('toggleSidebarIcon');
        const isMobileViewport = () => window.innerWidth < 1024;

        function openMobileSidebar() {
            sidebar.classList.remove('-translate-x-full', 'w-0', 'border-r-0', 'border-y-0', 'opacity-0');
            sidebar.classList.add('translate-x-0', 'w-70', 'border-y', 'border-r');
            sidebarBackdrop?.classList.remove('hidden');
            toggleSidebarBtn.setAttribute('aria-expanded', 'true');
        }

        function closeMobileSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            sidebarBackdrop?.classList.add('hidden');
            toggleSidebarBtn.setAttribute('aria-expanded', 'false');
        }

        toggleSidebarBtn.addEventListener('click', () => {
            if (isMobileViewport()) {
                const isDrawerOpen = sidebar.classList.contains('translate-x-0');
                if (isDrawerOpen) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
                return;
            }

            const isClosed = sidebar.classList.contains('w-0');
            if (isClosed) {
                sidebar.classList.remove('w-0', 'border-r-0', 'border-y-0', 'opacity-0');
                sidebar.classList.add('w-70', 'border-y', 'border-r');
                toggleSidebarIcon.classList.remove('rotate-180');
                toggleSidebarBtn.setAttribute('aria-expanded', 'true');
            } else {
                sidebar.classList.remove('w-70', 'border-y', 'border-r');
                sidebar.classList.add('w-0', 'border-r-0', 'border-y-0', 'opacity-0');
                toggleSidebarIcon.classList.add('rotate-180');
                toggleSidebarBtn.setAttribute('aria-expanded', 'false');
            }
        });

        sidebarBackdrop?.addEventListener('click', closeMobileSidebar);

        // Tutup drawer otomatis saat link menu diklik di mode mobile
        sidebar?.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (isMobileViewport()) {
                    closeMobileSidebar();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (!isMobileViewport()) {
                sidebarBackdrop?.classList.add('hidden');
                sidebar.classList.remove('-translate-x-full', 'translate-x-0');
            }
        });

        // 2. Paksa ke halaman Login jika belum masuk dan mengklik apapun di Sidebar (kecuali Beranda '/')
        const searchDropdown = document.getElementById('searchDropdown');
        [sidebar, searchDropdown].forEach((container) => {
            container?.addEventListener('click', (e) => {
                const target = e.target.closest('a, summary, button');
                if (!target) return;

                // Izinkan klik ke Beranda ('/')
                if (target.tagName === 'A' && target.getAttribute('href') === '/') {
                    return;
                }

                const hasToken = Boolean(localStorage.getItem('access_token'));
                const hasServerUser = Boolean(window.SERVER_USER);

                if (!hasToken && !hasServerUser) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.location.href = '/login';
                }
            }, true);
        });

        // 3. Animasi Smooth Slide & Fade untuk semua Dropdown/Accordion (<details>)
        document.querySelectorAll('details.sidebar-accordion').forEach((details) => {
            const summary = details.querySelector('summary');
            const content = details.querySelector('.accordion-content');
            const icon = details.querySelector('.accordion-icon');
            let animation = null;

            summary.addEventListener('click', (e) => {
                e.preventDefault();

                if (animation) {
                    animation.cancel();
                }

                if (details.open) {
                    // Animasi Menutup
                    const startHeight = `${content.offsetHeight}px`;
                    icon?.classList.remove('rotate-180');

                    animation = content.animate(
                        [
                            { height: startHeight, opacity: 1, transform: 'translateY(0)' },
                            { height: '0px', opacity: 0, transform: 'translateY(-4px)' }
                        ],
                        { duration: 220, easing: 'cubic-bezier(0.4, 0, 0.2, 1)' }
                    );

                    animation.onfinish = () => {
                        details.removeAttribute('open');
                        animation = null;
                    };
                } else {
                    // Animasi Membuka
                    details.setAttribute('open', '');
                    icon?.classList.add('rotate-180');
                    const endHeight = `${content.offsetHeight}px`;

                    animation = content.animate(
                        [
                            { height: '0px', opacity: 0, transform: 'translateY(-4px)' },
                            { height: endHeight, opacity: 1, transform: 'translateY(0)' }
                        ],
                        { duration: 240, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' }
                    );

                    animation.onfinish = () => {
                        animation = null;
                    };
                }
            });
        });

        // 3. Tutup dropdown profil otomatis saat klik di luar
        document.addEventListener('click', (e) => {
            const profileDropdown = document.getElementById('navAuthDropdown');
            if (profileDropdown && profileDropdown.open && !profileDropdown.contains(e.target)) {
                profileDropdown.querySelector('summary')?.click();
            }
        });

        window.SERVER_USER = @json(auth()->user());

        // 4. Auto-inject data-label pada setiap cell tabel agar tampil sebagai card compact di mobile
        function applyTableMobileLabels(table) {
            const headers = Array.from(table.querySelectorAll('thead th')).map((th) => th.textContent.trim());
            if (!headers.length) return;

            table.querySelectorAll('tbody tr').forEach((row) => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 1 && cells[0].hasAttribute('colspan')) return;
                cells.forEach((td, idx) => {
                    if (headers[idx] && !td.hasAttribute('data-label')) {
                        td.setAttribute('data-label', headers[idx]);
                    }
                });
            });
        }

        const mainContainer = document.querySelector('main');
        if (mainContainer) {
            mainContainer.querySelectorAll('table').forEach(applyTableMobileLabels);
            const tableObserver = new MutationObserver(() => {
                mainContainer.querySelectorAll('table').forEach(applyTableMobileLabels);
            });
            tableObserver.observe(mainContainer, { childList: true, subtree: true });
        }
    </script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/globalSearch.js') }}"></script>
    @stack('scripts')
</body>
</html>
