/**
 * public/js/login.js
 * Pengelola halaman Login mandiri dengan real-time checker & alert di bawah tiap input
 */

const LoginPage = (() => {
    const el = {};

    function initDomRefs() {
        el.form = document.getElementById("loginForm");

        el.inputEmail = document.getElementById("loginEmail");
        el.wrapEmail = document.getElementById("wrapLoginEmail");
        el.feedbackEmail = document.getElementById("feedbackLoginEmail");

        el.inputPassword = document.getElementById("loginPassword");
        el.wrapPassword = document.getElementById("wrapLoginPassword");
        el.feedbackPassword = document.getElementById("feedbackLoginPassword");

        el.rememberMe = document.getElementById("rememberMe");
        el.togglePasswordBtns = document.querySelectorAll("[data-toggle-password]");
        el.btnSubmit = document.getElementById("btnSubmitLogin");
        el.btnSubmitText = document.getElementById("btnSubmitLoginText");
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

    function evaluateEmailRealtime(forceShow = false) {
        const email = (el.inputEmail?.value || "").trim().toLowerCase();

        if (!email && !forceShow) {
            setFieldDrawer(el.wrapEmail, el.feedbackEmail, "", false);
            return false;
        }

        if (!email && forceShow) {
            setFieldDrawer(
                el.wrapEmail,
                el.feedbackEmail,
                `${iconWarn()}<span class="text-three font-medium">Alamat email wajib diisi.</span>`,
                true,
                false
            );
            return false;
        }

        const isValidFormat = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        if (!isValidFormat) {
            setFieldDrawer(
                el.wrapEmail,
                el.feedbackEmail,
                `${iconWarn()}<span class="text-three font-medium">Format email belum lengkap (contoh: nama@gmail.com).</span>`,
                true,
                false
            );
            return false;
        }

        setFieldDrawer(
            el.wrapEmail,
            el.feedbackEmail,
            `${iconCheck()}<span class="text-four/90 font-medium">Format email valid.</span>`,
            true,
            true
        );
        return true;
    }

    function evaluatePasswordRealtime(forceShow = false) {
        const pass = el.inputPassword?.value || "";

        if (!pass && !forceShow) {
            setFieldDrawer(el.wrapPassword, el.feedbackPassword, "", false);
            return false;
        }

        if (!pass && forceShow) {
            setFieldDrawer(
                el.wrapPassword,
                el.feedbackPassword,
                `${iconWarn()}<span class="text-three font-medium">Password wajib diisi.</span>`,
                true,
                false
            );
            return false;
        }

        if (pass.length < 8) {
            setFieldDrawer(
                el.wrapPassword,
                el.feedbackPassword,
                `${iconWarn()}<span class="text-three font-medium">Password minimal 8 karakter (${pass.length}/8).</span>`,
                true,
                false
            );
            return false;
        }

        setFieldDrawer(
            el.wrapPassword,
            el.feedbackPassword,
            `${iconCheck()}<span class="text-four/90 font-medium">Panjang password memenuhi syarat (${pass.length} karakter).</span>`,
            true,
            true
        );
        return true;
    }

    async function handleSubmit(e) {
        e.preventDefault();

        const emailOk = evaluateEmailRealtime(true);
        const passOk = evaluatePasswordRealtime(true);

        if (!emailOk || !passOk) return;

        el.btnSubmit.disabled = true;
        if (el.btnSubmitText) el.btnSubmitText.textContent = "Memverifikasi...";

        try {
            const res = await fetchData("/login", {
                method: "POST",
                body: JSON.stringify({
                    email: el.inputEmail.value.trim(),
                    password: el.inputPassword.value,
                    remember_me: Boolean(el.rememberMe?.checked),
                }),
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

            window.location.href = "/products";
        } catch (error) {
            const errs = error?.errors || {};

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
                    `${iconWarn()}<span class="text-three font-medium">${escapeHtml(errs.password[0])}</span>`,
                    true,
                    false
                );
            }

            if (!errs.email && !errs.password) {
                const msg =
                    error?.status === 401
                        ? "Email atau password yang dimasukkan tidak sesuai."
                        : error?.message || "Gagal masuk ke sistem.";

                setFieldDrawer(
                    el.wrapPassword,
                    el.feedbackPassword,
                    `${iconWarn()}<span class="text-three font-medium">${escapeHtml(msg)}</span>`,
                    true,
                    false
                );
            }
        } finally {
            el.btnSubmit.disabled = false;
            if (el.btnSubmitText) el.btnSubmitText.textContent = "Masuk Sekarang";
        }
    }

    function bindEvents() {
        el.inputEmail?.addEventListener("input", () => evaluateEmailRealtime(false));
        el.inputPassword?.addEventListener("input", () => evaluatePasswordRealtime(false));

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
    LoginPage.init();
});
