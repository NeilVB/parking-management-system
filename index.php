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
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-logo">
            <a href="../index.php" class="logo-link">
                <img src="assets/NULogo.png" alt="NU Logo" class="nu-logo">
                <span class="site-title">NUSpace</span>
            </a>
        </div>
    </header>

    <?php
    // Calculate parking availability BEFORE hero section
    require_once __DIR__ . '/controller/config.php';
    require_once __DIR__ . '/models/ParkingSpace.php';

    $parkingModel = new ParkingSpace($conn);
    
    // Get all parking spaces
    $query = "SELECT * FROM parking_spaces ORDER BY id DESC";
    $result = $conn->query($query);

    // Initialize aggregated data
    $carSpaces = [
        'Student & Visitor' => ['available' => 0, 'total' => 0],
        'Faculty' => ['available' => 0, 'total' => 0],
        'Executive' => ['available' => 0, 'total' => 0],
        'Guest' => ['available' => 0, 'total' => 0]
    ];

    $motorSpaces = [
        'Student & Visitor' => ['available' => 0, 'total' => 0],
        'Faculty' => ['available' => 0, 'total' => 0],
        'Executive' => ['available' => 0, 'total' => 0],
        'Guest' => ['available' => 0, 'total' => 0]
    ];

    // Process each parking space
    while ($row = $result->fetch_assoc()) {
        $allocations = $parkingModel->getAllocations($row['id']);

        foreach ($allocations as $alloc) {
            $allocatedSlots = (int)$alloc['allocated_slots'];
            
            if ($allocatedSlots <= 0) continue;

            // Normalize user type
            $userType = trim($alloc['user_type']);
            $normalizedType = 'Student & Visitor'; // default
            
            $userTypeLower = strtolower($userType);
            if (strpos($userTypeLower, 'faculty') !== false) {
                $normalizedType = 'Faculty';
            } elseif (strpos($userTypeLower, 'executive') !== false) {
                $normalizedType = 'Executive';
            } elseif (strpos($userTypeLower, 'guest') !== false) {
                $normalizedType = 'Guest';
            }

            // Determine if it's car or motorcycle
            $vehicleTypeId = (int)$alloc['vehicle_type_id'];
            $isCar = ($vehicleTypeId == 1);

            if ($isCar) {
                // Car spaces
                $carSpaces[$normalizedType]['total'] += $allocatedSlots;
                
                // Calculate available
                $parkedCars = (int)$row['parked_car_count'];
                $availableInThisSpace = max(0, $allocatedSlots - $parkedCars);
                $carSpaces[$normalizedType]['available'] += $availableInThisSpace;
            } else {
                // Motorcycle spaces
                $motorSpaces[$normalizedType]['total'] += $allocatedSlots;
                
                $parkedMotors = (int)$row['parked_motorcycle_count'];
                $availableInThisSpace = max(0, $allocatedSlots - $parkedMotors);
                $motorSpaces[$normalizedType]['available'] += $availableInThisSpace;
            }
        }
    }
    ?>

<!-- Hero Section with Available Parking Spaces -->
<section class="hero">
    <div class="hero-content">
        <h1>Welcome to NUSpace</h1>
        <p class="tagline">Smart Parking Management for NU Laguna</p>

        <div class="availability-container">
            <!-- Car Parking -->
            <div class="availability-section">
                <h4><i class="fas fa-car"></i> Car Parking Spaces</h4>
                <ul class="availability-list">
                    <?php foreach ($carSpaces as $userType => $data): ?>
                        <?php if ($data['total'] > 0): 
                            $availabilityRatio = $data['available'] / $data['total'];
                            $class = '';
                            if ($availabilityRatio < 0.3) $class = 'critical';
                            elseif ($availabilityRatio < 0.6) $class = 'low';
                        ?>
                            <li class="<?php echo $class; ?>">
                                <span class="user-type"><?php echo htmlspecialchars($userType); ?></span>
                                <span class="availability-count"><?php echo $data['available']; ?>/<?php echo $data['total']; ?></span>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Motorcycle Parking -->
            <div class="availability-section">
                <h4><i class="fas fa-motorcycle"></i> Motorcycle Parking Spaces</h4>
                <ul class="availability-list">
                    <?php foreach ($motorSpaces as $userType => $data): ?>
                        <?php if ($data['total'] > 0): 
                            $availabilityRatio = $data['available'] / $data['total'];
                            $class = '';
                            if ($availabilityRatio < 0.3) $class = 'critical';
                            elseif ($availabilityRatio < 0.6) $class = 'low';
                        ?>
                            <li class="<?php echo $class; ?>">
                                <span class="user-type"><?php echo htmlspecialchars($userType); ?></span>
                                <span class="availability-count"><?php echo $data['available']; ?>/<?php echo $data['total']; ?></span>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="hero-buttons">
            <a href="controller/register.php" class="btn btn-primary"> Create Account</a>
            <a href="controller/login.php" class="btn btn-outline"></i> Login Now</a>
        </div>
    </div>
</section>


    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> NU Laguna – Parking Management System</p>
    </footer>
</body>
</html>