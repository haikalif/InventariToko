/**
 * public/js/product.js
 * Pengirim request & pengelola UI halaman Data Produk
 * Menggunakan helper fetchData() dari public/js/api.js
 */

const ProductPage = (() => {
    let state = {
        mode: "active",
        currentPage: 1,
        meta: null,
        products: [],
        categories: [],
        suppliers: [],
        searchQuery: "",
        categoryFilter: "",
    };

    const el = {};

    function initDomRefs() {
        el.tableBody = document.getElementById("productTableBody");
        el.paginationInfo = document.getElementById("paginationInfo");
        el.btnPrevPage = document.getElementById("btnPrevPage");
        el.btnNextPage = document.getElementById("btnNextPage");

        el.tabActive = document.getElementById("tabActiveProducts");
        el.tabTrashed = document.getElementById("tabTrashedProducts");

        el.searchInput = document.getElementById("filterSearchProduct");
        el.categoryFilterInput = document.getElementById("filterCategoryProduct");
        el.dropdownFilterCategory = document.getElementById("dropdownFilterCategory");
        el.btnRefresh = document.getElementById("btnRefreshProducts");
        el.btnOpenCreate = document.getElementById("btnOpenCreateModal");

        el.statTotal = document.getElementById("statTotalProducts");
        el.statSafe = document.getElementById("statSafeStock");
        el.statLow = document.getElementById("statLowStock");
        el.statInactive = document.getElementById("statInactiveProducts");

        el.authBanner = document.getElementById("authWarningBanner");
        el.quickRoleButtons = document.querySelectorAll("[data-quick-role]");

        el.formModal = document.getElementById("productFormModal");
        el.formModalCard = document.getElementById("productFormCard");
        el.formModalTitle = document.getElementById("productFormTitle");
        el.form = document.getElementById("productForm");
        el.formErrorBox = document.getElementById("productFormErrorBox");
        el.formErrorList = document.getElementById("productFormErrorList");
        el.btnSubmitForm = document.getElementById("btnSubmitProductForm");
        el.btnCloseForm = document.querySelectorAll(".btn-close-form-modal");

        el.inputId = document.getElementById("product_id");
        el.inputSku = document.getElementById("sku");
        el.inputNama = document.getElementById("nama_produk");
        el.inputCategory = document.getElementById("category_id");
        el.inputSupplier = document.getElementById("supplier_id");
        el.dropdownFormCategory = document.getElementById("dropdownFormCategory");
        el.dropdownFormSupplier = document.getElementById("dropdownFormSupplier");
        el.inputSatuan = document.getElementById("satuan");
        el.inputHargaBeli = document.getElementById("harga_beli");
        el.inputHargaJual = document.getElementById("harga_jual");
        el.inputStok = document.getElementById("stok");
        el.inputStokMin = document.getElementById("stok_minimum");
        el.inputBarcode = document.getElementById("barcode");
        el.inputDeskripsi = document.getElementById("deskripsi");
        el.inputAktif = document.getElementById("aktif");

        el.detailModal = document.getElementById("productDetailModal");
        el.detailCard = document.getElementById("productDetailCard");
        el.detailContent = document.getElementById("productDetailContent");
        el.btnCloseDetail = document.querySelectorAll(".btn-close-detail-modal");

        el.toastContainer = document.getElementById("toastContainer");
    }

    // 1. Komponen Custom Dropdown Menyatu dengan Toggle
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

    function setCustomDropdownValue(container, value, defaultLabel) {
        if (!container) return;
        const hiddenInput = container.querySelector("input[type='hidden']");
        const labelEl = container.querySelector("[data-dropdown-label]");
        const option = container.querySelector(`[data-value="${CSS.escape(String(value || ""))}"]`);

        if (hiddenInput) hiddenInput.value = value || "";
        if (labelEl) {
            labelEl.textContent = option ? option.dataset.label : defaultLabel;
        }
    }

    function renderDropdownItems(container, defaultLabel, items, idKey, nameKey) {
        if (!container) return;
        const menu = container.querySelector("[data-dropdown-menu]");
        if (!menu) return;

        const allOptions = [
            { value: "", label: defaultLabel },
            ...items.map((item) => ({
                value: item[idKey],
                label: item[nameKey],
            })),
        ];

        menu.innerHTML = allOptions
            .map(
                (opt) => `
                <button type="button"
                        data-value="${escapeHtml(opt.value)}"
                        data-label="${escapeHtml(opt.label)}"
                        class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center gap-2.5 truncate">
                        <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                        <span class="truncate">${escapeHtml(opt.label)}</span>
                    </div>
                </button>
            `
            )
            .join("");
    }

    // 2. Pengirim Request ke API (CRUD)
    async function fetchMasterOptions() {
        try {
            const [catRes, supRes] = await Promise.allSettled([
                fetchData("/categories"),
                fetchData("/supliers"),
            ]);

            if (catRes.status === "fulfilled" && catRes.value?.data) {
                state.categories = catRes.value.data;
            }
            if (supRes.status === "fulfilled" && supRes.value?.data) {
                state.suppliers = supRes.value.data;
            }

            renderCategoryOptions();
            renderSupplierOptions();
        } catch (err) {
            console.warn("Gagal memuat master kategori/pemasok:", err);
        }
    }

    async function loadProducts(page = 1) {
        state.currentPage = page;
        renderLoadingTable();

        const token = localStorage.getItem("access_token");
        if (!token && !window.SERVER_USER) {
            window.location.href = "/login";
            return;
        }

        const endpoint =
            state.mode === "trashed"
                ? `/products/trashed?page=${page}`
                : `/products?page=${page}`;

        try {
            const response = await fetchData(endpoint, { method: "GET" });
            state.products = response?.data || [];
            state.meta = response?.meta || null;

            updateStats();
            renderTable();
            renderPagination();
        } catch (error) {
            renderEmptyTable(error.message || "Gagal memuat data produk dari server.");
            showToast(error.message || "Gagal memuat data produk", "error");
        }
    }

    async function fetchProductDetail(id) {
        try {
            const response = await fetchData(`/products/${id}`, { method: "GET" });
            return response?.data;
        } catch (error) {
            showToast(error.message || "Gagal mengambil detail produk", "error");
            throw error;
        }
    }

    async function saveProduct(id, payload) {
        const isEdit = Boolean(id);
        const endpoint = isEdit ? `/products/${id}` : "/products";
        const method = isEdit ? "PUT" : "POST";

        return await fetchData(endpoint, {
            method,
            body: JSON.stringify(payload),
        });
    }

    async function deleteProduct(id, namaProduk) {
        const confirmed = await customConfirm({
            title: "Pindahkan ke Tong Sampah",
            message: `Produk "${namaProduk}" akan dipindahkan ke tong sampah. Anda masih dapat memulihkannya nanti.`,
            confirmText: "Ya, Hapus",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/products/${id}`, { method: "DELETE" });
            showToast(res?.message || "Produk berhasil dihapus", "success");
            loadProducts(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus produk", "error");
        }
    }

    async function restoreProduct(id, namaProduk) {
        try {
            const res = await fetchData(`/products/${id}/restore`, { method: "POST" });
            showToast(res?.message || `Produk "${namaProduk}" berhasil dipulihkan`, "success");
            loadProducts(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal memulihkan produk", "error");
        }
    }

    async function forceDeleteProduct(id, namaProduk) {
        const confirmed = await customConfirm({
            title: "Hapus Produk Secara Permanen",
            message: `Produk "${namaProduk}" akan dihapus permanen dari database dan tidak dapat dikembalikan lagi. Lanjutkan?`,
            confirmText: "Hapus Permanen",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/products/${id}/force-delete`, { method: "DELETE" });
            showToast(res?.message || "Produk dihapus permanen", "success");
            loadProducts(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus permanen produk", "error");
        }
    }

    async function quickRoleLogin(roleType = "admin") {
        const credentialsMap = {
            superadmin: { email: "superadmin@inventaris.com", password: "superadmin123!" },
            admin: { email: "admin@inventaris.com", password: "admin123!" },
            staff: { email: "staff@inventaris.com", password: "staff123!" },
        };
        const creds = credentialsMap[roleType] || credentialsMap.admin;

        try {
            el.quickRoleButtons?.forEach((b) => (b.disabled = true));

            const res = await fetchData("/login", {
                method: "POST",
                body: JSON.stringify(creds),
            });

            if (res?.access_token) {
                localStorage.setItem("access_token", res.access_token);
                if (res.user) {
                    localStorage.setItem("user", JSON.stringify(res.user));
                }
                if (typeof syncAuthAndRoleUI === "function") {
                    syncAuthAndRoleUI();
                }
                showToast(`Berhasil masuk sebagai ${res.user?.name || roleType}`, "success");
                el.authBanner?.classList.add("hidden");
                await fetchMasterOptions();
                await loadProducts(1);
            }
        } catch (error) {
            showToast(error.message || "Gagal masuk, pastikan database sudah di-seed.", "error");
        } finally {
            el.quickRoleButtons?.forEach((b) => (b.disabled = false));
        }
    }

    // 3. Fungsi Render UI & Tabel
    function formatRupiah(angka) {
        const num = Number(angka || 0);
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(num);
    }

    function getCategoryName(categoryId) {
        if (!categoryId) return "Tanpa Kategori";
        const found = state.categories.find((c) => c.id === categoryId);
        return found ? found.nama_kategori : categoryId;
    }

    function getSupplierName(supplierId) {
        if (!supplierId) return "Tanpa Pemasok";
        const found = state.suppliers.find((s) => s.id === supplierId);
        return found ? found.nama_supplier : supplierId;
    }

    function renderCategoryOptions() {
        renderDropdownItems(
            el.dropdownFilterCategory,
            "Semua Kategori",
            state.categories,
            "id",
            "nama_kategori"
        );
        renderDropdownItems(
            el.dropdownFormCategory,
            "Pilih Kategori",
            state.categories,
            "id",
            "nama_kategori"
        );
    }

    function renderSupplierOptions() {
        renderDropdownItems(
            el.dropdownFormSupplier,
            "Pilih Pemasok",
            state.suppliers,
            "id",
            "nama_supplier"
        );
    }

    function updateStats() {
        const list = state.products;
        const total = state.meta?.total ?? list.length;
        const lowStock = list.filter((p) => Number(p.stok) <= Number(p.stok_minimum ?? 0)).length;
        const safeStock = list.filter((p) => Number(p.stok) > Number(p.stok_minimum ?? 0)).length;
        const inactive = list.filter((p) => !p.aktif).length;

        if (el.statTotal) el.statTotal.textContent = total;
        if (el.statSafe) el.statSafe.textContent = safeStock;
        if (el.statLow) el.statLow.textContent = lowStock;
        if (el.statInactive) el.statInactive.textContent = inactive;
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
                        <span>Memuat data produk...</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
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
        const filtered = state.products.filter((item) => {
            const matchSearch =
                !q ||
                (item.nama_produk || "").toLowerCase().includes(q) ||
                (item.sku || "").toLowerCase().includes(q) ||
                (item.barcode || "").toLowerCase().includes(q);

            const matchCat = !state.categoryFilter || item.category_id === state.categoryFilter;
            return matchSearch && matchCat;
        });

        if (filtered.length === 0) {
            renderEmptyTable(
                state.mode === "trashed"
                    ? "Tidak ada produk di dalam tong sampah."
                    : "Belum ada data produk yang sesuai."
            );
            return;
        }

        el.tableBody.innerHTML = filtered
            .map((item) => {
                const isLowStock = Number(item.stok) <= Number(item.stok_minimum ?? 0);
                const stockBadgeClass = isLowStock
                    ? "bg-three/20 text-three border-three/50"
                    : "bg-two text-four border-four/15";

                const statusBadge = item.aktif
                    ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-two border border-four/15 text-four">
                         <span class="w-1.5 h-1.5 rounded-full bg-three"></span> Aktif
                       </span>`
                    : `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-one border border-two text-four/50">
                         <span class="w-1.5 h-1.5 rounded-full bg-four/40"></span> Nonaktif
                       </span>`;

                const canEdit = typeof hasRole === "function" ? hasRole(["superadmin", "admin", "staff"]) : true;
                const canDelete = typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false;
                const canRestore = typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false;
                const canForceDelete = typeof hasRole === "function" ? hasRole(["superadmin"]) : false;

                const actionButtons =
                    state.mode === "trashed"
                        ? `
                        ${
                            canRestore
                                ? `<button type="button" data-action="restore" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nama_produk)}"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-two border border-four/15 text-four hover:bg-three hover:border-three active:scale-95 transition-all duration-200 text-xs font-medium cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Pulihkan</span>
                                </button>`
                                : ""
                        }
                        ${
                            canForceDelete
                                ? `<button type="button" data-action="force-delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nama_produk)}"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-three/20 border border-three/50 text-three hover:bg-three hover:text-four active:scale-95 transition-all duration-200 text-xs font-medium cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus Permanen</span>
                                </button>`
                                : ""
                        }
                    `
                        : `
                        <button type="button" data-action="detail" data-id="${escapeHtml(item.id)}"
                                title="Rincian Produk"
                                class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        ${
                            canEdit
                                ? `<button type="button" data-action="edit" data-id="${escapeHtml(item.id)}"
                                        title="Ubah Produk"
                                        class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>`
                                : ""
                        }
                        ${
                            canDelete
                                ? `<button type="button" data-action="delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nama_produk)}"
                                        title="Hapus Produk"
                                        class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-three hover:bg-three hover:text-four hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>`
                                : ""
                        }
                    `;

                return `
                    <tr class="hover:bg-two/40 transition-colors duration-150">
                        <td class="px-4 py-3.5 font-mono text-xs text-three font-semibold">
                            ${escapeHtml(item.sku)}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-four">${escapeHtml(item.nama_produk)}</div>
                            <div class="text-xs text-four/50">${escapeHtml(item.barcode || "Tanpa barcode")}</div>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-four/80">
                            <div>${escapeHtml(getCategoryName(item.category_id))}</div>
                            <div class="text-xs text-four/50">${escapeHtml(getSupplierName(item.supplier_id))}</div>
                        </td>
                        <td class="px-4 py-3.5 text-sm">
                            <div class="font-semibold text-four">${formatRupiah(item.harga_jual)}</div>
                            <div class="text-xs text-four/50">Beli: ${formatRupiah(item.harga_beli)}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border ${stockBadgeClass}">
                                ${Number(item.stok)} ${escapeHtml(item.satuan || "pcs")}
                            </span>
                            <div class="text-[11px] text-four/50 mt-0.5">Min: ${Number(item.stok_minimum ?? 0)}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            ${statusBadge}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                ${actionButtons}
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
            el.paginationInfo.textContent = `Halaman ${current_page} dari ${total_pages || 1} (${count} dari total ${total} produk)`;
        }
        if (el.btnPrevPage) {
            el.btnPrevPage.disabled = current_page <= 1;
        }
        if (el.btnNextPage) {
            el.btnNextPage.disabled = current_page >= total_pages;
        }
    }

    // 4. Modal Form & Detail
    function openFormModal(product = null) {
        hideFormErrors();
        el.form.reset();
        closeAllCustomDropdowns();

        if (product) {
            el.formModalTitle.textContent = "Ubah Data Produk";
            el.inputId.value = product.id;
            el.inputSku.value = product.sku || "";
            el.inputNama.value = product.nama_produk || "";
            setCustomDropdownValue(el.dropdownFormCategory, product.category_id, "Pilih Kategori");
            setCustomDropdownValue(el.dropdownFormSupplier, product.supplier_id, "Pilih Pemasok");
            el.inputSatuan.value = product.satuan || "pcs";
            el.inputHargaBeli.value = formatRibuan(product.harga_beli ?? 0) || "0";
            el.inputHargaJual.value = formatRibuan(product.harga_jual ?? 0) || "0";
            el.inputStok.value = product.stok ?? 0;
            el.inputStokMin.value = product.stok_minimum ?? 0;
            el.inputBarcode.value = product.barcode || "";
            el.inputDeskripsi.value = product.deskripsi || "";
            el.inputAktif.checked = Boolean(product.aktif);
        } else {
            el.formModalTitle.textContent = "Tambah Produk Baru";
            el.inputId.value = "";
            setCustomDropdownValue(el.dropdownFormCategory, "", "Pilih Kategori");
            setCustomDropdownValue(el.dropdownFormSupplier, "", "Pilih Pemasok");
            el.inputSatuan.value = "pcs";
            el.inputHargaBeli.value = "0";
            el.inputHargaJual.value = "0";
            el.inputStok.value = 0;
            el.inputStokMin.value = 5;
            el.inputAktif.checked = true;
        }

        el.formModal.classList.remove("hidden");
        requestAnimationFrame(() => {
            el.formModal.classList.remove("opacity-0");
            el.formModalCard.classList.remove("scale-95", "translate-y-4");
            el.formModalCard.classList.add("scale-100", "translate-y-0");
        });
    }

    function closeFormModal() {
        closeAllCustomDropdowns();
        el.formModal.classList.add("opacity-0");
        el.formModalCard.classList.remove("scale-100", "translate-y-0");
        el.formModalCard.classList.add("scale-95", "translate-y-4");
        setTimeout(() => {
            el.formModal.classList.add("hidden");
        }, 200);
    }

    async function openDetailModal(id) {
        try {
            const item = await fetchProductDetail(id);
            if (!item) return;

            el.detailContent.innerHTML = `
                <div class="space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4 pb-3.5 border-b border-two">
                        <div>
                            <span class="font-mono text-xs text-three font-semibold">${escapeHtml(item.sku)}</span>
                            <h3 class="text-base font-bold text-four mt-0.5">${escapeHtml(item.nama_produk)}</h3>
                            <p class="text-xs text-four/50 mt-0.5">Barcode: ${escapeHtml(item.barcode || "Tanpa barcode")}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-md bg-two border border-four/10 text-xs font-semibold text-four shrink-0">
                            ${item.aktif ? "Aktif" : "Nonaktif"}
                        </span>
                    </div>

                    <div class="divide-y divide-two/60 border-y border-two/60 text-xs">
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-four/60">Kategori</span>
                            <span class="font-medium text-four">${escapeHtml(getCategoryName(item.category_id))}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-four/60">Pemasok</span>
                            <span class="font-medium text-four">${escapeHtml(getSupplierName(item.supplier_id))}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-four/60">Harga Beli</span>
                            <span class="font-medium text-four">${formatRupiah(item.harga_beli)}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-four/60">Harga Jual</span>
                            <span class="font-semibold text-three text-sm">${formatRupiah(item.harga_jual)}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-four/60">Stok Tersedia / Minimum</span>
                            <span class="font-medium text-four">${Number(item.stok)} ${escapeHtml(item.satuan || "pcs")} <span class="text-four/50">(Min: ${Number(item.stok_minimum ?? 0)})</span></span>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-four/50 mb-1">Catatan / Deskripsi</p>
                        <p class="text-xs text-four/80 bg-two/40 p-3 rounded-lg border border-two whitespace-pre-line leading-relaxed">${escapeHtml(item.deskripsi || "Tidak ada deskripsi.")}</p>
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

    function showFormErrors(errorObj) {
        if (!el.formErrorBox || !el.formErrorList) return;
        const messages = [];

        if (errorObj?.errors && typeof errorObj.errors === "object") {
            Object.values(errorObj.errors).forEach((arr) => {
                if (Array.isArray(arr)) messages.push(...arr);
            });
        }
        if (messages.length === 0 && errorObj?.message) {
            messages.push(errorObj.message);
        }

        el.formErrorList.innerHTML = messages
            .map((m) => `<li class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-three shrink-0"></span><span>${escapeHtml(m)}</span></li>`)
            .join("");
        el.formErrorBox.classList.remove("hidden");
    }

    function hideFormErrors() {
        if (!el.formErrorBox || !el.formErrorList) return;
        el.formErrorList.innerHTML = "";
        el.formErrorBox.classList.add("hidden");
    }

    function bindEvents() {
        setupCustomDropdown(el.dropdownFilterCategory, (val) => {
            state.categoryFilter = val;
            renderTable();
        });
        setupCustomDropdown(el.dropdownFormCategory);
        setupCustomDropdown(el.dropdownFormSupplier);

        document.addEventListener("click", () => {
            closeAllCustomDropdowns();
        });

        el.btnOpenCreate?.addEventListener("click", () => openFormModal(null));
        el.btnCloseForm?.forEach((btn) => btn.addEventListener("click", closeFormModal));
        el.btnCloseDetail?.forEach((btn) => btn.addEventListener("click", closeDetailModal));

        el.form?.addEventListener("submit", async (e) => {
            e.preventDefault();
            hideFormErrors();

            const id = el.inputId.value.trim();
            const payload = {
                sku: el.inputSku.value.trim(),
                nama_produk: el.inputNama.value.trim(),
                category_id: el.inputCategory.value || null,
                supplier_id: el.inputSupplier.value || null,
                satuan: el.inputSatuan.value.trim() || "pcs",
                harga_beli: parseRupiahValue(el.inputHargaBeli.value),
                harga_jual: parseRupiahValue(el.inputHargaJual.value),
                stok: parseInt(el.inputStok.value || "0", 10),
                stok_minimum: parseInt(el.inputStokMin.value || "0", 10),
                barcode: el.inputBarcode.value.trim() || null,
                deskripsi: el.inputDeskripsi.value.trim() || null,
                aktif: el.inputAktif.checked,
            };

            el.btnSubmitForm.disabled = true;

            try {
                const res = await saveProduct(id, payload);
                showToast(res?.message || "Data produk berhasil disimpan", "success");
                closeFormModal();
                loadProducts(state.currentPage);
            } catch (error) {
                showFormErrors(error);
            } finally {
                el.btnSubmitForm.disabled = false;
            }
        });

        el.tableBody?.addEventListener("click", (e) => {
            const btn = e.target.closest("button[data-action]");
            if (!btn) return;

            const action = btn.dataset.action;
            const id = btn.dataset.id;
            const name = btn.dataset.name || "";

            if (action === "detail") {
                openDetailModal(id);
            } else if (action === "edit") {
                const found = state.products.find((p) => p.id === id);
                if (found) openFormModal(found);
            } else if (action === "delete") {
                deleteProduct(id, name);
            } else if (action === "restore") {
                restoreProduct(id, name);
            } else if (action === "force-delete") {
                forceDeleteProduct(id, name);
            }
        });

        el.searchInput?.addEventListener("input", (e) => {
            state.searchQuery = e.target.value;
            renderTable();
        });

        el.btnRefresh?.addEventListener("click", () => {
            loadProducts(state.currentPage);
        });

        el.tabActive?.addEventListener("click", () => {
            if (state.mode === "active") return;
            state.mode = "active";
            el.tabActive.classList.add("bg-three", "text-four", "font-semibold");
            el.tabActive.classList.remove("text-four/70");
            el.tabTrashed.classList.remove("bg-three", "text-four", "font-semibold");
            el.tabTrashed.classList.add("text-four/70");
            loadProducts(1);
        });

        el.tabTrashed?.addEventListener("click", () => {
            if (state.mode === "trashed") return;
            state.mode = "trashed";
            el.tabTrashed.classList.add("bg-three", "text-four", "font-semibold");
            el.tabTrashed.classList.remove("text-four/70");
            el.tabActive.classList.remove("bg-three", "text-four", "font-semibold");
            el.tabActive.classList.add("text-four/70");
            loadProducts(1);
        });

        el.btnPrevPage?.addEventListener("click", () => {
            if (state.currentPage > 1) {
                loadProducts(state.currentPage - 1);
            }
        });

        el.btnNextPage?.addEventListener("click", () => {
            if (state.meta && state.currentPage < state.meta.total_pages) {
                loadProducts(state.currentPage + 1);
            }
        });

        el.quickRoleButtons?.forEach((btn) => {
            btn.addEventListener("click", () => {
                quickRoleLogin(btn.dataset.quickRole);
            });
        });

        window.addEventListener("auth:changed", () => {
            state.mode = "active";
            loadProducts(1);
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
        renderCategoryOptions();
        renderSupplierOptions();
        bindEvents();

        if (localStorage.getItem("access_token")) {
            await fetchMasterOptions();
        }
        await loadProducts(1);
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    ProductPage.init();
});
