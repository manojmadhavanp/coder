<?php $BASE_URL = 'https://navimumbaiweb.com'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NMW Superadmin Manager</title>
  <link rel="icon" type="image/x-icon" href="<?php echo $BASE_URL; ?>/assets/images/favicon.ico">
  <style>
    /* Custom transition for submenus in the expanded sidebar */
    .submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    .submenu.open {
      max-height: 500px; /* adjust as needed */
    }
    /* Styling for the collapsed sidebar slideout: initially hidden */
    .slideout {
    opacity: 0;
    transform: translateX(-10px);
    transition: opacity 0.2s ease, transform 0.2s ease;
    pointer-events: none;
  }
  .group:hover .slideout {
    opacity: 1;
    transform: translateX(0);
    pointer-events: auto;
  }
  </style>
  <!-- Tailwind CSS for prototyping; use local build in production -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class', // Enables manual toggling with the "dark" class
      theme: {
        extend: {
          colors: {
            lightBg: "#f3f3f3",
            darkBg: "#1E293B",
            lightCard: "#ffffff",
            darkCard: "#334155",
          }
        }
      }
    };
  </script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Chart.js Library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-lightBg text-gray-900 dark:bg-darkBg dark:text-white transition duration-300">
  <div class="relative min-h-screen">
    <!-- Expanded Sidebar with Collapsible Menus (visible by default) -->
    <div id="sidebarExpanded" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 text-white shadow-xl p-4 transition-transform duration-300 flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <!-- Logo/Heading -->
          <h2 class="text-xl font-bold">
            <img src="<?php echo $BASE_URL; ?>/assets/images/navimumbaiweb-logo.png" alt="Navi Mumbai Web Logo" class="w-20">
          </h2>
          <!-- Close button -->
          <button id="closeSidebar" class="focus:outline-none">
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>
        <nav class="space-y-2">
          <!-- Dashboard (no submenu) -->
          <a href="#" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span>Dashboard</span>
          </a>
          <!-- Orders -->
          <a href="#" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            <span>Orders</span>
          </a>
          <!-- Products Menu Item with Dropdown Submenu -->
          <div>
            <button class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded submenu-toggle">
              <i data-lucide="box" class="w-5 h-5"></i>
              <span>Products</span>
              <!-- Arrow indicator -->
              <i data-lucide="chevron-down" class="w-4 h-4 ml-auto"></i>
            </button>
            <!-- Submenu -->
            <div class="submenu ml-8 mt-1 space-y-1">
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Collections</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Inventory</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Purchase Orders</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Transfer</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Catalogs</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Price Lists</a>
            </div>
          </div>
          <!-- Customers Menu Item with Dropdown Submenu -->
          <div>
            <button class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded submenu-toggle">
              <i data-lucide="user" class="w-5 h-5"></i>
              <span>Customers</span>
              <i data-lucide="chevron-down" class="w-4 h-4 ml-auto"></i>
            </button>
            <div class="submenu ml-8 mt-1 space-y-1">
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Segments</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-700 rounded">Companies</a>
            </div>
          </div>
        </nav>
      </div>
      <!-- Footer menu items for Expanded Sidebar -->
      <div class="space-y-2 border-t border-gray-700 pt-4">
        <!-- Dark/Light Mode Toggle -->
        <button id="toggleDarkModeSide" class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded focus:outline-none">
          <i data-lucide="sun" class="w-5 h-5"></i>
          <span>Dark / Light Mode</span>
        </button>
        <!-- Settings -->
        <a href="settings.php" class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
          <i data-lucide="settings" class="w-5 h-5"></i>
          <span>Settings</span>
        </a>
        <!-- Logout -->
        <button onclick="logout()" class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded focus:outline-none">
          <i data-lucide="log-out" class="w-5 h-5"></i>
          <span>Logout</span>
        </button>
      </div>
    </div>

    <!-- Collapsed Sidebar (Icon Only) with Hover Slide-out for Submenus -->
    <!-- Collapsed Sidebar (Icon Only) with Hover Slide-out for Submenus -->
