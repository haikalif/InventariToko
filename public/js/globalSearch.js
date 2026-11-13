// Global Search Engine untuk #searchbar & #searchDropdown

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchbar');
    const searchDropdown = document.getElementById('searchDropdown');
    if (!searchInput || !searchDropdown) return;

    const MENU_ITEMS = [
        { title: 'Beranda', subtitle: 'Ringkasan dashboard & statistik toko', category: 'Menu Utama', url: '/', keywords: 'dashboard home statistik grafik' },
        { title: 'Kasir / Penjualan Baru', subtitle: 'Buat transaksi kasir baru', category: 'Menu Utama', url: '/sales/create', keywords: 'pos kasir transaksi bayar' },
        { title: 'Data Produk', subtitle: 'Kelola stok, harga, dan SKU produk', category: 'Master Barang', url: '/products', keywords: 'barang item sku stok harga' },
        { title: 'Kategori Produk', subtitle: 'Kelola pengelompokan kategori barang', category: 'Master Barang', url: '/categories', keywords: 'kategori jenis grup' },
        { title: 'Data Pemasok', subtitle: 'Kelola daftar supplier & kontak', category: 'Master Barang', url: '/suppliers', roles: ['superadmin', 'admin', 'staff'], keywords: 'supplier vendor distributor' },
        { title: 'Purchase Order (PO)', subtitle: 'Pemesanan barang ke pemasok', category: 'Transaksi & Stok', url: '/purchase-orders', roles: ['superadmin', 'admin', 'staff'], keywords: 'po pembelian restok kulakan' },
        { title: 'Riwayat Penjualan', subtitle: 'Daftar transaksi penjualan & struk', category: 'Transaksi & Stok', url: '/sales', keywords: 'riwayat invoice nota struk penjualan' },
        { title: 'Mutasi / Riwayat Stok', subtitle: 'Log keluar masuk pergerakan stok', category: 'Inventaris', url: '/stock-movements', keywords: 'mutasi log masuk keluar stok' },
        { title: 'Kelola Karyawan & Role', subtitle: 'Manajemen akun staff, kasir, dan admin', category: 'Administrasi', url: '/users', roles: ['superadmin', 'admin'], keywords: 'user pegawai karyawan akun role' },
        { title: 'Profil Saya', subtitle: 'Edit foto profil, cover, nama, dan sandi', category: 'Akun', url: '/profile', keywords: 'profil akun password avatar cover' },
    ];

    const defaultHtml = searchDropdown.innerHTML;
    let debounceTimer = null;
    let activeController = null;
    let activeIndex = -1;

    function escapeHtml(text) {
        return String(text ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function highlightText(text, query) {
        const safeText = escapeHtml(text);
        if (!query) return safeText;
        const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return safeText.replace(new RegExp(`(${escapedQuery})`, 'gi'), '<mark class="bg-three/35 text-four rounded px-0.5">$1</mark>');
    }

    function getCurrentRole() {
        try {
            const cached = JSON.parse(localStorage.getItem('user') || 'null');
            return (cached && cached.role) || (window.SERVER_USER && window.SERVER_USER.role) || null;
        } catch {
            return (window.SERVER_USER && window.SERVER_USER.role) || null;
        }
    }

    function filterMenus(query) {
        const q = query.toLowerCase();
        const role = getCurrentRole();

        return MENU_ITEMS.filter((item) => {
            if (item.roles && role && !item.roles.includes(role)) return false;
            return (
                item.title.toLowerCase().includes(q) ||
                item.subtitle.toLowerCase().includes(q) ||
                item.category.toLowerCase().includes(q) ||
                item.keywords.toLowerCase().includes(q)
            );
        });
    }

    function renderResults(query, menuMatches, apiResults, isLoading = false) {
        activeIndex = -1;
        const allItems = [...menuMatches, ...apiResults];

        let html = `
            <div class="px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-four/50 bg-one/35 flex items-center justify-between sticky top-0 backdrop-blur-xs">
                <span>Hasil Pencarian "${escapeHtml(query)}"</span>
                <span>${isLoading ? 'Mencari...' : `${allItems.length} ditemukan`}</span>
            </div>
        `;

        if (isLoading && allItems.length === 0) {
            html += `<div class="px-4 py-5 text-center text-xs text-four/60">Mencari di seluruh data...</div>`;
            searchDropdown.innerHTML = html;
            return;
        }

        if (allItems.length === 0) {
            html += `<div class="px-4 py-5 text-center text-xs text-four/60">Tidak ada hasil untuk "<span class="text-four font-medium">${escapeHtml(query)}</span>".</div>`;
            searchDropdown.innerHTML = html;
            return;
        }

        allItems.forEach((item) => {
            html += `
                <a href="${escapeHtml(item.url)}"
                   class="search-result-item group/item flex items-center justify-between gap-3 px-4 py-2.5 text-sm text-four hover:bg-three hover:pl-5 transition-all duration-150 border-t border-one/30 first:border-t-0">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-2 h-2 rounded-full bg-three group-hover/item:bg-four shrink-0 transition-all duration-150"></span>
                        <div class="min-w-0">
                            <p class="font-medium truncate leading-snug">${highlightText(item.title, query)}</p>
                            <p class="text-[11px] text-four/60 group-hover/item:text-four/85 truncate">${highlightText(item.subtitle, query)}</p>
                        </div>
                    </div>
                    <span class="shrink-0 text-[11px] px-2 py-0.5 rounded bg-one/55 text-four/75 group-hover/item:bg-one/80 group-hover/item:text-four transition-colors">
                        ${escapeHtml(item.category)}
                    </span>
                </a>
            `;
        });

        searchDropdown.innerHTML = html;
    }

    function updateKeyboardHighlight(items) {
        items.forEach((el, idx) => {
            if (idx === activeIndex) {
                el.classList.add('bg-three', 'pl-5');
                el.scrollIntoView({ block: 'nearest' });
            } else {
                el.classList.remove('bg-three', 'pl-5');
            }
        });
    }

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();
        clearTimeout(debounceTimer);

        if (activeController) {
            activeController.abort();
            activeController = null;
        }

        if (!query) {
            searchDropdown.innerHTML = defaultHtml;
            activeIndex = -1;
            return;
        }

        const menuMatches = filterMenus(query);
        const isAuthenticated = Boolean(localStorage.getItem('access_token') || window.SERVER_USER);

        if (!isAuthenticated) {
            renderResults(query, menuMatches, [], false);
            return;
        }

        renderResults(query, menuMatches, [], true);

        debounceTimer = setTimeout(async () => {
            try {
                activeController = new AbortController();
                const res = await fetchData(`/search?q=${encodeURIComponent(query)}`, {
                    signal: activeController.signal,
                });
                renderResults(query, menuMatches, (res && res.results) || [], false);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    renderResults(query, menuMatches, [], false);
                }
            }
        }, 220);
    });

    searchInput.addEventListener('keydown', (e) => {
        const items = Array.from(searchDropdown.querySelectorAll('a'));
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            updateKeyboardHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            updateKeyboardHighlight(items);
        } else if (e.key === 'Enter' && activeIndex >= 0 && items[activeIndex]) {
            e.preventDefault();
            items[activeIndex].click();
        } else if (e.key === 'Escape') {
            searchInput.blur();
        }
    });
});
