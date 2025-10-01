<!-- views/admin_login.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Space - Admin Log In</title>

    <!-- JS -->
    <script src="../views/js/navigation.js"></script>

    <!-- CS -->
    <link rel="stylesheet" href="../views/loginstyle.css">
    <link rel="stylesheet" href="../views/mobile_loginstyle.css">

    <style>
        /* Style for error and success messages */
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

    </style>
</head>

<body>
    <div class="login-container">
        <header class="header">
            <div class="header-logo">
                <h2><i class="fas fa-user-shield"></i> Admin Portal</h2>
            </div>
            <div class="user-profile">
                <i class="fas fa-lock"></i>
            </div>
        </header>
        <div class="bottom">
            <div class="login-box">
                <div class="maincontainer text-container">
                    <div class="textsubcontainer logo-container">
                        <img src="../assets/NULogo.png" alt="NULogo" class="logo">
                    </div>
                    <div class="textsubcontainer brand-container">
                        <h1><span class="yellowhighlight-color">NU</span>Space</h1>
                        <section>
                            <p>Welcome <span class="yellowhighlight-color">Administrator</span></p>
                            <p>Log in to manage the <span class="yellowhighlight-color">system</span></p>
                        </section>
                    </div>
                </div>
                <div class="maincontainer input-container">
                    <div class="inputsubcontainer tasknamecontainer">
                        <h2>Admin Log In</h2>
                    </div>
                    <div class="inputsubcontainer formcontainer">

                        <!-- Display error or success messages (passed from controller) -->
                        <?php if (!empty($error_message)): ?>
                            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success_message)): ?>
                            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>

                        <form action="admin_login.php" method="POST">
                            <div class="input-group">
                                <label for="email">Admin Email</label>
                                <input type="email" id="email" name="admin_email" required
                                    placeholder="admin@example.com"
                                    value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="admin_password" required
                                    placeholder="admin1">
                            </div>
                            <div class="label-div">
                                <section>
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">Remember Me</label>
                                </section>
                            </div>
                            <button type="submit" class="login-button">Log In</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <p>NU Space &copy; 2025 All rights reserved.</p>
            <section>
                <div class="nav-item">
                    <a href="#" data-target="terms-container">Terms and Conditions</a>
                </div>
                <div class="nav-item">
                    <a href="#" data-target="policy-container">Policy and Regulations</a>
                </div>
            </section>

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
            </div>
        </footer>
    </div>
</body>

</html>