<div id="sidebarCollapsed" class="fixed top-0 left-0 z-40 h-full w-16 bg-gray-800 text-white shadow-xl flex flex-col justify-between">
  <div class="flex flex-col items-center space-y-6 py-4">
    <!-- Toggle button (opens expanded sidebar) -->
    <button id="openSidebar" class="focus:outline-none">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
    <nav class="space-y-4">
      <!-- Dashboard (icon only) -->
      <div class="relative group">
        <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-700 rounded">
          <i data-lucide="home" class="w-5 h-5"></i>
        </a>
        <div class="slideout absolute left-full top-0 ml-0 bg-gray-800 rounded shadow-lg min-w-[150px] px-2 py-1 whitespace-nowrap">
          Dashboard
        </div>
      </div>
      <!-- Products (icon only) with submenu -->
      <div class="relative group">
        <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-700 rounded">
          <i data-lucide="box" class="w-5 h-5"></i>
        </a>
        <div class="slideout absolute left-full top-0 ml-0 bg-gray-800 rounded shadow-lg min-w-[150px] px-2 py-1">
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Collections</a>
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Inventory</a>
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Purchase Orders</a>
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Transfer</a>
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Catalogs</a>
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Price Lists</a>
        </div>
      </div>
      <!-- Customers (icon only) with submenu -->
      <div class="relative group">
        <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-700 rounded">
          <i data-lucide="user" class="w-5 h-5"></i>
        </a>
        <div class="slideout absolute left-full top-0 ml-0 bg-gray-800 rounded shadow-lg min-w-[150px] px-2 py-1">
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Segments</a>
          <a href="#" class="block hover:bg-gray-700 px-2 py-1">Companies</a>
        </div>
      </div>
    </nav>
  </div>
  <!-- Footer menu for Collapsed Sidebar remains unchanged -->
  <div class="mb-4">
    <div class="flex flex-col items-center space-y-4">
      <!-- Dark / Light Mode Toggle -->
      <div class="relative group">
        <button id="toggleDarkModeCollapsed" class="p-2 hover:bg-gray-700 rounded focus:outline-none">
          <i data-lucide="sun" class="w-5 h-5"></i>
        </button>
        <div class="slideout absolute left-full top-0 ml-0 bg-gray-800 rounded shadow-lg min-w-[150px] px-2 py-1 whitespace-nowrap">
          Dark / Light Mode
        </div>
      </div>
      <!-- Settings -->
      <div class="relative group">
        <a href="./settings" class="p-2 hover:bg-gray-700 rounded focus:outline-none inline-flex">
          <i data-lucide="settings" class="w-5 h-5"></i>
        </a>
        <div class="slideout absolute left-full top-0 ml-0 bg-gray-800 rounded shadow-lg min-w-[150px] px-2 py-1 whitespace-nowrap">
          Settings
        </div>
      </div>
      <!-- Logout -->
      <div class="relative group">
        <a href="./logout" onclick="logout()" class="p-2 hover:bg-gray-700 rounded focus:outline-none inline-flex">
          <i data-lucide="log-out" class="w-5 h-5"></i>
        </a>
        <div class="slideout absolute left-full top-0 ml-0 bg-gray-800 rounded shadow-lg min-w-[150px] px-2 py-1 whitespace-nowrap">
          Logout
        </div>
      </div>
    </div>
  </div>
