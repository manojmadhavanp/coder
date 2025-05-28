<?php $BASE_URL = 'http://xpressphp.local/'; 
$activeuri = "./". $active_uri; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $title ?? 'VinuLMS'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.min.css">
    <link rel="stylesheet" href="/assets/css/customstyle.min.css">
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="<?php echo $BASE_URL; ?>assets/images/xpressphp-logo.webp" alt="XPressPHP Logo" height="40">
        </div>
        <nav class="main-nav d-none d-md-flex">
            <ul class="nav nav-pills nav-flush">
            <?php foreach ($menu as $item): ?>
            <li class="nav-item">
               
                <a href="<?php echo $item['url']; ?>" class="nav-link <?php echo $activeuri === $item['url'] ? 'active' : ''; ?>">
                    <i class="bi <?php echo $item['icon']; ?>"></i>
                    <?php echo $item['title']; ?>
                </a>
            </li    
            <?php endforeach; ?>
            </ul>
        </nav>
        <div class="header-actions d-none d-md-flex">
            <button id="themeToggle" class="btn btn-theme">
                <i class="bi bi-sun-fill"></i>
            </button>
            <button id="logoutButton" class="btn btn-danger signout">
                <i class="bi bi-box-arrow-right"></i> Sign Out
            </button>
        </div>
    </header>

    <nav class="drawer" id="drawer">
        <div class="drawer-header">
            <img src="<?php echo $BASE_URL; ?>assets/images/logo.webp" alt="VinuLMS Logo" class="drawer-logo">
            <button class="close-drawer" id="closeDrawer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="drawer-menu">
            <?php foreach ($menu as $item): ?>
                <a href="<?php echo $item['url']; ?>" class="drawer-item">
                    <i class="bi <?php echo $item['icon']; ?>"></i> <?php echo $item['title']; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="drawer-bottom">
            <button id="drawerThemeToggle" class="btn-theme">
                <i class="bi bi-sun-fill"></i>
                <span>Theme</span>
            </button>
            <a href="/admin/settings" class="btn-settings">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </div>
    </nav>

    <main class="content" id="content">
               <?php echo $content ?? ''; ?>
    </main>

    <footer class="mobile-footer d-md-none">
        <button id="mobileMenuIcon" class="footer-icon">
            <i class="bi bi-list"></i>
        </button>
        <a href="/admin/dashboard" class="footer-icon">
            <i class="bi bi-speedometer2"></i>
        </a>
        <button id="mobileLogoutButton" class="footer-icon">
            <i class="bi bi-box-arrow-right"></i>
        </button>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- App.js -->
    <script src="/assets/js/app.min.js"></script>
</body>
</html>
