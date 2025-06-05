<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Sign Up - Fusion Syndicate</title>
    <style>
        #bck_inpt {
            background-color: #f2f2e6;
            padding: 30px;
        }
        #text {
            height: 40px;
            width: 300px;
            border-radius: 4px;
            border: 1px solid #ccc;
            padding: 10px;
        }
        #button {
            width: 70px;
            height: 40px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-weight: bold;
            background-color: #f2f2e6;
            font-size: 15px;
        }
        #button:hover {
            background-color: #04AA6D;
            color: white;
        }
        #button1 {
            width: 300px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-weight: bold;
            background-color: #f2f2e6;
        }
        #button1:hover {
            background-color: #04AA6D;
            color: white;
        }
        .bottomright {
            position: absolute;
            bottom: 8px;
            right: 16px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <section class="first">
        <div style="font-size:50px;">Sign Up - Fusion Syndicate</div>
        <div class="bottomright">
            <div id="button_container">
                <button id="button"><a href="index.php">Home</a></button>
                <button id="button"><a href="login.php">Login</a></button>
            </div>
        </div>
    </section>
    
    <section class="body_input">
        <div id="bck_inpt">
            <div style="font-size:30px; font-weight:bold;">
                Join Fusion Syndicate Today!
            </div>
            <br><br>
            <div style="background-color:white; width:400px; margin:auto; padding:30px;">
                <!-- Form submits to signupDb.php -->
                <form method="post" action="classes/signupDb.php">
                    <label for="Fname">First Name:</label><br>
                    <input id="text" name="first_name" placeholder="First Name" required><br><br>
                    
                    <label for="Lname">Last Name:</label><br>
                    <input id="text" name="last_name" placeholder="Last Name" required><br><br>
                    
                    <label for="pNumber">Phone Number:</label><br>
                    <input id="text" name="pNumber" placeholder="Phone Number" required><br><br>
                    
                    <label for="address">Address:</label><br>
                    <input id="text" name="address" placeholder="Address" required><br><br>
                    
                    <label for="e_mail">Email:</label><br>
                    <input type="email" id="text" name="e_mail" placeholder="example@fusion.com" required><br><br>
                    
                    <label for="password">Password:</label><br>
                    <input type="password" id="text" name="password" placeholder="Password" required><br><br>
                    
                    <input type="password" id="text" name="password2" placeholder="Retype Password" required><br><br>
                    
                    <input type="submit" id="button1" value="Sign Up">
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-social-links">
            <a href="https://www.facebook.com/" target="_blank"><img src="img/fb.png" alt="Facebook"></a>
            <a href="https://www.instagram.com/" target="_blank"><img src="img/ig.jpg" alt="Instagram"></a>
            <a href="https://www.tiktok.com/" target="_blank"><img src="img/tt.png" alt="TikTok"></a>
        </div>
        <div class="footer-copy">
            <p>&copy; 2024 Fusion Syndicate Online Ordering System</p>
        </div>
    </footer>
</body>
</html>
