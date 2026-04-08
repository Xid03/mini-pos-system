document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebarDismiss = document.querySelector('[data-sidebar-dismiss]');
    const passwordToggle = document.querySelector('[data-password-toggle]');
    const passwordInput = document.querySelector('[data-password-input]');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            body.classList.toggle('sidebar-open');
        });
    }

    if (sidebarDismiss) {
        sidebarDismiss.addEventListener('click', () => {
            body.classList.remove('sidebar-open');
        });
    }

    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            passwordToggle.innerHTML = isPassword
                ? '<i class="bi bi-eye-slash"></i>'
                : '<i class="bi bi-eye"></i>';
        });
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            body.classList.remove('sidebar-open');
        }
    });
});

