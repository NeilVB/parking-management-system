<!-- views/register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NU Space - Register</title>
	<link rel="stylesheet" href="../views/registerstyle.css">
    <link rel="stylesheet" href="../views/mobile_registerstyle.css">
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
	<div class="register-container">
		<header class="header">
			<div class="header-logo">
                <h2><img src="../assets/NULogo.png" alt="NUSpace_Logo" class="nu-logo"> NUSpace</h2>
			</div>
			<div class="user-profile">
				<i class="fas fa-bell"></i>
			</div>
		</header>
        <div class="bottom">
            <div class="register-box">
                <div class="maincontainer input-container">
                    <div class="inputsubcontainer tasknamecontainer">
                        <h2>Register</h2>
                    </div>
                    <div class="inputsubcontainer formcontainer">
                        <?php 
                        // Display error or success messages (passed from controller)
                        if (!empty($error_message)): 
                        ?>
                            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>
                        
                        <form action="../controller/register.php" method="POST">
                            <div class="input-group">
                                <label for="FirstName">First Name</label>
                                    <input type="text" id="FirstName" name="FirstName" required placeholder="John" 
                                            value="<?php echo htmlspecialchars($form_data['FirstName'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="LastName">Last Name</label>
                                    <input type="text" id="LastName" name="LastName" required placeholder="Doe"
                                            value="<?php echo htmlspecialchars($form_data['LastName'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="PhoneNumber">Phone Number</label>
                                <input type="tel" id="PhoneNumber" name="PhoneNumber" required placeholder="09171234567"
                                            value="<?php echo htmlspecialchars($form_data['PhoneNumber'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="Type">Type</label>
                                <select id="Type" name="Type" required>
                                    <option value="" disabled <?php echo empty($form_data['Type']) ? 'selected' : ''; ?>>Select Type</option>
                                    <option value="Student" <?php echo (isset($form_data['Type']) && $form_data['Type'] == 'Student') ? 'selected' : ''; ?>>Student/Visitor</option>
                                    <option value="Faculty" <?php echo (isset($form_data['Type']) && $form_data['Type'] == 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                                    <option value="Executive" <?php echo (isset($form_data['Type']) && $form_data['Type'] == 'Executive') ? 'selected' : ''; ?>>Executive</option>
                                    <option value="Guest" <?php echo (isset($form_data['Type']) && $form_data['Type'] == 'Guest') ? 'selected' : ''; ?>>Guest</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="Email">Email</label>
                                <input type="email" id="Email" name="Email" required placeholder="john.doe@example.com"
                                            value="<?php echo htmlspecialchars($form_data['Email'] ?? ''); ?>">
                            </div>
                            <div class="input-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" required placeholder="Password">
                            </div>
                            <div class="label-div">
                                <input type="checkbox" id="tnc" name="tnc" required>
                                <label for="tnc">Yes, I agree to the <a href="#">Terms and Conditions</a></label>
                            </div>
                            <div class="label-div">
                                <input type="checkbox" id="policy" name="policy" required>
                                <label for="policy">Yes, I have read the <a href="#">Policy and Regulation</a></label>
                            </div>
                            <button type="submit" class="register-button">Register</button>
                        </form>
                        <h2>Already Have an Account? <a href="login.php">Log In Here!</a></h2>
                    </div>
                </div>
                <div class="maincontainer text-container">
                    <div class="textsubcontainer logo-container">
                        <img src="../assets/NULogo.png" alt="NULogo" class="logo">
                    </div>
                    <div class="textsubcontainer brand-container">
                        <h1><span class="bluehighlight-color">NU</span>Space</h1>
                        <section>
                            <p>Save time, reserve <span class="bluehighlight-color">smart</span> —</p>
                            <p>Log in to secure your <span class="bluehighlight-color">spot</span>!</p>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <p>NU Space &copy; 2025  All rights reserved.</p>
            <section>
                <a href="#">Terms and Conditions</a>
                <a href="#">Policy and Regulations</a>
            </section>
        </footer>
	</div>
</body>
</html>