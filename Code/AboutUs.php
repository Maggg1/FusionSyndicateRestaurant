<?php
session_start();

// Debugging: Print session data
/*echo '<pre>';
print_r($_SESSION);
echo '</pre>';*/

// Check if user is logged in
$loggedIn = isset($_SESSION['id']); // Use 'id' or another unique session variable
$user_role = $_SESSION['role'] ?? '';
$username = $_SESSION['first_name'] ?? ''; // Use 'first_name' for the welcome message
?>
<!DOCTYPE>
<html>
<head>
    <title>Fusion Syndicate Online Ordering System</title>
    <link rel="stylesheet" href="styles.css">
	
	<style>
	.sec-1 {
		position: relative;
		padding: 20px 0;
		color: white;
		text-align: center;
		z-index: 1;
	}

	.sec-1::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: 
			linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.1)),
			url('img/fusion1.jpg');
		background-size: cover;
		background-position: center;
		opacity: 0.6;  /* Set the opacity of the image */
		z-index: -1;   /* Make sure the background stays behind the content */
	}

	.sec-2 {
	background: 
            rgba(242, 242, 230, 0.8);
			padding:30px;
			text-align:center;

	}


	h2 {
		color: #333;
		text-align: center;
		margin-bottom: 20px;
	}

	.sec-2 p {
		text-align: justify;
		margin: 20px;
		padding-left:60px;
		padding-right:100px;
		color: #555;
	}
	.sec-2 img{
	width:250px;
	height:250px;
	}
	
	.sec-3 {
	padding:50px;
	margin-top:20px;
			background: 
            linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0,0.3));
			transition: background-color 0.2s ease;
	}
	.sec-3 p{
	padding:10px;
	margin-bottom:5px;
	}

	form {
		max-width: 600px;
		margin: 30 auto;
        background-color: 
            rgba(242, 242, 230, 0.8);
			border-radius: 10px;
	}

	.form-group {
		margin-bottom: 15px;
		padding-left:20px;
		padding-right:20px;
		color:#855c22;
	}

	label {
		display: block;
		font-weight: bold;
		margin-bottom: 5px;
		padding-top:20px;
	}

	input[type="text"],
	input[type="email"],
	textarea {
		width: 100%;
		padding: 10px;
		border: 1px solid #ccc;
		border-radius: 5px;
		box-sizing: border-box;
	}

	button {
		width: 93%;
		padding: 10px;
		background-color: #d6b485;
		color: black;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		font-size: 16px;
		margin:20px;
		transition: background-color 0.3s ease-in-out;
	}

	button:hover {
		background-color:  #806c50;
		color:white;
	}

	</style>
	<script>
    window.onscroll = function() {
        // Get the scroll position
        var scrollPosition = window.scrollY;
        
        // Calculate the opacity value (the further the scroll, the less opacity)
        var maxScroll = 200;  // The max scroll height at which opacity becomes 0
        var opacity = 1 - (scrollPosition / maxScroll);

        // Ensure opacity is between 0 and 1
        if (opacity < 0) opacity = 0;
        if (opacity > 1) opacity = 1;

        // Apply the new opacity to the background color of sec-1
        document.querySelector('.sec-3').style.backgroundColor = 'rgba(133, 114, 89, ' + opacity + ')';
    };
</script>

</head>

<body>
 <div class="container">
        <header>
            <div class="head1">
                <div class="logo">
                    <a href="index.html">
                        <img src="img/logo.png" alt="Fusion Syndicate Logo" />
                    </a>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="menu_cart.php">Menus</a></li>
                        <li><a href="AboutUs.php">About Us</a></li>
                    </ul>
                </nav>
                <div class="icons">
				<!--only change depends on user login-->
