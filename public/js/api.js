const API_BASE_URL = "/api";

async function fetchData(endpoint, options = {}) {
    const token = localStorage.getItem("access_token");
    const isFormData = options.body instanceof FormData;

    const headers = {
        "Accept": "application/json",
        ...(isFormData ? {} : { "Content-Type": "application/json" }),
        ...options.headers,
    };

    if (token) {
        headers["Authorization"] = `Bearer ${token}`;
    }

    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        ...options,
        headers,
    });

    if (response.status === 401 && window.location.pathname !== "/login" && window.location.pathname !== "/register") {
        localStorage.removeItem("access_token");
        localStorage.removeItem("user");
        syncAuthAndRoleUI();
        window.location.href = "/login";
        return;
    }

    const result = await response.json();

    if (!response.ok) {
        throw {
            status: response.status,
            message: result.message || "Terjadi kesalahan pada permintaan.",
            errors: result.errors || null,
        };
    }
    return result;
}

/**
 * Ambil objek user yang sedang login (dari localStorage atau session Blade)
 */
function getCurrentUser() {
    try {
        const raw = localStorage.getItem("user");
        if (raw) {
            return JSON.parse(raw);
        }
    } catch (_) {}

    if (window.SERVER_USER) {
        return window.SERVER_USER;
    }
    return null;
}

/**
 * Ambil role user saat ini ('superadmin' | 'admin' | 'staff' | 'kasir' | null)
 */
function getUserRole() {
    const user = getCurrentUser();
    return user?.role || null;
}

/**
 * Cek apakah role user saat ini termasuk dalam daftar role yang diizinkan
 */
function hasRole(allowedRoles = []) {
    const role = getUserRole();
    if (!role) return false;
    return allowedRoles.includes(role);
}

/**
 * Sinkronisasi seluruh elemen UI yang bergantung pada autentikasi & role (atribut data-roles)
 */
function syncAuthAndRoleUI() {
    const user = getCurrentUser();
    const role = user?.role || null;

    // 1. Tampilkan atau sembunyikan elemen yang memiliki atribut data-roles="superadmin,admin"
    document.querySelectorAll("[data-roles]").forEach((el) => {
        const allowed = el.dataset.roles
            .split(",")
            .map((r) => r.trim())
            .filter(Boolean);

        if (role && allowed.includes(role)) {
            el.classList.remove("hidden");
        } else {
            el.classList.add("hidden");
        }
    });

    // 2. Sinkronisasi tampilan Profil vs Tombol Masuk/Daftar di UpperNav
    const guestNav = document.getElementById("navGuestActions");
    const authNav = document.getElementById("navAuthProfile");
    const navUserName = document.getElementById("navUserName");
    const navUserEmail = document.getElementById("navUserEmail");
    const navUserRole = document.getElementById("navUserRole");
    const navUserAvatar = document.getElementById("navUserAvatar");

    if (user && role) {
        guestNav?.classList.add("hidden");
        authNav?.classList.remove("hidden");
        if (navUserName) navUserName.textContent = user.name || "Pengguna";
        if (navUserEmail) navUserEmail.textContent = user.email || "";
        if (navUserRole) navUserRole.textContent = role;
        if (navUserAvatar) {
            navUserAvatar.src =
                user.avatar_url ||
                (user.avatar ? `/storage/${user.avatar}` : "/image/blankFoto.png");
        }
    } else {
        guestNav?.classList.remove("hidden");
        authNav?.classList.add("hidden");
    }
}

/**
 * Fungsi Keluar (Logout) dari sesi API & bersihkan localStorage
 */
async function logoutUser() {
    try {
        if (localStorage.getItem("access_token")) {
            await fetchData("/logout", { method: "POST" });
        }
    } catch (_) {
    } finally {
        localStorage.removeItem("access_token");
        localStorage.removeItem("user");
        syncAuthAndRoleUI();
        window.location.href = "/login";
    }
}

/**
 * Dialog Konfirmasi & Alert Kustom Global (Menggantikan window.confirm & window.alert)
 */
