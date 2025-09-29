<?php
// Start session and check authentication
// session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../controller/login.php");
    exit();
}

// Fetch session variables
$user_id     = $_SESSION['user_id'];
$firstname   = $_SESSION['firstname'] ?? 'User'; 
$lastname    = $_SESSION['lastname'] ?? '';
$userType    = $_SESSION['user_type'] ?? 'N/A';
$phoneNumber = $_SESSION['phone_number'] ?? 'N/A';
$email       = $_SESSION['email'] ?? 'N/A';
$plateNumber = $_SESSION['plate_number'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - NUSpace</title>
    <link rel="stylesheet" href="../views/dashboardstyle.css">
    <link rel="stylesheet" href="../views/accountstyle.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="header">
            <div class="header-logo">
                <h2><a href="dashboard.php" style="text-decoration: none; color: inherit;">
                    <img src="../assets/NULogo.png" alt="NUSpace_Logo" class="nu-logo"> NUSpace</a></h2>
            </div>
            <div class="user-profile">
                <i class="fas fa-bell"></i>
                <a href="../views/account.php" class="user-profile-link">
                    <span>Hi, <?php echo htmlspecialchars($firstname); ?></span>  
                    <img src="https://i.pravatar.cc/150" alt="User Avatar" class="avatar">
                </a>
            </div>
        </header>

        <!-- Main Content for Account Page -->
        <div class="account-page-content">
            <div class="account-card">
                <h1 class="account-title">Account</h1>
                
                <div class="account-header">
                    <img src="https://i.pravatar.cc/150" alt="User Profile Picture" class="account-avatar">
                    <h2 class="account-name"><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></h2>
                </div>

                <ul class="account-details">
                    <li><i class="fas fa-user-tag"></i>
                        <div><span class="detail-label">Type</span>
                        <span class="detail-value"><?php echo htmlspecialchars($userType ?? ''); ?></span></div>
                    </li>
                    <li><i class="fas fa-phone"></i>
                        <div><span class="detail-label">Phone Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($phoneNumber ?? ''); ?></span></div>
                    </li>
                    <li><i class="fas fa-envelope"></i>
                        <div><span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo htmlspecialchars($email ?? ''); ?></span></div>
                    </li>
                </ul>
                <div class="vehicle-tabs-container">
                    <?php if (!empty($vehicles)): ?>
                        <div class="vehicle-tab-buttons">
                            <?php foreach ($vehicles as $idx => $vehicle): ?>
                                <button class="vehicle-tab-btn<?php if ($idx === 0) echo ' active'; ?>" data-slot="<?php echo $idx; ?>">
                                    Vehicle <?php echo $idx + 1; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="vehicle-tab-content">
                            <?php foreach ($vehicles as $idx => $vehicle): ?>
                                <div class="vehicle-slot<?php if ($idx === 0) echo ' active'; ?>" data-slot="<?php echo $idx; ?>">
                                    <ul class="vehicle-details">
                                        <li><strong>Plate Number:</strong> <?php echo htmlspecialchars($vehicle['plate_number']); ?></li>
                                        <li><strong>Type:</strong> <?php echo htmlspecialchars($vehicle['vehicle_type_id']); ?></li>
                                        <li><strong>Color:</strong> <?php echo htmlspecialchars($vehicle['color']); ?></li>
                                        <li><strong>Make:</strong> <?php echo htmlspecialchars($vehicle['make']); ?></li>
                                        <li><strong>Model:</strong> <?php echo htmlspecialchars($vehicle['model']); ?></li>
                                        <li><strong>Year:</strong> <?php echo htmlspecialchars($vehicle['year']); ?></li>
                                    </ul>
                                    <div class="qr-section">
                                        <h3>QR Code</h3>
                                        <img src="assets/qr-<?php echo htmlspecialchars($vehicle['plate_number']); ?>.png" alt="QR for <?php echo htmlspecialchars($vehicle['plate_number']); ?>" class="qr-code-img">
                                        <a href="assets/qr-<?php echo htmlspecialchars($vehicle['plate_number']); ?>.png" class="download-qr-link">Download QR</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="empty-slot">You have no registered vehicles.</p>
                    <?php endif; ?>
                </div>
            </div>
            </div>

            <footer class="account-footer">
                <span>NUSpace © 2025</span>
                <div class="footer-links">
                    <a href="#">Terms And Conditions</a>
                    <a href="#">Policy And Regulation</a>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
