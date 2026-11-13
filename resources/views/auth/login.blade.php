<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <title>Masuk | MyInventaris</title>
</head>
<body class="bg-canvas min-h-screen flex flex-col items-center justify-center p-4 text-four select-none">

    <!-- Bar Atas: Brand Logo & Navigasi Kembali -->
    <div class="w-full max-w-md mb-3 flex items-center justify-between animate-fade-in">
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
            <span>Beranda</span>
        </a>
    </div>

    <!-- Kartu Utama Login Mandiri -->
    <form id="loginForm"
          novalidate
          class="w-full max-w-md rounded-lg bg-one border border-two/80 overflow-hidden shadow-2xl p-6 sm:p-7 space-y-5 animate-fade-in">

        <div class="border-b border-two pb-3.5 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-four tracking-tight">Masuk ke Sistem</h1>
                <p class="text-xs text-four/55 mt-0.5">Gunakan akun terdaftar untuk melanjutkan.</p>
            </div>
            <a href="/register"
               class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-two/80 border border-four/10 hover:border-three/70 text-xs text-four/80 hover:text-four transition-all duration-200">
                <span>Buat Akun</span>
                <svg class="w-3.5 h-3.5 text-three transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="space-y-4 text-sm">

            <!-- 1. INPUT ALAMAT EMAIL + REAL-TIME CHECKER DI BAWAHNYA -->
            <div>
                <label for="loginEmail" class="block text-xs font-medium text-four/75 mb-1">
                    Alamat Email
                </label>
                <div id="wrapLoginEmail" class="flex items-center bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200">
                    <svg class="w-4 h-4 text-three mr-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <input type="email"
                           id="loginEmail"
                           name="email"
                           required
                           autocomplete="email"
                           placeholder="nama@gmail.com"
                           class="w-full bg-transparent text-sm text-four placeholder-four/40 focus:outline-none select-text">
                </div>
                <div id="feedbackLoginEmail"
                     class="hidden px-3.5 py-2 bg-one/70 border-x border-b border-three rounded-b-lg text-xs text-four/90 flex items-center gap-2 transition-all duration-200">
                </div>
            </div>

            <!-- 2. INPUT PASSWORD + REAL-TIME CHECKER / ALERT DI BAWAHNYA -->
            <div>
                <label for="loginPassword" class="block text-xs font-medium text-four/75 mb-1">
                    Password
                </label>
                <div id="wrapLoginPassword" class="flex items-center bg-two px-3.5 py-2 rounded-lg border border-four/10 focus-within:border-three transition-all duration-200">
                    <svg class="w-4 h-4 text-three mr-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <input type="password"
                           id="loginPassword"
                           name="password"
                           required
                           placeholder="Masukkan password akun..."
                           class="w-full bg-transparent text-sm text-four placeholder-four/40 focus:outline-none select-text">
                    <button type="button"
                            data-toggle-password="loginPassword"
                            class="ml-2 text-four/50 hover:text-four transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <div id="feedbackLoginPassword"
                     class="hidden px-3.5 py-2 bg-one/70 border-x border-b border-three rounded-b-lg text-xs text-four/90 flex items-center gap-2 transition-all duration-200">
                </div>
            </div>

            <!-- 3. OPSI INGAT SAYA -->
            <div class="flex items-center justify-between pt-1">
                <label for="rememberMe" class="inline-flex items-center gap-2 text-xs text-four/75 hover:text-four cursor-pointer">
                    <input type="checkbox" id="rememberMe" name="remember_me" class="w-4 h-4 rounded accent-three cursor-pointer">
                    <span>Ingat sesi masuk saya</span>
                </label>
            </div>

        </div>

        <div class="pt-2">
            <button type="submit"
                    id="btnSubmitLogin"
                    class="w-full flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-three border border-four/20 text-four text-sm font-semibold hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span id="btnSubmitLoginText">Masuk Sekarang</span>
            </button>
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
    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
