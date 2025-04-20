<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Frontend-Admin/Login2.css"> <!-- External CSS file -->
</head>
<body>

    <div class="login-container">
        <h2>Admin Login</h2>
        <form id="login-form">
            <div class="input-group">
                <label for="username">UserName:</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required>
                <span class="error" id="usernameError"></span>  <!-- Error message for username -->
            </div>

            <div class="input-group password-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Password" required>
                <span class="error" id="passwordError"></span>  <!-- Error message for password -->
            </div>

            <div class="options">
                <label>
                    <input type="checkbox" id="remember-me" name="remember-me">
                    Remember me
                </label>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit">Sign In</button>
            <button onclick="window.location.href='Registration.php'">Create an Account</button>
            <div class="google-login">
                    <button class="google-btn" onclick="googleSignIn()">Sign in using Google</button>
                </button>
            </div>
        </form>
    </div>

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
    <script src="../Frontend-Admin/login.js"></script>
</body>
</html>
