/**
 * public/js/stockMovements.js
 * Pengirim request & pengelola UI halaman Riwayat Pergerakan Stok (/stock-movements)
 * Menggunakan helper fetchData() dari public/js/api.js
 */

const StockMovementPage = (() => {
    let state = {
        currentPage: 1,
        meta: null,
        movements: [],
        productsMap: {},
        usersMap: {},
        searchQuery: "",
        typeFilter: "",
    };

    const el = {};

    function initDomRefs() {
        el.tableBody = document.getElementById("movementTableBody");
        el.paginationInfo = document.getElementById("paginationInfo");
        el.btnPrevPage = document.getElementById("btnPrevPage");
        el.btnNextPage = document.getElementById("btnNextPage");

        el.searchInput = document.getElementById("filterSearchMovement");
        el.typeFilterInput = document.getElementById("filterTypeMovement");
        el.dropdownFilterType = document.getElementById("dropdownFilterType");
        el.btnRefresh = document.getElementById("btnRefreshMovements");

        el.statTotal = document.getElementById("statTotalMovements");
        el.statPage = document.getElementById("statPageMovements");
        el.statIn = document.getElementById("statInMovements");
        el.statOut = document.getElementById("statOutMovements");

        el.detailModal = document.getElementById("movementDetailModal");
        el.detailCard = document.getElementById("movementDetailCard");
        el.detailContent = document.getElementById("movementDetailContent");
        el.btnCloseDetail = document.querySelectorAll(".btn-close-movement-detail-modal");

        el.toastContainer = document.getElementById("toastContainer");
    }

    // 1. Custom Dropdown Menyatu dengan Toggle
    function setupCustomDropdown(container, onSelectCallback = null) {
        if (!container) return;
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
                onSelectCallback(val, text);
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
                if (Array.isArray(res?.data)) {
                    items.push(...res.data);
                }
            });
        }
        return items;
    }

    async function loadLookupMaps() {
        try {
            const products = await fetchAllPaginated("/products");
            state.productsMap = {};
            products.forEach((p) => {
                state.productsMap[p.id] = p;
            });
        } catch (_) {}

        if (typeof hasRole === "function" && hasRole(["superadmin", "admin"])) {
            try {
                const users = await fetchAllPaginated("/users");
                state.usersMap = {};
                users.forEach((u) => {
                    state.usersMap[u.id] = u;
                });
            } catch (_) {}
        }
    }

    async function loadMovements(page = 1) {
        state.currentPage = page;
        renderLoadingTable();

        const token = localStorage.getItem("access_token");
        if (!token && !window.SERVER_USER) {
            window.location.href = "/login";
            return;
        }

        try {
            const response = await fetchData(`/stock-movements?page=${page}`, { method: "GET" });
            state.movements = response?.data || [];
            state.meta = response?.meta || null;

            updateStats();
            renderTable();
            renderPagination();
        } catch (error) {
            renderEmptyTable(error.message || "Gagal memuat data pergerakan stok.");
            showToast(error.message || "Gagal memuat data pergerakan stok", "error");
        }
    }

    async function fetchMovementDetail(id) {
        try {
            const response = await fetchData(`/stock-movements/${id}`, { method: "GET" });
            return response?.data;
        } catch (error) {
            showToast(error.message || "Gagal mengambil detail mutasi stok", "error");
            throw error;
        }
    }

    // 3. Fungsi Render UI & Tabel
    function getTypeBadge(tipe) {
        const t = (tipe || "").toLowerCase();
        if (t === "masuk") {
            return `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-two border border-four/15 text-xs font-semibold text-four">Masuk</span>`;
        }
        if (t === "keluar") {
            return `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-three/20 border border-three/50 text-xs font-semibold text-three">Keluar</span>`;
        }
        if (t === "retur") {
            return `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-two border border-three/40 text-xs font-semibold text-four">Retur</span>`;
        }
        return `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-one border border-four/10 text-xs font-semibold text-four/80">${escapeHtml(tipe || "-")}</span>`;
    }

    function updateStats() {
        const list = state.movements;
        const total = state.meta?.total ?? list.length;
        const inCount = list.filter((m) => m.tipe === "masuk" || m.tipe === "retur").length;
        const outCount = list.filter((m) => m.tipe === "keluar").length;

        if (el.statTotal) el.statTotal.textContent = total;
        if (el.statPage) el.statPage.textContent = list.length;
        if (el.statIn) el.statIn.textContent = inCount;
        if (el.statOut) el.statOut.textContent = outCount;
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
                        <span>Memuat data pergerakan stok...</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
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
        const filtered = state.movements.filter((item) => {
            const product = state.productsMap[item.product_id];
            const productName = product?.nama_produk || "";
            const productSku = product?.sku || "";

            const matchSearch =
                !q ||
                (item.id || "").toLowerCase().includes(q) ||
                (item.product_id || "").toLowerCase().includes(q) ||
                productName.toLowerCase().includes(q) ||
                productSku.toLowerCase().includes(q) ||
                (item.referensi || "").toLowerCase().includes(q) ||
                (item.keterangan || "").toLowerCase().includes(q);

            const matchType = !state.typeFilter || item.tipe === state.typeFilter;
            return matchSearch && matchType;
        });

        if (filtered.length === 0) {
            renderEmptyTable("Belum ada data pergerakan stok yang sesuai.");
            return;
        }

        el.tableBody.innerHTML = filtered
            .map((item) => {
                const product = state.productsMap[item.product_id];
                const productTitle = product?.nama_produk || item.product_id;
                const productSub = product?.sku ? `${product.sku}` : item.product_id;
                const sign = item.tipe === "keluar" ? "-" : "+";
                const qtyClass = item.tipe === "keluar" ? "text-three" : "text-four";

                return `
                    <tr class="hover:bg-two/40 transition-colors duration-150">
                        <td class="px-4 py-3.5">
                            <div class="font-mono text-xs text-three font-semibold">${escapeHtml(item.id)}</div>
                            <div class="text-xs text-four/50 mt-0.5">${escapeHtml(item.created_at || "-")}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-four">${escapeHtml(productTitle)}</div>
                            <div class="font-mono text-xs text-four/50 mt-0.5">${escapeHtml(productSub)}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            ${getTypeBadge(item.tipe)}
                        </td>
                        <td class="px-4 py-3.5 font-bold ${qtyClass}">
                            ${sign}${escapeHtml(item.jumlah)}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="inline-flex items-center gap-2 text-xs font-medium text-four/80">
                                <span class="px-2 py-0.5 rounded bg-two/80 border border-four/10">${escapeHtml(item.stok_sebelum)}</span>
                                <svg class="w-3.5 h-3.5 text-three shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                                <span class="px-2 py-0.5 rounded bg-two border border-three/40 text-four font-semibold">${escapeHtml(item.stok_sesudah)}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="text-xs font-mono text-four/90">${escapeHtml(item.referensi || "-")}</div>
                            <div class="text-xs text-four/60 line-clamp-1 mt-0.5">${escapeHtml(item.keterangan || "Tanpa keterangan")}</div>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <button type="button" data-action="detail" data-id="${escapeHtml(item.id)}"
                                    title="Rincian Mutasi"
                                    class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
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
            el.paginationInfo.textContent = `Halaman ${current_page} dari ${total_pages || 1} (${count} dari total ${total} catatan)`;
        }
        if (el.btnPrevPage) el.btnPrevPage.disabled = current_page <= 1;
        if (el.btnNextPage) el.btnNextPage.disabled = current_page >= total_pages;
    }

    // 4. Modal Detail
    async function openDetailModal(id) {
        try {
            const item = await fetchMovementDetail(id);
            if (!item) return;

            const product = state.productsMap[item.product_id];
            const user = state.usersMap[item.user_id];
            const productLabel = product ? `${product.nama_produk} (${product.sku})` : item.product_id;
            const userLabel = user ? `${user.name} (${user.role})` : item.user_id || "-";

            el.detailContent.innerHTML = `
                <div class="space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4 pb-3.5 border-b border-two">
                        <div>
                            <span class="font-mono text-xs text-three font-semibold">${escapeHtml(item.id)}</span>
                            <h3 class="text-base font-bold text-four mt-0.5">${escapeHtml(productLabel)}</h3>
                            <p class="text-xs text-four/50 mt-0.5">${escapeHtml(item.created_at || "-")}</p>
                        </div>
                        ${getTypeBadge(item.tipe)}
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-xs text-four/50 mb-1">Stok Sebelum</p>
                            <p class="text-base font-bold text-four">${escapeHtml(item.stok_sebelum)}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-xs text-four/50 mb-1">Jumlah</p>
                            <p class="text-base font-bold text-three">${item.tipe === "keluar" ? "-" : "+"}${escapeHtml(item.jumlah)}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-xs text-four/50 mb-1">Stok Sesudah</p>
                            <p class="text-base font-bold text-four">${escapeHtml(item.stok_sesudah)}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-xs text-four/50 mb-1">Referensi</p>
                            <p class="text-xs font-mono font-semibold text-four">${escapeHtml(item.referensi || "-")}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-xs text-four/50 mb-1">Dicatat Oleh</p>
                            <p class="text-xs font-medium text-four">${escapeHtml(userLabel)}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-four/50 mb-1.5">Keterangan</p>
                        <p class="text-xs text-four/80 bg-two/40 p-3.5 rounded-lg border border-two whitespace-pre-line leading-relaxed">${escapeHtml(item.keterangan || "Tidak ada keterangan.")}</p>
                    </div>
                </div>
            `;

            el.detailModal.classList.remove("hidden");
            requestAnimationFrame(() => {
                el.detailModal.classList.remove("opacity-0");
                el.detailCard.classList.remove("scale-95", "translate-y-4");
                el.detailCard.classList.add("scale-100", "translate-y-0");
            });
        } catch (_) {}
    }

    function closeDetailModal() {
        el.detailModal.classList.add("opacity-0");
        el.detailCard.classList.remove("scale-100", "translate-y-0");
        el.detailCard.classList.add("scale-95", "translate-y-4");
        setTimeout(() => {
            el.detailModal.classList.add("hidden");
        }, 200);
    }

    function bindEvents() {
        setupCustomDropdown(el.dropdownFilterType, (val) => {
            state.typeFilter = val;
            renderTable();
        });

        document.addEventListener("click", () => {
            closeAllCustomDropdowns();
        });

        el.btnCloseDetail?.forEach((btn) => btn.addEventListener("click", closeDetailModal));

        el.tableBody?.addEventListener("click", (e) => {
            const btn = e.target.closest("button[data-action]");
            if (!btn) return;
            if (btn.dataset.action === "detail") {
                openDetailModal(btn.dataset.id);
            }
        });

        el.searchInput?.addEventListener("input", (e) => {
            state.searchQuery = e.target.value;
            renderTable();
        });

        el.btnRefresh?.addEventListener("click", async () => {
            await loadLookupMaps();
            loadMovements(state.currentPage);
        });

        el.btnPrevPage?.addEventListener("click", () => {
            if (state.currentPage > 1) loadMovements(state.currentPage - 1);
        });

        el.btnNextPage?.addEventListener("click", () => {
            if (state.meta && state.currentPage < state.meta.total_pages) {
                loadMovements(state.currentPage + 1);
            }
        });

        window.addEventListener("auth:changed", async () => {
            await loadLookupMaps();
            loadMovements(1);
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
        await loadLookupMaps();
        await loadMovements(1);
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    StockMovementPage.init();
});
