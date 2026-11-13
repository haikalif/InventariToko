/**
 * public/js/salesCreate.js
 * Pengirim request & pengelola UI halaman Kasir / Penjualan (/sales/create)
 * Menggunakan helper fetchData(), customConfirm(), formatRibuan(), parseRupiahValue() dari public/js/api.js
 */

const SalesCreatePage = (() => {
    let state = {
        products: [],
        categories: [],
        cart: [], // [{ product_id, sku, nama_produk, satuan, stok, harga_satuan, jumlah }]
        searchQuery: "",
        categoryFilter: "",
        paymentMethod: "Tunai",
        isSubmitting: false,
    };

    const el = {};

    function initDomRefs() {
        el.searchInput = document.getElementById("searchPosProduct");
        el.dropdownCategory = document.getElementById("dropdownPosCategory");
        el.categoryMenu = document.getElementById("posCategoryMenu");
        el.categoryFilterInput = document.getElementById("filterPosCategory");

        el.productGrid = document.getElementById("posProductGrid");
        el.productCount = document.getElementById("posProductCount");

        el.btnRefreshProducts = document.getElementById("btnRefreshPosProducts");
        el.btnResetCart = document.getElementById("btnResetPosCart");

        el.inputTrxNumber = document.getElementById("nomor_transaksi");
        el.btnGenerateTrx = document.getElementById("btnGenerateTrxNumber");
        el.inputTanggal = document.getElementById("tanggal");
        el.datePicker = document.getElementById("customDatePicker");
        el.datePickerToggle = document.getElementById("datePickerToggle");
        el.datePickerMenu = document.getElementById("datePickerMenu");
        el.datePickerLabel = document.getElementById("datePickerLabel");
        el.datePickerMonthYear = document.getElementById("datePickerMonthYear");
        el.datePickerDays = document.getElementById("datePickerDays");
        el.datePickerPrev = document.getElementById("datePickerPrevMonth");
        el.datePickerNext = document.getElementById("datePickerNextMonth");
        el.datePickerToday = document.getElementById("datePickerTodayBtn");
        el.dropdownPayment = document.getElementById("dropdownPaymentMethod");
        el.inputPayment = document.getElementById("metode_pembayaran");

        el.cartList = document.getElementById("posCartList");
        el.cartBadge = document.getElementById("posCartBadge");
        el.totalQty = document.getElementById("posTotalQty");
        el.grandTotal = document.getElementById("posGrandTotal");

        el.inputCash = document.getElementById("inputCashReceived");
        el.quickCashBtns = document.querySelectorAll("[data-quick-cash]");
        el.changeAmount = document.getElementById("posChangeAmount");

        el.errorBox = document.getElementById("posErrorBox");
        el.errorList = document.getElementById("posErrorList");

        el.btnSavePending = document.getElementById("btnSavePendingSale");
        el.btnCompleteSale = document.getElementById("btnCompleteSale");

        el.receiptModal = document.getElementById("saleReceiptModal");
        el.receiptCard = document.getElementById("saleReceiptCard");
        el.receiptContent = document.getElementById("saleReceiptContent");
        el.btnCloseReceipt = document.querySelectorAll(".btn-close-receipt-modal");
        el.btnNewTrxAfterReceipt = document.getElementById("btnNewTransactionAfterReceipt");

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

    // 1b. Custom Date Picker Bertema Aplikasi
    const MONTH_NAMES = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];
    const SHORT_MONTH_NAMES = [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
    ];
    let pickerViewDate = new Date();

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
        for (let i = 0; i < firstDayOfMonth; i++) {
            html += `<span></span>`;
        }

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

    // 2. Pengambilan Data Katalog Produk & Kategori
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

    async function loadCatalog() {
        renderLoadingProducts();

        const token = localStorage.getItem("access_token");
        if (!token && !window.SERVER_USER) {
            window.location.href = "/login";
            return;
        }

        try {
            const [products, categories] = await Promise.all([
                fetchAllPaginated("/products"),
                fetchAllPaginated("/categories"),
            ]);

            state.products = products.filter((p) => p.aktif !== false);
            state.categories = categories;

            renderCategoryDropdown();
            renderProducts();
        } catch (error) {
            renderEmptyProducts(error.message || "Gagal memuat katalog produk.");
            showToast(error.message || "Gagal memuat katalog produk", "error");
        }
    }

    function renderCategoryDropdown() {
        if (!el.categoryMenu) return;

        const defaultOpt = `
            <button type="button"
                    data-value=""
                    data-label="Semua Kategori"
                    class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                    <span class="truncate">Semua Kategori</span>
                </div>
            </button>
        `;

        const catOpts = state.categories
            .map(
                (cat) => `
                <button type="button"
                        data-value="${escapeHtml(cat.id)}"
                        data-label="${escapeHtml(cat.nama_kategori)}"
                        class="group/opt w-full flex items-center justify-between px-3.5 py-2 text-left text-sm text-four hover:bg-three hover:pl-4.5 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center gap-2.5 truncate">
                        <span class="w-1.5 h-1.5 rounded-full bg-three group-hover/opt:bg-four group-hover/opt:scale-125 transition-all duration-200 shrink-0"></span>
                        <span class="truncate">${escapeHtml(cat.nama_kategori)}</span>
                    </div>
                </button>
            `
            )
            .join("");

        el.categoryMenu.innerHTML = defaultOpt + catOpts;
    }

    // 3. Render Katalog Produk
    function renderLoadingProducts() {
        if (!el.productGrid) return;
        el.productGrid.innerHTML = `
            <div class="py-12 text-center text-four/60">
                <div class="inline-flex items-center gap-3">
                    <svg class="w-5 h-5 animate-spin text-three" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memuat katalog produk...</span>
                </div>
            </div>
        `;
    }

    function renderEmptyProducts(message) {
        if (!el.productGrid) return;
        el.productGrid.innerHTML = `
            <div class="py-12 text-center text-four/60">
                <div class="flex flex-col items-center justify-center gap-2">
                    <svg class="w-9 h-9 text-four/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-sm">${escapeHtml(message)}</p>
                </div>
            </div>
        `;
    }

    function getFilteredProducts() {
        const q = state.searchQuery.toLowerCase().trim();
        return state.products.filter((item) => {
            const matchSearch =
                !q ||
                (item.nama_produk || "").toLowerCase().includes(q) ||
                (item.sku || "").toLowerCase().includes(q) ||
                (item.barcode || "").toLowerCase().includes(q);

            const matchCat =
                !state.categoryFilter || item.category_id === state.categoryFilter;

            return matchSearch && matchCat;
        });
    }

    function renderProducts() {
        if (!el.productGrid) return;

        const filtered = getFilteredProducts();
        if (el.productCount) {
            el.productCount.textContent = `${filtered.length} produk`;
        }

        if (filtered.length === 0) {
            renderEmptyProducts("Tidak ada produk yang sesuai dengan pencarian.");
            return;
        }

        el.productGrid.innerHTML = filtered
            .map((p) => {
                const stok = Number(p.stok || 0);
                const minStok = Number(p.stok_minimum || 0);
                const outOfStock = stok <= 0;
                const lowStock = !outOfStock && stok <= minStok;
                const inCart = state.cart.find((c) => c.product_id === p.id);

                const stockBadge = outOfStock
                    ? `<span class="px-2 py-0.5 rounded-lg bg-three/20 border border-three/40 text-[11px] font-semibold text-three">Habis</span>`
                    : lowStock
                    ? `<span class="px-2 py-0.5 rounded-lg bg-three/15 border border-three/30 text-[11px] font-semibold text-three">Stok ${stok}</span>`
                    : `<span class="px-2 py-0.5 rounded-lg bg-one border border-four/10 text-[11px] font-medium text-four/70">Stok ${stok}</span>`;

                return `
                    <button type="button"
                            data-add-product="${escapeHtml(p.id)}"
                            ${outOfStock ? "disabled" : ""}
                            class="group w-full text-left px-4 py-3 rounded-lg bg-two/60 border border-two/80 ${
                                outOfStock
                                    ? "opacity-45 cursor-not-allowed"
                                    : "hover:border-three hover:bg-two/90 hover:translate-x-0.5 active:scale-[0.99] cursor-pointer"
                            } transition-all duration-200 flex items-center justify-between gap-4 shadow-sm">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-[11px] text-three font-semibold truncate">${escapeHtml(p.sku || "-")}</span>
                                ${stockBadge}
                            </div>
                            <div class="flex items-baseline gap-2 min-w-0">
                                <h3 class="text-sm font-semibold text-four truncate">${escapeHtml(p.nama_produk)}</h3>
                                <span class="text-xs text-four/50 shrink-0">/ ${escapeHtml(p.satuan || "pcs")}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-sm font-bold text-four">${formatRupiah(p.harga_jual)}</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg ${
                                inCart
                                    ? "bg-three text-four"
                                    : "bg-one border border-four/10 text-three group-hover:bg-three group-hover:text-four group-hover:border-three"
                            } text-xs font-semibold transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>${inCart ? `Di keranjang (${inCart.jumlah})` : "Tambah"}</span>
                            </span>
                        </div>
                    </button>
                `;
            })
            .join("");
    }

    // 4. Pengelolaan Keranjang (Cart)
    function addToCart(productId) {
        const product = state.products.find((p) => p.id === productId);
        if (!product) return;

        const stok = Number(product.stok || 0);
        if (stok <= 0) {
            showToast(`Stok "${product.nama_produk}" sedang habis.`, "error");
            return;
        }

        const existing = state.cart.find((c) => c.product_id === productId);
        if (existing) {
            if (existing.jumlah + 1 > stok) {
                showToast(`Stok "${product.nama_produk}" hanya tersisa ${stok}.`, "error");
                return;
            }
            existing.jumlah += 1;
        } else {
            state.cart.push({
                product_id: product.id,
                sku: product.sku,
                nama_produk: product.nama_produk,
                satuan: product.satuan || "pcs",
                stok,
                harga_satuan: Math.round(Number(product.harga_jual || 0)),
                jumlah: 1,
            });
        }

        hideErrors();
        renderCart();
        renderProducts();
    }

    function updateCartQty(productId, newQty) {
        const item = state.cart.find((c) => c.product_id === productId);
        if (!item) return;

        const parsed = parseInt(newQty, 10);
        if (isNaN(parsed) || parsed <= 0) {
            removeFromCart(productId);
            return;
        }

        if (parsed > item.stok) {
            showToast(`Maksimal stok tersedia untuk "${item.nama_produk}" adalah ${item.stok}.`, "error");
            item.jumlah = item.stok;
        } else {
            item.jumlah = parsed;
        }

        renderCart();
        renderProducts();
    }

    function removeFromCart(productId) {
        state.cart = state.cart.filter((c) => c.product_id !== productId);
        renderCart();
        renderProducts();
    }

    function getCartTotals() {
        const totalQty = state.cart.reduce((sum, item) => sum + item.jumlah, 0);
        const grandTotal = state.cart.reduce(
            (sum, item) => sum + item.jumlah * item.harga_satuan,
            0
        );
        return { totalQty, grandTotal };
    }

    function renderCart() {
        if (!el.cartList) return;

        const { totalQty, grandTotal } = getCartTotals();

        if (el.cartBadge) el.cartBadge.textContent = `${state.cart.length} item`;
        if (el.totalQty) el.totalQty.textContent = `${totalQty} pcs`;
        if (el.grandTotal) el.grandTotal.textContent = formatRupiah(grandTotal);

        updateChangeDisplay();

        if (state.cart.length === 0) {
            el.cartList.innerHTML = `
                <div class="py-8 text-center text-four/50">
                    <svg class="w-8 h-8 mx-auto mb-2 text-four/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-xs">Keranjang masih kosong. Pilih produk di sebelah kiri.</p>
                </div>
            `;
            return;
        }

        el.cartList.innerHTML = state.cart
            .map((item) => {
                const subtotal = item.jumlah * item.harga_satuan;
                return `
                    <div class="p-3 rounded-lg bg-two/50 border border-two flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-four truncate">${escapeHtml(item.nama_produk)}</p>
                                <p class="text-xs text-four/60">${formatRupiah(item.harga_satuan)} / ${escapeHtml(item.satuan)}</p>
                            </div>
                            <button type="button"
                                    data-remove-cart="${escapeHtml(item.product_id)}"
                                    title="Hapus Item"
                                    class="p-1 rounded-lg text-four/50 hover:text-three hover:bg-one/60 active:scale-95 transition-all cursor-pointer shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1">
                            <div class="inline-flex items-center rounded-lg bg-one border border-four/10 overflow-hidden">
                                <button type="button"
                                        data-qty-dec="${escapeHtml(item.product_id)}"
                                        class="px-2.5 py-1 text-four/80 hover:bg-two hover:text-four active:scale-95 transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                    </svg>
                                </button>
                                <input type="number"
                                       min="1"
                                       max="${item.stok}"
                                       value="${item.jumlah}"
                                       data-qty-input="${escapeHtml(item.product_id)}"
                                       class="w-12 text-center bg-transparent text-xs font-semibold text-four focus:outline-none">
                                <button type="button"
                                        data-qty-inc="${escapeHtml(item.product_id)}"
                                        class="px-2.5 py-1 text-four/80 hover:bg-two hover:text-four active:scale-95 transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>

                            <span class="text-sm font-bold text-four">${formatRupiah(subtotal)}</span>
                        </div>
                    </div>
                `;
            })
            .join("");
    }

    function updateChangeDisplay() {
        if (!el.changeAmount) return;
        const { grandTotal } = getCartTotals();
        const cash = parseRupiahValue(el.inputCash?.value || "0");
        const diff = cash - grandTotal;

        if (grandTotal === 0) {
            el.changeAmount.textContent = "Rp 0";
            el.changeAmount.className = "font-bold text-four";
            return;
        }

        if (diff < 0) {
            el.changeAmount.textContent = `Kurang ${formatRupiah(Math.abs(diff))}`;
            el.changeAmount.className = "font-bold text-three";
        } else {
            el.changeAmount.textContent = formatRupiah(diff);
            el.changeAmount.className = "font-bold text-four";
        }
    }

    // 5. Submit Transaksi ke API (/api/sales -> /api/sales/{id}/items -> PUT /api/sales/{id})
    async function submitTransaction(targetStatus) {
        if (state.isSubmitting) return;
        hideErrors();

        if (state.cart.length === 0) {
            showErrors({ message: "Keranjang belanja masih kosong. Tambahkan minimal 1 produk." });
            return;
        }

        const nomorTransaksi = el.inputTrxNumber?.value.trim() || "";
        const tanggal = el.inputTanggal?.value.trim() || "";
        const metode = el.inputPayment?.value.trim() || "Tunai";
        const { grandTotal } = getCartTotals();
        const cashReceived = parseRupiahValue(el.inputCash?.value || "0");

        if (!nomorTransaksi) {
            showErrors({ message: "Nomor transaksi wajib diisi." });
            return;
        }
        if (!tanggal) {
            showErrors({ message: "Tanggal transaksi wajib diisi." });
            return;
        }

        if (targetStatus === "selesai" && metode === "Tunai" && cashReceived < grandTotal) {
            showErrors({
                message: `Uang diterima (${formatRupiah(cashReceived)}) masih kurang dari total tagihan (${formatRupiah(grandTotal)}).`,
            });
            return;
        }

        if (targetStatus === "selesai") {
            const confirmed = await customConfirm({
                title: "Selesaikan Transaksi",
                message: `Proses transaksi ${nomorTransaksi} senilai ${formatRupiah(grandTotal)} (${metode})?`,
                confirmText: "Ya, Selesaikan",
                cancelText: "Batal",
            });
            if (!confirmed) return;
        }

        state.isSubmitting = true;
        setSubmittingState(true);

        let createdSale = null;

        try {
            // 1. Buat header transaksi sebagai 'pending' agar item dapat ditambahkan
            const saleRes = await fetchData("/sales", {
                method: "POST",
                body: JSON.stringify({
                    nomor_transaksi: nomorTransaksi,
                    tanggal,
                    status: "pending",
                    metode_pembayaran: metode,
                }),
            });

            createdSale = saleRes?.data;
            if (!createdSale?.id) {
                throw { message: "Gagal membuat transaksi penjualan." };
            }

            // 2. Tambahkan seluruh item keranjang ke /api/sales/{saleId}/items
            for (const item of state.cart) {
                await fetchData(`/sales/${createdSale.id}/items`, {
                    method: "POST",
                    body: JSON.stringify({
                        product_id: item.product_id,
                        jumlah: item.jumlah,
                        harga_satuan: item.harga_satuan,
                    }),
                });
            }

            // 3. Jika targetStatus 'selesai', perbarui status transaksi
            let finalStatus = "pending";
            if (targetStatus === "selesai") {
                try {
                    const updateRes = await fetchData(`/sales/${createdSale.id}`, {
                        method: "PUT",
                        body: JSON.stringify({
                            status: "selesai",
                            metode_pembayaran: metode,
                        }),
                    });
                    finalStatus = updateRes?.data?.status || "selesai";
                } catch (updateErr) {
                    if (updateErr?.status === 403) {
                        showToast(
                            "Transaksi & item tersimpan sebagai pending (butuh persetujuan Admin untuk status selesai).",
                            "error"
                        );
                    } else {
                        throw updateErr;
                    }
                }
            }

            const snapshotCart = [...state.cart];
            showToast(
                finalStatus === "selesai"
                    ? `Transaksi ${nomorTransaksi} berhasil diselesaikan.`
                    : `Transaksi ${nomorTransaksi} disimpan sebagai pending.`,
                "success"
            );

            openReceiptModal({
                nomor_transaksi: nomorTransaksi,
                tanggal,
                metode_pembayaran: metode,
                status: finalStatus,
                items: snapshotCart,
                total: grandTotal,
                cash: cashReceived,
            });

            resetTransactionForm();
            await loadCatalog();
        } catch (error) {
            showErrors(error);
            showToast(error.message || "Gagal memproses transaksi.", "error");
        } finally {
            state.isSubmitting = false;
            setSubmittingState(false);
        }
    }

    function setSubmittingState(disabled) {
        if (el.btnSavePending) el.btnSavePending.disabled = disabled;
        if (el.btnCompleteSale) el.btnCompleteSale.disabled = disabled;
    }

    function openReceiptModal(receipt) {
        if (!el.receiptModal || !el.receiptContent) return;

        const change = Math.max(0, receipt.cash - receipt.total);
        const statusLabel = receipt.status === "selesai" ? "Selesai" : "Pending";
        const statusClass =
            receipt.status === "selesai"
                ? "bg-three/20 border-three text-three"
                : "bg-two border-four/15 text-four/80";

        el.receiptContent.innerHTML = `
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-two">
                <div>
                    <p class="font-mono text-xs text-three font-semibold">${escapeHtml(receipt.nomor_transaksi)}</p>
                    <p class="text-xs text-four/60 mt-0.5">${escapeHtml(receipt.tanggal)} • ${escapeHtml(receipt.metode_pembayaran)}</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg border text-xs font-semibold ${statusClass}">
                    ${statusLabel}
                </span>
            </div>

            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                ${receipt.items
                    .map(
                        (item) => `
                    <div class="flex items-center justify-between gap-2 text-xs">
                        <div class="min-w-0">
                            <p class="font-semibold text-four truncate">${escapeHtml(item.nama_produk)}</p>
                            <p class="text-four/60">${item.jumlah} x ${formatRupiah(item.harga_satuan)}</p>
                        </div>
                        <span class="font-semibold text-four shrink-0">${formatRupiah(item.jumlah * item.harga_satuan)}</span>
                    </div>
                `
                    )
                    .join("")}
            </div>

            <div class="space-y-1.5 pt-3 border-t border-two text-sm">
                <div class="flex justify-between">
                    <span class="text-four/70">Total Tagihan</span>
                    <span class="font-bold text-four">${formatRupiah(receipt.total)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-four/70">Uang Diterima</span>
                    <span class="font-semibold text-four">${formatRupiah(receipt.cash)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-four/70">Kembalian</span>
                    <span class="font-bold text-three">${formatRupiah(change)}</span>
                </div>
            </div>
        `;

        el.receiptModal.classList.remove("hidden");
        requestAnimationFrame(() => {
            el.receiptModal.classList.remove("opacity-0");
            el.receiptCard?.classList.remove("scale-95", "translate-y-4");
            el.receiptCard?.classList.add("scale-100", "translate-y-0");
        });
    }

    function closeReceiptModal() {
        el.receiptModal?.classList.add("opacity-0");
        el.receiptCard?.classList.remove("scale-100", "translate-y-0");
        el.receiptCard?.classList.add("scale-95", "translate-y-4");
        setTimeout(() => {
            el.receiptModal?.classList.add("hidden");
        }, 200);
    }

    // 6. Helper Form, Nomor Transaksi & Error
    function generateTrxNumber() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, "0");
        const d = String(now.getDate()).padStart(2, "0");
        const rand = String(Math.floor(1000 + Math.random() * 9000));
        return `TRX-${y}${m}${d}-${rand}`;
    }

    function getTodayDateString() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, "0");
        const d = String(now.getDate()).padStart(2, "0");
        return `${y}-${m}-${d}`;
    }

    function resetTransactionForm() {
        state.cart = [];
        if (el.inputTrxNumber) el.inputTrxNumber.value = generateTrxNumber();
        setSelectedDate(getTodayDateString());
        if (el.inputCash) el.inputCash.value = "0";
        hideErrors();
        renderCart();
        renderProducts();
    }

    function showErrors(errorObj) {
        if (!el.errorBox || !el.errorList) return;
        const messages = [];

        if (errorObj?.errors && typeof errorObj.errors === "object") {
            Object.values(errorObj.errors).forEach((arr) => {
                if (Array.isArray(arr)) messages.push(...arr);
            });
        }
        if (messages.length === 0 && errorObj?.message) {
            messages.push(errorObj.message);
        }

        el.errorList.innerHTML = messages
            .map(
                (m) =>
                    `<li class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-three shrink-0"></span><span>${escapeHtml(m)}</span></li>`
            )
            .join("");
        el.errorBox.classList.remove("hidden");
    }

    function hideErrors() {
        if (!el.errorBox || !el.errorList) return;
        el.errorList.innerHTML = "";
        el.errorBox.classList.add("hidden");
    }

    function formatRupiah(value) {
        const formatted = formatRibuan(Math.round(Number(value || 0)));
        return `Rp ${formatted || "0"}`;
    }

    function bindEvents() {
        setupCustomDropdown(el.dropdownCategory, (val) => {
            state.categoryFilter = val;
            renderProducts();
        });

        setupCustomDatePicker();

        setupCustomDropdown(el.dropdownPayment, (val) => {
            state.paymentMethod = val;
        });

        document.addEventListener("click", () => {
            closeAllCustomDropdowns();
        });

        el.searchInput?.addEventListener("input", (e) => {
            state.searchQuery = e.target.value;
            renderProducts();
        });

        // Tekan Enter saat scan barcode / SKU langsung tambah ke keranjang
        el.searchInput?.addEventListener("keydown", (e) => {
            if (e.key !== "Enter") return;
            e.preventDefault();
            const q = state.searchQuery.trim().toLowerCase();
            if (!q) return;

            const exactMatch = state.products.find(
                (p) =>
                    (p.barcode && p.barcode.toLowerCase() === q) ||
                    (p.sku && p.sku.toLowerCase() === q)
            );
            const target = exactMatch || (getFilteredProducts().length === 1 ? getFilteredProducts()[0] : null);
            if (target) {
                addToCart(target.id);
                state.searchQuery = "";
                el.searchInput.value = "";
                renderProducts();
            }
        });

        el.productGrid?.addEventListener("click", (e) => {
            const btn = e.target.closest("button[data-add-product]");
            if (!btn || btn.disabled) return;
            addToCart(btn.dataset.addProduct);
        });

        el.cartList?.addEventListener("click", (e) => {
            const removeBtn = e.target.closest("button[data-remove-cart]");
            if (removeBtn) {
                removeFromCart(removeBtn.dataset.removeCart);
                return;
            }

            const decBtn = e.target.closest("button[data-qty-dec]");
            if (decBtn) {
                const id = decBtn.dataset.qtyDec;
                const item = state.cart.find((c) => c.product_id === id);
                if (item) updateCartQty(id, item.jumlah - 1);
                return;
            }

            const incBtn = e.target.closest("button[data-qty-inc]");
            if (incBtn) {
                const id = incBtn.dataset.qtyInc;
                const item = state.cart.find((c) => c.product_id === id);
                if (item) updateCartQty(id, item.jumlah + 1);
            }
        });

        el.cartList?.addEventListener("change", (e) => {
            const qtyInput = e.target.closest("input[data-qty-input]");
            if (!qtyInput) return;
            updateCartQty(qtyInput.dataset.qtyInput, qtyInput.value);
        });

        el.inputCash?.addEventListener("input", () => {
            updateChangeDisplay();
        });

        el.quickCashBtns?.forEach((btn) => {
            btn.addEventListener("click", () => {
                const mode = btn.dataset.quickCash;
                const { grandTotal } = getCartTotals();
                const current = parseRupiahValue(el.inputCash?.value || "0");
                const nextVal = mode === "exact" ? grandTotal : current + Number(mode);
                if (el.inputCash) {
                    el.inputCash.value = formatRibuan(nextVal) || "0";
                }
                updateChangeDisplay();
            });
        });

        el.btnGenerateTrx?.addEventListener("click", () => {
            if (el.inputTrxNumber) el.inputTrxNumber.value = generateTrxNumber();
        });

        el.btnRefreshProducts?.addEventListener("click", () => {
            loadCatalog();
        });

        el.btnResetCart?.addEventListener("click", async () => {
            if (state.cart.length === 0) return;
            const confirmed = await customConfirm({
                title: "Kosongkan Keranjang",
                message: "Semua item di keranjang belanja akan dihapus. Lanjutkan?",
                confirmText: "Ya, Kosongkan",
                cancelText: "Batal",
            });
            if (confirmed) {
                resetTransactionForm();
            }
        });

        el.btnSavePending?.addEventListener("click", () => submitTransaction("pending"));
        el.btnCompleteSale?.addEventListener("click", () => submitTransaction("selesai"));

        el.btnCloseReceipt?.forEach((btn) => btn.addEventListener("click", closeReceiptModal));
        el.btnNewTrxAfterReceipt?.addEventListener("click", () => {
            closeReceiptModal();
            el.searchInput?.focus();
        });

        window.addEventListener("auth:changed", () => {
            loadCatalog();
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
        if (el.inputTrxNumber) el.inputTrxNumber.value = generateTrxNumber();
        setSelectedDate(getTodayDateString());
        bindEvents();
        renderCart();
        await loadCatalog();
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    SalesCreatePage.init();
});
