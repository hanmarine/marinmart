<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="./assets/../assets/css/login.css?v=<?php echo time(); ?>">
</head>
<body>
    <h1 class="logo">marinmart</h1>
    <div class="container">
        <div class="form-wrapper">
            <!-- Typically, no signups for admins and managers -->
            <h1>REGISTER</h1> 
            </select>
            <form action="fetch_signup.php" method="POST">
                <div id="user-signup-form">
                <div class="input-container">
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="first-name" placeholder="e.g. John" required>
                </div>
                <div class="input-container">
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="last-name" placeholder="e.g. Doe" required>
                </div>
                <div class="input-container">
                    <label for="company">Company:</label>
                    <input type="text" id="company" name="company" placeholder="e.g. Wonka Industries" required>
                </div>
                <div class="input-container">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="e.g. johndoe" required>
                </div>
                <div class="input-container">
                    <label for="contact-number">Contact Number:</label>
                    <input type="tel" id="contact-number" name="contact-number" placeholder="e.g. 1234567890" required pattern="[0-9]{11}" maxlength="11">
                </div>
                <div class="input-container">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="e.g. yourpassword" required>
                </div>
                <div class="input-container">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="e.g. yourpassword" required>
                </div>
                <button type="submit">Sign Up</button>
                </div>
                <p>Already have an account? <a href="login.php">Log In</a></p>
                </div>
            </form>
        </div>
    </div>

    <footer id="footer"></footer>
    <script src="./assets/js/footer.js"></script>
</body>
</html>