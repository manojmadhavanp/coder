// Authentication-related functionality (login, registration, logout)

function handleFormSubmit(formId, successCallback, errorCallback) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(form.action, {
                    method: form.method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));

                const responseText = await response.text();
                console.log('Raw server response:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse response as JSON:', parseError);
                    throw new Error('Server returned an invalid response');
                }

                if (response.ok) {
                    successCallback(result);
                } else {
                    console.error('Server returned an error:', result);
                    errorCallback(result);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                errorCallback({ message: 'An unexpected error occurred', details: error.toString() });
            }
        });
    }
}

// Handle login form
handleFormSubmit('loginForm', 
    (data) => {
        console.log('Login response:', data);
        if (data.status === 'success' && data.data && data.data.token) {
            localStorage.setItem('token', data.data.token);
            console.log('Token and UUID stored in localStorage');
            
            if (data.action === 'redirect' && data.action_data) {
                console.log('Redirecting to:', data.action_data);
                window.location.href = data.action_data;
            } else {
                console.warn('No redirect action provided, defaulting to dashboard');
                window.location.href = '/admin/dashboard';
            }
        } else {
            console.error('Login response is not in the expected format:', data);
            alert('An unexpected error occurred. Please try again.');
        }
    },
    (error) => {
        console.error('Login failed:', error);
        alert(error.message || 'Login failed. Please try again.');
    }
);

// Handle registration form
handleFormSubmit('registerForm',
    (data) => {
        console.log('Registration response:', data);
        if (data.status === 'success') {
            alert('Registration successful! Please log in.');
            window.location.href = '/login';
        } else {
            console.error('Registration response is not in the expected format:', data);
            alert('An unexpected error occurred. Please try again.');
        }
    },
    (error) => {
        console.error('Registration failed:', error);
        alert(error.message || 'Registration failed. Please try again.');
    }
);

function checkLoggedIn() {
    const token = localStorage.getItem('token');
    console.log('Checking login status. Token exists:', !!token);
    if (token) {
        if (window.location.pathname === '/login' || window.location.pathname === '/register') {
            console.log('Already logged in, redirecting to /admin/dashboard');
            window.location.href = '/admin/dashboard';
        }
    } else if (window.location.pathname.startsWith('/admin/')) {
        console.log('Not logged in, redirecting to /login');
        window.location.href = '/login';
    }
}

// Run checkLoggedIn on login and register pages
if (window.location.pathname === '/login' || window.location.pathname === '/register') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded in auth.js, running checkLoggedIn');
        checkLoggedIn();
    });
}

// Add event listener for logout
document.addEventListener('DOMContentLoaded', () => {
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('token');
            localStorage.removeItem('uuid');
            console.log('Token and UUID removed from localStorage');
            window.location.href = '/login';
        });
    } else {
        console.log('Logout button not found');
    }
});
