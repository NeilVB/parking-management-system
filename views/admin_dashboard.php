<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../controller/admin_login.php");
    exit();
}

// Load admin session details
$admin_id   = $_SESSION['admin_id'];
$firstname  = $_SESSION['firstname'] ?? 'Admin';
$lastname   = $_SESSION['lastname'] ?? '';
$email      = $_SESSION['email'] ?? '';
$user_type  = $_SESSION['user_type'] ?? 'Administrator';

// Load dependencies
require_once __DIR__ . '/../controller/config.php';
require_once __DIR__ . '/../models/ParkingSpace.php';

// Initialize model and fetch data
$model = new ParkingSpace($conn);
$counts = $model->getVehicleCounts();
$distribution = $model->getSlotDistribution();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Space - Admin Dashboard</title>

    <!-- Include Modals -->
    <?php include 'qr_modals.php'; ?>
    <?php include 'parking_modals.php'; ?>
    <?php include 'management_modals.php'; ?>

    <!-- CSS -->
    <link rel="stylesheet" href="../views/admindashboardstyle.css">
    <link rel="stylesheet" href="../views/css/modals_styles.css">
    <link rel="stylesheet" href="../views/css/qr_modals_styles.css">
    <link rel="stylesheet" href="../views/css/parking_spaces_style.css">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="dashboard-container">
    <!-- Header -->
    <header class="header">
        <div class="header-logo">
            <h2>
                <img src="../assets/NULogo.png" alt="NUSpace_Logo" class="nu-logo"> NUSpace Admin
            </h2>
        </div>
        <div class="user-profile">
            <i class="fas fa-bell"></i>
            <span>Hi, <?php echo htmlspecialchars($firstname); ?></span>
            <img src="https://i.pravatar.cc/40?u=admin<?php echo $admin_id; ?>" alt="Admin Avatar" class="avatar">
        </div>
    </header>

    <div class="dashboard-body">
        <!-- Sidebar -->
        <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-hidden="true">
        <label for="menu-toggle" class="hamburger-btn" aria-hidden="true">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </label>
        <nav class="sidebar">
            <ul class="nav-menu">
                <li class="nav-item active">
                    <a href="#" data-target="dashboard"><i class="fas fa-th-large"></i><span>Dashboard Overview</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" data-target="parking-spaces"><i class="fas fa-car-alt"></i><span>Manage Parking Spaces</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" data-target="users-management"><i class="fas fa-users"></i><span>Users Management</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" data-target="monitoring"><i class="fas fa-desktop"></i><span>Monitoring & Reports</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" data-target="settings"><i class="fas fa-cog"></i><span>System Settings</span></a>
                </li>

                <!-- qr scanner button -->
                <li class="nav-item">
                    <button id="open-scan-modal-btn" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-qrcode"></i> Scan Vehicle
                    </button>
                </li>

            </ul>
            <div class="log-out">
                <a href="?logout=1" onclick="return confirm('Logout from admin panel?')">
                    <i class="fas fa-sign-out-alt"></i><span>Admin Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Overview -->
            <div id="dashboard" class="content-section active">
                <h3>Featured</h3>
                <p class="section-subtitle">Availability</p>
                <div class="featured-cards">
                    <div class="card">
                        <div class="card-icon"><i class="fas fa-car"></i></div>
                        <div class="card-content">
                            <div class="card-value available">
                                <?php echo $counts['car']; ?>
                            </div>
                            <div class="card-label">Available Spaces</div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon"><i class="fa-solid fa-motorcycle"></i></div>
                        <div class="card-content">
                            <div class="card-value taken">
                                <?php echo $counts['motorcycle']; ?>
                            </div>

                            <div class="card-label">Available Spaces</div>
                        </div>
                    </div>
                </div>

                <section class="map-section">
                    <h3>NU Laguna Campus Map</h3>
                    <div class="map-container">
                        <img src="../assets/nu-map.png" alt="NU Laguna Park Space Map" class="map-image">
                        <div class="parking-slots-list">

                            <!-- Bottom Right: Display Location with Available Spaces count -->
                            <h4><i class="fas fa-list"></i> Parking Areas Distribution</h4>

                            <!-- Filter Dropdowns -->
                            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                <!-- User Type Filter -->
                                <div class="filter-container">
                                    <select id="distribution-type-filter" class="filter-select" 
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
                                    <select id="distribution-vehicle-filter" class="filter-select" 
                                            style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; cursor: pointer; background: white;">
                                        <option value="all">All Vehicles</option>
                                        <option value="car">Car</option>
                                        <option value="motorcycle">Motorcycle</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Distribution List -->
                            <div id="distribution-container">
                                <?php if (!empty($distribution)): ?>
                                    <?php foreach ($distribution as $slot): ?>
                                        <div class="slot-item">
                                            <span class="slot-count"><?php echo $slot['slots']; ?></span>
                                            <span class="slot-name"><?php echo htmlspecialchars($slot['location']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No parking areas found.</p>
                                <?php endif; ?>
                            </div>                      

                        </div>
                    </div>
                </section>
            </div>

            <!-- Parking Spaces -->
            <div id="parking-spaces" class="content-section"> 
                
                <div class="page-header">
                    <h3><i class="fas fa-car-alt"></i> Parking Spaces Management</h3>
                    <div class="header-actions">
                        <button class="btn btn-primary" id="edit-mode-btn"><i class="fas fa-edit"></i> Edit Mode</button>
                        <button id="delete-mode-btn" class="mode-toggle-btn" style="background-color: #dc3545;">Delete Mode</button>
                        <button class="btn btn-success" id="add-space-btn"><i class="fas fa-plus"></i> Add New Space</button>
                    </div>
                </div>

                <!-- Search and Filter Bar -->
                <div class="filter-bar" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: center; flex-wrap: wrap;">
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
                                <option value="car">Car</option>
                                <option value="motorcycle">Motorcycle Only</option>
                            </select>
                        </div>
                        
                        <!-- Count Display -->
                        <div class="count-display">
                            <span id="spaces-count" style="color: #666; font-size: 14px;">Loading...</span>
                        </div>
                    </div>

                <div class="parking-container">
                    <?php
                    require_once __DIR__ . '/../controller/config.php';
                    require_once __DIR__ . '/../models/ParkingSpace.php';

                    $parkingModel = new ParkingSpace($conn);
                    $query = "SELECT * FROM parking_spaces ORDER BY id DESC";
                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()):
                        // Fix image path
                        $imagePath = !empty($row['image_path']) ? '../' . ltrim($row['image_path'], '/.') : '../assets/img/default_parking.jpg';

                        // Get allocations for this parking space
                        $allocations = $parkingModel->getAllocations($row['id']);

                        // Organize allocations by vehicle type and user type
                        $carAlloc = ['General' => 0, 'Faculty' => 0, 'Executive' => 0, 'Guest' => 0];
                        $motoAlloc = ['General' => 0, 'Faculty' => 0, 'Executive' => 0, 'Guest' => 0];
                        $userTypesSet = [];

                        foreach ($allocations as $alloc) {
                            if ((int)$alloc['allocated_slots'] > 0) {
                                $ut = trim($alloc['user_type']);
                                $slots = (int)$alloc['allocated_slots'];
                                
                                // Map user type to simplified name
                                $utKey = 'General';
                                if (stripos($ut, 'faculty') !== false) {
                                    $utKey = 'Faculty';
                                    $userTypesSet['faculty'] = true;
                                } elseif (stripos($ut, 'executive') !== false) {
                                    $utKey = 'Executive';
                                    $userTypesSet['executive'] = true;
                                } elseif (stripos($ut, 'guest') !== false) {
                                    $utKey = 'Guest';
                                    $userTypesSet['guest'] = true;
                                } else {
                                    $userTypesSet['general'] = true;
                                }
                                
                                // Assign to car or motorcycle
                                if ($alloc['vehicle_type_id'] == 1) {
                                    $carAlloc[$utKey] += $slots;
                                } else {
                                    $motoAlloc[$utKey] += $slots;
                                }
                            }
                        }

                        // Calculate totals
                        $carSlots = array_sum($carAlloc);
                        $motorcycleSlots = array_sum($motoAlloc);
                        $totalSlots = $carSlots + $motorcycleSlots;
                        $parkedCount = (int)$row['parked_car_count'] + (int)$row['parked_motorcycle_count'];
                        $available = $totalSlots - $parkedCount;
                        $status = $available > 0 ? 'Available' : 'Full';

                        // Prepare data attributes
                        $userTypes = !empty($userTypesSet) ? implode(',', array_keys($userTypesSet)) : '';
                        $vehicles = [];
                        if ($row['supports_car']) $vehicles[] = 'car';
                        if ($row['supports_motorcycle']) $vehicles[] = 'motorcycle';
                        $vehiclesAttr = implode(',', $vehicles);

                        // Safe output
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

                                <div class="allocations-grid">
                                    <!-- Car Column -->
                                    <?php if ($row['supports_car']): ?>
                                    <div class="alloc-column">
                                        <div class="alloc-title">Cars: <?php echo $carSlots; ?></div>
                                        <?php foreach ($carAlloc as $type => $count): ?>
                                            <div class="alloc-row"><?php echo $type; ?>: <?php echo $count; ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Motorcycle Column -->
                                    <?php if ($row['supports_motorcycle']): ?>
                                    <div class="alloc-column">
                                        <div class="alloc-title">Motors: <?php echo $motorcycleSlots; ?></div>
                                        <?php foreach ($motoAlloc as $type => $count): ?>
                                            <div class="alloc-row"><?php echo $type; ?>: <?php echo $count; ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <p class="occupancy-info">
                                    <strong>Occupancy:</strong>
                                    <?php echo $parkedCount; ?> / <?php echo $totalSlots; ?>
                                    <span class="status-badge <?php echo strtolower($status); ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </p>

                                <button class="btn edit-space-btn edit-btn"
                                    data-id="<?php echo (int)$row['id']; ?>"
                                    data-name="<?php echo $nameSafe; ?>"
                                    data-supports-car="<?php echo (int)$row['supports_car']; ?>"
                                    data-supports-motorcycle="<?php echo (int)$row['supports_motorcycle']; ?>"
                                    data-image="<?php echo $imgSafe; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    ?>
                </div>
            </div>

            <!-- Users Management -->
            <div id="users-management" class="content-section">
                <div class="page-header">
                    <h3><i class="fas fa-users"></i> Users Management</h3>
                </div>
                <div class="search-container">
                    <input type="text" id="user-search" placeholder="Search users by name, email, or type...">
                    <select id="user-type-filter">
                        <option value="">All User Types</option>
                        <?php
                        $typeQuery = "SELECT * FROM user_type_table ORDER BY user_type_id";
                        $typeResult = $conn->query($typeQuery);
                        while($type = $typeResult->fetch_assoc()): ?>
                            <option value="<?php echo $type['user_type_id']; ?>">
                                <?php echo htmlspecialchars($type['user_type']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Users Table -->
                <div class="table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>User Type</th>
                                <th>Registered Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            <?php
                            $query = "SELECT u.*, ut.user_type 
                                    FROM users_table u 
                                    JOIN user_type_table ut ON u.user_type_id = ut.user_type_id 
                                    ORDER BY u.user_id";
                            $result = $conn->query($query);

                            while ($user = $result->fetch_assoc()):
                            ?>
                            <tr data-type-id="<?php echo $user['user_type_id']; ?>">
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>0<?php echo htmlspecialchars($user['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn-edit" data-id="<?php echo $user['user_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" data-id="<?php echo $user['user_id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monitoring -->
            <div id="monitoring" class="content-section">
                <h3><i class="fas fa-desktop"></i> Monitoring & Reports</h3>

                <?php
                $logs = null;
                $error = null;
                
                try {
                    if (!class_exists('ParkingLog')) {
                        require_once __DIR__ . '/../models/ParkingLog.php';
                    }
                    
                    $parkingLog = new ParkingLog($conn);
                    $logs = $parkingLog->getAllLogs();
                    
                    if (!$logs) {
                        throw new Exception("Failed to retrieve logs from database");
                    }
                    
                } catch (Exception $e) {
                    $error = "Error loading logs: " . $e->getMessage();
                    error_log("Monitoring Section Error: " . $e->getMessage());
                }
                ?>

                <div style="max-height: 70vh; overflow-y: auto; overflow-x: auto; background: #fff; padding: 10px; border-radius: 6px;">
                    <?php if ($error): ?>
                        <div style="color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 4px; margin-bottom: 15px;">
                            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($logs && $logs->num_rows > 0): ?>
                        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse; font-size: 14px;">
                            <thead style="background:#f4f4f4; position: sticky; top: 0;">
                                <tr>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Log ID</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Plate Number</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Owner</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">User Type</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Vehicle Type</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Parking Space</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Time In</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Time Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $logs->fetch_assoc()): ?>
                                    <tr>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['log_id']) ?></td>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['plate_number']) ?></td>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['owner_name']) ?></td>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['user_type']) ?></td>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['vehicle_type']) ?></td>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['parking_space_name']) ?></td>
                                        <td style="padding: 8px; border: 1px solid #ddd;">
                                            <span class="status-badge <?= strtolower($row['status']) ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['time_in'] ?? '—') ?></td>
                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($row['time_out'] ?? '—') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 10px; color: #666; font-size: 12px;">
                            Total Records: <?= $logs->num_rows ?>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; padding: 20px; color: #666;">No parking logs found in the system.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Settings -->
            <div id="settings" class="content-section">
                <h3><i class="fas fa-cog"></i> System Settings</h3>
                <p class="placeholder-text">System configuration interface.</p>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS Libraries -->
<script src="https://unpkg.com/html5-qrcode"></script>

<!-- JS -->
<script src="../views/js/helpers.js"></script>
<script src="../views/js/navigation.js"></script>
<script src="../views/js/modals.js"></script>
<script src="../views/js/qr_modals.js"></script>
<script src="../views/js/qr_scanner.js"></script>
<script src="../views/js/forms.js"></script>
<script src="../views/js/imagePreview.js"></script>
<script src="../views/js/refresh.js"></script>
<script src="../views/js/editMode.js"></script>
<script src="../views/js/deleteMode.js"></script>
<script src="../views/js/init.js"></script>
<script src="../views/js/filter.js"></script>
<script src="../views/js/users.js"></script>
<script src="../views/js/user-management.js"></script>

</body>
</html>
