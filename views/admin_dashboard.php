<?php
// Ensure admin is authenticated
// session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../controller/admin_login.php");
    exit();
}

// Admin details from session
$admin_id   = $_SESSION['admin_id'];
$firstname  = $_SESSION['firstname'] ?? 'Admin';
$lastname   = $_SESSION['lastname'] ?? '';
$email      = $_SESSION['email'] ?? '';
$user_type  = $_SESSION['user_type'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Space - Admin Dashboard</title>
    <link rel="stylesheet" href="../views/admindashboardstyle.css">
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
                    <a href="#" data-target="reservations"><i class="fas fa-calendar-alt"></i><span>Reservations</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" data-target="monitoring"><i class="fas fa-desktop"></i><span>Monitoring & Reports</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" data-target="settings"><i class="fas fa-cog"></i><span>System Settings</span></a>
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
                            <div class="card-value available">0</div>
                            <div class="card-label">Available Spaces</div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon"><i class="fa-solid fa-motorcycle"></i></div>
                        <div class="card-content">
                            <div class="card-value taken">0</div>
                            <div class="card-label">Taken Spaces</div>
                        </div>
                    </div>
                </div>

                <section class="map-section">
                    <h3>NU Laguna Campus Map</h3>
                    <div class="map-container">
                        <img src="../assets/nu-map.png" alt="NU Laguna Park Space Map" class="map-image">
                        <div class="parking-slots-list">
                            <h4><i class="fas fa-list"></i> Parking Areas Distribution</h4>
                            <div class="slot-item"><span class="slot-count">78</span><span class="slot-name">Football Field Front</span><span class="slot-status available">Active</span></div>
                            <div class="slot-item"><span class="slot-count">65</span><span class="slot-name">Football Field Side</span><span class="slot-status available">Active</span></div>
                            <div class="slot-item"><span class="slot-count">31</span><span class="slot-name">Cafeteria Front</span><span class="slot-status available">Active</span></div>
                            <div class="slot-item"><span class="slot-count">14</span><span class="slot-name">Gravel Parking</span><span class="slot-status available">Active</span></div>
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
                            <option value="all">🔍 All Types</option>
                            <option value="general">🚗 General</option>
                            <option value="faculty">👨‍🏫 Faculty</option>
                            <option value="executive">💼 Executive</option>
                            <option value="guest">👤 Guest</option>
                        </select>
                    </div>
                    
                    <!-- Count Display -->
                    <div class="count-display">
                        <span id="spaces-count" style="color: #666; font-size: 14px;">Loading...</span>
                    </div>
                </div>

                <div class="parking-container">
                    <!-- Quick Add Card -->
                    <div class="parking-card add-card" id="quick-add-card">
                        <div class="overlay">
                            <h3>Quick Add Parking Space</h3>
                            <p>Click to add new parking area</p>
                            <button class="btn">+ Add</button>
                        </div>
                    </div>

                    <?php
                    require_once __DIR__ . '/../controller/config.php';
                    $query = "SELECT * FROM parking_spaces";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()): 
                        // Fix image path - prepend '../' to make it relative to the current location
                        $imagePath = !empty($row['image_path']) ? '../' . $row['image_path'] : '../assets/img/default_parking.jpg';
                    ?>
                        <div class="parking-card"
                             style="background-image: url('<?php echo $imagePath; ?>');">
                            <div class="overlay">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p>Type: <?php echo ucfirst($row['space_type']); ?></p>
                                <p>Slots: 0/<?php echo $row['max_space']; ?></p>
                                <button class="btn edit-space-btn edit-btn"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                        data-type="<?php echo $row['space_type']; ?>"
                                        data-max="<?php echo $row['max_space']; ?>"
                                        data-image="<?php echo $imagePath; ?>">
                                    Edit
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
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

            <!-- Reservations -->
            <div id="reservations" class="content-section">
                <h3><i class="fas fa-calendar-alt"></i> Reservations Management</h3>
                <p class="placeholder-text">Reservations module coming soon.</p>
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
        </main>
    </div>
</div>

<!-- Add Space Modal -->
<div id="add-space-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Parking Space</h3>
            <span class="close-btn">&times;</span>
        </div>
        <form id="add-space-form" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Name</label>
                <input type="text" name="name" required placeholder="e.g., Main Building Front">
            </div>
            <div class="form-group">
                <label><i class="fas fa-hashtag"></i> Maximum Slots</label>
                <input type="number" name="max_space" required min="1">
            </div>
            <div class="form-group">
                <label><i class="fas fa-list"></i> Space Type</label>
                <select name="space_type">
                    <option value="general">General</option>
                    <option value="faculty">Faculty</option>
                    <option value="executive">Executive</option>
                    <option value="guest">Guest</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-image"></i> Upload Image</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Space Modal -->
<div id="edit-space-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Parking Space</h3>
            <span class="close-btn">&times;</span>
        </div>
        <form id="edit-space-form" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit-space-id">
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Name</label>
                <input type="text" name="name" id="edit-space-name" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-hashtag"></i> Maximum Slots</label>
                <input type="number" name="max_space" id="edit-space-max" required min="1">
            </div>
            <div class="form-group">
                <label><i class="fas fa-list"></i> Space Type</label>
                <select name="space_type" id="edit-space-type">
                    <option value="general">General</option>
                    <option value="faculty">Faculty</option>
                    <option value="executive">Executive</option>
                    <option value="guest">Guest</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-image"></i> Update Image</label>
                <input type="file" name="image" id="edit-space-image" accept="image/*">
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" id="delete-space-btn">Delete</button>
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="../views/admin_dashboard.js"></script>
</body>
</html>
