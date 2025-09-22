<?php
// session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../controller/login.php");
    exit();
}

// Optional: fetch session variables for display
$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['firstname'] ?? 'User'; 
?>

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
                        <a href="account.php"><i class="fas fa-user"></i><span>Account</span></a>
                    </li>
                </ul>
				<div class="log-out">
					<!-- Admin logout with confirmation -->
					<a href="?logout=1" onclick="return confirm('Are you sure you want to logout from admin panel?')">
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
                <!-- <?php require "controller/connection.php"; ?>
                <div id="parking-spaces" class="content-section">
                    <div class="page-header">
                        <h3>Available Parking Spaces</h3>
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

                        <div class="header-actions">
                            <p>Map</p>
                            <div class="drops">
                                <div class="dropdown">
                                    <input type="checkbox" id="dropdown-toggle-vehicle" class="dropdown-checkbox">
                                    <label for="dropdown-toggle-vehicle" class="btn btn-secondary">
                                        Vehicle <i class="fas fa-chevron-down"></i>
                                    </label>
                                    <div class="dropdown-content">
                                        <a href="#" data-filter="cars">Cars</a>
                                        <a href="#" data-filter="motorcycles">Motorcycles</a>
                                    </div>
                                </div>

                                <div class="dropdown">
                                    <input type="checkbox" id="dropdown-toggle-person" class="dropdown-checkbox">
                                    <label for="dropdown-toggle-person" class="btn btn-secondary">
                                        Person <i class="fas fa-chevron-down"></i>
                                    </label>
                                    <div class="dropdown-content">
                                        <a href="#" data-filter="student">Student</a>
                                        <a href="#" data-filter="visitor">Visitor</a>
                                        <a href="#" data-filter="staff">School Staff</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="parking-grid">
                        <!-- Parking space cards will be dynamically inserted here -->
                        <!-- Figuring out how to fix this -->
                    </div>
                </div> -->

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
                        <a href="non-admin.php" class="btn-back">← Back to Dashboard</a>
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
                        <a href="non-admin.php" class="btn-back">← Back to Dashboard</a>
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
    <script src="script.js"></script>
</body>

</html>