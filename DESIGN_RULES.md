c# Panduan Desain Anti-Slop (UI/UX Guidelines)

Dokumen ini adalah aturan wajib yang harus dipatuhi saat membuat atau memodifikasi tampilan frontend (Blade, CSS, dan JavaScript) pada proyek **MyInventaris**.

---

## 1. Konsistensi Desain (Design Consistency)
- Gunakan palet warna dari `@theme` di `resources/css/app.css`:
  - `bg-canvas` (`#0d1b2e`) untuk latar belakang layar utama.
  - `bg-one` (`#152c4a`) untuk panel utama (`#sidebar`, `#upperNav`, `main`, kartu modal).
  - `bg-two` (`#21436e`) untuk elemen interaktif sekunder (input, *toggle dropdown*, header tabel, efek *hover*).
  - `bg-three` / `text-three` / `border-three` (`#d45368`) untuk aksen utama, status aktif, dan *highlight*.
  - `text-four` (`#f2f8ff`) untuk teks utama (gunakan opasitas seperti `text-four/70` atau `text-four/50` untuk teks sekunder).
- Gunakan radius sudut yang seragam (`rounded-lg`), ketebalan border yang konsisten (`border-two/80` atau `border-four/10`), serta *spacing* yang rapi.

## 2. Dilarang Menggunakan Simbol Aneh pada Teks UI
- **JANGAN** menggunakan simbol dekoratif kaku seperti `-- Pilih Kategori --`, `=== Judul ===`, `***`, atau `[ Klik Disini ]`.
- Gunakan teks yang bersih dan natural, misalnya: `Pilih Kategori`, `Semua Kategori`, `Pilih Pemasok`, `Tanpa Barcode`.

## 3. Wajib Menggunakan Custom Scrollbar
- **JANGAN** membiarkan *scrollbar* bawaan sistem operasi tampil mentah.
- Seluruh elemen dengan `overflow-y-auto` atau `overflow-x-auto` menggunakan *custom scrollbar* tipis yang sudah dikustomisasi di `resources/css/app.css` dengan warna tema (`--color-one`, `--color-two`, dan `--color-three`).

## 4. Wajib Ada Animasi Halus (Micro-interactions)
- Setiap elemen interaktif wajib memiliki transisi atau animasi yang mulus:
  - Tombol & menu: transisi warna, pergeseran halus (`hover:translate-x-1` atau `hover:-translate-y-0.5`), dan efek tekan (`active:scale-95`).
  - Ikon: rotasi atau perubahan skala saat di-*hover* atau saat *state* berubah (`rotate-180`, `scale-110`).
  - Modal, *toast*, & *dropdown*: wajib menggunakan animasi *fade* + *slide/scale* (tidak boleh muncul/hilang secara patah dengan sekadar `hidden`/`block`).

## 5. Dropdown Harus Menyatu dengan Toggle-nya
- **JANGAN** menggunakan `<select>` bawaan browser yang tampilannya kaku dan terpisah dari desain aplikasi.
- Semua *dropdown* (pencarian, profil, maupun pilihan form/filter) harus menyatu langsung dengan tombol pemicunya (*toggle*):
  - Saat *dropdown* terbuka, sudut bawah pemicu berubah menjadi rata (`rounded-b-none`) dengan border aksen (`border-three`).
  - Panel *dropdown* menempel tepat di bawah pemicu (`top-full left-0 right-0`) tanpa celah, menggunakan `bg-two border-x border-b border-three rounded-b-lg shadow-2xl`.

## 6. Wajib Menggunakan SVG untuk Ikon & Tanda
- **JANGAN** menggunakan emoji (seperti 📦, ✅, ⚠️) atau karakter panah/simbol bawaan sistem (seperti `&larr;`, `&rarr;`, `×`, `▼`, `•` teks) untuk ikon UI.
- Gunakan elemen `<svg>` dengan `stroke="currentColor"` agar ketebalan garis, ukuran, warna, dan animasinya konsisten di semua perangkat.

## 7. Input Angka Tanpa Tombol Panah Bawaan (No Number Spinners)
- **JANGAN** menampilkan tombol panah atas/bawah (*spin buttons*) bawaan browser pada elemen `<input type="number">`.
- Seluruh `<input type="number">` wajib menyembunyikan `::-webkit-inner-spin-button` dan `::-webkit-outer-spin-button` serta menggunakan `-moz-appearance: textfield` di `resources/css/app.css`.

## 8. Alert & Dialog Konfirmasi Wajib Custom dan Konsisten
- **JANGAN PERNAH** menggunakan `window.alert()`, `window.confirm()`, atau `window.prompt()` bawaan browser.
- Gunakan komponen modal konfirmasi/alert kustom global (`customConfirm()` dan `customAlert()` di `public/js/api.js`) serta *toast notification* yang memiliki desain seragam (`bg-one`, `bg-two`, `border-two`, aksen `three`, teks `four`, ikon `<svg>`, dan animasi *fade + scale*).
- Untuk validasi form (seperti di halaman autentikasi), *real-time checker* / *alert* diletakkan langsung menyatu di bawah masing-masing input terkait (`rounded-b-none` pada input + bar *feedback* di bawahnya).

## 9. Halaman Login & Register Berdiri Mandiri (Standalone)
- Halaman autentikasi (`/login` dan `/register`) **JANGAN** menggunakan `@extends('main.index')` atau di-*render* di dalam `@yield('content')` halaman utama.
- Keduanya harus menjadi halaman HTML mandiri (*standalone*) di luar *sidebar* dan *upperNav* utama, namun tetap memakai tema, palet warna, animasi, dan aturan desain yang sama.

## 10. Input Nominal Rupiah Wajib Format Ribuan Otomatis (Real-time)
- Seluruh input harga/nominal uang (seperti `harga_beli`, `harga_jual`, biaya, atau pembayaran) wajib menggunakan `<input type="text" inputmode="numeric" data-rupiah>` dengan label/prefix `Rp`.
- Saat user mengetik angka, nilainya otomatis diformat dengan pemisah titik ribuan secara *real-time* (contoh: `15000` langsung berubah menjadi `15.000` melalui `initRupiahInputs()` dan `formatRibuan()` di `public/js/api.js`), lalu dikembalikan ke angka murni dengan `parseRupiahValue()` saat dikirim ke API.



