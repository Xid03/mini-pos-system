document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const pageLoader = document.querySelector('[data-page-loader]');
    const pageLoaderBar = pageLoader ? pageLoader.querySelector('.page-loader__bar') : null;
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebarDismiss = document.querySelector('[data-sidebar-dismiss]');
    const passwordToggle = document.querySelector('[data-password-toggle]');
    const passwordInput = document.querySelector('[data-password-input]');
    const posCheckoutForms = document.querySelectorAll('[data-pos-checkout]');
    const confirmModalElement = document.getElementById('confirmActionModal');
    const confirmTitle = document.getElementById('confirmActionTitle');
    const confirmMessage = document.getElementById('confirmActionMessage');
    const confirmButton = document.getElementById('confirmActionButton');
    const confirmForms = document.querySelectorAll('form[data-confirm-dialog]');
    const forms = document.querySelectorAll('form');
    const links = document.querySelectorAll('a[href]');
    let pendingConfirmForm = null;

    const confirmModal = confirmModalElement ? new bootstrap.Modal(confirmModalElement) : null;

    const resetPageLoader = () => {
        if (!pageLoader || !pageLoaderBar) {
            return;
        }

        body.classList.remove('is-loading');
        pageLoader.classList.remove('is-active');
        pageLoaderBar.style.transform = 'scaleX(0)';
    };

    const showPageLoader = () => {
        if (!pageLoader || !pageLoaderBar) {
            return;
        }

        body.classList.add('is-loading');
        pageLoader.classList.add('is-active');
        pageLoaderBar.style.transform = 'scaleX(0.82)';
    };

    const completePageLoader = () => {
        if (!pageLoader || !pageLoaderBar) {
            return;
        }

        pageLoader.classList.add('is-active');
        pageLoaderBar.style.transform = 'scaleX(1)';

        window.setTimeout(() => {
            resetPageLoader();
        }, 280);
    };

    const setButtonLoadingState = (button) => {
        if (!button || button.dataset.loadingApplied === 'true') {
            return;
        }

        button.dataset.loadingApplied = 'true';
        button.dataset.originalContent = button.innerHTML;
        button.disabled = true;
        button.classList.add('is-loading');
        button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Processing...</span>';
    };

    if (pageLoader && pageLoaderBar) {
        pageLoaderBar.style.transform = 'scaleX(0.28)';
        pageLoader.classList.add('is-active');
        window.setTimeout(() => {
            completePageLoader();
        }, 120);
    }

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

    if (confirmModal && confirmButton && confirmTitle && confirmMessage) {
        confirmForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                pendingConfirmForm = form;
                confirmTitle.textContent = form.dataset.confirmTitle || 'Confirm action';
                confirmMessage.textContent = form.dataset.confirmMessage || 'Please confirm that you want to continue.';
                confirmButton.textContent = form.dataset.confirmButton || 'Continue';
                confirmModal.show();
            });
        });

        confirmButton.addEventListener('click', () => {
            if (!pendingConfirmForm) {
                return;
            }

            const formToSubmit = pendingConfirmForm;
            pendingConfirmForm = null;
            confirmModal.hide();
            showPageLoader();
            setButtonLoadingState(confirmButton);
            formToSubmit.submit();
        });

        confirmModalElement.addEventListener('hidden.bs.modal', () => {
            pendingConfirmForm = null;
            confirmButton.disabled = false;
            confirmButton.classList.remove('is-loading');
            confirmButton.innerHTML = 'Delete';
        });
    }

    posCheckoutForms.forEach((form) => {
        const totalAmount = Number.parseFloat(form.dataset.totalAmount || '0');
        const paidInput = form.querySelector('[data-pos-paid]');
        const balanceOutput = form.querySelector('[data-pos-balance]');

        if (!paidInput || !balanceOutput) {
            return;
        }

        const renderBalance = () => {
            const paidAmount = Number.parseFloat(paidInput.value || '0');
            const balance = Number.isFinite(paidAmount) ? Math.max(0, paidAmount - totalAmount) : 0;
            balanceOutput.textContent = `RM ${balance.toFixed(2)}`;
        };

        paidInput.addEventListener('input', renderBalance);
        renderBalance();
    });

    links.forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.defaultPrevented) {
                return;
            }

            const href = link.getAttribute('href') || '';

            if (
                href === '' ||
                href.startsWith('#') ||
                href.startsWith('javascript:') ||
                link.target === '_blank' ||
                link.hasAttribute('download')
            ) {
                return;
            }

            const url = new URL(link.href, window.location.href);

            if (url.origin !== window.location.origin) {
                return;
            }

            showPageLoader();
        });
    });

    forms.forEach((form) => {
        if (form.hasAttribute('data-confirm-dialog')) {
            return;
        }

        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) {
                return;
            }

            showPageLoader();
            setButtonLoadingState(event.submitter || form.querySelector('button[type="submit"], input[type="submit"]'));
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            body.classList.remove('sidebar-open');
        }
    });

    window.addEventListener('pageshow', () => {
        resetPageLoader();
    });
});
