<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NU Space - Dashboard</title>
	<link rel="stylesheet" href="registerstyle.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
	<div class="register-container">
		<header class="header">
			<div class="header-logo">
				<h2><i class="fas fa-car-side"></i> NUSpace</h2>
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
                        <form action="register.php" method="POST">
                            <div class="input-group">
                                <div class="name-group">
                                    <section>
                                        <label for="FirstName">First Name</label>
                                        <input type="text" id="FirstName" name="FirstName" required placeholder="John">
                                    </section>
                                    <section>
                                        <label for="LastName">Last Name</label>
                                        <input type="text" id="LastName" name="LastName" required placeholder="Doe">
                                    </section>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="PlateNumber">Plate Number</label>
                                <input type="text" id="PlateNumber" name="PlateNumber" required placeholder="ABC-1234">
                            </div>
                            <div class="input-group">
                                <label for="PhoneNumber">Phone Number</label>
                                <input type="tel" id="PhoneNumber" name="PhoneNumber" required placeholder="0917-123-4567">
                            </div>
                            <div class="input-group">
                                <div class="dropdown-group">
                                    <section>
                                        <label for="Type">Type</label>
                                        <select id="Type" name="Type" required>
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="Student">Student</option>
                                            <option value="Faculty">Faculty</option>
                                            <option value="Visitor">Executive</option>
                                            <option value="Visitor">Visitor</option>
                                        </select>
                                    </section>
                                    <section>
                                        <label for="VehicleType">Vehicle Type</label>
                                        <select name="VehicleType" id="VehicleType" required>
                                            <option value="" disabled selected>Select Vehicle Type</option>
                                            <option value="Car">Car</option>
                                            <option value="Motorcycle">Motorcycle</option>
                                        </select>
                                    </section>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="Email">Email</label>
                                <input type="email" id="Email" name="Email" required placeholder="john.doe@example.com">
                            </div>
                            <div class="input-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" required placeholder="Password">
                            </div>
                            <div class="label-div">
                                <input type="checkbox" id="remember" name="remember" required>
                                <label for="TnC agree">Yes, I agree to the <a href="#">Terms and Conditions</a></label>
                            </div>
                            <div class="label-div">
                                <input type="checkbox" id="remember" name="remember" required>
                                <label for="PnR agree">Yes, I have read the <a href="#">Policy and Regulation</a></label>
                            </div>
                            <button type="submit" class="register-button">Register</button>
                        </form>
                        <h2>Already Have an Account? <a href="login.php">Log In  Here!</a></h2>
                    </div>
                </div>
                <div class="maincontainer text-container">
                    <div class="textsubcontainer logo-container">
                        <img src="assets/NULogo.png" alt="NULogo" class="logo">
                    </div>
                    <div class="textsubcontainer brand-container">
                        <h1><span class="bluehighlight-color">NU</span>Space</h1>
                        <section>
                            <p>Save time, reserve <span class="bluehighlight-color">smart</span> –</p>
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