</div>
    
    <!-- Overlay for Floating Sidebar (optional) -->
    <div id="overlay" class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden"></div>
    
    <!-- Main Content Area -->
    <!-- Starts with left margin ml-64 (expanded sidebar width) -->
    <div id="mainContent" class="transition-all duration-300 ml-64">
      <!-- Top Navbar -->
      <header class="flex items-center justify-between p-4 bg-white dark:bg-darkCard shadow">
        <div class="flex items-center gap-4">
          <h1 class="text-2xl font-bold">Dashboard</h1>
        </div>
        <!-- Additional top navbar items (if needed) -->
        <div class="flex items-center gap-4">
          <!-- User Details -->
          <div class="flex items-center gap-2">
            <img src="https://via.placeholder.com/40" alt="User Photo" class="w-8 h-8 rounded-full object-cover">
            <div class="text-sm">
              <p class="font-medium">John Doe</p>
              <p class="text-xs text-gray-500 dark:text-gray-300">Acme Inc.</p>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Dashboard Components -->
      <main class="p-8 sm:p-4 md:p-6 lg:p-8 space-y-8">
        <!-- Overview Cards, Charts, Tables, etc. -->
        <section>
          <div class="grid grid-cols-1 "
        </section
        <section>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-cyan-100 to-white dark:from-cyan-200 dark:to-gray-900 p-5 rounded-lg shadow flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold">Total Users</h3>
                <p class="text-2xl font-bold">1,250</p>
              </div>
              <i data-lucide="users" class="w-8 h-8 text-blue-500"></i>
            </div>
            <!-- Additional Cards -->
            <div class="bg-gradient-to-br from-[#e0f7fa] to-[#ffffff] dark:from-[#1e293b] dark:to-[#0f172a] p-5 rounded-lg shadow flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold">Total Users</h3>
                <p class="text-2xl font-bold">1,250</p>
              </div>
              <i data-lucide="users" class="w-8 h-8 text-blue-500"></i>
            </div>
            <div class="bg-white dark:bg-darkCard p-5 rounded-lg shadow flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold">Total Users</h3>
                <p class="text-2xl font-bold">1,250</p>
              </div>
              <i data-lucide="users" class="w-8 h-8 text-blue-500"></i>
            </div>
            <div class="bg-white dark:bg-darkCard p-5 rounded-lg shadow flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold">Total Users</h3>
                <p class="text-2xl font-bold">1,250</p>
              </div>
              <i data-lucide="users" class="w-8 h-8 text-blue-500"></i>
            </div>
          </div>
        </section>
        
        <!-- Chart Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <section class="bg-white dark:bg-darkCard p-6 rounded-lg shadow">
          <h2 class="text-xl font-semibold mb-4">Revenue Growth</h2>
          <canvas id="chart" class="w-full h-64"></canvas>
        </section>
        <section class="bg-white dark:bg-darkCard p-6 rounded-lg shadow">
          <h2 class="text-xl font-semibold mb-4">Profit Margin</h2>
          <canvas id="chart2" class="w-full h-64"></canvas>
        </section>

        <!-- Graph Card 3 -->
        <section class="bg-white dark:bg-darkCard p-6 rounded-lg shadow">
          <h2 class="text-xl font-semibold mb-4">User Growth</h2>
          <canvas id="chart3" class="w-full h-64"></canvas>
        </section>

        <!-- Graph Card 4 -->
        <section class="bg-white dark:bg-darkCard p-6 rounded-lg shadow">
          <h2 class="text-xl font-semibold mb-4">Conversion Rate</h2>
          <canvas id="chart4" class="w-full h-64"></canvas>
        </section>
        </div>
        
        <!-- Table Section -->
        <section class="bg-white dark:bg-darkCard p-6 rounded-lg shadow">
  <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>

  <!-- Search and Filter Controls -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
    <input type="text" id="searchInput" placeholder="Search..." class="w-full md:w-1/3 px-4 py-2 border rounded shadow-sm dark:bg-darkCard dark:text-white dark:border-gray-600" />
    <select id="statusFilter" class="w-full md:w-1/4 px-4 py-2 border rounded shadow-sm dark:bg-darkCard dark:text-white dark:border-gray-600">
      <option value="">All Statuses</option>
      <option value="Completed">Completed</option>
      <option value="Pending">Pending</option>
      <option value="Cancelled">Cancelled</option>
    </select>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-semibold cursor-pointer text-gray-700 dark:text-gray-200" onclick="sortTable(0)">Order ID</th>
          <th class="px-4 py-2 text-left text-sm font-semibold cursor-pointer text-gray-700 dark:text-gray-200" onclick="sortTable(1)">Customer</th>
          <th class="px-4 py-2 text-left text-sm font-semibold cursor-pointer text-gray-700 dark:text-gray-200" onclick="sortTable(2)">Amount</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Status</th>
        </tr>
      </thead>
      <tbody id="ordersTable" class="bg-white dark:bg-darkCard divide-y divide-gray-200 dark:divide-gray-700">
        <!-- Rows populated via JS -->
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="flex justify-center mt-4">
    <button onclick="prevPage()" class="px-4 py-2 mr-2 text-sm bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-white">Previous</button>
    <button onclick="nextPage()" class="px-4 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-white">Next</button>
  </div>
</section>

<script>
const orders = [
  { id: '#1001', customer: 'John Doe', amount: 120, status: 'Completed' },
  { id: '#1002', customer: 'Jane Smith', amount: 80, status: 'Pending' },
  { id: '#1003', customer: 'Alice Johnson', amount: 95, status: 'Cancelled' },
  { id: '#1004', customer: 'Bob Brown', amount: 140, status: 'Completed' },
  // Add more mock data as needed
];

let currentPage = 1;
const rowsPerPage = 5;

function renderTable() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const filter = document.getElementById('statusFilter').value;
  const tbody = document.getElementById('ordersTable');
  tbody.innerHTML = '';

  let filtered = orders.filter(order => {
    return (
      (!filter || order.status === filter) &&
      (order.id.toLowerCase().includes(search) ||
       order.customer.toLowerCase().includes(search))
    );
  });

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  filtered.slice(start, end).forEach(order => {
    const row = `<tr>
      <td class="px-4 py-2 text-sm">${order.id}</td>
      <td class="px-4 py-2 text-sm">${order.customer}</td>
      <td class="px-4 py-2 text-sm">$${order.amount.toFixed(2)}</td>
      <td class="px-4 py-2 text-sm">
        <span class="px-2 py-1 rounded bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200">${order.status}</span>
      </td>
    </tr>`;
    tbody.insertAdjacentHTML('beforeend', row);
  });
}

