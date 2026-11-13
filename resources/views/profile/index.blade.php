@extends('main.index')

@section('title', 'Profil Saya')

@section('content')
<div class="w-full space-y-6 pb-8">

    {{-- Top Hero Profile Card (Cover + Avatar ala Media Sosial) --}}
    <div class="bg-one border border-two rounded-lg shadow-lg overflow-hidden">
        {{-- Rectangular Cover / Background Banner --}}
        <div class="h-36 sm:h-56 bg-gradient-to-r from-two via-three/25 to-two border-b border-two relative group overflow-hidden">
            <img id="profileCoverImg" src="" alt="Foto Background"
                class="w-full h-full object-cover hidden transition-transform duration-300 group-hover:scale-[1.01]">

            {{-- Subtle dark gradient overlay for readability of top buttons --}}
            <div class="absolute inset-0 bg-gradient-to-t from-canvas/60 via-transparent to-canvas/35 pointer-events-none"></div>

            {{-- Clickable zoom area on cover --}}
            <button type="button" id="btnZoomMyCover" title="Lihat foto background"
                class="absolute inset-0 w-full h-full cursor-pointer focus:outline-none z-0"></button>

            {{-- Top-right action buttons --}}
            <div class="absolute top-3 right-4 z-10 flex items-center gap-2">
                <button type="button" id="btnQuickPickCover"
                    class="inline-flex items-center gap-1.5 bg-canvas/75 hover:bg-two text-four border border-two text-xs font-medium py-1.5 px-3 rounded-lg transition-all cursor-pointer backdrop-blur-sm shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Ganti Cover
                </button>
                <button type="button" id="btnProfileLogout"
                    class="inline-flex items-center gap-1.5 bg-canvas/75 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-medium py-1.5 px-3 rounded-lg transition-all cursor-pointer backdrop-blur-sm shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar Sesi
                </button>
            </div>
        </div>

        {{-- Profile Identity --}}
        <div class="px-4 sm:px-6 pb-5 sm:pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 -mt-12 sm:-mt-16 relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-4">
                    {{-- Circular Avatar with Zoom & Quick Edit --}}
                    <div class="relative shrink-0 self-start">
                        <button type="button" id="btnZoomMyAvatar" title="Lihat foto profil"
                            class="group relative w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-two border-4 border-one overflow-hidden flex items-center justify-center shadow-xl cursor-pointer focus:outline-none">
                            <img id="profileAvatarImg" src="" alt="Foto Profil" class="w-full h-full object-cover hidden">
                            <span id="profileAvatarInitials" class="text-3xl font-bold text-four tracking-wide">U</span>
                            <div class="absolute inset-0 bg-canvas/55 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg class="w-6 h-6 text-four" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                        </button>
                        <button type="button" id="btnQuickPickAvatar" title="Ganti foto profil"
                            class="absolute bottom-1 right-1 w-8 h-8 rounded-full bg-three hover:opacity-90 text-four border-2 border-one flex items-center justify-center shadow-md transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-1 pb-1">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 id="profileDisplayName" class="text-xl sm:text-2xl font-bold text-four">Memuat...</h2>
                            <span id="profileRoleBadge"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-two text-four/80 border border-two">
                                -
                            </span>
                        </div>
                        <p id="profileDisplayEmail" class="text-sm text-four/60">-</p>
                    </div>
                </div>

                {{-- Account Metadata Bar --}}
                <div class="grid grid-cols-3 gap-3 sm:gap-5 bg-canvas/60 border border-two rounded-lg px-4 py-2.5 text-xs">
                    <div>
                        <p class="text-four/45 uppercase tracking-wider text-[10px]">ID Akun</p>
                        <p id="profileMetaId" class="font-semibold text-four mt-0.5">#-</p>
                    </div>
                    <div>
                        <p class="text-four/45 uppercase tracking-wider text-[10px]">Terdaftar</p>
                        <p id="profileMetaCreated" class="font-medium text-four/90 mt-0.5">-</p>
                    </div>
                    <div>
                        <p class="text-four/45 uppercase tracking-wider text-[10px]">Diperbarui</p>
                        <p id="profileMetaUpdated" class="font-medium text-four/90 mt-0.5">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Settings Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column: Informasi Akun, Foto Profil & Cover --}}
        <div class="lg:col-span-7 bg-one border border-two rounded-lg shadow-lg p-5 sm:p-6">
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-two">
                <div class="w-9 h-9 rounded-lg bg-three/15 border border-three/30 flex items-center justify-center text-three shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-four">Informasi Akun & Tampilan Profil</h3>
                    <p class="text-xs text-four/55">Perbarui foto profil, foto background (cover), nama tampilan, dan email Anda.</p>
                </div>
            </div>

            <form id="profileInfoForm" class="space-y-5">
                {{-- Dual Photo Upload Controls: Circular Avatar & Rectangular Cover --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Circular Profile Photo Control --}}
                    <div class="p-4 rounded-lg bg-canvas/50 border border-two flex flex-col justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-two border border-two overflow-hidden flex items-center justify-center shrink-0">
                                <img id="profileEditPreviewImg" src="" alt="Preview Foto Profil" class="w-full h-full object-cover hidden">
                                <span id="profileEditPreviewInitials" class="text-lg font-bold text-four">U</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-four">Foto Profil</p>
                                <p id="profileSelectedFileName" class="text-xs text-four/55 truncate">
                                    JPG, PNG, WEBP (maks. 2 MB)
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="file" id="profile_avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                            <label for="profile_avatar"
                                class="inline-flex items-center gap-1.5 bg-two hover:bg-two/80 text-four text-xs font-medium py-2 px-3 rounded-lg border border-two transition-all cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Pilih Foto
                            </label>
                            <button type="button" id="btnClearSelectedAvatar"
                                class="hidden bg-canvas hover:bg-two text-four/70 hover:text-four text-xs font-medium py-2 px-3 rounded-lg border border-two transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>

                    {{-- Rectangular Cover / Background Photo Control --}}
                    <div class="p-4 rounded-lg bg-canvas/50 border border-two flex flex-col justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-24 h-14 rounded-lg bg-gradient-to-r from-two via-three/25 to-two border border-two overflow-hidden flex items-center justify-center shrink-0">
                                <img id="profileCoverPreviewImg" src="" alt="Preview Foto Background" class="w-full h-full object-cover hidden">
                                <svg id="profileCoverPreviewFallback" class="w-6 h-6 text-four/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-four">Foto Background</p>
                                <p id="profileSelectedCoverName" class="text-xs text-four/55 truncate">
                                    JPG, PNG, WEBP (maks. 4 MB)
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="file" id="profile_cover" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                            <label for="profile_cover"
                                class="inline-flex items-center gap-1.5 bg-two hover:bg-two/80 text-four text-xs font-medium py-2 px-3 rounded-lg border border-two transition-all cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Pilih Cover
                            </label>
                            <button type="button" id="btnClearSelectedCover"
                                class="hidden bg-canvas hover:bg-two text-four/70 hover:text-four text-xs font-medium py-2 px-3 rounded-lg border border-two transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="profile_name" class="block text-xs font-medium text-four/80 mb-1.5">Nama Lengkap</label>
                        <input type="text" id="profile_name" required placeholder="Masukkan nama lengkap"
                            class="w-full bg-canvas border border-two rounded-lg px-3.5 py-2.5 text-sm text-four placeholder-four/40 focus:outline-none focus:border-three transition-colors">
                    </div>

                    <div>
                        <label for="profile_email" class="block text-xs font-medium text-four/80 mb-1.5">Alamat Email</label>
                        <input type="email" id="profile_email" required placeholder="nama@email.com"
                            class="w-full bg-canvas border border-two rounded-lg px-3.5 py-2.5 text-sm text-four placeholder-four/40 focus:outline-none focus:border-three transition-colors">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" id="btnSaveProfileInfo"
                        class="inline-flex items-center gap-2 bg-three hover:opacity-90 text-four text-sm font-medium py-2.5 px-5 rounded-lg transition-all cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column: Keamanan & Kata Sandi --}}
        <div class="lg:col-span-5 bg-one border border-two rounded-lg shadow-lg p-5 sm:p-6">
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-two">
                <div class="w-9 h-9 rounded-lg bg-three/15 border border-three/30 flex items-center justify-center text-three shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-four">Keamanan & Kata Sandi</h3>
                    <p class="text-xs text-four/55">Gunakan kata sandi minimal 8 karakter.</p>
                </div>
            </div>

            <form id="profilePasswordForm" class="space-y-4">
                <div>
                    <label for="profile_password" class="block text-xs font-medium text-four/80 mb-1.5">Kata Sandi Baru</label>
                    <input type="password" id="profile_password" required minlength="8" placeholder="Minimal 8 karakter"
                        class="w-full bg-canvas border border-two rounded-lg px-3.5 py-2.5 text-sm text-four placeholder-four/40 focus:outline-none focus:border-three transition-colors">
                </div>

                <div>
                    <label for="profile_password_confirmation" class="block text-xs font-medium text-four/80 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" id="profile_password_confirmation" required minlength="8" placeholder="Ulangi kata sandi baru"
                        class="w-full bg-canvas border border-two rounded-lg px-3.5 py-2.5 text-sm text-four placeholder-four/40 focus:outline-none focus:border-three transition-colors">
                </div>

                <div class="p-3 rounded-lg bg-canvas/50 border border-two text-xs text-four/60 leading-relaxed">
                    Pastikan kombinasi huruf dan angka mudah Anda ingat namun sulit ditebak orang lain.
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" id="btnSaveProfilePassword"
                        class="inline-flex items-center gap-2 bg-three hover:opacity-90 text-four text-sm font-medium py-2.5 px-5 rounded-lg transition-all cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- Modal Preview Foto Profil / Background --}}
<div id="profileAvatarPreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-canvas/85 backdrop-blur-sm p-4">
    <div id="profileAvatarPreviewBackdrop" class="absolute inset-0"></div>
    <div class="relative z-10 w-full max-w-xl bg-one border border-two rounded-lg shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-two">
            <h4 id="profileAvatarPreviewTitle" class="text-sm font-semibold text-four truncate">Foto Profil</h4>
            <button type="button" id="btnCloseProfileAvatarPreview"
                class="text-four/60 hover:text-four p-1 rounded-lg hover:bg-two transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6 flex items-center justify-center bg-canvas/50 min-h-[280px]">
            <img id="profileAvatarPreviewImg" src="" alt="Preview Foto"
                class="max-h-96 w-auto rounded-lg object-contain border border-two shadow-md hidden">
            <div id="profileAvatarPreviewFallback"
                class="w-40 h-40 rounded-full bg-two border border-two flex items-center justify-center text-4xl font-bold text-four">
                U
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/profile.js') }}"></script>
@endpush
