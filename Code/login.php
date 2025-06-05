<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - Fusion Syndicate</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        <div style="font-size:60px;">Log In Fusion Syndicate</div>
        <div class="bottomright">
            <div id="button_container">
                <button id="button"><a href="signup.php">Signup</a></button>
                <button id="button"><a href="index.php">Home</a></button>
            </div>
        </div>
    </section>
    <section class="body_input">
        <div id="bck_inpt">
            <div style="font-size:30px;">Welcome Syndicate Member.</div>
            <br><br>
            <div style="background-color:white; width:400px; margin:auto; padding:30px;">
                <form method="post" action="classes/loginDb.php">
                    <input id="text" name="e_mail" type="text" placeholder="Email" required><br><br>
                    <input id="text" name="password" type="password" placeholder="Password" required><br><br>
                    <input id="button1" type="submit" name="submit" value="Log In">
                </form>
                <br>
                <button id="button1" onclick="showForgot()">Forgot Password?</button>
                <div id="forgotForm" style="display: none;">
                    <form action="reset_password.php" method="post">
                        <input id="text" name="e_mail" type="email" placeholder="Enter your email" required><br><br>
                        <input id="text" name="new_password" type="password" placeholder="Enter new password" required><br><br>
                        <input id="button1" type="submit" name="forgot" value="Reset Password">
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
        function showForgot() {
            document.getElementById("forgotForm").style.display = "block";
        }
    </script>
    <section>
        <div style="display: flex; align-items: center; padding: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px; padding: 10px; margin-left:60px;">
                <iframe width="80%" height="350" src="https://www.youtube.com/embed/L5uJE1p0Dos"
                        frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                </iframe>
            </div>
            <div style="flex: 1; min-width: 300px; padding: 10px;color:#784d02; margin-right:50px;">
                <h2 style="padding-bottom:10px;">Introducing the ultimate feast for your taste buds... <br>Fusion Syndicate!</h2>
                <p>From our signature Original Recipe chicken to our mouthwatering sides and refreshing drinks,<br>FS offers a delicious meal for every craving.</p>
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
