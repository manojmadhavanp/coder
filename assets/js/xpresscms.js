document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const actionUrl = '/xpresscms' + form.action.replace(window.location.origin, '');
            const method = form.method.toUpperCase();

            // Check for authorization requirement
            const isAuthorized = form.dataset.authorized === 'true';
            const token = isAuthorized ? localStorage.getItem('authToken') : null;

            try {
                const response = await fetch(actionUrl, {
                    method: method,
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        ...(isAuthorized && token ? { 'Authorization': `Bearer ${token}` } : {}),
                    },
                });

                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

                const result = await response.json();
                handleApiResponse(result, form);
            } catch (error) {
                console.error('Form submission error:', error);
                showToast(`Submission failed: ${error.message}`, 'error');
            }
        });
    });

    function handleApiResponse(response, form) {
        const { status, message, actions } = response;

        // Display message if present
        if (message) {
            showToast(message, status === 'success' ? 'success' : 'error');
        }

        // Handle action instructions from API
        if (Array.isArray(actions)) {
            actions.forEach(action => {
                switch (action.type) {
                    case 'redirect':
                        if (action.url) window.location.href = action.url;
                        break;

                    case 'store':
                        if (action.key && action.value) {
                            localStorage.setItem(action.key, action.value);
                        }
                        break;

                    case 'update':
                        if (action.selector && action.content !== undefined) {
                            const element = document.querySelector(action.selector);
                            if (element) element.innerHTML = action.content;
                        }
                        break;

                    case 'add':
                        if (action.selector && action.content) {
                            const target = document.querySelector(action.selector);
                            if (target) target.insertAdjacentHTML('beforeend', action.content);
                        }
                        break;

                    case 'remove':
                        if (action.selector) {
                            const elementToRemove = document.querySelector(action.selector);
                            if (elementToRemove) elementToRemove.remove();
                        }
                        break;

                    default:
                        console.warn('Unhandled action type:', action.type);
                }
            });
        }

        // Optional: Reset the form after successful submission
        if (status === 'success') {
            form.reset();
        }
    }

    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type === 'error' ? 'danger' : 'success'} border-0 show`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }
});
