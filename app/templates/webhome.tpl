<?php $BASE_URL = 'https://navimumbaiweb.com'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Local Directory | Find the Best Local Businesses</title>
  <meta name="description" content="Discover local businesses and services in your area. Find restaurants, shops, service providers, and more in our comprehensive directory listing website." />
  <meta name="keywords" content="directory, local business, listing, restaurants, shops, services" />
  <meta name="author" content="Your Company Name" />
  
  <link rel="icon" type="image/x-icon" href="<?php echo $BASE_URL; ?>/assets/images/favicon.ico">
  <!-- Open Graph / Facebook -->
  <meta property="og:title" content="Local Directory | Find the Best Local Businesses" />
  <meta property="og:description" content="Discover local businesses and services in your area. Find restaurants, shops, service providers, and more." />
  <meta property="og:image" content="https://navimumbaiweb.com/images/og-image.jpg" />
  <meta property="og:url" content="https://navimumbaiweb.com" />
  <meta property="og:type" content="website" />
  
  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Local Directory | Find the Best Local Businesses" />
  <meta name="twitter:description" content="Discover local businesses and services in your area." />
  <meta name="twitter:image" content="https://navimumbaiweb.com/images/twitter-image.jpg" />
  
  <!-- Canonical URL -->
  <link rel="canonical" href="https://navimumbaiweb.com" />
  
  <!-- Favicon -->
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  
  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "Local Directory",
    "image": "<?php echo $BASE_URL; ?>/assets/images/navimumbaiweb-logo.png",
    "url": "https://navimumbaiweb.com",
    "telephone": "+1234567890",
    "priceRange": "$$",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Vakratund Tower, Sector 4, Plot No 38, Kharghar",
      "addressLocality": "Navi Mumbai",
      "addressRegion": "Maharashtra",
      "postalCode": "410210",
      "addressCountry": "India"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.5",
      "reviewCount": "150"
    }
  }
  </script>
  
  <!-- Tailwind CSS (for prototyping) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            lightBg: "#f8fafc",
            darkBg: "#1E293B",
            lightCard: "#ffffff",
            darkCard: "#334155"
          }
        }
      }
    };
  </script>
  
  <style>
    /* Optional custom styles */
  </style>
</head>
<body class="bg-lightBg text-gray-900 dark:bg-darkBg dark:text-white">
  <!-- Header -->
  <header class="bg-white dark:bg-darkCard shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center py-6">
        <div class="flex-shrink-0">
          <a href="https://navimumbaiweb.com" class="text-2xl font-bold text-blue-600">
            <img src="<?php echo $BASE_URL; ?>/assets/images/navimumbaiweb-logo.png" alt="Navi Mumbai Web Logo" class="w-20">
          </a>
        </div>
        <nav class="hidden md:flex space-x-8">
          <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Home</a>
          <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Listings</a>
          <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">About</a>
          <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Contact</a>
        </nav>
        <div class="flex items-center gap-4">
          <!-- Theme Toggle -->
          <button id="toggleDarkMode" class="bg-gray-200 dark:bg-gray-700 p-2 rounded focus:outline-none">
            <svg id="themeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path id="themeIconPath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8-9h1M4 12H3m15.364 6.364l.707.707M6.343 6.343l-.707-.707m12.728 0l.707-.707M6.343 17.657l-.707.707M12 5a7 7 0 000 14 7 7 0 000-14z"></path>
            </svg>
          </button>
          <!-- Mobile Menu Toggle -->
          <button id="mobile-menu-button" class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden">
      <div class="px-4 pt-2 pb-3 space-y-1 sm:px-3">
        <a href="#" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600">Home</a>
        <a href="#" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600">Listings</a>
        <a href="#" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600">About</a>
        <a href="#" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600">Contact</a>
      </div>
    </div>
  </header>
  
  <!-- Hero Section -->
  <section class="bg-blue-600">
    <div class="max-w-7xl mx-auto px-4 py-16 text-center">
      <h1 class="text-4xl font-bold text-white">Find Best Local Businesses</h1>
      <p class="mt-4 text-lg text-blue-100">Discover restaurants, clinics, dentist, pre schools and more near you.</p>
      <div class="mt-8 flex justify-center">
        <input type="text" placeholder="Search for businesses..." class="w-full max-w-md px-4 py-2 rounded-l-lg focus:outline-none">
        <button class="bg-blue-800 text-white px-6 py-2 rounded-r-lg hover:bg-blue-700 focus:outline-none">Search</button>
      </div>
    </div>
  </section>
  
  <!-- Directory Listings -->
  <main class="max-w-7xl mx-auto px-4 py-10">
    <h2 class="text-2xl font-bold mb-6">Featured Listings</h2>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Listing Card 1 -->
      <article class="bg-white dark:bg-darkCard shadow rounded-lg overflow-hidden">
        <img src="https://via.placeholder.com/600x400" alt="The Great Restaurant" class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">The Great Restaurant</h3>
          <p class="text-gray-600 dark:text-gray-300">Delicious food, excellent service, and a great atmosphere.</p>
          <a href="#" class="mt-4 inline-block text-blue-600 dark:text-blue-400 font-medium">View Details &rarr;</a>
        </div>
      </article>
      <!-- Listing Card 2 -->
      <article class="bg-white dark:bg-darkCard shadow rounded-lg overflow-hidden">
        <img src="https://via.placeholder.com/600x400" alt="Urban Boutique" class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Urban Boutique</h3>
          <p class="text-gray-600 dark:text-gray-300">Trendy clothing and accessories in a chic setting.</p>
          <a href="#" class="mt-4 inline-block text-blue-600 dark:text-blue-400 font-medium">View Details &rarr;</a>
        </div>
      </article>
      <!-- Listing Card 3 -->
      <article class="bg-white dark:bg-darkCard shadow rounded-lg overflow-hidden">
        <img src="https://via.placeholder.com/600x400" alt="Tech Solutions" class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Tech Solutions</h3>
          <p class="text-gray-600 dark:text-gray-300">Innovative IT services and tech support for businesses.</p>
          <a href="#" class="mt-4 inline-block text-blue-600 dark:text-blue-400 font-medium">View Details &rarr;</a>
        </div>
      </article>
      <!-- Additional cards as needed -->
    </div>
  </main>
  
  <!-- Footer -->
  <footer class="bg-white dark:bg-darkCard shadow mt-10">
    <div class="max-w-7xl mx-auto px-4 py-8 text-center">
      <p class="text-gray-600 dark:text-gray-300">&copy; 2025 Navi Mumbai Web. All rights reserved.</p>
      <nav class="mt-4 space-x-4">
        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Privacy Policy</a>
        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Terms of Service</a>
        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Contact Us</a>
      </nav>
    </div>
  </footer>
  
  <script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById("mobile-menu-button");
    const mobileMenu = document.getElementById("mobile-menu");
    mobileMenuButton.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
    });
    
    // Dark mode toggle with localStorage support
    const toggleDarkMode = document.getElementById("toggleDarkMode");
    const themeIcon = document.getElementById("themeIcon");
    const htmlElement = document.documentElement;
    // Load saved theme from localStorage
    if (localStorage.getItem("theme") === "dark") {
      htmlElement.classList.add("dark");
      themeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>`;
    }
    toggleDarkMode.addEventListener("click", () => {
      if (htmlElement.classList.contains("dark")) {
        htmlElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
        themeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8-9h1M4 12H3m15.364 6.364l.707.707M6.343 6.343l-.707-.707m12.728 0l.707-.707M6.343 17.657l-.707.707"/>`;
      } else {
        htmlElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
        themeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>`;
      }
    });
  </script>
</body>
</html>
