<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Space - Public View</title>

    <!-- External styles -->
    <link rel="stylesheet" href="publicstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-logo">
            <img src="assets/NULogo.png" alt="NUSpace_Logo" class="nu-logo">
            <h1 class="site-title">NUSpace</h1>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>Welcome to NUSpace</h1>
            <p class="tagline">Smart Parking Management for NU Laguna</p>

            <ul class="features">
                <li><i class="fas fa-parking"></i> Real-time available parking slots</li>
                <li><i class="fas fa-user-plus"></i> Easy login and registration</li>
            </ul>

            <div class="hero-buttons">
                <a href="controller/register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Create Account</a>
                <a href="controller/login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> NU Laguna – Parking Management System</p>
    </footer>
</body>
</html>
