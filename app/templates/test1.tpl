<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modern Admin Dashboard</title>
  <!-- Use local build for production -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    /* Optional: Customize overlay transition */
    #overlay {
      transition: opacity 0.3s ease;
    }
  </style>
</head>
<body class="bg-gray-100">
  <div class="relative min-h-screen">
    <!-- Floating Full Sidebar (hidden by default) -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 text-white shadow-xl transform -translate-x-full transition-transform duration-300">
      <div class="flex items-center justify-between p-4">
        <h2 class="text-xl font-bold">Admin Panel</h2>
        <button id="closeSidebar" class="focus:outline-none">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <nav class="mt-4 space-y-2">
        <a href="#" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
          <i data-lucide="home" class="w-5 h-5"></i>
          <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
          <i data-lucide="users" class="w-5 h-5"></i>
          <span>Users</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
          <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
          <span>Reports</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
          <i data-lucide="settings" class="w-5 h-5"></i>
          <span>Settings</span>
        </a>
      </nav>
    </div>

    <!-- Collapsed Sidebar (always visible; only icons) -->
    <div id="sidebarCollapsed" class="fixed top-0 left-0 z-40 h-full w-16 bg-gray-800 text-white shadow-xl">
      <div class="flex flex-col items-center space-y-6 py-4">
        <!-- Toggle button to open floating sidebar -->
        <button id="openSidebar" class="focus:outline-none">
          <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <nav class="space-y-4">
          <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-700 rounded">
            <i data-lucide="home" class="w-5 h-5"></i>
          </a>
          <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-700 rounded">
            <i data-lucide="users" class="w-5 h-5"></i>
          </a>
          <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-700 rounded">
            <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
          </a>
          <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-700 rounded">
            <i data-lucide="settings" class="w-5 h-5"></i>
          </a>
        </nav>
      </div>
    </div>

    <!-- Overlay for Floating Sidebar -->
    <div id="overlay" class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden"></div>

    <!-- Main Content Area -->
    <div class="ml-16 transition-all duration-300">
      <header class="p-4 bg-white shadow">
        <h1 class="text-3xl font-bold">Dashboard</h1>
      </header>
      <main class="p-6">
        <p class="text-gray-700">Main content goes here. This area takes up the maximum available space.</p>
      </main>
    </div>
  </div>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Elements
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapsed = document.getElementById('sidebarCollapsed');
    const openSidebarBtn = document.getElementById('openSidebar');
    const closeSidebarBtn = document.getElementById('closeSidebar');
    const overlay = document.getElementById('overlay');

    // Open the floating sidebar and show overlay
    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }

    // Close the floating sidebar and hide overlay
    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }

    // Event listeners
    openSidebarBtn.addEventListener('click', openSidebar);
    closeSidebarBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
  </script>
</body>
</html>
