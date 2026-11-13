/**
 * public/js/sales.js
 * Pengirim request & pengelola UI halaman Riwayat Penjualan (/sales)
 * Menggunakan helper fetchData(), customConfirm(), formatRibuan(), parseRupiahValue(), initRupiahInputs() dari public/js/api.js
 */

const SalesHistoryPage = (() => {
    let state = {
        currentPage: 1,
        meta: null,
        sales: [],
        products: [],
        searchQuery: "",
        statusFilter: "",
        methodFilter: "",
    };

    const el = {};

    function initDomRefs() {
        el.tableBody = document.getElementById("saleTableBody");
        el.paginationInfo = document.getElementById("paginationInfo");
        el.btnPrevPage = document.getElementById("btnPrevPage");
        el.btnNextPage = document.getElementById("btnNextPage");

        el.searchInput = document.getElementById("filterSearchSale");
        el.dropdownStatus = document.getElementById("dropdownFilterSaleStatus");
        el.dropdownMethod = document.getElementById("dropdownFilterSaleMethod");
        el.btnRefresh = document.getElementById("btnRefreshSales");

        el.statTotal = document.getElementById("statTotalSales");
        el.statCompleted = document.getElementById("statCompletedSales");
        el.statPending = document.getElementById("statPendingSales");
        el.statRevenue = document.getElementById("statPageRevenue");

        el.detailModal = document.getElementById("saleDetailModal");
        el.detailCard = document.getElementById("saleDetailCard");
        el.detailContent = document.getElementById("saleDetailContent");
        el.btnCloseDetail = document.querySelectorAll(".btn-close-sale-detail-modal");

        el.toastContainer = document.getElementById("toastContainer");
    }

    // 1. Custom Dropdown Menyatu dengan Toggle
    function setupCustomDropdown(container, onSelectCallback = null) {
        if (!container || container.dataset.bound === "true") return;
        container.dataset.bound = "true";

        const toggleBtn = container.querySelector("[data-dropdown-toggle]");
        const menu = container.querySelector("[data-dropdown-menu]");
        const hiddenInput = container.querySelector("input[type='hidden']");
        const labelEl = container.querySelector("[data-dropdown-label]");
        const iconEl = container.querySelector("[data-dropdown-icon]");

        toggleBtn?.addEventListener("click", (e) => {
            e.stopPropagation();
            const isOpen = container.dataset.open === "true";
            closeAllCustomDropdowns();
            if (!isOpen) {
                openDropdown(container, toggleBtn, menu, iconEl);
            }
        });

        menu?.addEventListener("click", (e) => {
            const optionBtn = e.target.closest("[data-value]");
            if (!optionBtn) return;

            const val = optionBtn.dataset.value;
            const text = optionBtn.dataset.label;

            if (hiddenInput) hiddenInput.value = val;
            if (labelEl) labelEl.textContent = text;

            closeDropdown(container, toggleBtn, menu, iconEl);
            if (typeof onSelectCallback === "function") {
                onSelectCallback(val, text, optionBtn);
            }
        });
    }

    function openDropdown(container, toggleBtn, menu, iconEl) {
        container.dataset.open = "true";
        toggleBtn?.classList.add("rounded-b-none", "border-three", "border-b-one/50");
        iconEl?.classList.add("rotate-180");
        menu?.classList.remove("opacity-0", "-translate-y-2", "scale-y-95", "pointer-events-none");
        menu?.classList.add("opacity-100", "translate-y-0", "scale-y-100", "pointer-events-auto");
    }

    function closeDropdown(container, toggleBtn, menu, iconEl) {
        container.dataset.open = "false";
        toggleBtn?.classList.remove("rounded-b-none", "border-three", "border-b-one/50");
        iconEl?.classList.remove("rotate-180");
        menu?.classList.remove("opacity-100", "translate-y-0", "scale-y-100", "pointer-events-auto");
        menu?.classList.add("opacity-0", "-translate-y-2", "scale-y-95", "pointer-events-none");
    }

    function closeAllCustomDropdowns() {
        document.querySelectorAll("[data-open='true']").forEach((container) => {
            const toggleBtn = container.querySelector("[data-dropdown-toggle]");
            const menu = container.querySelector("[data-dropdown-menu]");
            const iconEl = container.querySelector("[data-dropdown-icon]");
            closeDropdown(container, toggleBtn, menu, iconEl);
        });
    }

    // 2. Pengambilan Data API
    async function fetchAllPaginated(baseEndpoint) {
        const firstRes = await fetchData(`${baseEndpoint}?page=1`, { method: "GET" });
        const items = [...(firstRes?.data || [])];
        const totalPages = firstRes?.meta?.total_pages || 1;

        if (totalPages > 1) {
            const requests = [];
            for (let p = 2; p <= totalPages; p++) {
                requests.push(fetchData(`${baseEndpoint}?page=${p}`, { method: "GET" }));
            }
            const rest = await Promise.all(requests);
            rest.forEach((res) => {
                if (Array.isArray(res?.data)) items.push(...res.data);
            });
        }
        return items;
    }

    async function loadProducts() {
        try {
            const products = await fetchAllPaginated("/products");
            state.products = products.filter((p) => p.aktif !== false);
        } catch (_) {}
    }

    async function loadSales(page = 1) {
        state.currentPage = page;
        renderLoadingTable();

        const token = localStorage.getItem("access_token");
        if (!token && !window.SERVER_USER) {
            window.location.href = "/login";
            return;
        }

        try {
            const response = await fetchData(`/sales?page=${page}`, { method: "GET" });
            state.sales = response?.data || [];
            state.meta = response?.meta || null;

            updateStats();
            renderTable();
            renderPagination();
        } catch (error) {
            renderEmptyTable(error.message || "Gagal memuat data penjualan.");
            showToast(error.message || "Gagal memuat data penjualan", "error");
        }
    }

    async function fetchSaleDetail(id) {
        try {
            const response = await fetchData(`/sales/${id}`, { method: "GET" });
            return response?.data;
        } catch (error) {
            showToast(error.message || "Gagal mengambil rincian transaksi", "error");
            throw error;
        }
    }

    async function deleteSale(id, nomorTrx) {
        const confirmed = await customConfirm({
            title: "Hapus Transaksi Pending",
            message: `Transaksi "${nomorTrx}" akan dihapus. Hanya transaksi berstatus Pending yang dapat dihapus. Lanjutkan?`,
            confirmText: "Ya, Hapus",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/sales/${id}`, { method: "DELETE" });
            showToast(res?.message || "Transaksi berhasil dihapus", "success");
            loadSales(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus transaksi", "error");
        }
    }

    // 3. Render Statistik, Tabel & Pagination
    const STATUS_LABELS = {
        pending: "Pending",
        selesai: "Selesai",
        dibatalkan: "Dibatalkan",
    };

    function getStatusBadge(status) {
        const label = STATUS_LABELS[status] || status || "-";
        if (status === "selesai") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-two border border-four/20 text-xs font-semibold text-four">${label}</span>`;
        }
        if (status === "dibatalkan") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-three/20 border border-three/50 text-xs font-semibold text-three">${label}</span>`;
        }
        return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-two border border-three/40 text-xs font-semibold text-three">${label}</span>`;
    }

    function updateStats() {
        const list = state.sales;
        const total = state.meta?.total ?? list.length;
        const completed = list.filter((s) => s.status === "selesai").length;
        const pending = list.filter((s) => s.status === "pending").length;
        const revenue = list
            .filter((s) => s.status !== "dibatalkan")
            .reduce((sum, s) => sum + Number(s.total || 0), 0);

        if (el.statTotal) el.statTotal.textContent = total;
        if (el.statCompleted) el.statCompleted.textContent = completed;
        if (el.statPending) el.statPending.textContent = pending;
        if (el.statRevenue) el.statRevenue.textContent = formatRupiah(revenue);
    }

    function renderLoadingTable() {
        if (!el.tableBody) return;
        el.tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-four/60">
                    <div class="inline-flex items-center gap-3">
                        <svg class="w-5 h-5 animate-spin text-three" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memuat data penjualan...</span>
                    </div>
                </td>
            </tr>
        `;
    }

    function renderEmptyTable(message) {
        if (!el.tableBody) return;
        el.tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-four/60">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <svg class="w-9 h-9 text-four/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm">${escapeHtml(message)}</p>
                    </div>
                </td>
            </tr>
        `;
    }

    function renderTable() {
        if (!el.tableBody) return;

        const q = state.searchQuery.toLowerCase().trim();
        const filtered = state.sales.filter((item) => {
            const cashierName = item.user?.name || "";
            const matchSearch =
                !q ||
                (item.nomor_transaksi || "").toLowerCase().includes(q) ||
                cashierName.toLowerCase().includes(q) ||
                (item.metode_pembayaran || "").toLowerCase().includes(q);

            const matchStatus = !state.statusFilter || item.status === state.statusFilter;
            const matchMethod = !state.methodFilter || item.metode_pembayaran === state.methodFilter;
            return matchSearch && matchStatus && matchMethod;
        });

        if (filtered.length === 0) {
            renderEmptyTable("Belum ada transaksi penjualan yang sesuai.");
            return;
        }

        const canDelete = typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false;

        el.tableBody.innerHTML = filtered
            .map((item) => {
                const itemCount = Array.isArray(item.items) ? item.items.length : 0;
                const isPending = item.status === "pending";

                return `
                    <tr class="hover:bg-two/40 transition-colors duration-150">
                        <td class="px-4 py-3.5">
                            <div class="font-mono text-xs text-three font-semibold">${escapeHtml(item.nomor_transaksi)}</div>
                            <div class="text-xs text-four/60 mt-0.5">${escapeHtml(item.tanggal || "-")}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-medium text-four">${escapeHtml(item.user?.name || "-")}</div>
                            <div class="text-xs text-four/50 capitalize">${escapeHtml(item.user?.role || "")}</div>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-four/80 font-medium">
                            ${escapeHtml(item.metode_pembayaran || "-")}
                        </td>
                        <td class="px-4 py-3.5">
                            ${getStatusBadge(item.status)}
                        </td>
                        <td class="px-4 py-3.5 text-xs text-four/80 font-medium">
                            ${itemCount} item
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-four">
                            ${formatRupiah(item.total)}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <button type="button" data-action="detail" data-id="${escapeHtml(item.id)}"
                                        title="Rincian Transaksi"
                                        class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                ${
                                    canDelete && isPending
                                        ? `<button type="button" data-action="delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nomor_transaksi)}"
                                                title="Hapus Transaksi Pending"
                                                class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-three hover:bg-three hover:text-four hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>`
                                        : ""
                                }
                            </div>
                        </td>
                    </tr>
                `;
            })
            .join("");
    }

    function renderPagination() {
        if (!state.meta) {
            if (el.paginationInfo) el.paginationInfo.textContent = "Menampilkan 0 data";
            if (el.btnPrevPage) el.btnPrevPage.disabled = true;
            if (el.btnNextPage) el.btnNextPage.disabled = true;
            return;
        }
        const { current_page, total_pages, total, count } = state.meta;
        if (el.paginationInfo) {
            el.paginationInfo.textContent = `Halaman ${current_page} dari ${total_pages || 1} (${count} dari total ${total} transaksi)`;
        }
        if (el.btnPrevPage) el.btnPrevPage.disabled = current_page <= 1;
        if (el.btnNextPage) el.btnNextPage.disabled = current_page >= total_pages;
    }

    // 4. Modal Detail & Kelola Transaksi
    async function openDetailModal(id) {
        try {
            const sale = await fetchSaleDetail(id);
            if (!sale) return;
            renderDetailContent(sale);

            el.detailModal.classList.remove("hidden");
            requestAnimationFrame(() => {
                el.detailModal.classList.remove("opacity-0");
                el.detailCard.classList.remove("scale-95", "translate-y-4");
                el.detailCard.classList.add("scale-100", "translate-y-0");
            });
        } catch (_) {}
    }

    function renderDetailContent(sale) {
        if (!el.detailContent) return;

        const isPending = sale.status === "pending";
        const isCancelled = sale.status === "dibatalkan";
        const canUpdateStatus = typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false;
        const items = Array.isArray(sale.items) ? sale.items : [];

        const productOptionsHtml = state.products
            .filter((p) => Number(p.stok || 0) > 0)
            .map(
                (p) => `
                <button type="button"
                        data-value="${escapeHtml(p.id)}"
                        data-label="${escapeHtml(`${p.nama_produk} (Stok ${p.stok})`)}"
                        data-harga="${escapeHtml(Math.round(Number(p.harga_jual || 0)))}"
                        class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center gap-2.5 truncate">
                        <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                        <span class="truncate">${escapeHtml(p.nama_produk)} (Stok ${p.stok})</span>
                    </div>
                    <span class="text-xs text-four/60 group-hover/opt:text-four shrink-0 ml-2">${formatRupiah(p.harga_jual)}</span>
                </button>
            `
            )
            .join("");

        el.detailContent.innerHTML = `
            <!-- Header Transaksi -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-two">
                <div>
                    <span class="font-mono text-xs text-three font-semibold">${escapeHtml(sale.nomor_transaksi)}</span>
                    <h3 class="text-lg font-bold text-four mt-0.5">${escapeHtml(sale.metode_pembayaran || "Tanpa Metode")}</h3>
                    <p class="text-xs text-four/60 mt-1">Tanggal: ${escapeHtml(sale.tanggal || "-")} • Kasir: ${escapeHtml(sale.user?.name || "-")}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    ${getStatusBadge(sale.status)}
                    ${
                        canUpdateStatus && isPending
                            ? `<button type="button" id="btnCompletePendingSale"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-three border border-four/20 text-xs font-semibold text-four hover:brightness-110 active:scale-95 transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Selesaikan</span>
                            </button>`
                            : ""
                    }
                    ${
                        canUpdateStatus && !isCancelled
                            ? `<button type="button" id="btnCancelSale"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-two border border-three/50 text-xs font-semibold text-three hover:bg-three hover:text-four active:scale-95 transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Batalkan</span>
                            </button>`
                            : ""
                    }
                </div>
            </div>

            <!-- Daftar Item -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-four/60">Daftar Item Terjual (${items.length})</h4>
                    <span class="text-sm font-bold text-three">Total: ${formatRupiah(sale.total)}</span>
                </div>

                ${
                    items.length === 0
                        ? `<div class="p-6 text-center rounded-lg bg-two/30 border border-two text-xs text-four/60">Belum ada item dalam transaksi ini.</div>`
                        : `<div class="rounded-lg border border-two overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-two/70 border-b border-two text-four/60 uppercase">
                                        <th class="px-3.5 py-2.5">Produk</th>
                                        <th class="px-3.5 py-2.5 text-center">Qty</th>
                                        <th class="px-3.5 py-2.5">Harga Satuan</th>
                                        <th class="px-3.5 py-2.5">Subtotal</th>
                                        ${isPending ? `<th class="px-3.5 py-2.5 text-right">Aksi</th>` : ""}
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-two/60">
                                    ${items
                                        .map(
                                            (it) => `
                                        <tr class="hover:bg-two/30">
                                            <td class="px-3.5 py-2.5">
                                                <div class="font-semibold text-four">${escapeHtml(it.product?.nama_produk || it.product_id)}</div>
                                                <div class="font-mono text-[11px] text-four/50">${escapeHtml(it.product?.sku || "")}</div>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-center font-semibold text-four">${escapeHtml(it.jumlah)}</td>
                                            <td class="px-3.5 py-2.5 text-four/80">${formatRupiah(it.harga_satuan)}</td>
                                            <td class="px-3.5 py-2.5 font-semibold text-four">${formatRupiah(it.subtotal)}</td>
                                            ${
                                                isPending
                                                    ? `<td class="px-3.5 py-2.5 text-right">
                                                        <button type="button" data-delete-sale-item="${escapeHtml(it.id)}"
                                                                class="p-1 rounded-lg text-three hover:bg-three hover:text-four active:scale-95 transition-all cursor-pointer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </td>`
                                                    : ""
                                            }
                                        </tr>
                                    `
                                        )
                                        .join("")}
                                </tbody>
                            </table>
                        </div>`
                }
            </div>

            <!-- Form Tambah Item ke Transaksi Pending -->
            ${
                isPending
                    ? `<form id="saleAddItemForm" class="p-4 rounded-lg bg-two/40 border border-two space-y-3">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-four/70">Tambah Item ke Transaksi Pending</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-5">
                                <label class="block text-[11px] font-semibold text-four/60 mb-1">Produk</label>
                                <div id="dropdownSaleItemProduct" class="relative select-none">
                                    <input type="hidden" id="sale_item_product_id">
                                    <button type="button" data-dropdown-toggle
                                            class="w-full flex items-center justify-between gap-2 bg-two px-3 py-2 rounded-lg border border-four/10 hover:border-three/70 text-xs text-four transition-all duration-200 cursor-pointer">
                                        <span data-dropdown-label class="truncate">Pilih Produk</span>
                                        <svg data-dropdown-icon class="w-3.5 h-3.5 text-four/70 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div data-dropdown-menu
                                         class="opacity-0 -translate-y-2 scale-y-95 origin-top pointer-events-none transition-all duration-200 ease-out absolute left-0 right-0 top-full bg-two border-x border-b border-three rounded-b-lg shadow-2xl overflow-y-auto max-h-52 z-40">
                                        ${productOptionsHtml}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="sale_item_jumlah" class="block text-[11px] font-semibold text-four/60 mb-1">Jumlah</label>
                                <input type="number" id="sale_item_jumlah" min="1" value="1" required
                                       class="w-full px-3 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-xs text-four focus:outline-none">
                            </div>

                            <div class="sm:col-span-3">
                                <label for="sale_item_harga" class="block text-[11px] font-semibold text-four/60 mb-1">Harga Satuan</label>
                                <div class="flex items-center bg-two rounded-lg border border-four/10 focus-within:border-three overflow-hidden">
                                    <span class="px-2 py-2 text-xs text-four/60 bg-one/40 border-r border-four/10">Rp</span>
                                    <input type="text" id="sale_item_harga" inputmode="numeric" data-rupiah value="0" required
                                           class="w-full px-2.5 py-2 bg-transparent text-xs text-four focus:outline-none">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <button type="submit"
                                        class="w-full px-3 py-2 rounded-lg bg-three border border-four/20 text-xs font-semibold text-four hover:brightness-110 active:scale-95 transition-all cursor-pointer">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </form>`
                    : ""
            }
        `;

        if (typeof initRupiahInputs === "function") {
            initRupiahInputs(el.detailContent);
        }

        const itemProductDropdown = document.getElementById("dropdownSaleItemProduct");
        setupCustomDropdown(itemProductDropdown, (val, label, optBtn) => {
            const hargaInput = document.getElementById("sale_item_harga");
            if (hargaInput && optBtn?.dataset.harga) {
                hargaInput.value = formatRibuan(optBtn.dataset.harga) || "0";
            }
        });

        document.getElementById("saleAddItemForm")?.addEventListener("submit", async (e) => {
            e.preventDefault();
            const productId = document.getElementById("sale_item_product_id")?.value.trim();
            const jumlah = parseInt(document.getElementById("sale_item_jumlah")?.value || "0", 10);
            const hargaSatuan = parseRupiahValue(document.getElementById("sale_item_harga")?.value || "0");

            if (!productId) {
                showToast("Pilih produk terlebih dahulu.", "error");
                return;
            }
            if (!jumlah || jumlah < 1) {
                showToast("Jumlah minimal 1.", "error");
                return;
            }

            try {
                const res = await fetchData(`/sales/${sale.id}/items`, {
                    method: "POST",
                    body: JSON.stringify({
                        product_id: productId,
                        jumlah,
                        harga_satuan: hargaSatuan,
                    }),
                });
                showToast(res?.message || "Item berhasil ditambahkan", "success");
                const refreshed = await fetchSaleDetail(sale.id);
                renderDetailContent(refreshed);
                loadSales(state.currentPage);
                loadProducts();
            } catch (error) {
                showToast(error.message || "Gagal menambahkan item", "error");
            }
        });

        el.detailContent.querySelectorAll("button[data-delete-sale-item]").forEach((btn) => {
            btn.addEventListener("click", async () => {
                const itemId = btn.dataset.deleteSaleItem;
                const confirmed = await customConfirm({
                    title: "Hapus Item Transaksi",
                    message: "Hapus item ini dan kembalikan stok produk?",
                    confirmText: "Ya, Hapus",
                    cancelText: "Batal",
                });
                if (!confirmed) return;

                try {
                    const res = await fetchData(`/sales/${sale.id}/items/${itemId}`, {
                        method: "DELETE",
                    });
                    showToast(res?.message || "Item berhasil dihapus", "success");
                    const refreshed = await fetchSaleDetail(sale.id);
                    renderDetailContent(refreshed);
                    loadSales(state.currentPage);
                    loadProducts();
                } catch (error) {
                    showToast(error.message || "Gagal menghapus item", "error");
                }
            });
        });

        document.getElementById("btnCompletePendingSale")?.addEventListener("click", async () => {
            const confirmed = await customConfirm({
                title: "Selesaikan Transaksi",
                message: `Tandai transaksi ${sale.nomor_transaksi} sebagai "Selesai"?`,
                confirmText: "Ya, Selesaikan",
                cancelText: "Batal",
            });
            if (!confirmed) return;

            try {
                const res = await fetchData(`/sales/${sale.id}`, {
                    method: "PUT",
                    body: JSON.stringify({ status: "selesai" }),
                });
                showToast(res?.message || "Transaksi selesai", "success");
                const refreshed = await fetchSaleDetail(sale.id);
                renderDetailContent(refreshed);
                loadSales(state.currentPage);
            } catch (error) {
                showToast(error.message || "Gagal menyelesaikan transaksi", "error");
            }
        });

        document.getElementById("btnCancelSale")?.addEventListener("click", async () => {
            const confirmed = await customConfirm({
                title: "Batalkan Transaksi",
                message: `Batalkan transaksi ${sale.nomor_transaksi}? Seluruh stok item dalam transaksi ini akan dikembalikan otomatis.`,
                confirmText: "Ya, Batalkan",
                cancelText: "Tutup",
            });
            if (!confirmed) return;

            try {
                const res = await fetchData(`/sales/${sale.id}`, {
                    method: "PUT",
                    body: JSON.stringify({ status: "dibatalkan" }),
                });
                showToast(res?.message || "Transaksi dibatalkan & stok dikembalikan", "success");
                const refreshed = await fetchSaleDetail(sale.id);
                renderDetailContent(refreshed);
                loadSales(state.currentPage);
                loadProducts();
            } catch (error) {
                showToast(error.message || "Gagal membatalkan transaksi", "error");
            }
        });
    }

    function closeDetailModal() {
        closeAllCustomDropdowns();
        el.detailModal.classList.add("opacity-0");
        el.detailCard.classList.remove("scale-100", "translate-y-0");
        el.detailCard.classList.add("scale-95", "translate-y-4");
        setTimeout(() => {
            el.detailModal.classList.add("hidden");
        }, 200);
    }

    function formatRupiah(value) {
        const formatted = formatRibuan(Math.round(Number(value || 0)));
        return `Rp ${formatted || "0"}`;
    }

    function bindEvents() {
        setupCustomDropdown(el.dropdownStatus, (val) => {
            state.statusFilter = val;
            renderTable();
        });
        setupCustomDropdown(el.dropdownMethod, (val) => {
            state.methodFilter = val;
            renderTable();
        });

        document.addEventListener("click", () => {
            closeAllCustomDropdowns();
        });

        el.btnCloseDetail?.forEach((btn) => btn.addEventListener("click", closeDetailModal));

        el.tableBody?.addEventListener("click", (e) => {
            const btn = e.target.closest("button[data-action]");
            if (!btn) return;
            const action = btn.dataset.action;
            const id = btn.dataset.id;
            const name = btn.dataset.name || "";

            if (action === "detail") {
                openDetailModal(id);
            } else if (action === "delete") {
                deleteSale(id, name);
            }
        });

        el.searchInput?.addEventListener("input", (e) => {
            state.searchQuery = e.target.value;
            renderTable();
        });

        el.btnRefresh?.addEventListener("click", async () => {
            await loadProducts();
            loadSales(state.currentPage);
        });

        el.btnPrevPage?.addEventListener("click", () => {
            if (state.currentPage > 1) loadSales(state.currentPage - 1);
        });

        el.btnNextPage?.addEventListener("click", () => {
            if (state.meta && state.currentPage < state.meta.total_pages) {
                loadSales(state.currentPage + 1);
            }
        });

        window.addEventListener("auth:changed", async () => {
            await loadProducts();
            loadSales(1);
        });
    }

    function showToast(message, type = "success") {
        if (!el.toastContainer) return;
        const toast = document.createElement("div");
        const borderClass = type === "error" ? "border-three bg-one" : "border-three bg-two";
        const iconSvg =
            type === "error"
                ? `<svg class="w-4 h-4 text-three shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                   </svg>`
                : `<svg class="w-4 h-4 text-three shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                   </svg>`;

        toast.className = `flex items-center gap-2.5 px-4 py-3 rounded-lg border shadow-xl text-sm font-medium text-four transition-all duration-300 translate-y-2 opacity-0 ${borderClass}`;
        toast.innerHTML = `${iconSvg}<span>${escapeHtml(message)}</span>`;

        el.toastContainer.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.remove("translate-y-2", "opacity-0");
        });

        setTimeout(() => {
            toast.classList.add("translate-y-2", "opacity-0");
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return "";
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    async function init() {
        initDomRefs();
        bindEvents();
        await loadProducts();
        await loadSales(1);
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    SalesHistoryPage.init();
});
