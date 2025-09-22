<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - NUSpace</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="account.css"> 
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
                <h2><a href="index.php" style="text-decoration: none; color: inherit;"><img src="assets/nu-logo.png" alt="NUSpace_Logo" class="nu-logo"> NUSpace</a></h2>
            </div>
            <div class="user-profile">
                <i class="fas fa-bell"></i>
                <a href="account.php" class="user-profile-link">
                    <span>Hi, Chad</span> 
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
                    <h2 class="account-name">Villamor, Christian Chad</h2>
                </div>

                <ul class="account-details">
                    <li><i class="fas fa-user-tag"></i><div><span class="detail-label">Type</span><span class="detail-value">Student</span></div></li>
                    <li><i class="fas fa-phone"></i><div><span class="detail-label">Phone Number</span><span class="detail-value">0422 398 6120</span></div></li>
                    <li><i class="fas fa-envelope"></i><div><span class="detail-label">Email</span><span class="detail-value">villamorcn@students.nu-laguna.edu.ph</span></div></li>
                    <li><i class="fas fa-car-side"></i><div><span class="detail-label">Plate Number</span><span class="detail-value">CAR 100</span></div></li>
                </ul>

                <div class="qr-section">
                    <h3>My QR Code</h3>
                    <img src="assets/sample-qr.png" alt="User QR Code" class="qr-code-img">
                    <a href="assets/sample-qr.png"  class="download-qr-link">Download QR</a>
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