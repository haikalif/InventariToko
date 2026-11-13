<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
    <title>Daftar Akun | MyInventaris</title>
    <style>
        .auth-shell-body {
            min-height: 100vh;
            min-height: 100dvh;
            padding-top: max(1rem, env(safe-area-inset-top));
            padding-bottom: max(1.5rem, env(safe-area-inset-bottom));
        }
    </style>
</head>
<body class="bg-canvas auth-shell-body min-h-dvh flex flex-col items-center justify-center p-4 text-four select-none">

    <!-- Bar Atas: Brand Logo & Navigasi Kembali -->
    <div class="w-full max-w-3xl mb-3 flex items-center justify-between animate-fade-in">
        <a href="/" class="group/logo inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-one border border-two/80 hover:border-three/60 transition-all duration-200 shadow-md">
            <span class="text-three font-semibold text-xl">My</span>
            <span class="text-four font-bold text-xl">Inventaris</span>
            <svg class="ml-1 animate-float transition-transform duration-300 group-hover/logo:scale-110 group-hover/logo:rotate-6" width="28" height="28" viewBox="0 0 32 32" fill="none">
                <path d="M 4 14 C 4 6, 15 2, 23 6" class="stroke-three" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3" />
                <path d="M 16 7 L 6 12 L 16 17 L 26 12 Z" class="fill-four" />
                <path d="M 11 9.5 L 21 14.5 L 23 13.5 L 13 8.5 Z" class="fill-three" />
                <path d="M 6 12 V 23 L 16 28 V 17 Z" class="fill-two" />
                <path d="M 16 17 V 28 L 26 23 V 12 Z" class="fill-one" />
                <path d="M 3 19 C 5 29, 20 31, 28 20" class="stroke-three" stroke-width="2.5" stroke-linecap="round" />
                <path d="M 24 19 L 28.5 19.5 L 29 24" class="stroke-three" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>

        <a href="/"
           class="group inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-one border border-two/80 hover:border-three/60 text-xs text-four/80 hover:text-four transition-all duration-200 shadow-md">
            <svg class="w-3.5 h-3.5 text-three transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <!-- Kartu Utama Registrasi Mandiri -->
    <form id="registerForm"
          enctype="multipart/form-data"
          novalidate
          class="w-full max-w-3xl rounded-lg bg-one border border-two/80 overflow-hidden shadow-2xl flex flex-col md:flex-row animate-fade-in">

        <!-- PANEL KIRI: BINGKAI FOTO PROFIL INTERAKTIF -->
        <div class="md:w-64 bg-two/45 border-b md:border-b-0 md:border-r border-two p-6 flex flex-col items-center justify-between">
            <div class="w-full text-center">
                <p class="text-xs font-semibold uppercase tracking-wider text-four/60">Foto Profil</p>
                <p class="text-[11px] text-four/45 mt-0.5">Klik bingkai untuk memilih foto</p>
            </div>

            <div class="my-6 flex flex-col items-center w-full">
                <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp" class="hidden">

                <label for="avatarInput"
                       class="group relative w-36 h-36 rounded-lg bg-one border-2 border-dashed border-four/20 hover:border-three p-1.5 cursor-pointer transition-all duration-300 shadow-inner flex items-center justify-center overflow-hidden">
                    <img id="avatarPreview"
                         src="{{ asset('image/blankFoto.png') }}"
                         alt="Foto Profil"
                         class="w-full h-full rounded-md object-cover transition-transform duration-300 group-hover:scale-105">

                    <div class="absolute inset-0 bg-canvas/75 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center gap-1.5 text-four">
                        <svg class="w-6 h-6 text-three transition-transform duration-200 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span class="text-[11px] font-medium">Ubah Foto</span>
                    </div>
                </label>

                <button type="button"
                        id="btnRemoveAvatar"
                        class="hidden mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-one border border-three/40 text-[11px] text-three hover:bg-three hover:text-four active:scale-95 transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Hapus Foto</span>
                </button>

                <!-- Alert Khusus di Bawah Input Foto -->
                <div id="avatarFeedback" class="hidden mt-2.5 w-full px-3 py-1.5 rounded-lg bg-one border border-three text-[11px] text-three text-center animate-fade-in"></div>
            </div>

            <div class="w-full text-center border-t border-two pt-3">
                <span id="avatarFileName" class="block text-[11px] text-four/50 truncate">Opsional (Maks. 2 MB)</span>
            </div>
        </div>

        <!-- PANEL KANAN: FORM INPUT VERTIKAL DENGAN REAL-TIME CHECKER DI BAWAH TIAP INPUT -->
        <div class="flex-1 p-6 sm:p-7 flex flex-col justify-between space-y-5">
            <div>
                <div class="border-b border-two pb-3.5 mb-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-four tracking-tight">Buat Akun Baru</h1>
                        <p class="text-xs text-four/55 mt-0.5">Lengkapi data diri untuk mendaftar ke sistem.</p>
                    </div>
                    <a href="/login"
                       class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-two/80 border border-four/10 hover:border-three/70 text-xs text-four/80 hover:text-four transition-all duration-200">
                        <span>Halaman Masuk</span>
                        <svg class="w-3.5 h-3.5 text-three transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="space-y-4 text-sm">

                    <!-- 1. INPUT NAMA LENGKAP + REAL-TIME CHECKER DI BAWAHNYA -->
                    <div>
                        <label for="regName" class="block text-xs font-medium text-four/75 mb-1">
                            Nama Lengkap
                        </label>
                        <div id="wrapName" class="flex items-center bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200">
                            <svg class="w-4 h-4 text-three mr-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <input type="text"
                                   id="regName"
                                   name="name"
                                   required
                                   autocomplete="off"
                                   placeholder="Masukkan nama pengguna..."
                                   class="w-full bg-transparent text-sm text-four placeholder-four/40 focus:outline-none select-text">
                        </div>
                        <div id="feedbackName"
                             class="hidden px-3.5 py-2 bg-one/70 border-x border-b border-three rounded-b-lg text-xs text-four/90 flex items-center gap-2 transition-all duration-200">
                        </div>
                    </div>

                    <!-- 2. INPUT EMAIL + REAL-TIME CHECKER DI BAWAHNYA -->
                    <div>
                        <label for="regEmail" class="block text-xs font-medium text-four/75 mb-1">
                            Alamat Email
                        </label>
                        <div id="wrapEmail" class="flex items-center bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200">
                            <svg class="w-4 h-4 text-three mr-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input type="email"
                                   id="regEmail"
                                   name="email"
                                   required
                                   autocomplete="off"
                                   placeholder="nama@gmail.com"
                                   class="w-full bg-transparent text-sm text-four placeholder-four/40 focus:outline-none select-text">
                        </div>
                        <div id="feedbackEmail"
                             class="hidden px-3.5 py-2 bg-one/70 border-x border-b border-three rounded-b-lg text-xs text-four/90 flex items-center gap-2 transition-all duration-200">
                        </div>
                    </div>

                    <!-- 3. INPUT PASSWORD + REAL-TIME SYARAT DI BAWAHNYA -->
                    <div>
                        <label for="regPassword" class="block text-xs font-medium text-four/75 mb-1">
                            Password
                        </label>
                        <div id="wrapPassword" class="flex items-center bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200">
                            <svg class="w-4 h-4 text-three mr-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input type="password"
                                   id="regPassword"
                                   name="password"
                                   required
                                   placeholder="Minimal 8 karakter kombinasi huruf & angka..."
                                   class="w-full bg-transparent text-sm text-four placeholder-four/40 focus:outline-none select-text">
                            <button type="button"
                                    data-toggle-password="regPassword"
                                    class="ml-2 text-four/50 hover:text-four transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div id="feedbackPassword"
                             class="hidden px-3.5 py-2 bg-one/70 border-x border-b border-three rounded-b-lg text-xs space-y-1 transition-all duration-200">
                        </div>
                    </div>

                    <!-- 4. INPUT KONFIRMASI PASSWORD + REAL-TIME CHECKER DI BAWAHNYA -->
                    <div>
                        <label for="regPasswordConfirm" class="block text-xs font-medium text-four/75 mb-1">
                            Konfirmasi Password
                        </label>
                        <div id="wrapPasswordConfirm" class="flex items-center bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200">
                            <svg class="w-4 h-4 text-three mr-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <input type="password"
                                   id="regPasswordConfirm"
                                   name="password_confirmation"
                                   required
                                   placeholder="Ketik ulang password..."
                                   class="w-full bg-transparent text-sm text-four placeholder-four/40 focus:outline-none select-text">
                            <button type="button"
                                    data-toggle-password="regPasswordConfirm"
                                    class="ml-2 text-four/50 hover:text-four transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div id="feedbackPasswordConfirm"
                             class="hidden px-3.5 py-2 bg-one/70 border-x border-b border-three rounded-b-lg text-xs text-four/90 flex items-center gap-2 transition-all duration-200">
                        </div>
                    </div>

                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        id="btnSubmitRegister"
                        class="w-full flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="btnSubmitRegisterText">Daftar Sekarang</span>
                </button>
            </div>
        </div>

    </form>

    <!-- MODAL ALERT & KONFIRMASI KUSTOM GLOBAL -->
    <div id="globalDialogModal"
         class="hidden opacity-0 fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-200 select-none">
        <div id="globalDialogBackdrop" class="absolute inset-0 bg-canvas/80 backdrop-blur-xs"></div>

        <div id="globalDialogCard"
             class="relative w-full max-w-md rounded-lg bg-one border border-two shadow-2xl transform scale-95 translate-y-4 transition-all duration-200 z-10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 bg-two/70 border-b border-two">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-5 rounded-full bg-three"></span>
                    <h3 id="globalDialogTitle" class="text-sm font-bold text-four">Pemberitahuan</h3>
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
                    <span id="globalDialogConfirmText">Mengerti</span>
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>
