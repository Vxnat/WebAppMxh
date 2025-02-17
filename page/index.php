<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/index.css" />
    <title>Login Page</title>
</head>

<body>
    <div class="container" id="container">
        <!-- Sign UpUp -->
        <div class="form-container sign-up">
            <form method="post">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <!-- <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
                </div>
                <span>or use your email for registeration</span>
                <input type="email" placeholder="Email" id="su-email" />
                <input type="password" placeholder="Password" id="su-pw" />
                <input type="password" placeholder="Confirm Password" id="cf-pw" />
                <button type="button" id="su-btn">Sign Up</button>
            </form>
        </div>
        <!-- Sign In -->
        <div class="form-container sign-in">
            <form method="post">
                <h1>Sign In</h1>
                <div class="social-icons">
                    <!-- <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
                </div>
                <span>or use your email password</span>
                <input type="email" placeholder="Email" id="si-email" />
                <input type="password" placeholder="Password" id="si-pw" />
                <a href="forgot_password.php">Forget Your Password?</a>
                <button type="button" id="si-btn">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button type="button" class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button type="button" class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/index.js"></script>
</body>

</html>