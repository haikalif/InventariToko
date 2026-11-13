/**
 * public/js/register.js
 * Real-time checker untuk Nama, Email, Password, & Konfirmasi Password tepat di bawah tiap input
 */

const RegisterPage = (() => {
    const el = {};
    let nameTimer = null;
    let emailTimer = null;
    let nameAbortCtrl = null;
    let emailAbortCtrl = null;

    let availabilityState = {
        nameChecking: false,
        nameTaken: false,
        emailChecking: false,
        emailTaken: false,
    };

    function initDomRefs() {
        el.form = document.getElementById("registerForm");

        el.avatarInput = document.getElementById("avatarInput");
        el.avatarPreview = document.getElementById("avatarPreview");
        el.avatarFileName = document.getElementById("avatarFileName");
        el.btnRemoveAvatar = document.getElementById("btnRemoveAvatar");
        el.avatarFeedback = document.getElementById("avatarFeedback");

        el.inputName = document.getElementById("regName");
        el.wrapName = document.getElementById("wrapName");
        el.feedbackName = document.getElementById("feedbackName");

        el.inputEmail = document.getElementById("regEmail");
        el.wrapEmail = document.getElementById("wrapEmail");
        el.feedbackEmail = document.getElementById("feedbackEmail");

        el.inputPassword = document.getElementById("regPassword");
        el.wrapPassword = document.getElementById("wrapPassword");
        el.feedbackPassword = document.getElementById("feedbackPassword");

        el.inputPasswordConfirm = document.getElementById("regPasswordConfirm");
        el.wrapPasswordConfirm = document.getElementById("wrapPasswordConfirm");
        el.feedbackPasswordConfirm = document.getElementById("feedbackPasswordConfirm");

        el.togglePasswordBtns = document.querySelectorAll("[data-toggle-password]");
        el.btnSubmit = document.getElementById("btnSubmitRegister");
        el.btnSubmitText = document.getElementById("btnSubmitRegisterText");
    }

    // Ikon SVG untuk status Real-Time Checker
    function iconSpinner() {
        return `<svg class="w-3.5 h-3.5 text-four/70 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>`;
    }

    function iconCheck() {
        return `<svg class="w-3.5 h-3.5 text-four shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>`;
    }

    function iconWarn() {
        return `<svg class="w-3.5 h-3.5 text-three shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
    }

    // Tampilkan / sembunyikan bar checker menyatu di bawah input
    function setFieldDrawer(wrapEl, feedbackEl, htmlContent, show = true, isValid = false) {
        if (!wrapEl || !feedbackEl) return;

        if (show && htmlContent) {
            const borderClass = isValid ? "border-two" : "border-three";
            wrapEl.classList.add("rounded-b-none", "border-three");
            feedbackEl.classList.remove("border-two", "border-three", "hidden");
            feedbackEl.classList.add(borderClass);
            feedbackEl.innerHTML = htmlContent;
        } else {
            wrapEl.classList.remove("rounded-b-none", "border-three");
            feedbackEl.innerHTML = "";
            feedbackEl.classList.add("hidden");
        }
    }

    // 1. Foto Profil
    function handleAvatarChange(e) {
        const file = e.target.files?.[0];
        if (!file) return;

        el.avatarFeedback?.classList.add("hidden");

        const validTypes = ["image/jpeg", "image/png", "image/webp"];
        if (!validTypes.includes(file.type)) {
            resetAvatar();
            if (el.avatarFeedback) {
                el.avatarFeedback.textContent = "Format wajib JPG, PNG, atau WEBP.";
                el.avatarFeedback.classList.remove("hidden");
            }
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            resetAvatar();
            if (el.avatarFeedback) {
                el.avatarFeedback.textContent = "Ukuran foto maksimal 2 MB.";
                el.avatarFeedback.classList.remove("hidden");
            }
            return;
        }

        const reader = new FileReader();
        reader.onload = (ev) => {
            if (el.avatarPreview) {
                el.avatarPreview.src = ev.target.result;
            }
        };
        reader.readAsDataURL(file);

        if (el.avatarFileName) {
            el.avatarFileName.textContent = file.name;
        }
        el.btnRemoveAvatar?.classList.remove("hidden");
    }

    function resetAvatar() {
        if (el.avatarInput) el.avatarInput.value = "";
        if (el.avatarPreview) el.avatarPreview.src = "/image/blankFoto.png";
        if (el.avatarFileName) el.avatarFileName.textContent = "Opsional (Maks. 2 MB)";
        el.btnRemoveAvatar?.classList.add("hidden");
        el.avatarFeedback?.classList.add("hidden");
    }

    // 2. Real-Time Checker untuk Nama Lengkap
    function handleNameInput() {
        const name = (el.inputName?.value || "").replace(/\s+/g, " ").trim();

        // Update juga syarat password "tidak sama dengan nama" secara real-time
        if (el.inputPassword?.value.length > 0) {
            evaluatePasswordRealtime();
        }

        if (nameTimer) clearTimeout(nameTimer);
        if (nameAbortCtrl) nameAbortCtrl.abort();

        if (!name) {
            availabilityState.nameTaken = false;
            availabilityState.nameChecking = false;
            setFieldDrawer(el.wrapName, el.feedbackName, "", false);
            return;
        }

        availabilityState.nameChecking = true;
        setFieldDrawer(
            el.wrapName,
            el.feedbackName,
            `${iconSpinner()}<span class="text-four/70">Mengecek ketersediaan nama di database...</span>`,
            true,
            true
        );

        nameTimer = setTimeout(async () => {
            nameAbortCtrl = new AbortController();
            try {
                const res = await fetchData("/register/check", {
                    method: "POST",
                    body: JSON.stringify({ name }),
                    signal: nameAbortCtrl.signal,
                });

                availabilityState.nameChecking = false;
                availabilityState.nameTaken = Boolean(res?.name_taken);

                if (availabilityState.nameTaken) {
                    setFieldDrawer(
                        el.wrapName,
                        el.feedbackName,
                        `${iconWarn()}<span class="text-three font-medium">Nama "${escapeHtml(name)}" sudah terdaftar di database.</span>`,
                        true,
                        false
                    );
                } else {
                    setFieldDrawer(
                        el.wrapName,
                        el.feedbackName,
                        `${iconCheck()}<span class="text-four/90 font-medium">Nama tersedia dan dapat digunakan.</span>`,
                        true,
                        true
                    );
                }
            } catch (err) {
                if (err?.name === "AbortError") return;
                availabilityState.nameChecking = false;
            }
        }, 180);
    }

    // 3. Real-Time Checker untuk Alamat Email
    function handleEmailInput() {
        const email = (el.inputEmail?.value || "").trim().toLowerCase();

        // Update juga syarat password "tidak sama dengan email" secara real-time
        if (el.inputPassword?.value.length > 0) {
            evaluatePasswordRealtime();
        }

        if (emailTimer) clearTimeout(emailTimer);
        if (emailAbortCtrl) emailAbortCtrl.abort();

        if (!email) {
            availabilityState.emailTaken = false;
            availabilityState.emailChecking = false;
            setFieldDrawer(el.wrapEmail, el.feedbackEmail, "", false);
            return;
        }

        const emailFormatValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        if (!emailFormatValid) {
            availabilityState.emailTaken = false;
            setFieldDrawer(
                el.wrapEmail,
                el.feedbackEmail,
                `${iconWarn()}<span class="text-three font-medium">Format email belum lengkap (contoh: nama@gmail.com).</span>`,
                true,
                false
            );
            return;
        }

        availabilityState.emailChecking = true;
        setFieldDrawer(
            el.wrapEmail,
            el.feedbackEmail,
            `${iconSpinner()}<span class="text-four/70">Mengecek ketersediaan email di database...</span>`,
            true,
            true
        );

        emailTimer = setTimeout(async () => {
            emailAbortCtrl = new AbortController();
            try {
                const res = await fetchData("/register/check", {
                    method: "POST",
                    body: JSON.stringify({ email }),
                    signal: emailAbortCtrl.signal,
                });

                availabilityState.emailChecking = false;
                availabilityState.emailTaken = Boolean(res?.email_taken);

                if (availabilityState.emailTaken) {
                    setFieldDrawer(
                        el.wrapEmail,
                        el.feedbackEmail,
                        `${iconWarn()}<span class="text-three font-medium">Email sudah terpakai oleh pengguna lain.</span>`,
                        true,
                        false
                    );
                } else {
                    setFieldDrawer(
                        el.wrapEmail,
                        el.feedbackEmail,
                        `${iconCheck()}<span class="text-four/90 font-medium">Email tersedia dan dapat digunakan.</span>`,
                        true,
                        true
                    );
                }
            } catch (err) {
                if (err?.name === "AbortError") return;
                availabilityState.emailChecking = false;
            }
        }, 180);
    }

    // 4. Real-Time Checker untuk Password (langsung berubah tiap ketikan)
    function evaluatePasswordRealtime(forceShow = false) {
        const pass = el.inputPassword?.value || "";
        const name = (el.inputName?.value || "").trim().toLowerCase();
        const email = (el.inputEmail?.value || "").trim().toLowerCase();
        const passLower = pass.toLowerCase();

        if (!pass && !forceShow) {
            setFieldDrawer(el.wrapPassword, el.feedbackPassword, "", false);
            if (el.inputPasswordConfirm?.value.length > 0) {
                evaluateConfirmPasswordRealtime();
            }
            return false;
        }

        const hasMin8 = pass.length >= 8;
        const hasLettersAndNumbers = /[a-zA-Z]/.test(pass) && /[0-9]/.test(pass);
        const notIdentical =
            pass.length > 0 &&
            (!name || passLower !== name) &&
            (!email || passLower !== email);

        const allValid = hasMin8 && hasLettersAndNumbers && notIdentical;

        const rulesHtml = [
            {
                ok: hasMin8,
                text: `Minimal 8 karakter (${pass.length}/8)`,
            },
            {
                ok: hasLettersAndNumbers,
                text: "Kombinasi unik huruf dan angka",
            },
            {
                ok: notIdentical,
                text: "Tidak sama dengan nama atau email",
            },
        ]
            .map(
                (r) => `
                <div class="flex items-center gap-2 ${r.ok ? "text-four font-medium" : "text-three"} transition-colors duration-150">
                    ${r.ok ? iconCheck() : iconWarn()}
                    <span>${escapeHtml(r.text)}</span>
                </div>
            `
            )
            .join("");

        setFieldDrawer(el.wrapPassword, el.feedbackPassword, rulesHtml, true, allValid);

        // Sinkronkan juga status konfirmasi password secara real-time saat password utama berubah
        if (el.inputPasswordConfirm?.value.length > 0 || forceShow) {
            evaluateConfirmPasswordRealtime(forceShow);
        }

        return allValid;
    }

    // 5. Real-Time Checker untuk Konfirmasi Password
    function evaluateConfirmPasswordRealtime(forceShow = false) {
        const pass = el.inputPassword?.value || "";
        const confirmPass = el.inputPasswordConfirm?.value || "";

        if (!confirmPass && !forceShow) {
            setFieldDrawer(el.wrapPasswordConfirm, el.feedbackPasswordConfirm, "", false);
            return false;
        }

        if (!confirmPass && forceShow) {
            setFieldDrawer(
                el.wrapPasswordConfirm,
                el.feedbackPasswordConfirm,
                `${iconWarn()}<span class="text-three font-medium">Konfirmasi password wajib diisi.</span>`,
                true,
                false
            );
            return false;
        }

        const isMatched = pass.length > 0 && pass === confirmPass;

        if (isMatched) {
            setFieldDrawer(
                el.wrapPasswordConfirm,
                el.feedbackPasswordConfirm,
                `${iconCheck()}<span class="text-four font-medium">Konfirmasi password cocok.</span>`,
                true,
                true
            );
        } else {
            setFieldDrawer(
                el.wrapPasswordConfirm,
                el.feedbackPasswordConfirm,
                `${iconWarn()}<span class="text-three font-medium">Konfirmasi password belum cocok dengan password di atas.</span>`,
                true,
                false
            );
        }

        return isMatched;
    }

    // 6. Submit Form Registrasi
    async function handleSubmit(e) {
        e.preventDefault();

        const nameVal = el.inputName?.value.trim() || "";
        const emailVal = el.inputEmail?.value.trim() || "";
        let hasError = false;

        if (!nameVal) {
            setFieldDrawer(
                el.wrapName,
                el.feedbackName,
                `${iconWarn()}<span class="text-three font-medium">Nama lengkap wajib diisi.</span>`,
                true,
                false
            );
            hasError = true;
        } else if (availabilityState.nameTaken) {
            hasError = true;
        }

        if (!emailVal) {
            setFieldDrawer(
                el.wrapEmail,
                el.feedbackEmail,
                `${iconWarn()}<span class="text-three font-medium">Alamat email wajib diisi.</span>`,
                true,
                false
            );
            hasError = true;
        } else if (availabilityState.emailTaken || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            hasError = true;
        }

        const passOk = evaluatePasswordRealtime(true);
        const confirmOk = evaluateConfirmPasswordRealtime(true);

        if (!passOk || !confirmOk || hasError) {
            return;
        }

        const formData = new FormData();
        formData.append("name", nameVal);
        formData.append("email", emailVal);
        formData.append("password", el.inputPassword.value);
        formData.append("password_confirmation", el.inputPasswordConfirm.value);

        if (el.avatarInput?.files?.[0]) {
            formData.append("avatar", el.avatarInput.files[0]);
        }

        el.btnSubmit.disabled = true;
        if (el.btnSubmitText) el.btnSubmitText.textContent = "Memproses...";

        try {
            const res = await fetchData("/register", {
                method: "POST",
                body: formData,
            });

            if (res?.access_token) {
                localStorage.setItem("access_token", res.access_token);
            }
            if (res?.user) {
                localStorage.setItem("user", JSON.stringify(res.user));
            }
            if (typeof syncAuthAndRoleUI === "function") {
                syncAuthAndRoleUI();
            }

            await customAlert({
                title: "Pendaftaran Berhasil",
                message: "Akun Anda berhasil didaftarkan.",
                confirmText: "Lanjutkan",
            });

            window.location.href = "/products";
        } catch (error) {
            const errs = error?.errors || {};
            if (errs.name?.[0]) {
                setFieldDrawer(
                    el.wrapName,
                    el.feedbackName,
                    `${iconWarn()}<span class="text-three font-medium">${escapeHtml(errs.name[0])}</span>`,
                    true,
                    false
                );
            }
            if (errs.email?.[0]) {
                setFieldDrawer(
                    el.wrapEmail,
                    el.feedbackEmail,
                    `${iconWarn()}<span class="text-three font-medium">${escapeHtml(errs.email[0])}</span>`,
                    true,
                    false
                );
            }
            if (errs.password?.[0]) {
                setFieldDrawer(
                    el.wrapPassword,
                    el.feedbackPassword,
                    `<div class="flex items-center gap-2 text-three">${iconWarn()}<span>${escapeHtml(errs.password[0])}</span></div>`,
                    true,
                    false
                );
            }
            if (errs.avatar?.[0] && el.avatarFeedback) {
                el.avatarFeedback.textContent = errs.avatar[0];
                el.avatarFeedback.classList.remove("hidden");
            }
        } finally {
            el.btnSubmit.disabled = false;
            if (el.btnSubmitText) el.btnSubmitText.textContent = "Daftar Sekarang";
        }
    }

    function bindEvents() {
        el.avatarInput?.addEventListener("change", handleAvatarChange);
        el.btnRemoveAvatar?.addEventListener("click", resetAvatar);

        // Real-time event listeners pada setiap ketikan
        el.inputName?.addEventListener("input", handleNameInput);
        el.inputEmail?.addEventListener("input", handleEmailInput);

        el.inputPassword?.addEventListener("focus", () => evaluatePasswordRealtime(true));
        el.inputPassword?.addEventListener("input", () => evaluatePasswordRealtime(true));

        el.inputPasswordConfirm?.addEventListener("focus", () => {
            if (el.inputPasswordConfirm.value.length > 0 || el.inputPassword?.value.length > 0) {
                evaluateConfirmPasswordRealtime(true);
            }
        });
        el.inputPasswordConfirm?.addEventListener("input", () => evaluateConfirmPasswordRealtime(true));

        el.togglePasswordBtns?.forEach((btn) => {
            btn.addEventListener("click", () => {
                const targetId = btn.dataset.togglePassword;
                const input = document.getElementById(targetId);
                if (!input) return;
                input.type = input.type === "password" ? "text" : "password";
            });
        });

        el.form?.addEventListener("submit", handleSubmit);
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

    function init() {
        initDomRefs();
        bindEvents();
    }

    return { init };
})();

document.addEventListener("DOMContentLoaded", () => {
    RegisterPage.init();
});