function sortTable(index) {
  const key = ['id', 'customer', 'amount'][index];
  orders.sort((a, b) => a[key] > b[key] ? 1 : -1);
  renderTable();
}

function prevPage() {
  if (currentPage > 1) {
    currentPage--;
    renderTable();
  }
}

function nextPage() {
  const totalPages = Math.ceil(orders.length / rowsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    renderTable();
  }
}

document.getElementById('searchInput').addEventListener('input', () => {
  currentPage = 1;
  renderTable();
});

document.getElementById('statusFilter').addEventListener('change', () => {
  currentPage = 1;
  renderTable();
});

renderTable();
</script>
        
        <!-- Form Section (Example) -->
        <section class="bg-white dark:bg-darkCard p-6 rounded-lg shadow">
          <h2 class="text-xl font-semibold mb-4">New Order</h2>
          <form class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1" for="customer">Customer Name</label>
              <input id="customer" type="text" placeholder="John Doe" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" for="amount">Amount</label>
              <input id="amount" type="number" placeholder="$0.00" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" for="status">Status</label>
              <select id="status" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                <option>Pending</option>
                <option>Completed</option>
                <option>Cancelled</option>
              </select>
            </div>
            <div>
              <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white rounded p-2">Submit Order</button>
            </div>
          </form>
        </section>
      </main>
      
      <!-- Footer -->
      <footer class="p-4 bg-white dark:bg-darkCard shadow text-center">
        <p class="text-sm">&copy; 2025 Admin Dashboard. All rights reserved.</p>
      </footer>
    </div>
  </div>

  <!-- JavaScript Section -->
  <script>
    // Toggle dropdown submenus in the expanded sidebar:
    document.querySelectorAll('.submenu-toggle').forEach(function (button) {
      button.addEventListener('click', function () {
        let submenu = button.nextElementSibling;
        submenu.classList.toggle('open');
      });
    });

    // Elements for sidebar toggling and main content margin:
    const sidebarExpanded = document.getElementById('sidebarExpanded');
    const openSidebarBtn = document.getElementById('openSidebar');
    const closeSidebarBtn = document.getElementById('closeSidebar');
    const mainContent = document.getElementById('mainContent');
    const overlay = document.getElementById('overlay');

    function openSidebar() {
      sidebarExpanded.classList.remove('-translate-x-full');
      mainContent.classList.remove('ml-16');
      mainContent.classList.add('ml-64');
      overlay.classList.remove('hidden');
    }
    
    function closeSidebar() {
      sidebarExpanded.classList.add('-translate-x-full');
      mainContent.classList.remove('ml-64');
      mainContent.classList.add('ml-16');
      overlay.classList.add('hidden');
    }

    openSidebarBtn.addEventListener('click', openSidebar);
    closeSidebarBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Common dark mode toggle function:
    function toggleDarkMode() {
      const htmlElement = document.documentElement;
      // Toggle dark mode on the <html> element
      if (htmlElement.classList.contains("dark")) {
        htmlElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
        // Update any dark mode icons to "sun"
        lucide.createIcons();
      } else {
        htmlElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
        // Update any dark mode icons to "moon"
        lucide.createIcons();
      }
    }
    
    // Attach dark mode toggle to both sidebar buttons:
    document.getElementById("toggleDarkModeSide").addEventListener("click", toggleDarkMode);
    document.getElementById("toggleDarkModeCollapsed").addEventListener("click", toggleDarkMode);

    // Load saved theme from localStorage
    if (localStorage.getItem("theme") === "dark") {
      document.documentElement.classList.add("dark");
    }
    
    // Initialize Lucide icons
    lucide.createIcons();
    
    // ChartJS Initialization
    const ctx = document.getElementById("chart").getContext("2d");
    new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [{
          label: "Revenue",
          data: [12000, 15000, 14000, 18000, 22000, 25000],
          borderColor: "#3B82F6",
          backgroundColor: "rgba(59, 130, 246, 0.2)",
          borderWidth: 2,
          fill: true
        }]
      },
      options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
      }
    });
    
    // Dummy logout function
    function logout() {
      // Implement your logout process here
      alert("Logging out…");
    }
  </script>
</body>
</html>
