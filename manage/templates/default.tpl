<?php $BASE_URL = 'https://navimumbaiweb.com'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <meta name="description" content="<?php echo ($description ?? ''); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo $BASE_URL; ?>/assets/images/favicon.ico">
    <!-- <link rel="preload" as="style" crossorigin="crossorigin" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="preload" as="style" crossorigin="crossorigin" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="preload" as="font" type="font/woff2" crossorigin="crossorigin" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">-->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/manage.min.css">
</head>
<body class="">
    <main class="flex items-center justify-center min-h-screen bg-gray-100 content">
        <?php echo $content; ?>
    </main>

    <footer class="footer bg-secondary text-white">
        <div class="container">
            <div class="text-center">
                <p>&copy; <?php echo date('Y');?> NMWEB Digital Business Solutions. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    
    <script>
    BASE_URL = '<?php echo $BASE_URL; ?>';
    </script>
    <script>
   
    </script>

    <script src="<?php echo $BASE_URL; ?>/assets/js/manage.min.js"></script>
   
    
</body>
</html>