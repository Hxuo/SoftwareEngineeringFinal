<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../Frontend-Admin/Login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    
    <!-- Firebase SDK -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js";

        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyAPIelxIv7Hri58fdyTy6Fj1ZN7O8FiCsQ",
            authDomain: "softengfinal-7a580.firebaseapp.com",
            projectId: "softengfinal-7a580",
            storageBucket: "softengfinal-7a580.firebasestorage.app",
            messagingSenderId: "615237496560",
            appId: "1:615237496560:web:332c22d9f2040ea4d7dc35",
            measurementId: "G-J8YRKWJHYQ"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        // Google Sign-In Function
        window.googleSignIn = function() {
            const provider = new GoogleAuthProvider();
            signInWithPopup(auth, provider)
                .then((result) => {
                    var user = result.user;
                    console.log('User signed in:', user);

                    // Send Google user data to PHP session
                    fetch('google-login.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `name=${encodeURIComponent(user.displayName)}&email=${encodeURIComponent(user.email)}`

                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            window.location.href = "DashboardSched.php"; // Redirect to dashboard
                        } else {
                            alert("Google sign-in failed.");
                        }
                    })
                    .catch(error => console.error('Error:', error));
                })
                .catch((error) => {
                    console.error(error);
                    alert("Error signing in: " + error.message);
                });
        };
    </script>
    
    <script>
        function validateForm() {
            let username = document.getElementById("username").value.trim();
            let password = document.getElementById("password").value.trim();
            let usernameError = document.getElementById("usernameError");
            let passwordError = document.getElementById("passwordError");

            usernameError.textContent = "";
            passwordError.textContent = "";

            if (username === "") {
                usernameError.textContent = "Please fill up the field first";
                return false;
            }
            if (password === "") {
                passwordError.textContent = "Please fill up the field first";
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
<div class="login-container">
   

    <!-- Right Side - Login Form -->
    <div class="right-panel">
        <h2>Login</h2>

        <form action="Login-process.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" id="username" name="username" required>
                <span class="error" id="usernameError"></span>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error" id="passwordError"></span>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox"> Remember me</label>
                <a href="#">Forgot your password?</a>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="signup-link">
            <button onclick="window.location.href='Registration.php'">Create an Account</button>
        </div>

        <hr>

        <button class="google-btn" onclick="googleSignIn()">Sign in using Google</button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>