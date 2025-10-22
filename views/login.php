<!-- views/login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NU Space - Log In</title>

    <!-- CSS -->
	<link rel="stylesheet" href="../views/loginstyle.css">
    <link rel="stylesheet" href="../views/css/modals_styles.css">
    <link rel="stylesheet" href="../views/mobile_loginstyle.css">

    <!-- JS -->
    <script src="../views/js/navigation.js"></script>
    <script src="../views/js/modals.js"></script>

</head>
<body>
	<div class="login-container">
		<header class="header">
			<div class="header-logo">
                <h2><img src="../assets/NULogo.png" alt="NUSpace_Logo" class="nu-logo"> NUSpace</h2>
			</div>
			<div class="user-profile">
				<i class="fas fa-bell"></i>
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
                            <p>Save time, reserve <span class="yellowhighlight-color">smart</span> —</p>
                            <p>Log in to secure your <span class="yellowhighlight-color">spot</span>!</p>
                        </section>
                    </div>
                </div>
                <div class="maincontainer input-container">
                    <div class="inputsubcontainer tasknamecontainer">
                        <h2>Log In</h2>
                    </div>
                    <div class="inputsubcontainer formcontainer">
                        
                        <!-- Login: Success and Error Modals -->
                        <?php if (!empty($error_message)): ?>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    openFeedbackModal(
                                        "Login Failed",
                                        "<?php echo addslashes($error_message); ?>",
                                        "error",
                                        "login"
                                    );
                                });
                            </script>
                        <?php elseif (!empty($success_message)): ?>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    openFeedbackModal(
                                        "Login Successful",
                                        "<?php echo addslashes($success_message); ?>",
                                        "success",
                                        "login"
                                    );
                                });
                            </script>
                        <?php endif; ?>
   
                        <form action="login.php" method="POST">
                            <div class="input-group">
                                <label for="Email">Email</label>
                                <input type="email" id="Email" name="Email" required placeholder="doej@students.nu-laguna.edu.ph"
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            </div>  
                            <div class="input-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" required placeholder="Password">
                            </div>
                            <div class="label-div">
                                <section>
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">Remember Me</label>
                                </section>
                                <label for="forgot-password"><a href="forgot_password.php">Forgot Password?</a></label>
                            </div>
                            <button type="submit" class="login-button">Log In</button>
                        </form>
                        <h2>Don't Have an Account? <a href="register.php">Register Here!</a></h2>
                    </div>
                </div>
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
                <a href="dashboard.php" class="btn-back">← Close</a>
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

            <div class="terms-footer">
                <a href="dashboard.php" class="btn-back">← Close</a>
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
	</div>

</body>
</html>