<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NU Space - Log In</title>
	<link rel="stylesheet" href="loginstyle.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
	<div class="login-container">
		<header class="header">
			<div class="header-logo">
				<h2><img src="assets/nu-logo.png" alt="NUSpace_Logo" class="nu-logo"> NUSpace</h2>
			</div>
			<div class="user-profile">
				<i class="fas fa-bell"></i>
			</div>
		</header>
        <div class="bottom">
            <div class="login-box">
                <div class="maincontainer text-container">
                    <div class="textsubcontainer logo-container">
                        <img src="assets/NULogo.png" alt="NULogo" class="logo">
                    </div>
                    <div class="textsubcontainer brand-container">
                        <h1><span class="yellowhighlight-color">NU</span>Space</h1>
                        <section>
                            <p>Save time, reserve <span class="yellowhighlight-color">smart</span> –</p>
                            <p>Log in to secure your <span class="yellowhighlight-color">spot</span>!</p>
                        </section>
                    </div>
                </div>
                <div class="maincontainer input-container">
                    <div class="inputsubcontainer tasknamecontainer">
                        <h2>Log In</h2>
                    </div>
                    <div class="inputsubcontainer formcontainer">
                        <form action="login.php" method="POST">
                            <div class="input-group">
                                <label for="Email">Email</label>
                                <input type="email" id="Email" name="Email" required placeholder="doej@students.nu-laguna.edu.ph">
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
                                <label for="forgot-password"><a href="#">Forgot Password?</a></label>
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