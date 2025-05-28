// Main application functionality

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded in main.js');
    
    // Initialize the hamburger menu functionality
    initMobileMenu();

    // Initialize theme toggle
    initThemeToggle();

    // Set initial theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

   

    // Initialize service worker
   // initServiceWorker();
});

function initMobileMenu() {
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
}

function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
        console.log('Theme toggle button event listener added');
    }
}

function setTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);
    
    const themeIcons = document.querySelectorAll('.btn-theme i, #themeToggle i');
    themeIcons.forEach(icon => {
        if (theme === 'dark') {
            icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
        } else {
            icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
        }
    });
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    setTheme(newTheme);
}



function initServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
        });
    }
}

// Any other non-authentication related functionality can be added here