function customConfirm({
    title = "Konfirmasi Tindakan",
    message = "Apakah Anda yakin ingin melanjutkan tindakan ini?",
    confirmText = "Ya, Lanjutkan",
    cancelText = "Batal",
    isAlertOnly = false,
} = {}) {
    return new Promise((resolve) => {
        const modal = document.getElementById("globalDialogModal");
        const card = document.getElementById("globalDialogCard");
        const titleEl = document.getElementById("globalDialogTitle");
        const msgEl = document.getElementById("globalDialogMessage");
        const confirmBtn = document.getElementById("globalDialogConfirmBtn");
        const confirmTextEl = document.getElementById("globalDialogConfirmText");
        const cancelBtn = document.getElementById("globalDialogCancelBtn");
        const closeBtn = document.getElementById("globalDialogCloseBtn");
        const backdrop = document.getElementById("globalDialogBackdrop");

        if (!modal) {
            resolve(false);
            return;
        }

        if (titleEl) titleEl.textContent = title;
        if (msgEl) msgEl.textContent = message;
        if (confirmTextEl) confirmTextEl.textContent = confirmText;
        if (cancelBtn) {
            cancelBtn.textContent = cancelText;
            if (isAlertOnly) {
                cancelBtn.classList.add("hidden");
            } else {
                cancelBtn.classList.remove("hidden");
            }
        }

        modal.classList.remove("hidden");
        requestAnimationFrame(() => {
            modal.classList.remove("opacity-0");
            card?.classList.remove("scale-95", "translate-y-4");
            card?.classList.add("scale-100", "translate-y-0");
        });

        function cleanup(result) {
            modal.classList.add("opacity-0");
            card?.classList.remove("scale-100", "translate-y-0");
            card?.classList.add("scale-95", "translate-y-4");

            confirmBtn?.removeEventListener("click", onConfirm);
            cancelBtn?.removeEventListener("click", onCancel);
            closeBtn?.removeEventListener("click", onCancel);
            backdrop?.removeEventListener("click", onCancel);

            setTimeout(() => {
                modal.classList.add("hidden");
                resolve(result);
            }, 200);
        }

        function onConfirm() {
            cleanup(true);
        }

        function onCancel() {
            cleanup(false);
        }

        confirmBtn?.addEventListener("click", onConfirm);
        cancelBtn?.addEventListener("click", onCancel);
        closeBtn?.addEventListener("click", onCancel);
        backdrop?.addEventListener("click", onCancel);
    });
}

function customAlert({
    title = "Pemberitahuan",
    message = "",
    confirmText = "Mengerti",
} = {}) {
    return customConfirm({
        title,
        message,
        confirmText,
        isAlertOnly: true,
    });
}

/**
 * Helper Format & Parse Input Rupiah Otomatis (Contoh: 15000 -> "15.000")
 */
function formatRibuan(value) {
    if (value === null || value === undefined || value === "") return "";
    // Jika dari database berupa desimal seperti "15000.00", bulatkan dulu angkanya
    const numValue =
        typeof value === "number"
            ? Math.round(value)
            : /^\d+(\.\d+)?$/.test(String(value).trim())
            ? Math.round(Number(value))
            : null;

    const digits =
        numValue !== null
            ? String(numValue)
            : String(value).replace(/[^0-9]/g, "");

    if (!digits) return "";
    const cleaned = digits.replace(/^0+(?=\d)/, "");
    return cleaned.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function parseRupiahValue(formattedValue) {
    if (formattedValue === null || formattedValue === undefined) return 0;
    const digits = String(formattedValue).replace(/[^0-9]/g, "");
    return digits ? Number(digits) : 0;
}

function initRupiahInputs(root = document) {
    root.querySelectorAll("input[data-rupiah]").forEach((input) => {
        if (input.dataset.rupiahBound === "true") return;
        input.dataset.rupiahBound = "true";

        input.addEventListener("input", () => {
            const rawDigits = input.value.replace(/[^0-9]/g, "");
            input.value = rawDigits ? formatRibuan(rawDigits) : "";
        });

        input.addEventListener("focus", () => {
            if (input.value === "0") {
                input.value = "";
            }
        });

        input.addEventListener("blur", () => {
            if (!input.value.trim()) {
                input.value = "0";
            }
        });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    syncAuthAndRoleUI();
    initRupiahInputs();

    const btnLogout = document.getElementById("btnLogoutAction");
    btnLogout?.addEventListener("click", async (e) => {
        e.preventDefault();
        await logoutUser();
    });
});
