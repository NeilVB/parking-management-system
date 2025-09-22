<?php
// Check admin authentication (controller should handle this, but adding as backup)
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../controller/admin_login.php");
    exit();
}
?>
<!-- views/admin_dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NU Space - Admin Dashboard</title>
	<link rel="stylesheet" href="../views/dashboardstyle.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
	<div class="dashboard-container">
		<header class="header">
			<div class="header-logo">
				<h2><i class="fas fa-car-side"></i> NUSpace Admin</h2>
			</div>
			<div class="user-profile">
				<i class="fas fa-bell"></i>
				<!-- Display admin name from session -->
				<span>Hi, Admin <?php echo htmlspecialchars($firstname); ?></span> 
				<img src="https://i.pravatar.cc/40?u=admin<?php echo $admin_id; ?>" alt="Admin Avatar" class="avatar">
			</div>
		</header>
		<div class="dashboard-body">
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
					<!-- Admin logout with confirmation -->
					<a href="?logout=1" onclick="return confirm('Are you sure you want to logout from admin panel?')">
						<i class="fas fa-sign-out-alt"></i>
						<span>Admin Logout</span>
					</a>
				</div>
			</nav>

			<main class="main-content">
				<!-- Dashboard Overview -->
				<div id="dashboard" class="content-section active">
					<div class="admin-header">
						<h3><i class="fas fa-tachometer-alt"></i> System Overview</h3>
						<div class="last-updated">
							Last updated: <?php echo date('Y-m-d H:i:s'); ?>
						</div>
					</div>
					
					<div class="featured-cards">
						<div class="card">
							<div class="card-icon available"><i class="fas fa-car"></i></div>
							<div class="card-content">
								<div class="card-value available"><?php echo $available_spaces ?? 0; ?></div>
								<div class="card-label">Available Spaces</div>
							</div>
						</div>
						<div class="card">
							<div class="card-icon taken"><i class="fas fa-parking"></i></div>
							<div class="card-content">
								<div class="card-value taken"><?php echo $taken_spaces ?? 0; ?></div>
								<div class="card-label">Occupied Spaces</div>
							</div>
						</div>
						<div class="card">
							<div class="card-icon reserved"><i class="fas fa-calendar-check"></i></div>
							<div class="card-content">
								<div class="card-value reserved"><?php echo $reserved_spaces ?? 0; ?></div>
								<div class="card-label">Reserved Spaces</div>
							</div>
						</div>
						<div class="card">
							<div class="card-icon total"><i class="fas fa-chart-bar"></i></div>
							<div class="card-content">
								<div class="card-value total">216</div>
								<div class="card-label">Total Capacity</div>
							</div>
						</div>
					</div>

					<section class="map-section">
						<h3><i class="fas fa-map-marked-alt"></i> NU Laguna Campus Map</h3>
						<div class="map-container">
							<img src="../assets/nu-map.png" alt="NU Laguna Park Space Map" class="map-image">
							<div class="parking-slots-list">
								<h4>
									<i class="fas fa-list"></i>
									<span>Parking Areas Distribution</span>
								</h4>
								<div class="slot-item">
									<span class="slot-count">78</span>
									<span class="slot-name">Football Field Front</span>
									<span class="slot-status available">Active</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">65</span>
									<span class="slot-name">Football Field Side</span>
									<span class="slot-status available">Active</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">31</span>
									<span class="slot-name">Cafeteria Front</span>
									<span class="slot-status available">Active</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">14</span>
									<span class="slot-name">Gravel Parking</span>
									<span class="slot-status available">Active</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">14</span>
									<span class="slot-name">Sampalok Lane</span>
									<span class="slot-status available">Active</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">14</span>
									<span class="slot-name">Outside Parking</span>
									<span class="slot-status available">Active</span>
								</div>
							</div>
						</div>
					</section>
				</div>

				<!-- Parking Spaces Management -->
				<div id="parking-spaces" class="content-section">
					<div class="page-header">
						<h3><i class="fas fa-car-alt"></i> Parking Spaces Management</h3>
						<div class="header-actions">
							<button class="btn btn-primary" id="edit-mode-btn">
								<i class="fas fa-edit"></i> Edit Mode
							</button>
							<button class="btn btn-success" id="add-space-btn">
								<i class="fas fa-plus"></i> Add New Space
							</button>
							<div class="dropdown">
								<button class="btn btn-secondary dropdown-toggle">
									<i class="fas fa-filter"></i> Filter: All Spaces
									<i class="fas fa-chevron-down"></i>
								</button>
							</div>
						</div>
					</div>
					<div class="parking-grid">
						<!-- Parking space cards will be dynamically inserted here -->
						<div class="add-space-card" id="quick-add-card">
							<div class="add-space-content">
								<i class="fas fa-plus-circle"></i>
								<span>Quick Add Parking Space</span>
								<small>Click to add new parking area</small>
							</div>
						</div>
					</div>
				</div>

				<!-- Users Management -->
				<div id="users-management" class="content-section">
					<div class="page-header">
						<h3><i class="fas fa-users"></i> Users Management</h3>
						<div class="header-actions">
							<button class="btn btn-primary">
								<i class="fas fa-user-plus"></i> Add User
							</button>
							<button class="btn btn-secondary">
								<i class="fas fa-download"></i> Export Users
							</button>
						</div>
					</div>

					<!-- Admin info display -->
					<div class="admin-info-card">
						<h4><i class="fas fa-user-shield"></i> Current Admin Session</h4>
						<div class="admin-details">
							<p><strong>Name:</strong> <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></p>
							<p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
							<p><strong>Role:</strong> <?php echo htmlspecialchars($user_type); ?></p>
							<p><strong>Admin ID:</strong> #<?php echo htmlspecialchars($admin_id); ?></p>
							<p><strong>Session Started:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
						</div>
					</div>

					<!-- Users table placeholder -->
					<div class="users-table-container">
						<h4><i class="fas fa-table"></i> Registered Users</h4>
						<p class="placeholder-text">User management interface will be implemented here.</p>
					</div>
				</div>

				<!-- Reservations Management -->
				<div id="reservations" class="content-section">
					<div class="page-header">
						<h3><i class="fas fa-calendar-alt"></i> Reservations Management</h3>
						<div class="header-actions">
							<button class="btn btn-primary">
								<i class="fas fa-calendar-plus"></i> New Reservation
							</button>
							<button class="btn btn-secondary">
								<i class="fas fa-file-export"></i> Export Report
							</button>
						</div>
					</div>
					<div class="reservations-content">
						<p class="placeholder-text">Reservations management interface will be implemented here.</p>
					</div>
				</div>

				<!-- Monitoring and Reports -->
				<div id="monitoring" class="content-section">
					<div class="page-header">
						<h3><i class="fas fa-desktop"></i> Monitoring & Reports</h3>
						<div class="header-actions">
							<button class="btn btn-primary">
								<i class="fas fa-chart-line"></i> Generate Report
							</button>
						</div>
					</div>
					<div class="monitoring-content">
						<p class="placeholder-text">System monitoring and reports interface will be implemented here.</p>
					</div>
				</div>

				<!-- System Settings -->
				<div id="settings" class="content-section">
					<div class="page-header">
						<h3><i class="fas fa-cog"></i> System Settings</h3>
					</div>
					<div class="settings-content">
						<p class="placeholder-text">System configuration and settings interface will be implemented here.</p>
					</div>
				</div>
			</main>
		</div>
	</div>

	<!-- Add Parking Space Modal -->
	<div id="add-space-modal" class="modal">
		<div class="modal-content">
			<div class="modal-header">
				<h3><i class="fas fa-plus-circle"></i> Add New Parking Space</h3>
				<span class="close-btn">&times;</span>
			</div>
			<form id="add-space-form">
				<div class="form-group">
					<label for="new-space-name">
						<i class="fas fa-tag"></i> Parking Space Name
					</label>
					<input type="text" id="new-space-name" name="name" required placeholder="e.g., Main Building Front">
				</div>
				<div class="form-group">
					<label for="new-space-max">
						<i class="fas fa-hashtag"></i> Maximum Slots
					</label>
					<input type="number" id="new-space-max" name="max_space" required min="1" placeholder="Enter number of slots">
				</div>
				<div class="form-group">
					<label for="new-space-type">
						<i class="fas fa-list"></i> Space Type
					</label>
					<select id="new-space-type" name="space_type">
						<option value="general">General Parking</option>
						<option value="faculty">Faculty Only</option>
						<option value="student">Student Only</option>
						<option value="visitor">Visitor Parking</option>
					</select>
				</div>
				<div class="modal-actions">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-save"></i> Add Space
					</button>
					<button type="button" class="btn btn-secondary close-modal">
						<i class="fas fa-times"></i> Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Edit Parking Space Modal -->
	<div id="edit-space-modal" class="modal">
		<div class="modal-content">
			<div class="modal-header">
				<h3><i class="fas fa-edit"></i> Edit Parking Space</h3>
				<span class="close-btn">&times;</span>
			</div>
			<form id="edit-space-form">
				<input type="hidden" id="edit-space-id" name="id">
				<div class="form-group">
					<label for="edit-space-name">
						<i class="fas fa-tag"></i> Parking Space Name
					</label>
					<input type="text" id="edit-space-name" name="name" required>
				</div>
				<div class="form-group">
					<label for="edit-space-max">
						<i class="fas fa-hashtag"></i> Maximum Slots
					</label>
					<input type="number" id="edit-space-max" name="max_space" required min="1">
				</div>
				<div class="form-group">
					<label for="edit-space-type">
						<i class="fas fa-list"></i> Space Type
					</label>
					<select id="edit-space-type" name="space_type">
						<option value="general">General Parking</option>
						<option value="faculty">Faculty Only</option>
						<option value="student">Student Only</option>
						<option value="visitor">Visitor Parking</option>
					</select>
				</div>
				<div class="modal-actions">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-save"></i> Save Changes
					</button>
					<button type="button" class="btn btn-danger" id="delete-space-btn">
						<i class="fas fa-trash"></i> Delete
					</button>
					<button type="button" class="btn btn-secondary close-modal">
						<i class="fas fa-times"></i> Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

	<script src="../js/admin_dashboard.js"></script>
</body>
</html>