<!-- views/login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NU Space - Log In</title>
	<link rel="stylesheet" href="../views/loginstyle.css">
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
                        
                        <!-- Display error or success messages (passed from controller) -->
                        <?php 
                        if (!empty($error_message)): 
                        ?>
                            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success_message)): ?>
                            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
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