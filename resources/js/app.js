import 'bootstrap';

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-confirm]');

    if (trigger && ! window.confirm(trigger.dataset.confirm)) {
        event.preventDefault();
    }
});

// Toggle password visibility
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
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
        btn.setAttribute('title', 'Hiện mật khẩu');
    }
});
