<?php
// session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../controller/login.php");
    exit();
}

// Optional: fetch session variables for display
$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['firstname'] ?? 'User';

require_once __DIR__ . '/../controller/config.php';
require_once __DIR__ . '/../models/ParkingSpace.php';

$model = new ParkingSpace($conn);
$counts = $model->getVehicleCounts();
$distribution = $model->getSlotDistribution();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Space - Dashboard</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../views/dashboardstyle.css">
    <link rel="stylesheet" href="../views/css/parking_spaces_style.css">
    <link rel="stylesheet" href="../views/mobile_dashboardstyle.css">

    <!-- JS -->
    <script src="../views/js/helpers.js"></script>
    <script src="../views/js/navigation.js"></script>
    <script src="../views/js/modals.js"></script>
    <script src="../views/js/forms.js"></script>
    <script src="../views/js/imagePreview.js"></script>
    <script src="../views/js/refresh.js"></script>
    <script src="../views/js/editMode.js"></script>
    <script src="../views/js/filter.js"></script>
    <script src="../views/js/init.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <header class="header">
            <div class="header-logo">
                <h2><img src="../assets/NULogo.png" alt="NUSpace_Logo" class="nu-logo"> NUSpace</h2>
            </div>
            <div class="user-profile">
                <i class="fas fa-bell"></i>
                <a href="account.php" class="user-profile-link">
                    <span>Hi, <?php echo htmlspecialchars($firstname); ?></span>
                    <img src="https://i.pravatar.cc/150" alt="User Avatar" class="avatar">
                </a>
            </div>
        </header>
        <div class="dashboard-body">
            <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-hidden="true">
            <label for="menu-toggle" class="hamburger-btn" aria-hidden="true">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </label>
            <nav class="sidebar">
                <ul class="nav-menu">
                    <li class="nav-item active">
                        <a href="#" data-target="dashboard"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" data-target="parking-spaces"><i class="fas fa-car-alt"></i><span>Parking
                                Spaces</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" data-target="vehicle-registration"><i
                                class="fas fa-solid fa-briefcase"></i><span>Vehicle Registration</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="account.php"><i class="fas fa-user"></i><span>Account</span></a>
                    </li>
                </ul>
                <div class="log-out">
                    <!-- Admin logout with confirmation -->
                    <a href="?logout=1" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>

            <main class="main-content">
                <!-- Dashboard Content -->
                <div id="dashboard" class="content-section active">
                    <h3>Featured</h3>
                    <p class="section-subtitle">Availability</p>
                    <div class="featured-cards">
                        <div class="card <?php echo ($counts['car'] > 0) ? 'available' : 'full'; ?>">
                            <div class="card-icon"><i class="fas fa-car"></i></div>
                            <div class="card-content">
                                <div class="card-value">
                                    <?php echo $counts['car']; ?>
                                </div>
                                <div class="card-label">Available Spaces</div>
                            </div>
                        </div>
                        <div class="card <?php echo ($counts['motorcycle'] > 0) ? 'available' : 'full'; ?>">
                            <div class="card-icon"><i class="fa-solid fa-motorcycle"></i></div>
                            <div class="card-content">
                                <div class="card-value">
                                    <?php echo $counts['motorcycle']; ?>
                                </div>
                                <div class="card-label">Available Spaces</div>
                            </div>
                        </div>
                    </div>

                    <div class="availability-legend">
                        <div class="legend-item">
                            <i class="fa-solid fa-square-full"></i>
                            <span class="legend-text">Parking Available</span>
                        </div>
                        <div class="legend-item">
                            <i class="fa-solid fa-square-full"></i>
                            <span class="legend-text">No Spaces Left</span>
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
                                <div class="distribution-filter-bar">
                                    <!-- User Type Filter -->
                                    <div class="filter-container">
                                        <select id="distribution-type-filter" class="filter-select">
                                            <option value="all">All Types</option>
                                            <option value="general">General</option>
                                            <option value="faculty">Faculty</option>
                                            <option value="executive">Executive</option>
                                            <option value="guest">Guest</option>
                                        </select>
                                    </div>

                                    <!-- Vehicle Type Filter -->
                                    <div class="filter-container">
                                        <select id="distribution-vehicle-filter" class="filter-select">
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
                                                <span
                                                    class="slot-name"><?php echo htmlspecialchars($slot['location']); ?></span>
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
                    </div>

                    <!-- Search and Filter Bar -->
                    <div class="Search-Filter-Bar">
                        <!-- Search Input -->
                        <div class="search-container">
                            <input type="text" id="space-search" placeholder="Search parking spaces...">
                        </div>

                        <!-- Filter Dropdown -->
                        <div class="filter-container">
                            <select id="type-filter" class="filter-select">
                                <option value="all">All Types</option>
                                <option value="general">General</option>
                                <option value="faculty">Faculty</option>
                                <option value="executive">Executive</option>
                                <option value="guest">Guest</option>
                            </select>
                        </div>

                        <!-- Vehicle Type Filter -->
                        <div class="filter-container">
                            <select id="vehicle-filter" class="filter-select">
                                <option value="all">All Vehicles</option>
                                <option value="car">Car</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>

                        <!-- Count Display -->
                        <div class="count-display">
                            <span id="spaces-count">Loading...</span>
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
                                if ((int) $alloc['allocated_slots'] > 0) {
                                    $ut = trim($alloc['user_type']);
                                    $slots = (int) $alloc['allocated_slots'];

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
                            $parkedCount = (int) $row['parked_car_count'] + (int) $row['parked_motorcycle_count'];
                            $available = $totalSlots - $parkedCount;
                            $status = $available > 0 ? 'Available' : 'Full';

                            // Prepare data attributes
                            $userTypes = !empty($userTypesSet) ? implode(',', array_keys($userTypesSet)) : '';
                            $vehicles = [];
                            if ($row['supports_car'])
                                $vehicles[] = 'car';
                            if ($row['supports_motorcycle'])
                                $vehicles[] = 'motorcycle';
                            $vehiclesAttr = implode(',', $vehicles);

                            // Safe output
                            $imgSafe = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
                            $nameSafe = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="parking-card" data-id="<?php echo (int) $row['id']; ?>"
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

                                    <button class="btn edit-space-btn edit-btn" data-id="<?php echo (int) $row['id']; ?>"
                                        data-name="<?php echo $nameSafe; ?>"
                                        data-supports-car="<?php echo (int) $row['supports_car']; ?>"
                                        data-supports-motorcycle="<?php echo (int) $row['supports_motorcycle']; ?>"
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

                <div id="vehicle-registration" class="content-section">
                    <h3>Vehicle Registration</h3>
                    <p class="section-subtitle">Register your vehicle to get a parking QR code</p>
                    <div class="inputsubcontainer formcontainer">
                        <?php if (!empty($error_message)): ?>
                            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>
                        <form action="../controller/register_vehicle.php" method="POST">
                            <div class="input-group">
                                <label for="plate_number">Plate Number</label>
                                <input type="text" id="plate_number" name="plate_number" required placeholder="ABC-1234"
                                    value="<?php echo htmlspecialchars($form_data['plate_number'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="vehicle_type_id">Vehicle Type</label>
                                <select id="vehicle_type_id" name="vehicle_type_id" required>
                                    <option value="" disabled <?php echo empty($form_data['vehicle_type_id']) ? 'selected' : ''; ?>>Select Type</option>
                                    <option value="1" <?php echo (isset($form_data['vehicle_type_id']) && $form_data['vehicle_type_id'] == 1) ? 'selected' : ''; ?>>Car</option>
                                    <option value="2" <?php echo (isset($form_data['vehicle_type_id']) && $form_data['vehicle_type_id'] == 2) ? 'selected' : ''; ?>>Motorcycle</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="color">Color</label>
                                <input type="text" id="color" name="color" required placeholder="Red"
                                    value="<?php echo htmlspecialchars($form_data['color'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="make">Make</label>
                                <input type="text" id="make" name="make" required placeholder="Toyota"
                                    value="<?php echo htmlspecialchars($form_data['make'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="model">Model</label>
                                <input type="text" id="model" name="model" required placeholder="Corolla"
                                    value="<?php echo htmlspecialchars($form_data['model'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="year">Year</label>
                                <input type="number" id="year" name="year" required placeholder="2020" min="1900"
                                    max="2099" value="<?php echo htmlspecialchars($form_data['year'] ?? ''); ?>">
                            </div>
                            <button type="submit" class="register-button">Register Vehicle</button>
                        </form>
                    </div>
                </div>

                <!-- Terms and Conditions Content -->
                <div id="terms-container" class="content-section">
                    <h3>Parking Terms and Conditions</h3>
                    <p class="last-updated">Last Updated: September 2025</p>
                    <p>By registering your vehicle and using the University Parking Management System (PMS), you agree
                        to the following terms and conditions:</p>

                    <ol class="terms-list">
                        <li>
                            <strong>Registration Requirements</strong>
                            <ul>
                                <li>You must provide accurate details, including your plate number, University ID, and
                                    active phone number.</li>
                                <li>Your phone number may be used for emergency purposes or to contact you if issues
                                    arise
                                    (e.g., incorrect parking, overstaying, blocking another vehicle).</li>
                            </ul>
                        </li>
                        <li>
                            <strong>Guard Verification</strong>
                            <ul>
                                <li>Security guards are authorized to check plate numbers and scan QR/barcodes.</li>
                                <li>You must comply with guard instructions at all times.</li>
                            </ul>
                        </li>
                        <li>
                            <strong>Proper Use of Parking Spaces</strong>
                            <ul>
                                <li>Only registered vehicles are allowed to park.</li>
                                <li>You must park in the assigned area (student or staff) based on your status.</li>
                                <li>Overstaying, double-parking, or parking without notice/permission is prohibited.
                                </li>
                            </ul>
                        </li>
                        <li>
                            <strong>Violations & Consequences</strong>
                            <ul>
                                <li><strong>Minor Violations:</strong> Causing delays for other drivers, or incomplete
                                    registration may result in warnings.</li>
                                <li><strong>Major Violations (Troll Parking):</strong> Parking without notifying a
                                    guard,
                                    parking in the wrong area, using fake details, or repeated violations will result in
                                    disciplinary action or loss of parking privileges.</li>
                            </ul>
                        </li>
                        <li>
                            <strong>Liability & Responsibility</strong>
                            <ul>
                                <li>The University is not liable for loss, theft, or damage to vehicles while inside the
                                    premises.</li>
                                <li>Users are responsible for their own vehicles and must ensure they are parked safely.
                                </li>
                            </ul>
                        </li>
                        <li>
                            <strong>Agreement</strong>
                            <ul>
                                <li>By proceeding with registration, you confirm that you have read, understood, and
                                    agree
                                    to abide by these terms and conditions.</li>
                            </ul>
                        </li>
                    </ol>

                    <div class="terms-footer">
                        <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
                    </div>
                </div>

                <!-- Policy and Regulations Content -->
                <div id="policy-container" class="content-section">
                    <h3>Policy and Regulations</h3>
                    <p class="last-updated">Last Updated: September 2025</p>

                    <div class="policy-section">
                        <h4>A. General Policy</h4>
                        <p>The University provides regulated parking facilities for students, faculty, and staff. To
                            ensure safety, fairness, and orderliness, all members of the University community who wish
                            to avail of parking privileges must comply with the following policies and regulations.</p>
                    </div>

                    <div class="policy-section">
                        <h4>B. Registration and Access</h4>
                        <ul>
                            <li>All vehicle owners must register their vehicle(s) through the University Parking
                                Management System (PMS).</li>
                            <li>Registration requires submission of accurate information, including name, plate number,
                                college/department, and emergency contact number.</li>
                            <li>Each registered user shall be issued a unique QR code which must be presented to the
                                gate guard upon entry.</li>
                            <li>Parking privileges are non-transferable. Lending or falsifying a parking QR code is
                                subject to disciplinary action.</li>
                        </ul>
                    </div>

                    <div class="policy-section">
                        <h4>C. Role of Gate Guards</h4>
                        <ul>
                            <li>Gate guards shall verify the driver's identity and QR code.</li>
                            <li>Guards will assign parking areas (student, faculty, staff, visitors) based on
                                availability.</li>
                            <li>Guards may issue reminders, warnings, or report violations directly to the Discipline
                                Office (DO).</li>
                        </ul>
                    </div>

                    <div class="policy-section">
                        <h4>D. Parking Rules and Regulations</h4>
                        <ul>
                            <li>Vehicles must be parked only in the designated area assigned at entry.</li>
                            <li>Unauthorized use of faculty/staff parking areas by students, or vice versa, is a
                                violation.</li>
                            <li>Waiting in parking areas is prohibited, a waiting zone is provided outside the field.
                            </li>
                            <li>Vehicles must be properly aligned and must not obstruct other vehicles or pedestrian
                                pathways.</li>
                            <li>Users must comply with all guard instructions at all times.</li>
                        </ul>
                    </div>

                    <div class="policy-section">
                        <h4>E. Violations and Consequences</h4>
                        <div class="violation-type">
                            <h5>Minor Violations (first/occasional offenses):</h5>
                            <ul>
                                <li>Improper parking, failure to follow guard instructions.</li>
                                <li><strong>Sanction:</strong> Warning or violation slip.</li>
                            </ul>
                        </div>

                        <div class="violation-type">
                            <h5>Major Violations (habitual or deliberate misconduct):</h5>
                            <ul>
                                <li>"Troll parking" (parking without notifying the guard, blocking other cars, repeated
                                    abuse of the system).</li>
                                <li>Using falsified or borrowed QR codes.</li>
                                <li>Parking in restricted or reserved zones.</li>
                                <li><strong>Sanction:</strong> Suspension of parking privileges, disciplinary referral
                                    to DO, and possible fines for damages.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="policy-section">
                        <h4>F. Emergency and Accountability</h4>
                        <ul>
                            <li>In case of emergencies, the University may contact the registered phone number of the
                                vehicle owner.</li>
                            <li>The University is not liable for loss, theft, or damage to vehicles or personal property
                                within the parking premises.</li>
                        </ul>
                    </div>

                    <div class="policy-section">
                        <h4>G. Acceptance of Terms</h4>
                        <p>By registering a vehicle and using the University's parking facilities, the student/faculty
                            member agrees to abide by these policies and regulations and acknowledges the authority of
                            the University to impose sanctions for violations.</p>
                    </div>

                    <!-- Reuse the terms-footer button -->
                    <div class="terms-footer">
                        <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="footer">
                    <div class="footer-content">
                        <div class="copyright">
                            <p>NUSpace &copy; 2025</p>
                        </div>
                        <div class="footer-links">
                            <a href="#" data-target="terms-container">Terms and Conditions</a>
                            <a href="#" data-target="policy-container">Policy and Regulations</a>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>
</body>

</html>