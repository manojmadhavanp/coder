<?php 
$BASE_URL = 'https://navimumbaiweb.com'; 
$mainCategory = "Local Listings";

// Dummy filter options - these can be generated dynamically
$filters = [
    'Price Range' => ['$', '$$', '$$$', '$$$$'],
    'Location'    => ['Navi Mumbai', 'Thane', 'Mumbai', 'Pune'],
    'Rating'      => [1, 2, 3, 4, 5]
];

// Dummy sub-categories each with 12 items (simulated)
$subCategories = [
    'Pre-Schools' => range(1, 12),
    'Restaurants' => range(1, 12),
    'Clinics'     => range(1, 12)
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $mainCategory; ?> | Navi Mumbai Web</title>
  <meta name="description" content="Browse local listings in Navi Mumbai. Find the best businesses in various sub-categories with our comprehensive directory." />
  <link rel="icon" type="image/x-icon" href="<?php echo $BASE_URL; ?>/assets/images/favicon.ico">
  
  <!-- Tailwind CSS CDN -->
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
    /* Hide scrollbar for WebKit browsers */
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;  /* IE and Edge */
      scrollbar-width: none;  /* Firefox */
    }
  </style>
</head>
<body class="bg-lightBg text-gray-900 dark:bg-darkBg dark:text-white">
  <!-- Header Section -->
  <header class="bg-white dark:bg-darkCard shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-6">
      <a href="<?php echo $BASE_URL; ?>" class="text-2xl font-bold text-blue-600">
        <img src="<?php echo $BASE_URL; ?>/assets/images/navimumbaiweb-logo.png" alt="Navi Mumbai Web Logo" class="w-20">
      </a>
      <nav class="hidden md:flex space-x-8">
        <a href="<?php echo $BASE_URL; ?>" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Home</a>
        <a href="<?php echo $BASE_URL; ?>/category-listing.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Listings</a>
        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">About</a>
        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600">Contact</a>
      </nav>
      <div class="flex items-center gap-4">
        <!-- Theme Toggle & Mobile Menu Toggle if needed -->
      </div>
    </div>
  </header>

  <!-- Main Content Area: Two Column Layout -->
  <div class="max-w-7xl mx-auto px-4 py-10 flex flex-col md:flex-row gap-8">
    
    <!-- Left Panel: Filters (Sticky and 90% viewport height) -->
    <aside class="md:w-1/4">
      <div class="bg-white dark:bg-darkCard shadow rounded p-6 sticky top-4 h-[90vh] overflow-y-auto no-scrollbar">
        <h2 class="text-xl font-bold mb-4">Filters</h2>
        <?php foreach ($filters as $filterName => $options): ?>
          <div class="mb-4">
            <h3 class="font-semibold mb-2"><?php echo $filterName; ?></h3>
            <ul class="space-y-1">
              <?php foreach ($options as $option): ?>
                <li>
                  <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox text-blue-600">
                    <span class="ml-2"><?php echo $option; ?></span>
                  </label>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
        <button class="mt-4 block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Apply Filters</button>
      </div>
    </aside>
    
    <!-- Right Panel: Sub-Categories with Carousel -->
    <section class="md:w-3/4 space-y-12">
      <?php foreach ($subCategories as $subCatName => $items): ?>
      <div>
        <!-- Sub-Category Title & View All Button -->
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold"><?php echo $subCatName; ?></h2>
          <a href="<?php echo $BASE_URL; ?>/sub-category.php?category=<?php echo urlencode($subCatName); ?>" class="text-blue-600 hover:underline">View All</a>
        </div>
        <!-- Carousel Container -->
        <div class="flex space-x-4 overflow-x-auto no-scrollbar pb-4">
          <?php foreach ($items as $item): ?>
          <div class="flex-shrink-0 w-64 bg-white dark:bg-darkCard shadow rounded-lg overflow-hidden">
            <a href="<?php echo $BASE_URL; ?>/listing-item.php?id=<?php echo $item; ?>">
              <img src="https://via.placeholder.com/600x400" alt="<?php echo $subCatName; ?> <?php echo $item; ?>" class="w-full h-40 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold mb-2"><?php echo $subCatName; ?> <?php echo $item; ?></h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">Short description of the item.</p>
              </div>
            </a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </section>
  </div>

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

  <!-- Scripts -->
  <script>
    // You can initialize additional JavaScript for arrow controls or carousel functionality here if needed.
  </script>
</body>
</html>
