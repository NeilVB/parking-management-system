<?php require_once __DIR__ . '/controller/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Space - Public View</title>

    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="publicstyle.css">
    <link rel="stylesheet" href="views/css/parking_spaces_style.css">

    <!-- JS -->
    <script src="views/js/helpers.js"></script>
    <script src="views/js/navigation.js"></script>
    <script src="views/js/modals.js"></script>
    <script src="views/js/forms.js"></script>
    <script src="views/js/imagePreview.js"></script>
    <script src="views/js/refresh.js"></script>
    <script src="views/js/editMode.js"></script>
    <script src="views/js/filter.js"></script>
    <script src="views/js/init.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-logo">
            <img src="assets/NULogo.png" alt="NUSpace Logo" class="nu-logo">
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

    <!-- Parking Spaces Section -->
    <section id="parking-spaces" class="content-section">
        <div class="page-header">
            <h3><i class="fas fa-car-alt"></i> Parking Spaces Management</h3>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="search-container">
                <input type="text" id="space-search" placeholder="Search parking spaces...">
            </div>
            <div class="filter-container">
                <select id="type-filter" class="filter-select">
                    <option value="all">🔍 All Types</option>
                    <option value="general">🚗 General</option>
                    <option value="faculty">👨‍🏫 Faculty</option>
                    <option value="executive">💼 Executive</option>
                    <option value="guest">👤 Guest</option>
                </select>
            </div>
            <div class="filter-container">
                <select id="vehicle-filter" class="filter-select">
                    <option value="all">🚗🏍️ All Vehicles</option>
                    <option value="car">🚗 Car Only</option>
                    <option value="motorcycle">🏍️ Motorcycle Only</option>
                </select>
            </div>
            <div class="count-display">
                <span id="spaces-count">Loading...</span>
            </div>
        </div>

        <!-- Parking Cards Grid -->
        <div class="parking-container">
            <?php
            $query = "SELECT * FROM parking_spaces";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()):
                $imagePath = !empty($row['image_path']) ? 'uploads/parking/' . basename($row['image_path']) : 'assets/img/default_parking.jpg';
                $vehicleIcon = strtolower($row['vehicle_type']) === 'motorcycle' ? '🏍️' : '🚗';
                $available = (int)$row['max_space'] - (int)$row['parked_count'];
                $status = $available > 0 ? 'Available' : 'Full';
            ?>
            <div class="parking-card" style="background-image: url('<?php echo htmlspecialchars($imagePath); ?>');">
                <div class="overlay">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>Type: <?php echo ucfirst($row['space_type']); ?></p>
                    <p>Vehicle: <?php echo $vehicleIcon . ' ' . ucfirst($row['vehicle_type']); ?></p>
                    <p>Slots: <?php echo (int)$row['parked_count']; ?> / <?php echo (int)$row['max_space']; ?> (<?php echo $status; ?>)</p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> NU Laguna – Parking Management System</p>
    </footer>
</body>
</html>
