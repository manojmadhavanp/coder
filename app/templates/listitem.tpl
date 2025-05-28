<?php 
$BASE_URL = 'https://navimumbaiweb.com'; 
// In a real application you would fetch listing data based on an identifier (e.g., $_GET['id']).
$listing = [
    'title' => 'The Great Restaurant',
    'description' => 'Experience fine dining with an exquisite menu and exceptional service. Located in the heart of Navi Mumbai, The Great Restaurant is perfect for family gatherings and special occasions.',
    'image' => 'https://via.placeholder.com/1200x600',
    'address' => '123, Main Road, Navi Mumbai, Maharashtra, India',
    'phone' => '+911234567890',
    'website' => 'https://example.com'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $listing['title']; ?> | Navi Mumbai Web</title>
  <meta name="description" content="<?php echo $listing['description']; ?>" />
  <link rel="icon" type="image/x-icon" href="<?php echo $BASE_URL; ?>/assets/images/favicon.ico">
  <!-- Tailwind CSS -->
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
</head>
<body class="bg-lightBg text-gray-900 dark:bg-darkBg dark:text-white">
  <!-- Header -->
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
        <!-- Theme Toggle & Mobile Menu Toggle (if needed) -->
      </div>
    </div>
  </header>

  <!-- Listing Detail Section -->
  <main class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Listing Image -->
      <div class="lg:w-2/3">
        <img src="<?php echo $listing['image']; ?>" alt="<?php echo $listing['title']; ?>" class="w-full h-auto rounded-lg shadow">
      </div>
      <!-- Listing Info -->
      <div class="lg:w-1/3">
        <h1 class="text-3xl font-bold mb-4"><?php echo $listing['title']; ?></h1>
        <p class="mb-6"><?php echo $listing['description']; ?></p>
        <div class="mb-4">
          <h2 class="text-xl font-semibold">Contact Info</h2>
          <p><strong>Address:</strong> <?php echo $listing['address']; ?></p>
          <p><strong>Phone:</strong> <?php echo $listing['phone']; ?></p>
          <p><strong>Website:</strong> <a href="<?php echo $listing['website']; ?>" class="text-blue-600 hover:underline"><?php echo $listing['website']; ?></a></p>
        </div>
        <a href="<?php echo $BASE_URL; ?>/category-listing.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Back to Listings</a>
      </div>
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
  
  <!-- Scripts -->
  <script>
    // Mobile menu toggle and dark mode script if required.
  </script>
</body>
</html>