<?php if ($loggedIn): ?>
    <a href="<?= ($user_role === 'admin') ? 'adminprofile.php' : 'customerprofile.php' ?>" class="profile">
        <img src="img/profile.png" alt="Profile" class="icon">
        <span>Welcome, <?= htmlspecialchars(ucfirst($username), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars(ucfirst($user_role), ENT_QUOTES, 'UTF-8') ?>)</span>
    </a>

    <?php if ($user_role !== 'admin'): ?> <!-- Hide Cart for admin -->
        <a href="cart.php">
            <img src="img/Cart.png" alt="Cart" class="icon">
            <span class="login-text">Cart</span>
        </a>
    <?php endif; ?>

    <a href="logout.php" onclick="return confirmLogout();">
	<img src="img/logout.png" alt="Logout" class="icon">
	<span class="login-text">Logout</span>
	</a>
	<!--Logout Verification-->
	<script>
	function confirmLogout() {
	return confirm("Are you sure you want to log out?");
	}
	</script>
	
<?php else: ?>
    <a href="login.php" class="login">
        <img src="img/profile.png" alt="Profile" class="icon">
        <span class="login-text">Login</span>
    </a>
<?php endif; ?>
</div>
				</div>
            </div>
        </header>
		<section class="sec-1">
			<section class="sec-2">
			<h2>About Us</h2>
			<img src="img/logo.png" alt="Fusion Syndicate" />
            <p>
                Welcome to Fusion Syndicate, where culinary artistry meets a vibrant dining experience. Our passion for exquisite flavors and innovative dishes drives us to create memorable dining moments for our guests.
            </p>
            <p>
                Our Story: Founded in 2024, Fusion Syndicate has quickly become a beloved dining destination in Sabah. Our team of talented chefs combines traditional cooking techniques with modern flair to create a unique fusion cuisine that delights the senses.
            </p>
			<p>
                Our Philosophy: At Fusion Syndicate, we believe in using only the freshest ingredients sourced from local producers whenever possible. We are committed to sustainability and strive to minimize our environmental impact.
            </p>
            <p>
				Our Promise: We are dedicated to providing exceptional service and a warm, inviting atmosphere for every guest. Whether you're joining us for a casual meal or a special celebration, we promise to make your experience at Fusion Syndicate unforgettable.
            </p>
			<p>
                Join Us: Come and discover the magic of Fusion Syndicate. Indulge in our culinary creations, crafted with care and passion. We look forward to welcoming you to our table.
            </p>
			</section>
			<section class="sec-3">
			<h2>Contact Us</h2>
<p>SICC Sabah Address: 456 SICC Road, Sabah<br>
    Phone: 987-654-3210<br>
    Suria Sabah Address: 789 Suria Avenue, Sabah<br>
    Phone: 567-890-1234<br><br>
    For inquiries, reservations, or feedback, please contact us using the information provided above. We look forward to serving you!
</p>

<form action="submit_feedback.php" method="post">
    <input type="hidden" name="customer_id" value="<?php echo $_SESSION['id']; ?>"> <!-- Hidden field for user ID -->
    
    <div class="form-group">
        <label for="name">Name</label>
<input type="text" id="name" name="name" value="<?php echo $_SESSION['first_name']; ?>" readonly required>
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
<input type="email" id="email" name="email" value="<?php echo $_SESSION['email'] ?? ''; ?>" readonly required>
    </div>
    
    <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="comment" rows="4" required></textarea>
    </div>
    
    <button type="submit">Submit</button>
</form>
			</section>
		</section>
		<footer>
            <div class="footer-social-links">
                <a href="https://www.facebook.com/" target="_blank">
                    <img src="img/fb.png" alt="Facebook">
                </a>
                <a href="https://www.instagram.com/" target="_blank">
                    <img src="img/ig.jpg" alt="Instagram">
                </a>
                <a href="https://www.tiktok.com/" target="_blank">
                    <img src="img/tt.png" alt="TikTok">
                </a>
            </div>
            <div class="footer-copy">
                <p>&copy; 2024 Fusion Syndicate Online Ordering System</p>
            </div>
        </footer>
    </div>
</body>
</html>