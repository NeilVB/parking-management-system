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
    <script src="../views/js/helpers.js"></script>
    <script src="../views/js/navigation.js"></script>
    <script src="../views/js/modals.js"></script>
    <script src="../views/js/forms.js"></script>
    <script src="../views/js/imagePreview.js"></script>
    <script src="../views/js/refresh.js"></script>
    <script src="../views/js/editMode.js"></script>
    <script src="../views/js/deleteMode.js"></script>
    <script src="../views/js/init.js"></script>
    <script src="../views/js/filter.js"></script>
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
        <!-- Parking Spaces -->
        <div class="parking-spaces-content">
            <div class="page-header">
                <h3><i class="fas fa-car-alt"></i> Parking Spaces Management</h3>
            </div>

            <!-- Search and Filter Bar -->
            <div class="filter-bar" >
                <!-- Search Input -->
                <div class="search-container" style="flex: 1; min-width: 250px;">
                    <input type="text" id="space-search" placeholder="Search parking spaces..." 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                
                <!-- Filter Dropdown -->
                <div class="filter-container">
                    <select id="type-filter" class="filter-select" 
                            style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; cursor: pointer; background: white;">
                        <option value="all">All Types</option>
                        <option value="general">General</option>
                        <option value="faculty">Faculty</option>
                        <option value="executive">Executive</option>
                        <option value="guest">Guest</option>
                    </select>
                </div>
                
                <!-- Vehicle Type Filter -->
                <div class="filter-container">
                    <select id="vehicle-filter" class="filter-select" 
                            style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; cursor: pointer; background: white;">
                        <option value="all">All Vehicles</option>
                        <option value="car">Car Only</option>
                        <option value="motorcycle">Motorcycle Only</option>
                    </select>
                </div>
                
            </div>

            <div class="parking-container">
                <?php
                require_once __DIR__ . '../controller/config.php';
                require_once __DIR__ . '../models/ParkingSpace.php';

                $parkingModel = new ParkingSpace($conn);
                $query = "SELECT * FROM parking_spaces ORDER BY id DESC";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()):
                    // Fix image path
                    $imagePath = !empty($row['image_path']) ? '../' . ltrim($row['image_path'], '/.') : '/assets/img/default_parking.jpg';

                    // Get allocations for this parking space
                    $allocations = $parkingModel->getAllocations($row['id']);

                    // Compute slots and user types
                    $carSlots = 0;
                    $motorcycleSlots = 0;
                    $userTypesSet = []; // use associative to avoid duplicates
                    $allocationDetails = [];

                    foreach ($allocations as $alloc) {
                        if ($alloc['vehicle_type_id'] == 1) {
                            $carSlots += (int)$alloc['allocated_slots'];
                        } else {
                            $motorcycleSlots += (int)$alloc['allocated_slots'];
                        }

                        if ((int)$alloc['allocated_slots'] > 0) {
                            // record user type (use the user_type field from join)
                            $ut = strtolower(trim($alloc['user_type']));
                            // normalize to simple keys: student/visitor -> general
                            if (strpos($ut, 'student') !== false || strpos($ut, 'visitor') !== false) {
                                $userTypesSet['general'] = true;
                            } elseif (strpos($ut, 'faculty') !== false) {
                                $userTypesSet['faculty'] = true;
                            } elseif (strpos($ut, 'executive') !== false) {
                                $userTypesSet['executive'] = true;
                            } elseif (strpos($ut, 'guest') !== false) {
                                $userTypesSet['guest'] = true;
                            }
                            $allocationDetails[] = $alloc['user_type'] . ': ' . (int)$alloc['allocated_slots'];
                        }
                    }

                    // Prepare data attributes
                    $userTypes = !empty($userTypesSet) ? implode(',', array_keys($userTypesSet)) : '';
                    $vehicles = [];
                    if ($row['supports_car']) $vehicles[] = 'car';
                    if ($row['supports_motorcycle']) $vehicles[] = 'motorcycle';
                    $vehiclesAttr = implode(',', $vehicles);

                    // Calculate totals
                    $totalSlots = $carSlots + $motorcycleSlots;
                    $parkedCount = (int)$row['parked_car_count'] + (int)$row['parked_motorcycle_count'];
                    $available = $totalSlots - $parkedCount;
                    $status = $available > 0 ? 'Available' : 'Full';

                    // image path safe output
                    $imgSafe = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
                    $nameSafe = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                ?>
                    <div class="parking-card"
                        data-id="<?php echo (int)$row['id']; ?>"
                        data-name="<?php echo $nameSafe; ?>"
                        data-user-types="<?php echo htmlspecialchars($userTypes, ENT_QUOTES, 'UTF-8'); ?>"
                        data-vehicles="<?php echo htmlspecialchars($vehiclesAttr, ENT_QUOTES, 'UTF-8'); ?>"
                        style="background-image: url('<?php echo $imgSafe; ?>');">
                        <div class="overlay">
                            <h3><?php echo $nameSafe; ?></h3>

                            <p><strong>Vehicle Types:</strong>
                                <?php
                                $types = [];
                                if ($row['supports_car']) $types[] = '🚗 Car (' . $carSlots . ' slots)';
                                if ($row['supports_motorcycle']) $types[] = '🏍️ Motorcycle (' . $motorcycleSlots . ' slots)';
                                echo implode(', ', $types);
                                ?>
                            </p>

                            <p><strong>User Type Allocations:</strong><br>
                                <?php
                                if (!empty($allocationDetails)) {
                                    echo '<small>' . implode('<br>', array_slice($allocationDetails, 0, 4)) . '</small>';
                                    if (count($allocationDetails) > 4) {
                                        echo '<br><small>+' . (count($allocationDetails) - 4) . ' more...</small>';
                                    }
                                } else {
                                    echo '<small>No allocations</small>';
                                }
                                ?>
                            </p>

                            <p><strong>Occupancy:</strong>
                                <?php echo $parkedCount; ?> / <?php echo $totalSlots; ?>
                                <span class="status-badge <?php echo strtolower($status); ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                <?php
                endwhile;
                ?>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> NU Laguna – Parking Management System</p>
    </footer>
</body>
</html>
