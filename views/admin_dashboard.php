<?php
// views/admin_dashboard.php
// Start session and ensure admin is authenticated
// session_start(); // Uncomment if not already started elsewhere
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

    <!-- CSS -->
    <link rel="stylesheet" href="../views/admindashboardstyle.css">
    <link rel="stylesheet" href="../views/css/modals_styles.css">
    <link rel="stylesheet" href="../views/css/parking_spaces_style.css">

    <!-- JS Libraries -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <!-- JS -->
    <script src="../views/js/qr_scanner.js"></script>
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
                            <option value="car">Car Only</option>
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

            <!-- Users Management -->
            <div id="users-management" class="content-section">
                <div class="page-header">
                    <h3><i class="fas fa-users"></i> Users Management</h3>
                </div>
                <div class="admin-info-card">
                    <h4><i class="fas fa-user-shield"></i> Current Admin Session</h4>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Role:</strong> <?php echo htmlspecialchars($user_type); ?></p>
                    <p><strong>Admin ID:</strong> #<?php echo htmlspecialchars($admin_id); ?></p>
                    <p><strong>Session Started:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>

            <!-- Monitoring -->
            <div id="monitoring" class="content-section">
                <h3><i class="fas fa-desktop"></i> Monitoring & Reports</h3>
                <p class="placeholder-text">Monitoring tools will be added here.</p>
            </div>

            <!-- Settings -->
            <div id="settings" class="content-section">
                <h3><i class="fas fa-cog"></i> System Settings</h3>
                <p class="placeholder-text">System configuration interface.</p>
            </div>

            <!-- QR Code Scanner Section -->
            <div id="qr-scanner" class="content-section" style="display:none;">
                <div class="page-header">
                    <h3><i class="fas fa-qrcode"></i> QR Code Scanner</h3>
                </div>

                <div class="scanner-container">
                    <video id="qr-video" style="width: 100%; max-width: 500px; border-radius: 10px; border: 2px solid #ccc;"></video>

                    <div id="scan-result" class="result-box" style="margin-top: 15px;"></div>

                    <button id="start-scan" class="btn btn-success mt-3"><i class="fas fa-play"></i> Start Scan</button>
                    <button id="stop-scan" class="btn btn-danger mt-3" style="display:none;"><i class="fas fa-stop"></i> Stop Scan</button>
                </div>

                <div id="vehicle-info" style="margin-top: 25px; display:none;">
                    <h4>Vehicle Information</h4>
                    <div id="vehicle-details" class="info-box"></div>

                    <div class="action-buttons mt-3">
                        <button class="btn btn-primary" id="btn-entered">Mark Entered</button>
                        <button class="btn btn-warning" id="btn-exiting">Mark Exiting</button>
                        <button class="btn btn-secondary" id="btn-outside">Mark Outside</button>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- =============================
     ADD PARKING SPACE MODAL
============================= -->
<div id="add-space-modal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content add-space-modal">
        <div class="modal-header">
            <h2>Add Parking Space</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <form id="add-space-form" enctype="multipart/form-data">
            <!-- Space Name -->
            <div class="form-section">
                <label for="add-space-name">Parking Space Name</label>
                <input type="text" id="add-space-name" name="name" placeholder="Enter parking space name" required>
            </div>

            <!-- Vehicle Types -->
            <div class="form-section">
                <label>Select Vehicle Types</label>
                <div class="vehicle-checkboxes">
                    <label><input type="checkbox" id="add-supports-car" name="supports_car" value="1"> Car</label>
                    <label><input type="checkbox" id="add-supports-motorcycle" name="supports_motorcycle" value="1"> Motorcycle</label>
                </div>
            </div>

            <!-- Allocations -->
            <div id="add-allocations-container" class="slot-config" style="display:none;">
                <!-- Car Allocations -->
                <div id="add-car-allocations" class="slot-section" style="display:none;">
                    <h3>Car Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="add-car-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="add-car-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="add-car-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="add-car-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Car Slots: <span id="add-car-total">0</span></div>
                </div>

                <!-- Motorcycle Allocations -->
                <div id="add-motorcycle-allocations" class="slot-section" style="display:none;">
                    <h3>Motorcycle Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="add-motorcycle-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="add-motorcycle-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="add-motorcycle-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="add-motorcycle-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Motorcycle Slots: <span id="add-motorcycle-total">0</span></div>
                </div>
            </div>

            <!-- Upload Image -->
            <div class="form-section upload-section">
                <label for="add-space-image">Upload Parking Image</label>
                <input type="file" id="add-space-image" name="image" accept="image/*">
                <img id="add-space-preview" alt="Image preview" style="max-width:200px; display:none; margin-top:10px;">
            </div>

            <!-- Buttons -->
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Parking Space</button>
            </div>
        </form>
    </div>
</div>


<!-- =============================
     EDIT PARKING SPACE MODAL
============================= -->
<div id="edit-space-modal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content edit-space-modal">
        <div class="modal-header">
            <h2>Edit Parking Space</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <form id="edit-space-form" enctype="multipart/form-data">
            <input type="hidden" id="edit-space-id" name="id">
            <input type="hidden" id="edit-current-image" name="current_image">

            <!-- Space Name -->
            <div class="form-section">
                <label for="edit-space-name">Parking Space Name</label>
                <input type="text" id="edit-space-name" name="name" required>
            </div>

            <!-- Vehicle Types -->
            <div class="form-section">
                <label>Supported Vehicle Types</label>
                <div class="vehicle-checkboxes">
                    <label><input type="checkbox" id="edit-supports-car" name="supports_car" value="1"> Car</label>
                    <label><input type="checkbox" id="edit-supports-motorcycle" name="supports_motorcycle" value="1"> Motorcycle</label>
                </div>
            </div>

            <!-- Allocations -->
            <div id="edit-allocations-container" class="slot-config" style="display:none;">
                <!-- Car Allocations -->
                <div id="edit-car-allocations" class="slot-section" style="display:none;">
                    <h3>Car Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="edit-car-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="edit-car-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="edit-car-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="edit-car-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Car Slots: <span id="edit-car-total">0</span></div>
                </div>

                <!-- Motorcycle Allocations -->
                <div id="edit-motorcycle-allocations" class="slot-section" style="display:none;">
                    <h3>Motorcycle Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="edit-motorcycle-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="edit-motorcycle-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="edit-motorcycle-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="edit-motorcycle-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Motorcycle Slots: <span id="edit-motorcycle-total">0</span></div>
                </div>
            </div>

            <!-- Upload Image -->
            <div class="form-section upload-section">
                <label for="edit-space-image">Update Image (Optional)</label>
                <input type="file" id="edit-space-image" name="image" accept="image/*">
                <img id="edit-space-preview" alt="Image preview" style="max-width:200px; margin-top:10px; display:none;">
            </div>

            <!-- Buttons -->
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" id="delete-space-btn" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- QR Scanner Modal -->
<div id="scan-vehicle-modal" class="modal-overlay">
    <div class="modal-content qr-modal">
        <span class="close-btn" onclick="closeScanModal()">&times;</span>
        <h2>Scan Vehicle QR Code</h2>

        <div id="qr-video-container" style="display: flex; justify-content: center; align-items: center;">

        </div>

        <div class="scan-controls" style="margin-top: 10px;">
            <button id="start-scan-btn" class="btn btn-primary">Start Scan</button>
            <button id="stop-scan-btn" class="btn btn-secondary" style="display:none;">Stop Scan</button>
        </div>

        <div id="scan-result" style="display:none; margin-top: 15px;">
            <p id="result-text">Processing...</p>
        </div>
    </div>
</div>


</body>
</html>
