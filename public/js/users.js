/**
 * public/js/users.js
 * Pengirim request & pengelola UI halaman Kelola Karyawan (/users)
 * Menggunakan helper fetchData(), customConfirm(), getCurrentUser(), hasRole() dari public/js/api.js
 */

const UsersPage = (() => {
    let state = {
        mode: "active",
        currentPage: 1,
        meta: null,
        users: [],
        searchQuery: "",
        roleFilter: "",
    };

    const el = {};

    function initDomRefs() {
        el.tableBody = document.getElementById("userTableBody");
        el.paginationInfo = document.getElementById("paginationInfo");
        el.btnPrevPage = document.getElementById("btnPrevPage");
        el.btnNextPage = document.getElementById("btnNextPage");

        el.tabActive = document.getElementById("tabActiveUsers");
        el.tabTrashed = document.getElementById("tabTrashedUsers");

        el.searchInput = document.getElementById("filterSearchUser");
        el.dropdownFilterRole = document.getElementById("dropdownFilterUserRole");
        el.btnRefresh = document.getElementById("btnRefreshUsers");
        el.btnOpenCreate = document.getElementById("btnOpenCreateUserModal");

        el.statTotal = document.getElementById("statTotalUsers");
        el.statPage = document.getElementById("statPageUsers");
        el.statAdmin = document.getElementById("statAdminUsers");
        el.statStaffKasir = document.getElementById("statStaffKasirUsers");

        el.formModal = document.getElementById("userFormModal");
        el.formModalCard = document.getElementById("userFormCard");
        el.formModalTitle = document.getElementById("userFormTitle");
        el.form = document.getElementById("userForm");
        el.formErrorBox = document.getElementById("userFormErrorBox");
        el.formErrorList = document.getElementById("userFormErrorList");
        el.btnSubmitForm = document.getElementById("btnSubmitUserForm");
        el.btnCloseForm = document.querySelectorAll(".btn-close-user-form-modal");

        el.inputId = document.getElementById("user_id");
        el.inputName = document.getElementById("user_name");
        el.inputEmail = document.getElementById("user_email");
        el.dropdownRoleForm = document.getElementById("dropdownUserRoleForm");
        el.inputRole = document.getElementById("user_role");
        el.inputPassword = document.getElementById("user_password");
        el.inputPasswordConfirm = document.getElementById("user_password_confirmation");
        el.passwordRequiredStar = document.getElementById("passwordRequiredStar");
        el.passwordConfirmStar = document.getElementById("passwordConfirmStar");
        el.passwordEditHint = document.getElementById("passwordEditHint");

        el.detailModal = document.getElementById("userDetailModal");
        el.detailCard = document.getElementById("userDetailCard");
        el.detailContent = document.getElementById("userDetailContent");
        el.btnCloseDetail = document.querySelectorAll(".btn-close-user-detail-modal");

        el.avatarPreviewModal = document.getElementById("userAvatarPreviewModal");
        el.avatarPreviewCard = document.getElementById("userAvatarPreviewCard");
        el.avatarPreviewTitle = document.getElementById("userAvatarPreviewTitle");
        el.avatarPreviewImg = document.getElementById("userAvatarPreviewImg");
        el.btnCloseAvatarPreview = document.querySelectorAll(".btn-close-avatar-preview");

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
                onSelectCallback(val, text);
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

    // 2. Pengirim Request ke API
    async function loadUsers(page = 1) {
        state.currentPage = page;
        renderLoadingTable();

        const token = localStorage.getItem("access_token");
        if (!token && !window.SERVER_USER) {
            window.location.href = "/login";
            return;
        }

        const endpoint =
            state.mode === "trashed"
                ? `/users/trashed?page=${page}`
                : `/users?page=${page}`;

        try {
            const response = await fetchData(endpoint, { method: "GET" });
            state.users = response?.data || [];
            state.meta = response?.meta || null;

            updateStats();
            renderTable();
            renderPagination();
        } catch (error) {
            renderEmptyTable(error.message || "Gagal memuat data karyawan.");
            showToast(error.message || "Gagal memuat data karyawan", "error");
        }
    }

    async function fetchUserDetail(id) {
        try {
            const response = await fetchData(`/users/${id}`, { method: "GET" });
            return response?.user;
        } catch (error) {
            showToast(error.message || "Gagal mengambil rincian karyawan", "error");
            throw error;
        }
    }

    async function createUser(payload) {
        // Daftar via /api/register (tanpa menyentuh token sesi admin di localStorage)
        const regRes = await fetchData("/register", {
            method: "POST",
            body: JSON.stringify({
                name: payload.name,
                email: payload.email,
                password: payload.password,
                password_confirmation: payload.password_confirmation,
            }),
        });

        const newUser = regRes?.user;
        if (newUser?.id && payload.role && payload.role !== "kasir") {
            await fetchData(`/users/${newUser.id}`, {
                method: "PUT",
                body: JSON.stringify({ role: payload.role }),
            });
        }
        return regRes;
    }

    async function updateUser(id, payload) {
        const body = {
            name: payload.name,
            email: payload.email,
            role: payload.role,
        };
        if (payload.password) {
            body.password = payload.password;
            body.password_confirmation = payload.password_confirmation;
        }
        return await fetchData(`/users/${id}`, {
            method: "PUT",
            body: JSON.stringify(body),
        });
    }

    async function deleteUser(id, name) {
        const confirmed = await customConfirm({
            title: "Pindahkan ke Tong Sampah",
            message: `Akun karyawan "${name}" akan dinonaktifkan dan dipindahkan ke tong sampah. Lanjutkan?`,
            confirmText: "Ya, Hapus",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/users/${id}`, { method: "DELETE" });
            showToast(res?.message || "Karyawan berhasil dihapus", "success");
            loadUsers(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus karyawan", "error");
        }
    }

    async function restoreUser(id, name) {
        try {
            const res = await fetchData(`/users/${id}/restore`, { method: "POST" });
            showToast(res?.message || `Akun "${name}" berhasil dipulihkan`, "success");
            loadUsers(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal memulihkan akun", "error");
        }
    }

    async function forceDeleteUser(id, name) {
        const confirmed = await customConfirm({
            title: "Hapus Akun Secara Permanen",
            message: `Akun "${name}" akan dihapus permanen dari database dan tidak dapat dikembalikan. Lanjutkan?`,
            confirmText: "Hapus Permanen",
            cancelText: "Batal",
        });
        if (!confirmed) return;

        try {
            const res = await fetchData(`/users/${id}/force-delete`, { method: "DELETE" });
            showToast(res?.message || "Akun dihapus permanen", "success");
            loadUsers(state.currentPage);
        } catch (error) {
            showToast(error.message || "Gagal menghapus permanen akun", "error");
        }
    }

    // 3. Render Statistik, Tabel & Pagination
    function getRoleBadge(role) {
        const r = (role || "").toLowerCase();
        if (r === "superadmin") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-three/20 border border-three/50 text-xs font-semibold text-three capitalize">Superadmin</span>`;
        }
        if (r === "admin") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-two border border-three/40 text-xs font-semibold text-four capitalize">Admin</span>`;
        }
        if (r === "staff") {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-two border border-four/15 text-xs font-medium text-four/90 capitalize">Staff</span>`;
        }
        return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-one border border-four/15 text-xs font-medium text-four/70 capitalize">${escapeHtml(role || "Kasir")}</span>`;
    }

    function updateStats() {
        const list = state.users;
        const total = state.meta?.total ?? list.length;
        const adminCount = list.filter((u) => u.role === "superadmin" || u.role === "admin").length;
        const staffKasirCount = list.filter((u) => u.role === "staff" || u.role === "kasir").length;

        if (el.statTotal) el.statTotal.textContent = total;
        if (el.statPage) el.statPage.textContent = list.length;
        if (el.statAdmin) el.statAdmin.textContent = adminCount;
        if (el.statStaffKasir) el.statStaffKasir.textContent = staffKasirCount;
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
                        <span>Memuat data karyawan...</span>
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
        const filtered = state.users.filter((item) => {
            const matchSearch =
                !q ||
                (item.name || "").toLowerCase().includes(q) ||
                (item.email || "").toLowerCase().includes(q) ||
                (item.id || "").toLowerCase().includes(q);

            const matchRole = !state.roleFilter || item.role === state.roleFilter;
            return matchSearch && matchRole;
        });

        if (filtered.length === 0) {
            renderEmptyTable(
                state.mode === "trashed"
                    ? "Tidak ada akun karyawan di dalam tong sampah."
                    : "Belum ada data karyawan yang sesuai."
            );
            return;
        }

        const currentUser = typeof getCurrentUser === "function" ? getCurrentUser() : null;
        const isSuperadmin = currentUser?.role === "superadmin";
        const isAdmin = currentUser?.role === "admin";

        el.tableBody.innerHTML = filtered
            .map((item) => {
                const avatarUrl = item.avatar_url || "/image/blankFoto.png";
                const isSelf = currentUser?.id === item.id;
                const isTargetSuper = item.role === "superadmin";

                const canEdit =
                    !isTargetSuper && (isSuperadmin || isAdmin);
                const canDelete =
                    !isSelf && !isTargetSuper && (isSuperadmin || isAdmin);
                const canRestore = isSuperadmin;
                const canForceDelete = isSuperadmin;

                const actionButtons =
                    state.mode === "trashed"
                        ? `
                        ${
                            canRestore
                                ? `<button type="button" data-action="restore" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.name)}"
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
                                ? `<button type="button" data-action="force-delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.name)}"
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
                                title="Rincian Karyawan"
                                class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        ${
                            canEdit
                                ? `<button type="button" data-action="edit" data-id="${escapeHtml(item.id)}"
                                        title="Ubah Data Karyawan"
                                        class="p-1.5 rounded-lg bg-two/80 border border-four/10 text-four/80 hover:text-four hover:bg-two hover:border-three/60 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>`
                                : ""
                        }
                        ${
                            canDelete
                                ? `<button type="button" data-action="delete" data-id="${escapeHtml(item.id)}" data-name="${escapeHtml(item.name)}"
                                        title="Hapus Karyawan"
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
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="${escapeHtml(avatarUrl)}" alt="${escapeHtml(item.name)}"
                                     class="w-9 h-9 rounded-lg object-cover bg-two border border-four/10 shrink-0">
                                <div class="min-w-0">
                                    <div class="font-semibold text-four truncate">
                                        ${escapeHtml(item.name)}
                                        ${isSelf ? `<span class="ml-1.5 text-[10px] px-1.5 py-0.5 rounded bg-three/20 text-three font-semibold">Anda</span>` : ""}
                                    </div>
                                    <div class="font-mono text-[11px] text-four/50 truncate">${escapeHtml(item.id)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-four/80">
                            ${escapeHtml(item.email)}
                        </td>
                        <td class="px-4 py-3.5">
                            ${getRoleBadge(item.role)}
                        </td>
                        <td class="px-4 py-3.5 text-xs text-four/60">
                            ${escapeHtml(item.created_at || "-")}
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
            el.paginationInfo.textContent = `Halaman ${current_page} dari ${total_pages || 1} (${count} dari total ${total} akun)`;
        }
        if (el.btnPrevPage) el.btnPrevPage.disabled = current_page <= 1;
        if (el.btnNextPage) el.btnNextPage.disabled = current_page >= total_pages;
    }

    // 4. Modal Form & Detail
    function openFormModal(user = null) {
        hideFormErrors();
        el.form.reset();
        closeAllCustomDropdowns();

        if (user) {
            el.formModalTitle.textContent = "Ubah Data Karyawan";
            el.inputId.value = user.id;
            el.inputName.value = user.name || "";
            el.inputEmail.value = user.email || "";
            setDropdownValue(el.dropdownRoleForm, user.role || "kasir", user.role || "Kasir");
            el.inputPassword.required = false;
            el.inputPasswordConfirm.required = false;
            el.passwordRequiredStar?.classList.add("hidden");
            el.passwordConfirmStar?.classList.add("hidden");
            el.passwordEditHint?.classList.remove("hidden");
        } else {
            el.formModalTitle.textContent = "Tambah Karyawan Baru";
            el.inputId.value = "";
            setDropdownValue(el.dropdownRoleForm, "kasir", "Kasir");
            el.inputPassword.required = true;
            el.inputPasswordConfirm.required = true;
            el.passwordRequiredStar?.classList.remove("hidden");
            el.passwordConfirmStar?.classList.remove("hidden");
            el.passwordEditHint?.classList.add("hidden");
        }

        el.formModal.classList.remove("hidden");
        requestAnimationFrame(() => {
            el.formModal.classList.remove("opacity-0");
            el.formModalCard.classList.remove("scale-95", "translate-y-4");
            el.formModalCard.classList.add("scale-100", "translate-y-0");
            el.inputName?.focus();
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
            const item = await fetchUserDetail(id);
            if (!item) return;

            const avatarUrl = item.avatar_url || "/image/blankFoto.png";

            el.detailContent.innerHTML = `
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4 pb-4 border-b border-two">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <button type="button"
                                    id="btnZoomDetailAvatar"
                                    data-avatar-src="${escapeHtml(avatarUrl)}"
                                    data-avatar-name="${escapeHtml(item.name)}"
                                    title="Lihat Foto Lebih Detail"
                                    class="group relative w-16 h-16 rounded-lg overflow-hidden bg-two border border-four/15 hover:border-three transition-all duration-200 shrink-0 cursor-pointer">
                                <img src="${escapeHtml(avatarUrl)}" alt="${escapeHtml(item.name)}"
                                     class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                                <span class="absolute inset-0 bg-canvas/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center text-four">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 10.5a7.5 7.5 0 0013.15 6.15zM10.5 7.5v6m-3-3h6" />
                                    </svg>
                                </span>
                            </button>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-four truncate">${escapeHtml(item.name)}</h3>
                                <p class="text-xs text-four/70 truncate">${escapeHtml(item.email)}</p>
                                <span class="font-mono text-[11px] text-three font-semibold">${escapeHtml(item.id)}</span>
                            </div>
                        </div>
                        ${getRoleBadge(item.role)}
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-four/60">
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-four/50 mb-1">Tanggal Terdaftar</p>
                            <p class="text-four font-medium">${escapeHtml(item.created_at || "-")}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-two/40 border border-two">
                            <p class="text-four/50 mb-1">Terakhir Diperbarui</p>
                            <p class="text-four font-medium">${escapeHtml(item.updated_at || "-")}</p>
                        </div>
                    </div>
                </div>
            `;

            el.detailModal.classList.remove("hidden");
            requestAnimationFrame(() => {
                el.detailModal.classList.remove("opacity-0");
                el.detailCard.classList.remove("scale-95", "translate-y-4");
                el.detailCard.classList.add("scale-100", "translate-y-0");
            });

            document.getElementById("btnZoomDetailAvatar")?.addEventListener("click", (e) => {
                const btn = e.currentTarget;
                openAvatarPreview(btn.dataset.avatarSrc, btn.dataset.avatarName);
            });
        } catch (_) {}
    }

    function openAvatarPreview(src, name) {
        if (!el.avatarPreviewModal || !el.avatarPreviewImg) return;
        el.avatarPreviewImg.src = src || "/image/blankFoto.png";
        if (el.avatarPreviewTitle) {
            el.avatarPreviewTitle.textContent = name ? `Foto Profil — ${name}` : "Foto Profil Karyawan";
        }

        el.avatarPreviewModal.classList.remove("hidden");
        requestAnimationFrame(() => {
            el.avatarPreviewModal.classList.remove("opacity-0");
            el.avatarPreviewCard?.classList.remove("scale-95", "translate-y-4");
            el.avatarPreviewCard?.classList.add("scale-100", "translate-y-0");
        });
    }

    function closeAvatarPreview() {
        el.avatarPreviewModal?.classList.add("opacity-0");
        el.avatarPreviewCard?.classList.remove("scale-100", "translate-y-0");
        el.avatarPreviewCard?.classList.add("scale-95", "translate-y-4");
        setTimeout(() => {
            el.avatarPreviewModal?.classList.add("hidden");
        }, 200);
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
        setupCustomDropdown(el.dropdownFilterRole, (val) => {
            state.roleFilter = val;
            renderTable();
        });
        setupCustomDropdown(el.dropdownRoleForm);

        document.addEventListener("click", () => {
            closeAllCustomDropdowns();
        });

        el.btnOpenCreate?.addEventListener("click", () => openFormModal(null));
        el.btnCloseForm?.forEach((btn) => btn.addEventListener("click", closeFormModal));
        el.btnCloseDetail?.forEach((btn) => btn.addEventListener("click", closeDetailModal));
        el.btnCloseAvatarPreview?.forEach((btn) => btn.addEventListener("click", closeAvatarPreview));

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && el.avatarPreviewModal && !el.avatarPreviewModal.classList.contains("hidden")) {
                closeAvatarPreview();
            }
        });

        el.form?.addEventListener("submit", async (e) => {
            e.preventDefault();
            hideFormErrors();

            const id = el.inputId.value.trim();
            const payload = {
                name: el.inputName.value.trim(),
                email: el.inputEmail.value.trim(),
                role: el.inputRole.value.trim() || "kasir",
                password: el.inputPassword.value,
                password_confirmation: el.inputPasswordConfirm.value,
            };

            if (payload.password && payload.password !== payload.password_confirmation) {
                showFormErrors({ message: "Konfirmasi kata sandi tidak cocok." });
                return;
            }

            el.btnSubmitForm.disabled = true;

            try {
                const res = id ? await updateUser(id, payload) : await createUser(payload);
                showToast(res?.message || "Data karyawan berhasil disimpan", "success");
                closeFormModal();
                loadUsers(state.currentPage);
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
                const found = state.users.find((u) => u.id === id);
                if (found) openFormModal(found);
            } else if (action === "delete") {
                deleteUser(id, name);
            } else if (action === "restore") {
                restoreUser(id, name);
            } else if (action === "force-delete") {
                forceDeleteUser(id, name);
            }
        });

        el.searchInput?.addEventListener("input", (e) => {
            state.searchQuery = e.target.value;
            renderTable();
        });

        el.btnRefresh?.addEventListener("click", () => {
            loadUsers(state.currentPage);
        });

        el.tabActive?.addEventListener("click", () => {
            if (state.mode === "active") return;
            state.mode = "active";
            el.tabActive.classList.add("bg-three", "text-four", "font-semibold");
            el.tabActive.classList.remove("text-four/70");
            el.tabTrashed.classList.remove("bg-three", "text-four", "font-semibold");
            el.tabTrashed.classList.add("text-four/70");
            loadUsers(1);
        });

        el.tabTrashed?.addEventListener("click", () => {
            if (state.mode === "trashed") return;
            state.mode = "trashed";
            el.tabTrashed.classList.add("bg-three", "text-four", "font-semibold");
            el.tabTrashed.classList.remove("text-four/70");
            el.tabActive.classList.remove("bg-three", "text-four", "font-semibold");
            el.tabActive.classList.add("text-four/70");
            loadUsers(1);
        });

        el.btnPrevPage?.addEventListener("click", () => {
            if (state.currentPage > 1) loadUsers(state.currentPage - 1);
        });

        el.btnNextPage?.addEventListener("click", () => {
            if (state.meta && state.currentPage < state.meta.total_pages) {
                loadUsers(state.currentPage + 1);
            }
        });

        window.addEventListener("auth:changed", () => {
            state.mode = "active";
            loadUsers(1);
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
        await loadUsers(1);
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    UsersPage.init();
});
