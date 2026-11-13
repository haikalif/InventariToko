/**
 * public/js/purchaseOrders.js
 * Pengirim request & pengelola UI halaman Purchase Orders (/purchase-orders)
 * Menggunakan helper fetchData(), customConfirm(), formatRibuan(), parseRupiahValue(), initRupiahInputs() dari public/js/api.js
 */

const PurchaseOrderPage = (() => {
    let state = {
        currentPage: 1,
        meta: null,
        orders: [],
        suppliers: [],
        products: [],
        activeDetailPo: null,
        searchQuery: "",
        statusFilter: "",
    };

    const el = {};

    function initDomRefs() {
        el.tableBody = document.getElementById("poTableBody");
        el.paginationInfo = document.getElementById("paginationInfo");
        el.btnPrevPage = document.getElementById("btnPrevPage");
        el.btnNextPage = document.getElementById("btnNextPage");

        el.searchInput = document.getElementById("filterSearchPo");
        el.statusFilterInput = document.getElementById("filterStatusPo");
        el.dropdownFilterStatus = document.getElementById("dropdownFilterPoStatus");
        el.btnRefresh = document.getElementById("btnRefreshPo");
        el.btnOpenCreate = document.getElementById("btnOpenCreatePoModal");

        el.statTotal = document.getElementById("statTotalPo");
        el.statPage = document.getElementById("statPagePo");
        el.statPending = document.getElementById("statPendingPo");
        el.statReceived = document.getElementById("statReceivedPo");

        el.formModal = document.getElementById("poFormModal");
        el.formModalCard = document.getElementById("poFormCard");
        el.formModalTitle = document.getElementById("poFormTitle");
        el.form = document.getElementById("poForm");
        el.formErrorBox = document.getElementById("poFormErrorBox");
        el.formErrorList = document.getElementById("poFormErrorList");
        el.btnSubmitForm = document.getElementById("btnSubmitPoForm");
        el.btnCloseForm = document.querySelectorAll(".btn-close-po-form-modal");

        el.inputId = document.getElementById("po_id");
        el.inputNomorPo = document.getElementById("nomor_po");
        el.inputTanggal = document.getElementById("po_tanggal");
        el.datePicker = document.getElementById("poDatePicker");
        el.datePickerToggle = document.getElementById("poDatePickerToggle");
        el.datePickerMenu = document.getElementById("poDatePickerMenu");
        el.datePickerLabel = document.getElementById("poDatePickerLabel");
        el.datePickerMonthYear = document.getElementById("poDateMonthYear");
        el.datePickerDays = document.getElementById("poDateDays");
        el.datePickerPrev = document.getElementById("poDatePrevMonth");
        el.datePickerNext = document.getElementById("poDateNextMonth");
        el.datePickerToday = document.getElementById("poDateTodayBtn");

        el.dropdownSupplier = document.getElementById("dropdownPoSupplier");
        el.supplierMenu = document.getElementById("poSupplierMenu");
        el.inputSupplierId = document.getElementById("po_supplier_id");

        el.dropdownPoStatus = document.getElementById("dropdownPoStatus");
        el.inputPoStatus = document.getElementById("po_status");
        el.inputCatatan = document.getElementById("po_catatan");

        el.detailModal = document.getElementById("poDetailModal");
        el.detailCard = document.getElementById("poDetailCard");
        el.detailContent = document.getElementById("poDetailContent");
        el.btnCloseDetail = document.querySelectorAll(".btn-close-po-detail-modal");

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

    function setDropdownValue(container, value, fallbackLabel = "") {
        if (!container) return;
        const hiddenInput = container.querySelector("input[type='hidden']");
        const labelEl = container.querySelector("[data-dropdown-label]");
        const optionBtn = container.querySelector(`[data-value="${CSS.escape(String(value ?? ""))}"]`);

        if (hiddenInput) hiddenInput.value = value ?? "";
        if (labelEl) {
            labelEl.textContent = optionBtn?.dataset.label || fallbackLabel;
        }
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

    // 1b. Custom Date Picker
    const MONTH_NAMES = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];
    const SHORT_MONTH_NAMES = [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
    ];
    let pickerViewDate = new Date();

    function getTodayDateString() {
        const now = new Date();
        return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
    }

    function setSelectedDate(dateStr) {
        if (!el.inputTanggal) return;
        el.inputTanggal.value = dateStr;
        const [y, m, d] = dateStr.split("-").map(Number);
        if (y && m && d) {
            pickerViewDate = new Date(y, m - 1, 1);
            if (el.datePickerLabel) {
                el.datePickerLabel.textContent = `${d} ${SHORT_MONTH_NAMES[m - 1]} ${y}`;
            }
        }
        renderDatePickerDays();
    }

    function renderDatePickerDays() {
        if (!el.datePickerDays || !el.datePickerMonthYear) return;

        const year = pickerViewDate.getFullYear();
        const month = pickerViewDate.getMonth();
        el.datePickerMonthYear.textContent = `${MONTH_NAMES[month]} ${year}`;

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const selectedVal = el.inputTanggal?.value || "";
        const todayStr = getTodayDateString();

        let html = "";
        for (let i = 0; i < firstDayOfMonth; i++) html += `<span></span>`;

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
            const isSelected = dateStr === selectedVal;
            const isToday = dateStr === todayStr;

            const cls = isSelected
                ? "bg-three text-four font-bold shadow-sm"
                : isToday
                ? "bg-one/80 border border-three/60 text-three font-semibold hover:bg-three hover:text-four"
                : "text-four/80 hover:bg-one hover:text-four";

            html += `
                <button type="button"
                        data-pick-date="${dateStr}"
                        class="h-7 rounded-lg flex items-center justify-center transition-all duration-150 active:scale-90 cursor-pointer ${cls}">
                    ${day}
                </button>
            `;
        }
        el.datePickerDays.innerHTML = html;
    }

    function setupCustomDatePicker() {
        if (!el.datePicker) return;
        const iconEl = el.datePickerToggle?.querySelector("[data-dropdown-icon]");

        el.datePickerToggle?.addEventListener("click", (e) => {
            e.stopPropagation();
            const isOpen = el.datePicker.dataset.open === "true";
            closeAllCustomDropdowns();
            if (!isOpen) {
                renderDatePickerDays();
                openDropdown(el.datePicker, el.datePickerToggle, el.datePickerMenu, iconEl);
            }
        });

        el.datePickerMenu?.addEventListener("click", (e) => {
            e.stopPropagation();
            const dayBtn = e.target.closest("button[data-pick-date]");
            if (dayBtn) {
                setSelectedDate(dayBtn.dataset.pickDate);
                closeDropdown(el.datePicker, el.datePickerToggle, el.datePickerMenu, iconEl);
            }
        });

        el.datePickerPrev?.addEventListener("click", () => {
            pickerViewDate = new Date(pickerViewDate.getFullYear(), pickerViewDate.getMonth() - 1, 1);
            renderDatePickerDays();
        });

        el.datePickerNext?.addEventListener("click", () => {
            pickerViewDate = new Date(pickerViewDate.getFullYear(), pickerViewDate.getMonth() + 1, 1);
            renderDatePickerDays();
        });

        el.datePickerToday?.addEventListener("click", () => {
            setSelectedDate(getTodayDateString());
            closeDropdown(el.datePicker, el.datePickerToggle, el.datePickerMenu, iconEl);
        });
    }

    // 2. Pengiriman Request ke API
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

    async function loadReferences() {
        try {
            const [suppliers, products] = await Promise.all([
                fetchAllPaginated("/supliers"),
                fetchAllPaginated("/products"),
            ]);
            state.suppliers = suppliers;
            state.products = products.filter((p) => p.aktif !== false);
            renderSupplierOptions();
        } catch (_) {}
    }

    function renderSupplierOptions() {
        if (!el.supplierMenu) return;
        if (state.suppliers.length === 0) {
            el.supplierMenu.innerHTML = `<div class="px-3.5 py-2.5 text-xs text-four/50">Belum ada data pemasok.</div>`;
            return;
        }
        el.supplierMenu.innerHTML = state.suppliers
            .map(
                (s) => `
                <button type="button"
                        data-value="${escapeHtml(s.id)}"
                        data-label="${escapeHtml(s.nama_supplier)}"
                        class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center gap-2.5 truncate">
                        <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                        <span class="truncate">${escapeHtml(s.nama_supplier)}</span>
                    </div>
                </button>
            `
            )
            .join("");
    }

    async function loadOrders(page = 1) {
        state.currentPage = page;
        renderLoadingTable();

        const token = localStorage.getItem("access_token");
        if (!token && !window.SERVER_USER) {
            window.location.href = "/login";
            return;
        }

        try {
            const response = await fetchData(`/purchase-orders?page=${page}`, { method: "GET" });
            state.orders = response?.data || [];
            state.meta = response?.meta || null;

            updateStats();
            renderTable();
            renderPagination();
        } catch (error) {
            renderEmptyTable(error.message || "Gagal memuat data purchase order.");
            showToast(error.message || "Gagal memuat data purchase order", "error");
        }
    }

    async function fetchOrderDetail(id) {
        try {
            const response = await fetchData(`/purchase-orders/${id}`, { method: "GET" });
            return response?.data;
        } catch (error) {
            showToast(error.message || "Gagal mengambil rincian PO", "error");
            throw error;
        }
    }

    async function saveOrderHeader(id, payload) {
        const isEdit = Boolean(id);
        const endpoint = isEdit ? `/purchase-orders/${id}` : "/purchase-orders";
        const method = isEdit ? "PUT" : "POST";
        return await fetchData(endpoint, { method, body: JSON.stringify(payload) });
    }

    async function deleteOrder(id, nomorPo) {
        const confirmed = await customConfirm({
            title: "Hapus Purchase Order",
            message: `Purchase Order "${nomorPo}" akan dihapus permanen. Hanya PO berstatus Draft yang dapat dihapus. Lanjutkan?`,
            confirmText: "Ya, Hapus",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/purchase-orders/${id}`, { method: "DELETE" });
            showToast(res?.message || "Purchase order berhasil dihapus", "success");
            loadOrders(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus purchase order", "error");
        }
    }

    // 3. Render Statistik, Tabel & Pagination
    const STATUS_LABELS = {
        draft: "Draft",
        dipesan: "Dipesan",
        diterima_sebagian: "Diterima Sebagian",
        diterima: "Diterima",
        dibatalkan: "Dibatalkan",
    };

    function getStatusBadge(status) {
        const label = STATUS_LABELS[status] || status || "-";
        if (status === "diterima") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-two border border-four/20 text-xs font-semibold text-four">${label}</span>`;
        }
        if (status === "dibatalkan") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-three/20 border border-three/50 text-xs font-semibold text-three">${label}</span>`;
        }
        if (status === "dipesan" || status === "diterima_sebagian") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-two border border-three/40 text-xs font-semibold text-three">${label}</span>`;
        }
        return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-one border border-four/15 text-xs font-medium text-four/80">${label}</span>`;
    }

    function updateStats() {
        const list = state.orders;
        const total = state.meta?.total ?? list.length;
        const pendingCount = list.filter((o) => o.status === "draft" || o.status === "dipesan").length;
        const receivedCount = list.filter((o) => o.status === "diterima").length;

        if (el.statTotal) el.statTotal.textContent = total;
        if (el.statPage) el.statPage.textContent = list.length;
        if (el.statPending) el.statPending.textContent = pendingCount;
        if (el.statReceived) el.statReceived.textContent = receivedCount;
    }

    function renderLoadingTable() {
        if (!el.tableBody) return;
        el.tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-four/60">
                    <div class="inline-flex items-center gap-3">
                        <svg class="w-5 h-5 animate-spin text-three" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memuat data purchase order...</span>
                    </div>
                </td>
            </tr>
        `;
    }

    function renderEmptyTable(message) {
        if (!el.tableBody) return;
        el.tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-four/60">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <svg class="w-9 h-9 text-four/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
        const filtered = state.orders.filter((item) => {
            const supplierName = item.supplier?.nama_supplier || "";
            const matchSearch =
                !q ||
                (item.nomor_po || "").toLowerCase().includes(q) ||
                supplierName.toLowerCase().includes(q) ||
                (item.catatan || "").toLowerCase().includes(q);

            const matchStatus = !state.statusFilter || item.status === state.statusFilter;
            return matchSearch && matchStatus;
        });

        if (filtered.length === 0) {
            renderEmptyTable("Belum ada purchase order yang sesuai.");
            return;
        }

        const canEdit = typeof hasRole === "function" ? hasRole(["superadmin", "admin", "staff"]) : false;
        const canDelete = typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false;

        el.tableBody.innerHTML = filtered
            .map((item) => {
                const itemCount = Array.isArray(item.items) ? item.items.length : 0;
                const isDraft = item.status === "draft";

                return `
                    <tr class="hover:bg-two/40 transition-colors duration-150">
                        <td class="px-4 py-3.5">
                            <div class="font-mono text-xs text-three font-semibold">${escapeHtml(item.nomor_po)}</div>
                            <div class="text-xs text-four/60 mt-0.5">${escapeHtml(item.tanggal || "-")}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-four">${escapeHtml(item.supplier?.nama_supplier || "-")}</div>
                            <div class="text-xs text-four/50 mt-0.5 line-clamp-1">${escapeHtml(item.catatan || "Tanpa catatan")}</div>
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
                                        title="Rincian & Kelola Item"
                                        class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                ${
                                    canEdit
                                        ? `<button type="button" data-action="edit" data-id="${escapeHtml(item.id)}"
                                                title="Ubah Header PO"
                                                class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>`
                                        : ""
                                }
                                ${
                                    canDelete && isDraft
                                        ? `<button type="button" data-action="delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.nomor_po)}"
                                                title="Hapus Draft PO"
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
            el.paginationInfo.textContent = `Halaman ${current_page} dari ${total_pages || 1} (${count} dari total ${total} PO)`;
        }
        if (el.btnPrevPage) el.btnPrevPage.disabled = current_page <= 1;
        if (el.btnNextPage) el.btnNextPage.disabled = current_page >= total_pages;
    }

    // 4. Modal Form PO
    function generatePoNumber() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, "0");
        const d = String(now.getDate()).padStart(2, "0");
        const rand = String(Math.floor(1000 + Math.random() * 9000));
        return `PO-${y}${m}${d}-${rand}`;
    }

    function openFormModal(po = null) {
        hideFormErrors();
        el.form.reset();
        closeAllCustomDropdowns();

        if (po) {
            el.formModalTitle.textContent = "Ubah Purchase Order";
            el.inputId.value = po.id;
            el.inputNomorPo.value = po.nomor_po || "";
            setSelectedDate(po.tanggal || getTodayDateString());
            setDropdownValue(el.dropdownSupplier, po.supplier_id, po.supplier?.nama_supplier || "Pilih Pemasok");
            setDropdownValue(el.dropdownPoStatus, po.status || "draft", STATUS_LABELS[po.status] || "Draft");
            el.inputCatatan.value = po.catatan || "";
        } else {
            el.formModalTitle.textContent = "Buat Purchase Order Baru";
            el.inputId.value = "";
            el.inputNomorPo.value = generatePoNumber();
            setSelectedDate(getTodayDateString());
            setDropdownValue(el.dropdownSupplier, "", "Pilih Pemasok");
            setDropdownValue(el.dropdownPoStatus, "draft", "Draft");
            el.inputCatatan.value = "";
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

    // 5. Modal Detail & Kelola Item PO
    async function openDetailModal(id) {
        try {
            const po = await fetchOrderDetail(id);
            if (!po) return;
            state.activeDetailPo = po;
            renderDetailContent(po);

            el.detailModal.classList.remove("hidden");
            requestAnimationFrame(() => {
                el.detailModal.classList.remove("opacity-0");
                el.detailCard.classList.remove("scale-95", "translate-y-4");
                el.detailCard.classList.add("scale-100", "translate-y-0");
            });
        } catch (_) {}
    }

    function renderDetailContent(po) {
        if (!el.detailContent) return;

        const isDraft = po.status === "draft";
        const canReceive = po.status === "draft" || po.status === "dipesan" || po.status === "diterima_sebagian";
        const canDeleteItem = isDraft && (typeof hasRole === "function" ? hasRole(["superadmin", "admin"]) : false);
        const items = Array.isArray(po.items) ? po.items : [];

        const productOptionsHtml = state.products
            .map(
                (p) => `
                <button type="button"
                        data-value="${escapeHtml(p.id)}"
                        data-label="${escapeHtml(`${p.nama_produk} (${p.sku || "-"})`)}"
                        data-harga="${escapeHtml(Math.round(Number(p.harga_beli || 0)))}"
                        class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center gap-2.5 truncate">
                        <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                        <span class="truncate">${escapeHtml(p.nama_produk)} (${escapeHtml(p.sku || "-")})</span>
                    </div>
                    <span class="text-xs text-four/60 group-hover/opt:text-four shrink-0 ml-2">${formatRupiah(p.harga_beli)}</span>
                </button>
            `
            )
            .join("");

        el.detailContent.innerHTML = `
            <!-- Info Header PO -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-two">
                <div>
                    <span class="font-mono text-xs text-three font-semibold">${escapeHtml(po.nomor_po)}</span>
                    <h3 class="text-lg font-bold text-four mt-0.5">${escapeHtml(po.supplier?.nama_supplier || "-")}</h3>
                    <p class="text-xs text-four/60 mt-1">Tanggal: ${escapeHtml(po.tanggal || "-")} • Pembuat: ${escapeHtml(po.user?.name || "-")}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    ${getStatusBadge(po.status)}
                    ${
                        canReceive && items.length > 0
                            ? `<button type="button" id="btnReceivePoStock"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-three border border-four/20 text-xs font-semibold text-four hover:brightness-110 active:scale-95 transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Terima Barang (Masuk Stok)</span>
                            </button>`
                            : ""
                    }
                </div>
            </div>

            ${
                po.catatan
                    ? `<div class="p-3 rounded-lg bg-two/40 border border-two text-xs text-four/80">${escapeHtml(po.catatan)}</div>`
                    : ""
            }

            <!-- Daftar Item PO -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-four/60">Daftar Item Pesanan (${items.length})</h4>
                    <span class="text-sm font-bold text-three">Total: ${formatRupiah(po.total)}</span>
                </div>

                ${
                    items.length === 0
                        ? `<div class="p-6 text-center rounded-lg bg-two/30 border border-two text-xs text-four/60">Belum ada item di dalam Purchase Order ini.</div>`
                        : `<div class="rounded-lg border border-two overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-two/70 border-b border-two text-four/60 uppercase">
                                        <th class="px-3.5 py-2.5">Produk</th>
                                        <th class="px-3.5 py-2.5 text-center">Qty</th>
                                        <th class="px-3.5 py-2.5">Harga Satuan</th>
                                        <th class="px-3.5 py-2.5">Subtotal</th>
                                        ${canDeleteItem ? `<th class="px-3.5 py-2.5 text-right">Aksi</th>` : ""}
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
                                            <td class="px-3.5 py-2.5 text-center font-semibold text-four">
                                                ${escapeHtml(it.jumlah)}
                                            </td>
                                            <td class="px-3.5 py-2.5 text-four/80">
                                                ${formatRupiah(it.harga_satuan)}
                                            </td>
                                            <td class="px-3.5 py-2.5 font-semibold text-four">
                                                ${formatRupiah(it.subtotal)}
                                            </td>
                                            ${
                                                canDeleteItem
                                                    ? `<td class="px-3.5 py-2.5 text-right">
                                                        <button type="button" data-delete-po-item="${escapeHtml(it.id)}"
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

            <!-- Form Tambah Item (Khusus Status Draft) -->
            ${
                isDraft
                    ? `<form id="poAddItemForm" class="p-4 rounded-lg bg-two/40 border border-two space-y-3">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-four/70">Tambah Item ke Draft PO</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-5">
                                <label class="block text-[11px] font-semibold text-four/60 mb-1">Produk</label>
                                <div id="dropdownPoItemProduct" class="relative select-none">
                                    <input type="hidden" id="po_item_product_id">
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
                                <label for="po_item_jumlah" class="block text-[11px] font-semibold text-four/60 mb-1">Jumlah</label>
                                <input type="number" id="po_item_jumlah" min="1" value="1" required
                                       class="w-full px-3 py-2 rounded-lg bg-two border border-four/10 focus:border-three text-xs text-four focus:outline-none">
                            </div>

                            <div class="sm:col-span-3">
                                <label for="po_item_harga" class="block text-[11px] font-semibold text-four/60 mb-1">Harga Satuan</label>
                                <div class="flex items-center bg-two rounded-lg border border-four/10 focus-within:border-three overflow-hidden">
                                    <span class="px-2 py-2 text-xs text-four/60 bg-one/40 border-r border-four/10">Rp</span>
                                    <input type="text" id="po_item_harga" inputmode="numeric" data-rupiah value="0" required
                                           class="w-full px-2.5 py-2 bg-transparent text-xs text-four focus:outline-none">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <button type="submit" id="btnAddPoItem"
                                        class="w-full px-3 py-2 rounded-lg bg-three border border-four/20 text-xs font-semibold text-four hover:brightness-110 active:scale-95 transition-all cursor-pointer">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </form>`
                    : `<p class="text-xs text-four/50 italic">Penambahan atau penghapusan item hanya tersedia saat PO berstatus Draft.</p>`
            }
        `;

        // Bind dynamic elements inside Detail Modal
        if (typeof initRupiahInputs === "function") {
            initRupiahInputs(el.detailContent);
        }

        const itemProductDropdown = document.getElementById("dropdownPoItemProduct");
        setupCustomDropdown(itemProductDropdown, (val, label, optBtn) => {
            const hargaInput = document.getElementById("po_item_harga");
            if (hargaInput && optBtn?.dataset.harga) {
                hargaInput.value = formatRibuan(optBtn.dataset.harga) || "0";
            }
        });

        const addItemForm = document.getElementById("poAddItemForm");
        addItemForm?.addEventListener("submit", async (e) => {
            e.preventDefault();
            const productId = document.getElementById("po_item_product_id")?.value.trim();
            const jumlah = parseInt(document.getElementById("po_item_jumlah")?.value || "0", 10);
            const hargaSatuan = parseRupiahValue(document.getElementById("po_item_harga")?.value || "0");

            if (!productId) {
                showToast("Pilih produk terlebih dahulu.", "error");
                return;
            }
            if (!jumlah || jumlah < 1) {
                showToast("Jumlah minimal 1.", "error");
                return;
            }

            try {
                const res = await fetchData(`/purchase-orders/${po.id}/items`, {
                    method: "POST",
                    body: JSON.stringify({
                        product_id: productId,
                        jumlah,
                        harga_satuan: hargaSatuan,
                    }),
                });
                showToast(res?.message || "Item berhasil ditambahkan", "success");
                const refreshed = await fetchOrderDetail(po.id);
                state.activeDetailPo = refreshed;
                renderDetailContent(refreshed);
                loadOrders(state.currentPage);
            } catch (error) {
                showToast(error.message || "Gagal menambahkan item PO", "error");
            }
        });

        el.detailContent.querySelectorAll("button[data-delete-po-item]").forEach((btn) => {
            btn.addEventListener("click", async () => {
                const itemId = btn.dataset.deletePoItem;
                const confirmed = await customConfirm({
                    title: "Hapus Item PO",
                    message: "Hapus item ini dari Purchase Order?",
                    confirmText: "Ya, Hapus",
                    cancelText: "Batal",
                });
                if (!confirmed) return;

                try {
                    const res = await fetchData(`/purchase-orders/${po.id}/items/${itemId}`, {
                        method: "DELETE",
                    });
                    showToast(res?.message || "Item berhasil dihapus", "success");
                    const refreshed = await fetchOrderDetail(po.id);
                    state.activeDetailPo = refreshed;
                    renderDetailContent(refreshed);
                    loadOrders(state.currentPage);
                } catch (error) {
                    showToast(error.message || "Gagal menghapus item PO", "error");
                }
            });
        });

        document.getElementById("btnReceivePoStock")?.addEventListener("click", async () => {
            const confirmed = await customConfirm({
                title: "Terima Barang PO",
                message: `Tandai ${po.nomor_po} sebagai "Diterima"? Stok seluruh produk dalam PO ini akan otomatis bertambah dan dicatat ke Pergerakan Stok.`,
                confirmText: "Ya, Terima Barang",
                cancelText: "Batal",
            });
            if (!confirmed) return;

            try {
                const res = await fetchData(`/purchase-orders/${po.id}`, {
                    method: "PUT",
                    body: JSON.stringify({ status: "diterima" }),
                });
                showToast(res?.message || "PO diterima dan stok diperbarui", "success");
                const refreshed = await fetchOrderDetail(po.id);
                state.activeDetailPo = refreshed;
                renderDetailContent(refreshed);
                loadOrders(state.currentPage);
            } catch (error) {
                showToast(error.message || "Gagal memproses penerimaan PO", "error");
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

    // 6. Error & Event Binding
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

    function formatRupiah(value) {
        const formatted = formatRibuan(Math.round(Number(value || 0)));
        return `Rp ${formatted || "0"}`;
    }

    function bindEvents() {
        setupCustomDropdown(el.dropdownFilterStatus, (val) => {
            state.statusFilter = val;
            renderTable();
        });
        setupCustomDropdown(el.dropdownSupplier);
        setupCustomDropdown(el.dropdownPoStatus);
        setupCustomDatePicker();

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
                nomor_po: el.inputNomorPo.value.trim(),
                supplier_id: el.inputSupplierId.value.trim(),
                tanggal: el.inputTanggal.value.trim(),
                status: el.inputPoStatus.value.trim() || "draft",
                catatan: el.inputCatatan.value.trim() || null,
            };

            if (!payload.supplier_id) {
                showFormErrors({ message: "Pemasok wajib dipilih." });
                return;
            }

            el.btnSubmitForm.disabled = true;

            try {
                const res = await saveOrderHeader(id, payload);
                showToast(res?.message || "Purchase order berhasil disimpan", "success");
                closeFormModal();
                await loadOrders(state.currentPage);
                if (!id && res?.data?.id) {
                    openDetailModal(res.data.id);
                }
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
                const found = state.orders.find((o) => o.id === id);
                if (found) openFormModal(found);
            } else if (action === "delete") {
                deleteOrder(id, name);
            }
        });

        el.searchInput?.addEventListener("input", (e) => {
            state.searchQuery = e.target.value;
            renderTable();
        });

        el.btnRefresh?.addEventListener("click", async () => {
            await loadReferences();
            loadOrders(state.currentPage);
        });

        el.btnPrevPage?.addEventListener("click", () => {
            if (state.currentPage > 1) loadOrders(state.currentPage - 1);
        });

        el.btnNextPage?.addEventListener("click", () => {
            if (state.meta && state.currentPage < state.meta.total_pages) {
                loadOrders(state.currentPage + 1);
            }
        });

        window.addEventListener("auth:changed", async () => {
            await loadReferences();
            loadOrders(1);
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
        await loadReferences();
        await loadOrders(1);
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    PurchaseOrderPage.init();
});
