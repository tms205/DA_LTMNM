import 'bootstrap';

// ========================================================
// Confirmation Dialog Handling
// ========================================================
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-confirm]');

    if (trigger && ! window.confirm(trigger.dataset.confirm)) {
        event.preventDefault();
    }
});

// ========================================================
// Password Visibility Toggle
// ========================================================
document.addEventListener('click', (event) => {
    const btn = event.target.closest('.btn-toggle-password');
    if (!btn) {
        return;
    }

    event.preventDefault();
    const targetSelector = btn.getAttribute('data-target');
    const input = targetSelector ? document.querySelector(targetSelector) : null;
    if (!input) {
        return;
    }

    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
        btn.setAttribute('title', 'Ẩn mật khẩu');
        btn.setAttribute('aria-label', 'Ẩn mật khẩu');
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
        btn.setAttribute('title', 'Hiện mật khẩu');
        btn.setAttribute('aria-label', 'Hiện mật khẩu');
    }
});

// ========================================================
// Gentelella Sidebar Management (Desktop Rail & Mobile Drawer)
// ========================================================
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('btn-sidebar-toggle');
    const backdrop = document.getElementById('sidebar-backdrop');
    const isDesktop = () => window.innerWidth >= 992;
    const STORAGE_KEY = 'gentelella_sidebar_rail';

    // Restore desktop rail state
    if (isDesktop() && localStorage.getItem(STORAGE_KEY) === 'true') {
        document.body.classList.add('sidebar-rail');
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-pressed', 'true');
        }
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (isDesktop()) {
                const isRail = document.body.classList.toggle('sidebar-rail');
                localStorage.setItem(STORAGE_KEY, isRail ? 'true' : 'false');
                toggleBtn.setAttribute('aria-pressed', isRail ? 'true' : 'false');
            } else {
                const isOpen = document.body.classList.toggle('sidebar-open');
                toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }
        });
    }

    // Close mobile drawer on backdrop click
    if (backdrop) {
        backdrop.addEventListener('click', () => {
            document.body.classList.remove('sidebar-open');
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Close mobile drawer on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
            document.body.classList.remove('sidebar-open');
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.focus();
            }
        }
    });

    // Close mobile drawer on navigation link click
    document.querySelectorAll('.app-sidebar .sidebar-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (!isDesktop() && document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
        });
    });

    // Prevent double form submission on non-GET forms
    document.querySelectorAll('form:not([method="GET"])').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }
            const submitBtn = form.querySelector('button[type="submit"]:not([data-confirm])');
            if (submitBtn && !submitBtn.disabled) {
                form.dataset.submitting = 'true';
                submitBtn.disabled = true;
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang xử lý...';
                // Re-enable if submission fails or takes too long
                setTimeout(() => {
                    form.dataset.submitting = 'false';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }, 8000);
            }
        });
    });
});
