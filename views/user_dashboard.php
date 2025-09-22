<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../controller/login.php");
    exit();
}

// Optional: fetch session variables for display
$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['firstname'] ?? 'User'; 
?>
<!-- views/dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NU Space - Dashboard</title>
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
				<h2><i class="fas fa-car-side"></i> NUSpace</h2>
			</div>
			<div class="user-profile">
				<i class="fas fa-bell"></i>
				<!-- ✅ Display actual user name from session -->
				<span>Hi, <?php echo htmlspecialchars($firstname); ?></span> 
				<img src="https://i.pravatar.cc/40?u=<?php echo $user_id; ?>" alt="User Avatar" class="avatar">
			</div>
		</header>
		<div class="dashboard-body">
			<nav class="sidebar">
				<ul class="nav-menu">
					<li class="nav-item active">
						<a href="#" data-target="dashboard"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
					</li>
					<li class="nav-item">
						<a href="#" data-target="parking-spaces"><i class="fas fa-car-alt"></i><span>Parking Spaces</span></a>
					</li>
					<li class="nav-item">
						<a href="#" data-target="users-management"><i class="fas fa-user"></i><span>Users Management</span></a>
					</li>
					<li class="nav-item">
						<a href="#" data-target="monitoring"><i class="fas fa-desktop"></i><span>Monitoring and Enforcement</span></a>
					</li>
				</ul>
				<div class="log-out">
					<!-- ✅ Fixed logout: point to controller/logout.php -->
					<a href="../controller/logout.php" onclick="return confirm('Are you sure you want to logout?')">
						<i class="fas fa-sign-out-alt"></i>
						<span>Log Out</span>
					</a>
				</div>
			</nav>

			<main class="main-content">
				<!-- Dashboard Content -->
				<div id="dashboard" class="content-section active">
					<h3>Featured</h3>
					<div class="featured-cards">
						<div class="card">
							<div class="card-icon"><i class="fas fa-car"></i></div>
							<div class="card-content">
								<div class="card-value available">0</div>
								<div class="card-label">Available Spaces</div>
							</div>
						</div>
						<div class="card">
							<div class="card-icon"><i class="fas fa-parking"></i></div>
							<div class="card-content">
								<div class="card-value taken">0</div>
								<div class="card-label">Taken Spaces</div>
							</div>
						</div>
						<div class="card">
							<div class="card-icon"><i class="fas fa-calendar-check"></i></div>
							<div class="card-content">
								<div class="card-value reserved">0</div>
								<div class="card-label">Reserved Spaces</div>
							</div>
						</div>
					</div>

					<section class="map-section">
						<h3>NU Laguna Park Space</h3>
						<div class="map-container">
							<img src="../assets/nu-map.png" alt="NU Laguna Park Space Map" class="map-image">
							<div class="parking-slots-list">
								<h4>
									<i class="fas fa-car"></i>
									<span>Total Parking Slots</span>
								</h4>
								<div class="slot-item">
									<span class="slot-count">78</span>
									<span class="slot-name">Football Field Front</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">65</span>
									<span class="slot-name">Football Field Side</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">31</span>
									<span class="slot-name">Cafeteria Front</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">14</span>
									<span class="slot-name">Gravel Parking</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">14</span>
									<span class="slot-name">Sampalok Lane</span>
								</div>
								<div class="slot-item">
									<span class="slot-count">14</span>
									<span class="slot-name">Outside Parking</span>
								</div>
							</div>
						</div>
					</section>
				</div>
				<!-- Parking Spaces Content -->
				<?php require "controller/config.php"; ?>

				<div id="parking-spaces" class="content-section">
					<div class="page-header">
						<h3>Available Parking Spaces</h3>
						<div class="header-actions">
							<button class="btn btn-primary">Edit Parking Spaces</button>
							<div class="dropdown">
								<button class="btn btn-secondary">Student/Visitor <i
										class="fas fa-chevron-down"></i></button>
							</div>
						</div>
					</div>
					<div class="parking-grid">
						<!-- Parking space cards will be dynamically inserted here -->
						<div class="add-space-card">
							<div class="add-space-content">
								<i class="fas fa-plus"></i>
								<span>Add Parking Space</span>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>

</body>
</html>
