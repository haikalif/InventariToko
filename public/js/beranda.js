/**
 * public/js/beranda.js
 * Pengelola halaman Beranda (/):
 * - Jika Guest: tampilkan pengenalan aplikasi MyInventaris
 * - Jika Login: tampilkan ringkasan KPI, grafik penjualan 7 hari, proporsi mutasi stok, & daftar cepat
 */

const BerandaPage = (() => {
    const el = {};

    function initDomRefs() {
        el.guestSection = document.getElementById("berandaGuestSection");
        el.authSection = document.getElementById("berandaAuthSection");
        el.greetingTitle = document.getElementById("dashGreetingTitle");
        el.greetingSubtitle = document.getElementById("dashGreetingSubtitle");
        el.btnRefresh = document.getElementById("btnRefreshDashboard");

        el.totalProducts = document.getElementById("dashTotalProducts");
        el.lowStockHint = document.getElementById("dashLowStockHint");
        el.totalCategories = document.getElementById("dashTotalCategories");
        el.totalRevenue = document.getElementById("dashTotalRevenue");
        el.totalSalesCount = document.getElementById("dashTotalSalesCount");
        el.totalMovements = document.getElementById("dashTotalMovements");

        el.salesChart = document.getElementById("dashSalesChart");
        el.movementChart = document.getElementById("dashMovementChart");
        el.lowStockList = document.getElementById("dashLowStockList");
        el.recentSalesList = document.getElementById("dashRecentSalesList");
    }

    async function fetchAllPaginated(baseEndpoint) {
        const firstRes = await fetchData(`${baseEndpoint}?page=1`, { method: "GET" });
        const items = [...(firstRes?.data || [])];
        const total = firstRes?.meta?.total ?? items.length;
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
        return { items, total };
    }

    function formatRupiah(value) {
        const formatted = formatRibuan(Math.round(Number(value || 0)));
        return `Rp ${formatted || "0"}`;
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

    async function loadDashboard() {
        const user = typeof getCurrentUser === "function" ? getCurrentUser() : null;
        const token = localStorage.getItem("access_token");

        if (!user || (!token && !window.SERVER_USER)) {
            el.guestSection?.classList.remove("hidden");
            el.authSection?.classList.add("hidden");
            return;
        }

        el.guestSection?.classList.add("hidden");
        el.authSection?.classList.remove("hidden");

        if (el.greetingTitle) {
            el.greetingTitle.textContent = `Selamat Datang, ${user.name || "Pengguna"}`;
        }
        if (el.greetingSubtitle) {
            el.greetingSubtitle.textContent = `Masuk sebagai ${String(user.role || "pengguna").toUpperCase()} — ringkasan operasional toko MyInventaris.`;
        }

        try {
            const [prodRes, catRes, salesRes, movRes] = await Promise.allSettled([
                fetchAllPaginated("/products"),
                fetchData("/categories?page=1", { method: "GET" }),
                fetchAllPaginated("/sales"),
                fetchData("/stock-movements?page=1", { method: "GET" }),
            ]);

            const products = prodRes.status === "fulfilled" ? prodRes.value.items : [];
            const totalProducts = prodRes.status === "fulfilled" ? prodRes.value.total : 0;

            const totalCategories =
                catRes.status === "fulfilled" ? catRes.value?.meta?.total ?? (catRes.value?.data?.length || 0) : 0;

            const sales = salesRes.status === "fulfilled" ? salesRes.value.items : [];
            const totalSales = salesRes.status === "fulfilled" ? salesRes.value.total : 0;

            const movements = movRes.status === "fulfilled" ? movRes.value?.data || [] : [];
            const totalMovements =
                movRes.status === "fulfilled" ? movRes.value?.meta?.total ?? movements.length : 0;

            const lowStockProducts = products.filter(
                (p) => p.aktif !== false && Number(p.stok || 0) <= Number(p.stok_minimum || 0)
            );
            const validSales = sales.filter((s) => s.status !== "dibatalkan");
            const totalRevenue = validSales.reduce((sum, s) => sum + Number(s.total || 0), 0);

            if (el.totalProducts) el.totalProducts.textContent = totalProducts;
            if (el.lowStockHint) {
                el.lowStockHint.textContent =
                    lowStockProducts.length > 0
                        ? `${lowStockProducts.length} produk perlu stok ulang`
                        : "Semua stok dalam kondisi aman";
            }
            if (el.totalCategories) el.totalCategories.textContent = totalCategories;
            if (el.totalRevenue) el.totalRevenue.textContent = formatRupiah(totalRevenue);
            if (el.totalSalesCount) el.totalSalesCount.textContent = `${totalSales} transaksi tercatat`;
            if (el.totalMovements) el.totalMovements.textContent = totalMovements;

            renderSalesChart(validSales);
            renderMovementChart(movements);
            renderLowStockList(lowStockProducts);
            renderRecentSalesList(sales.slice(0, 5));
        } catch (_) {}
    }

    function renderSalesChart(validSales) {
        if (!el.salesChart) return;

        const days = [];
        const shortDayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
        for (let i = 6; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            const dateStr = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
            days.push({
                dateStr,
                label: `${shortDayNames[d.getDay()]} ${d.getDate()}`,
                total: 0,
            });
        }

        validSales.forEach((s) => {
            const target = days.find((d) => d.dateStr === s.tanggal);
            if (target) {
                target.total += Number(s.total || 0);
            }
        });

        const maxVal = Math.max(...days.map((d) => d.total), 1);

        el.salesChart.innerHTML = days
            .map((d) => {
                const heightPct = d.total > 0 ? Math.max(10, Math.round((d.total / maxVal) * 100)) : 4;
                return `
                    <div class="group flex-1 min-w-0 flex flex-col items-center justify-end h-full gap-1.5">
                        <span class="text-[10px] font-semibold text-four/70 opacity-0 group-hover:opacity-100 transition-opacity truncate max-w-full">
                            ${formatRupiah(d.total)}
                        </span>
                        <div class="w-full h-36 flex items-end" style="height: 144px;">
                            <div style="height: ${heightPct}%"
                                 title="${escapeHtml(d.dateStr)}: ${formatRupiah(d.total)}"
                                 class="w-full rounded-t-lg ${
                                     d.total > 0
                                         ? "bg-three/80 group-hover:bg-three"
                                         : "bg-two/70"
                                 } transition-all duration-300"></div>
                        </div>
                        <span class="text-[11px] text-four/60 font-medium whitespace-nowrap">${escapeHtml(d.label)}</span>
                    </div>
                `;
            })
            .join("");
    }

    function renderMovementChart(movements) {
        if (!el.movementChart) return;

        const total = movements.length || 1;
        const masuk = movements.filter((m) => m.tipe === "masuk").length;
        const keluar = movements.filter((m) => m.tipe === "keluar").length;
        const retur = movements.filter((m) => m.tipe === "retur").length;

        const rows = [
            { label: "Stok Masuk", count: masuk, barClass: "bg-four" },
            { label: "Stok Keluar", count: keluar, barClass: "bg-three" },
            { label: "Retur", count: retur, barClass: "bg-four/60" },
        ];

        el.movementChart.innerHTML = rows
            .map((r) => {
                const pct = movements.length > 0 ? Math.round((r.count / total) * 100) : 0;
                return `
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-four/85">${r.label}</span>
                            <span class="font-semibold text-four">${r.count} (${pct}%)</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-one overflow-hidden border border-four/10">
                            <div style="width: ${pct}%" class="h-full rounded-full ${r.barClass} transition-all duration-500"></div>
                        </div>
                    </div>
                `;
            })
            .join("");
    }

    function renderLowStockList(items) {
        if (!el.lowStockList) return;
        if (items.length === 0) {
            el.lowStockList.innerHTML = `<p class="text-xs text-four/55 py-4 text-center">Semua produk memiliki stok yang mencukupi.</p>`;
            return;
        }

        el.lowStockList.innerHTML = items
            .slice(0, 6)
            .map(
                (p) => `
                <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg bg-one/70 border border-two">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-four truncate">${escapeHtml(p.nama_produk)}</p>
                        <p class="font-mono text-[11px] text-four/50">${escapeHtml(p.sku || "-")}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-three/20 border border-three/40 text-[11px] font-semibold text-three shrink-0">
                        Sisa ${escapeHtml(p.stok)} (Min ${escapeHtml(p.stok_minimum)})
                    </span>
                </div>
            `
            )
            .join("");
    }

    function renderRecentSalesList(sales) {
        if (!el.recentSalesList) return;
        if (sales.length === 0) {
            el.recentSalesList.innerHTML = `<p class="text-xs text-four/55 py-4 text-center">Belum ada transaksi penjualan.</p>`;
            return;
        }

        el.recentSalesList.innerHTML = sales
            .map(
                (s) => `
                <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg bg-one/70 border border-two">
                    <div class="min-w-0">
                        <p class="font-mono text-xs font-semibold text-three truncate">${escapeHtml(s.nomor_transaksi)}</p>
                        <p class="text-[11px] text-four/60">${escapeHtml(s.tanggal || "-")} • ${escapeHtml(s.metode_pembayaran || "-")}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs font-bold text-four">${formatRupiah(s.total)}</p>
                        <p class="text-[11px] text-four/55 capitalize">${escapeHtml(s.status || "-")}</p>
                    </div>
                </div>
            `
            )
            .join("");
    }

    function bindEvents() {
        el.btnRefresh?.addEventListener("click", () => {
            loadDashboard();
        });
        window.addEventListener("auth:changed", () => {
            loadDashboard();
        });
    }

    async function init() {
        initDomRefs();
        bindEvents();
        await loadDashboard();
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    BerandaPage.init();
});
