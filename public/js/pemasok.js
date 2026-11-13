/**
 * public/js/pemasok.js
 * Pengirim request & pengelola UI halaman Data Pemasok
 * Menggunakan helper fetchData() dan customConfirm() dari public/js/api.js
 */

const SupplierPage = (() => {
    let state = {
        mode: "active",
        currentPage: 1,
        meta: null,
        suppliers: [],
        searchQuery: "",
        statusFilter: "",
    };

    const el = {};

    function initDomRefs() {
        el.tableBody = document.getElementById("supplierTableBody");
        el.paginationInfo = document.getElementById("paginationInfo");
        el.btnPrevPage = document.getElementById("btnPrevPage");
        el.btnNextPage = document.getElementById("btnNextPage");

        el.tabActive = document.getElementById("tabActiveSuppliers");
        el.tabTrashed = document.getElementById("tabTrashedSuppliers");

        el.searchInput = document.getElementById("filterSearchSupplier");
        el.statusFilterInput = document.getElementById("filterStatusSupplier");
        el.dropdownFilterStatus = document.getElementById("dropdownFilterStatus");
        el.btnRefresh = document.getElementById("btnRefreshSuppliers");
        el.btnOpenCreate = document.getElementById("btnOpenCreateSupplierModal");

        el.statTotal = document.getElementById("statTotalSuppliers");
        el.statPage = document.getElementById("statPageSuppliers");
        el.statComplete = document.getElementById("statCompleteSuppliers");
        el.statIncomplete = document.getElementById("statIncompleteSuppliers");

        el.formModal = document.getElementById("supplierFormModal");
        el.formModalCard = document.getElementById("supplierFormCard");
        el.formModalTitle = document.getElementById("supplierFormTitle");
        el.form = document.getElementById("supplierForm");
        el.formErrorBox = document.getElementById("supplierFormErrorBox");
        el.formErrorList = document.getElementById("supplierFormErrorList");
        el.btnSubmitForm = document.getElementById("btnSubmitSupplierForm");
        el.btnCloseForm = document.querySelectorAll(".btn-close-supplier-form-modal");

        el.inputId = document.getElementById("supplier_id");
        el.inputNama = document.getElementById("nama_supplier");
        el.inputKontak = document.getElementById("kontak");
        el.inputEmail = document.getElementById("email");
        el.inputAlamat = document.getElementById("alamat");

        el.detailModal = document.getElementById("supplierDetailModal");
        el.detailCard = document.getElementById("supplierDetailCard");
        el.detailContent = document.getElementById("supplierDetailContent");
        el.btnCloseDetail = document.querySelectorAll(".btn-close-supplier-detail-modal");

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

    // 2. Pengirim Request ke API (CRUD)
    async function loadSuppliers(page = 1) {
        state.currentPage = page;
        renderLoadingTable();

        const token = localStorage.getItem("access_token");
        if (!token && !window.SERVER_USER) {
            window.location.href = "/login";
            return;
        }

        const endpoint =
            state.mode === "trashed"
                ? `/supliers/trashed?page=${page}`
                : `/supliers?page=${page}`;

        try {
            const response = await fetchData(endpoint, { method: "GET" });
            state.suppliers = response?.data || [];
            state.meta = response?.meta || null;

            updateStats();
            renderTable();
            renderPagination();
        } catch (error) {
            renderEmptyTable(error.message || "Gagal memuat data pemasok dari server.");
            showToast(error.message || "Gagal memuat data pemasok", "error");
        }
    }

    async function fetchSupplierDetail(id) {
        try {
            const response = await fetchData(`/supliers/${id}`, { method: "GET" });
            return response?.data;
        } catch (error) {
            showToast(error.message || "Gagal mengambil detail pemasok", "error");
            throw error;
        }
    }

    async function saveSupplier(id, payload) {
        const isEdit = Boolean(id);
        const endpoint = isEdit ? `/supliers/${id}` : "/supliers";
        const method = isEdit ? "PUT" : "POST";

        return await fetchData(endpoint, {
            method,
            body: JSON.stringify(payload),
        });
    }

    async function deleteSupplier(id, namaSupplier) {
        const confirmed = await customConfirm({
            title: "Pindahkan ke Tong Sampah",
            message: `Pemasok "${namaSupplier}" akan dipindahkan ke tong sampah. Anda masih dapat memulihkannya nanti.`,
            confirmText: "Ya, Hapus",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/supliers/${id}`, { method: "DELETE" });
            showToast(res?.message || "Pemasok berhasil dihapus", "success");
            loadSuppliers(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus pemasok", "error");
        }
    }

    async function restoreSupplier(id, namaSupplier) {
        try {
            const res = await fetchData(`/supliers/${id}/restore`, { method: "POST" });
            showToast(res?.message || `Pemasok "${namaSupplier}" berhasil dipulihkan`, "success");
            loadSuppliers(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal memulihkan pemasok", "error");
        }
    }

    async function forceDeleteSupplier(id, namaSupplier) {
        const confirmed = await customConfirm({
            title: "Hapus Pemasok Secara Permanen",
            message: `Pemasok "${namaSupplier}" akan dihapus permanen dari database dan tidak dapat dikembalikan lagi. Lanjutkan?`,
            confirmText: "Hapus Permanen",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/supliers/${id}/force-delete`, { method: "DELETE" });
            showToast(res?.message || "Pemasok dihapus permanen", "success");
            loadSuppliers(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus permanen pemasok", "error");
        }
    }

    // 3. Fungsi Render UI & Tabel
    function isCompleteSupplier(item) {
        return Boolean(
            item.kontak && item.kontak.trim() &&
            item.email && item.email.trim() &&
            item.alamat && item.alamat.trim()
        );
    }

    function updateStats() {
        const list = state.suppliers;
        const total = state.meta?.total ?? list.length;
        const completeCount = list.filter(isCompleteSupplier).length;
        const incompleteCount = list.length - completeCount;

        if (el.statTotal) el.statTotal.textContent = total;
        if (el.statPage) el.statPage.textContent = list.length;
        if (el.statComplete) el.statComplete.textContent = completeCount;
        if (el.statIncomplete) el.statIncomplete.textContent = incompleteCount;
    }

    function renderLoadingTable() {
        if (!el.tableBody) return;
        el.tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-5 py-12 text-center text-four/60">
                    <div class="inline-flex items-center gap-3">
                        <svg class="w-5 h-5 animate-spin text-three" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memuat data pemasok...</span>
                    </div>
                </td>
            </tr>
        `;
    }

    function renderEmptyTable(message) {
        if (!el.tableBody) return;
        el.tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-5 py-12 text-center text-four/60">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <svg class="w-9 h-9 text-four/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
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
        const filtered = state.suppliers.filter((item) => {
            const matchSearch =
                !q ||
                (item.nama_supplier || "").toLowerCase().includes(q) ||
                (item.kontak || "").toLowerCase().includes(q) ||
                (item.email || "").toLowerCase().includes(q) ||
                (item.alamat || "").toLowerCase().includes(q) ||
                (item.id || "").toLowerCase().includes(q);

            const hasEmail = Boolean(item.email && item.email.trim());
            const hasKontak = Boolean(item.kontak && item.kontak.trim());
            const complete = isCompleteSupplier(item);

            const matchStatus =
                !state.statusFilter ||
                (state.statusFilter === "has_email" && hasEmail) ||
                (state.statusFilter === "has_kontak" && hasKontak) ||
                (state.statusFilter === "incomplete" && !complete);

            return matchSearch && matchStatus;
        });

        if (filtered.length === 0) {
            renderEmptyTable(
                state.mode === "trashed"
                    ? "Tidak ada pemasok di dalam tong sampah."
                    : "Belum ada data pemasok yang sesuai."
            );
            return;
        }

        const canEdit = typeof hasRole === "function" ? hasRole(["superadmin", "admin", "staff"]) : true;
        const canDelete = typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false;
        const canRestore = typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false;
        const canForceDelete = typeof hasRole === "function" ? hasRole(["superadmin"]) : false;

        el.tableBody.innerHTML = filtered
            .map((item) => {
                const hasKontak = Boolean(item.kontak && item.kontak.trim());
                const hasEmail = Boolean(item.email && item.email.trim());
                const hasAlamat = Boolean(item.alamat && item.alamat.trim());

                const kontakHtml = hasKontak
                    ? `<div class="flex items-center gap-1.5 text-four/90">
                           <svg class="w-3.5 h-3.5 text-three shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                           </svg>
                           <span class="text-xs font-medium">${escapeHtml(item.kontak)}</span>
                       </div>`
                    : `<span class="text-xs text-four/45 italic">Tanpa kontak</span>`;

                const emailHtml = hasEmail
                    ? `<div class="flex items-center gap-1.5 text-four/70 mt-1">
                           <svg class="w-3.5 h-3.5 text-four/50 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                           </svg>
                           <span class="text-xs truncate max-w-xs">${escapeHtml(item.email)}</span>
                       </div>`
                    : `<div class="text-xs text-four/45 italic mt-0.5">Tanpa email</div>`;

                const alamatCell = hasAlamat
                    ? `<p class="text-sm text-four/80 line-clamp-2">${escapeHtml(item.alamat)}</p>`
                    : `<span class="text-xs text-four/45 italic">Alamat belum diisi</span>`;

                const actionButtons =
                    state.mode === "trashed"
                        ? `
                        ${
                            canRestore
                                ? `<button type="button" data-action="restore" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nama_supplier)}"
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
                                ? `<button type="button" data-action="force-delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nama_supplier)}"
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
                                title="Rincian Pemasok"
                                class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        ${
                            canEdit
                                ? `<button type="button" data-action="edit" data-id="${escapeHtml(item.id)}"
                                        title="Ubah Pemasok"
                                        class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>`
                                : ""
                        }
                        ${
                            canDelete
                                ? `<button type="button" data-action="delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nama_supplier)}"
                                        title="Hapus Pemasok"
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
                            ${escapeHtml(item.id)}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-four">${escapeHtml(item.nama_supplier)}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            ${kontakHtml}
                            ${emailHtml}
                        </td>
                        <td class="px-4 py-3.5">
                            ${alamatCell}
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
            el.paginationInfo.textContent = `Halaman ${current_page} dari ${total_pages || 1} (${count} dari total ${total} pemasok)`;
        }
        if (el.btnPrevPage) {
            el.btnPrevPage.disabled = current_page <= 1;
        }
        if (el.btnNextPage) {
            el.btnNextPage.disabled = current_page >= total_pages;
        }
    }

    // 4. Modal Form & Detail
    function openFormModal(supplier = null) {
        hideFormErrors();
        el.form.reset();
        closeAllCustomDropdowns();

        if (supplier) {
            el.formModalTitle.textContent = "Ubah Data Pemasok";
            el.inputId.value = supplier.id;
            el.inputNama.value = supplier.nama_supplier || "";
            el.inputKontak.value = supplier.kontak || "";
            el.inputEmail.value = supplier.email || "";
            el.inputAlamat.value = supplier.alamat || "";
        } else {
            el.formModalTitle.textContent = "Tambah Pemasok Baru";
            el.inputId.value = "";
        }

        el.formModal.classList.remove("hidden");
        requestAnimationFrame(() => {
            el.formModal.classList.remove("opacity-0");
            el.formModalCard.classList.remove("scale-95", "translate-y-4");
            el.formModalCard.classList.add("scale-100", "translate-y-0");
            el.inputNama?.focus();
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
            const item = await fetchSupplierDetail(id);
            if (!item) return;

            el.detailContent.innerHTML = `
                <div class="space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4 pb-3.5 border-b border-two">
                        <div>
                            <span class="font-mono text-xs text-three font-semibold">${escapeHtml(item.id)}</span>
                            <h3 class="text-base font-bold text-four mt-0.5">${escapeHtml(item.nama_supplier)}</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-two border border-four/10 text-xs font-semibold text-four shrink-0">
                            Pemasok
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-xs text-four/50 mb-1">Nomor Kontak</p>
                            <p class="text-sm font-medium text-four">${escapeHtml(item.kontak || "Belum diisi")}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-xs text-four/50 mb-1">Alamat Email</p>
                            <p class="text-sm font-medium text-four break-all">${escapeHtml(item.email || "Belum diisi")}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-four/50 mb-1.5">Alamat Lengkap</p>
                        <p class="text-xs text-four/80 bg-two/40 p-3.5 rounded-lg border border-two whitespace-pre-line leading-relaxed">${escapeHtml(item.alamat || "Alamat belum diisi.")}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-four/50 pt-2 border-t border-two">
                        <div>Dibuat: <span class="text-four/80">${escapeHtml(item.created_at || "-")}</span></div>
                        <div>Diperbarui: <span class="text-four/80">${escapeHtml(item.updated_at || "-")}</span></div>
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
        setupCustomDropdown(el.dropdownFilterStatus, (val) => {
            state.statusFilter = val;
            renderTable();
        });

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
                nama_supplier: el.inputNama.value.trim(),
                kontak: el.inputKontak.value.trim() || null,
                email: el.inputEmail.value.trim() || null,
                alamat: el.inputAlamat.value.trim() || null,
            };

            el.btnSubmitForm.disabled = true;

            try {
                const res = await saveSupplier(id, payload);
                showToast(res?.message || "Data pemasok berhasil disimpan", "success");
                closeFormModal();
                loadSuppliers(state.currentPage);
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
                const found = state.suppliers.find((s) => s.id === id);
                if (found) openFormModal(found);
            } else if (action === "delete") {
                deleteSupplier(id, name);
            } else if (action === "restore") {
                restoreSupplier(id, name);
            } else if (action === "force-delete") {
                forceDeleteSupplier(id, name);
            }
        });

        el.searchInput?.addEventListener("input", (e) => {
            state.searchQuery = e.target.value;
            renderTable();
        });

        el.btnRefresh?.addEventListener("click", () => {
            loadSuppliers(state.currentPage);
        });

        el.tabActive?.addEventListener("click", () => {
            if (state.mode === "active") return;
            state.mode = "active";
            el.tabActive.classList.add("bg-three", "text-four", "font-semibold");
            el.tabActive.classList.remove("text-four/70");
            el.tabTrashed.classList.remove("bg-three", "text-four", "font-semibold");
            el.tabTrashed.classList.add("text-four/70");
            loadSuppliers(1);
        });

        el.tabTrashed?.addEventListener("click", () => {
            if (state.mode === "trashed") return;
            state.mode = "trashed";
            el.tabTrashed.classList.add("bg-three", "text-four", "font-semibold");
            el.tabTrashed.classList.remove("text-four/70");
            el.tabActive.classList.remove("bg-three", "text-four", "font-semibold");
            el.tabActive.classList.add("text-four/70");
            loadSuppliers(1);
        });

        el.btnPrevPage?.addEventListener("click", () => {
            if (state.currentPage > 1) {
                loadSuppliers(state.currentPage - 1);
            }
        });

        el.btnNextPage?.addEventListener("click", () => {
            if (state.meta && state.currentPage < state.meta.total_pages) {
                loadSuppliers(state.currentPage + 1);
            }
        });

        window.addEventListener("auth:changed", () => {
            state.mode = "active";
            loadSuppliers(1);
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
        await loadSuppliers(1);
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    SupplierPage.init();
});
