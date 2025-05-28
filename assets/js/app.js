// Functionality for all logged-in users (admin, superadmin, user)

function isAuthenticated() {
    const token = localStorage.getItem('token');
    console.log('Token found:', !!token);
    return !!token;
}



function logout() {
    console.log('Logout function called');
    fetch('/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
    })
    .then(response => response.json())
    .then(data => {
        console.log('Logout response:', data);
        if (data.status === 'success') {
            localStorage.removeItem('token');
            window.location.href = data.action_data;
        } else {
            console.error('Logout failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Error during logout:', error);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded in app.js');
    
    // Initialize the hamburger menu functionality
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    const closeDrawer = document.getElementById('closeDrawer');
    const drawer = document.getElementById('drawer');

    if (mobileMenuIcon && closeDrawer && drawer) {
        mobileMenuIcon.addEventListener('click', () => {
            drawer.classList.add('open');
        });

        closeDrawer.addEventListener('click', () => {
            drawer.classList.remove('open');
        });
    }

    // Add event listener for logout buttons
    const logoutButtons = document.querySelectorAll('#logoutButton, #mobileLogoutButton');
    logoutButtons.forEach(button => {
        if (button) {
            button.addEventListener('click', logout);
            console.log('Logout button event listener added');
        }
    });

    // Add event listeners for theme toggle buttons
    const themeToggleButtons = document.querySelectorAll('#themeToggle, #drawerThemeToggle');
    themeToggleButtons.forEach(button => {
        if (button) {
            button.addEventListener('click', toggleTheme);
            console.log('Theme toggle button event listener added');
        }
    });

    // Set initial theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    // Run initDashboard only on the dashboard page
    if (window.location.pathname.startsWith('/admin/')) {
        console.log('Running initDashboard');
        isAuthenticated();
    }

    });

function setTheme(theme) {
    document.body.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);
    
    const themeIcons = document.querySelectorAll('.btn-theme i');
    themeIcons.forEach(icon => {
        if (theme === 'dark') {
            icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
        } else {
            icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
        }
    });
}

function toggleTheme() {
    const currentTheme = document.body.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    setTheme(newTheme);
}

// ... (keep the existing isAuthenticated and logout functions)

