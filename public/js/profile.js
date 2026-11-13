// Logic untuk halaman Profil Saya (/profile)

let currentProfileUser = null;

const uploadState = {
    avatar: {
        file: null,
        objectUrl: null,
        maxMb: 2,
        defaultLabel: 'JPG, PNG, WEBP (maks. 2 MB)',
    },
    cover: {
        file: null,
        objectUrl: null,
        maxMb: 4,
        defaultLabel: 'JPG, PNG, WEBP (maks. 4 MB)',
    },
};

document.addEventListener('DOMContentLoaded', () => {
    loadMyProfile();
    setupMediaEvents();
    setupProfileForms();
});

function getInitials(name) {
    if (!name) return 'U';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function getRoleBadgeInfo(role) {
    const map = {
        superadmin: { label: 'Superadmin', cls: 'bg-purple-500/20 text-purple-300 border-purple-500/30' },
        admin: { label: 'Admin', cls: 'bg-sky-500/20 text-sky-300 border-sky-500/30' },
        kasir: { label: 'Kasir', cls: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' },
        staff: { label: 'Staff', cls: 'bg-amber-500/20 text-amber-300 border-amber-500/30' },
    };
    return map[role] || { label: role || '-', cls: 'bg-two text-four/80 border-two' };
}

function formatDateID(isoString) {
    if (!isoString) return '-';
    const date = new Date(isoString);
    if (Number.isNaN(date.getTime())) return '-';
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function renderMediaPreview(imgEl, fallbackEl, imageUrl, fallbackText = null) {
    if (fallbackEl && fallbackText !== null) {
        fallbackEl.textContent = fallbackText;
    }

    if (imageUrl && imgEl) {
        imgEl.src = imageUrl;
        imgEl.classList.remove('hidden');
        if (fallbackEl) fallbackEl.classList.add('hidden');
    } else if (imgEl) {
        imgEl.src = '';
        imgEl.classList.add('hidden');
        if (fallbackEl) fallbackEl.classList.remove('hidden');
    }
}

function renderProfile(user) {
    currentProfileUser = user;
    const initials = getInitials(user.name);

    document.getElementById('profileDisplayName').textContent = user.name || '-';
    document.getElementById('profileDisplayEmail').textContent = user.email || '-';
    document.getElementById('profileMetaId').textContent = `#${user.id ?? '-'}`;
    document.getElementById('profileMetaCreated').textContent = formatDateID(user.created_at);
    document.getElementById('profileMetaUpdated').textContent = formatDateID(user.updated_at);

    const badge = getRoleBadgeInfo(user.role);
    const badgeEl = document.getElementById('profileRoleBadge');
    badgeEl.textContent = badge.label;
    badgeEl.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${badge.cls}`;

    // Hero Avatar & Cover
    renderMediaPreview(
        document.getElementById('profileAvatarImg'),
        document.getElementById('profileAvatarInitials'),
        user.avatar_url,
        initials
    );

    renderMediaPreview(
        document.getElementById('profileCoverImg'),
        null,
        uploadState.cover.objectUrl || user.cover_url
    );

    // Form previews (only overwrite if user hasn't picked a pending local file)
    if (!uploadState.avatar.file) {
        renderMediaPreview(
            document.getElementById('profileEditPreviewImg'),
            document.getElementById('profileEditPreviewInitials'),
            user.avatar_url,
            initials
        );
    }

    if (!uploadState.cover.file) {
        renderMediaPreview(
            document.getElementById('profileCoverPreviewImg'),
            document.getElementById('profileCoverPreviewFallback'),
            user.cover_url
        );
    }

    document.getElementById('profile_name').value = user.name || '';
    document.getElementById('profile_email').value = user.email || '';

    localStorage.setItem('user', JSON.stringify(user));
    if (typeof syncAuthAndRoleUI === 'function') {
        syncAuthAndRoleUI();
    }
}

async function loadMyProfile() {
    try {
        const res = await fetchData('/me');
        if (res && res.user) {
            renderProfile(res.user);
        }
    } catch (error) {
        if (typeof showToast === 'function') {
            showToast(error.message || 'Gagal memuat profil pengguna.', 'error');
        }
    }
}

function clearSelectedMedia(type) {
    const state = uploadState[type];
    state.file = null;
    if (state.objectUrl) {
        URL.revokeObjectURL(state.objectUrl);
        state.objectUrl = null;
    }

    const isAvatar = type === 'avatar';
    const inputEl = document.getElementById(isAvatar ? 'profile_avatar' : 'profile_cover');
    const nameEl = document.getElementById(isAvatar ? 'profileSelectedFileName' : 'profileSelectedCoverName');
    const clearBtn = document.getElementById(isAvatar ? 'btnClearSelectedAvatar' : 'btnClearSelectedCover');

    if (inputEl) inputEl.value = '';
    if (nameEl) nameEl.textContent = state.defaultLabel;
    if (clearBtn) clearBtn.classList.add('hidden');

    if (!currentProfileUser) return;

    if (isAvatar) {
        renderMediaPreview(
            document.getElementById('profileEditPreviewImg'),
            document.getElementById('profileEditPreviewInitials'),
            currentProfileUser.avatar_url,
            getInitials(currentProfileUser.name)
        );
    } else {
        renderMediaPreview(
            document.getElementById('profileCoverPreviewImg'),
            document.getElementById('profileCoverPreviewFallback'),
            currentProfileUser.cover_url
        );
        renderMediaPreview(
            document.getElementById('profileCoverImg'),
            null,
            currentProfileUser.cover_url
        );
    }
}

function handleMediaSelection(type, file) {
    if (!file) return;
    const state = uploadState[type];
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    if (!allowedTypes.includes(file.type)) {
        if (typeof showToast === 'function') {
            showToast('Format foto harus JPG, PNG, atau WEBP.', 'error');
        }
        clearSelectedMedia(type);
        return;
    }

    if (file.size > state.maxMb * 1024 * 1024) {
        if (typeof showToast === 'function') {
            showToast(`Ukuran foto maksimal ${state.maxMb} MB.`, 'error');
        }
        clearSelectedMedia(type);
        return;
    }

    if (state.objectUrl) {
        URL.revokeObjectURL(state.objectUrl);
    }
    state.file = file;
    state.objectUrl = URL.createObjectURL(file);

    const isAvatar = type === 'avatar';
    document.getElementById(isAvatar ? 'profileSelectedFileName' : 'profileSelectedCoverName').textContent = file.name;
    document.getElementById(isAvatar ? 'btnClearSelectedAvatar' : 'btnClearSelectedCover').classList.remove('hidden');

    if (isAvatar) {
        renderMediaPreview(
            document.getElementById('profileEditPreviewImg'),
            document.getElementById('profileEditPreviewInitials'),
            state.objectUrl,
            getInitials(currentProfileUser ? currentProfileUser.name : 'U')
        );
    } else {
        renderMediaPreview(
            document.getElementById('profileCoverPreviewImg'),
            document.getElementById('profileCoverPreviewFallback'),
            state.objectUrl
        );
        renderMediaPreview(
            document.getElementById('profileCoverImg'),
            null,
            state.objectUrl
        );
    }
}

function openImagePreviewModal(mode = 'avatar') {
    const modal = document.getElementById('profileAvatarPreviewModal');
    const img = document.getElementById('profileAvatarPreviewImg');
    const fallback = document.getElementById('profileAvatarPreviewFallback');
    const title = document.getElementById('profileAvatarPreviewTitle');

    const isCover = mode === 'cover';
    const activeSrc = isCover
        ? (uploadState.cover.objectUrl || (currentProfileUser && currentProfileUser.cover_url) || '')
        : (uploadState.avatar.objectUrl || (currentProfileUser && currentProfileUser.avatar_url) || '');

    if (isCover && !activeSrc) {
        if (typeof showToast === 'function') {
            showToast('Belum ada foto background yang dipasang.', 'info');
        }
        return;
    }

    const displayName = (currentProfileUser && currentProfileUser.name) || 'Pengguna';
    title.textContent = isCover ? `Foto Background - ${displayName}` : `Foto Profil - ${displayName}`;

    renderMediaPreview(img, fallback, activeSrc, getInitials(displayName));

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImagePreviewModal() {
    const modal = document.getElementById('profileAvatarPreviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function setupMediaEvents() {
    const avatarInput = document.getElementById('profile_avatar');
    const coverInput = document.getElementById('profile_cover');
    const btnQuickPickAvatar = document.getElementById('btnQuickPickAvatar');
    const btnQuickPickCover = document.getElementById('btnQuickPickCover');
    const btnClearAvatar = document.getElementById('btnClearSelectedAvatar');
    const btnClearCover = document.getElementById('btnClearSelectedCover');
    const btnZoomAvatar = document.getElementById('btnZoomMyAvatar');
    const btnZoomCover = document.getElementById('btnZoomMyCover');
    const btnCloseModal = document.getElementById('btnCloseProfileAvatarPreview');
    const backdrop = document.getElementById('profileAvatarPreviewBackdrop');

    if (btnQuickPickAvatar && avatarInput) {
        btnQuickPickAvatar.addEventListener('click', () => avatarInput.click());
    }
    if (btnQuickPickCover && coverInput) {
        btnQuickPickCover.addEventListener('click', () => coverInput.click());
    }

    if (avatarInput) {
        avatarInput.addEventListener('change', (e) => handleMediaSelection('avatar', e.target.files?.[0]));
    }
    if (coverInput) {
        coverInput.addEventListener('change', (e) => handleMediaSelection('cover', e.target.files?.[0]));
    }

    if (btnClearAvatar) {
        btnClearAvatar.addEventListener('click', () => clearSelectedMedia('avatar'));
    }
    if (btnClearCover) {
        btnClearCover.addEventListener('click', () => clearSelectedMedia('cover'));
    }

    if (btnZoomAvatar) {
        btnZoomAvatar.addEventListener('click', () => openImagePreviewModal('avatar'));
    }
    if (btnZoomCover) {
        btnZoomCover.addEventListener('click', () => openImagePreviewModal('cover'));
    }

    if (btnCloseModal) {
        btnCloseModal.addEventListener('click', closeImagePreviewModal);
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeImagePreviewModal);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeImagePreviewModal();
        }
    });
}

function setupProfileForms() {
    const infoForm = document.getElementById('profileInfoForm');
    const passwordForm = document.getElementById('profilePasswordForm');
    const logoutBtn = document.getElementById('btnProfileLogout');

    if (infoForm) {
        infoForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!currentProfileUser) return;

            const btn = document.getElementById('btnSaveProfileInfo');
            const name = document.getElementById('profile_name').value.trim();
            const email = document.getElementById('profile_email').value.trim();

            if (!name || !email) {
                if (typeof showToast === 'function') {
                    showToast('Nama dan email wajib diisi.', 'error');
                }
                return;
            }

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                let res;
                const hasFiles = Boolean(uploadState.avatar.file || uploadState.cover.file);

                if (hasFiles) {
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('name', name);
                    formData.append('email', email);
                    if (uploadState.avatar.file) formData.append('avatar', uploadState.avatar.file);
                    if (uploadState.cover.file) formData.append('cover', uploadState.cover.file);

                    res = await fetchData(`/users/${currentProfileUser.id}`, {
                        method: 'POST',
                        body: formData,
                    });
                } else {
                    res = await fetchData(`/users/${currentProfileUser.id}`, {
                        method: 'PUT',
                        body: JSON.stringify({ name, email }),
                    });
                }

                if (res && res.user) {
                    clearSelectedMedia('avatar');
                    clearSelectedMedia('cover');
                    renderProfile(res.user);
                }

                if (typeof showToast === 'function') {
                    showToast((res && res.message) || 'Profil berhasil diperbarui.', 'success');
                }
            } catch (error) {
                if (typeof showToast === 'function') {
                    showToast(error.message || 'Gagal memperbarui profil.', 'error');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }

    if (passwordForm) {
        passwordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!currentProfileUser) return;

            const btn = document.getElementById('btnSaveProfilePassword');
            const password = document.getElementById('profile_password').value;
            const passwordConfirmation = document.getElementById('profile_password_confirmation').value;

            if (password.length < 8) {
                if (typeof showToast === 'function') {
                    showToast('Kata sandi minimal 8 karakter.', 'error');
                }
                return;
            }

            if (password !== passwordConfirmation) {
                if (typeof showToast === 'function') {
                    showToast('Konfirmasi kata sandi tidak cocok.', 'error');
                }
                return;
            }

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = 'Memperbarui...';

            try {
                const res = await fetchData(`/users/${currentProfileUser.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        password,
                        password_confirmation: passwordConfirmation,
                    }),
                });

                passwordForm.reset();
                if (res && res.user) {
                    renderProfile(res.user);
                }

                if (typeof showToast === 'function') {
                    showToast((res && res.message) || 'Kata sandi berhasil diperbarui.', 'success');
                }
            } catch (error) {
                if (typeof showToast === 'function') {
                    showToast(error.message || 'Gagal memperbarui kata sandi.', 'error');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            if (typeof logoutUser === 'function') {
                logoutUser();
            }
        });
    }
}
