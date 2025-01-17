<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Marinmart</title>
    <link rel="stylesheet" href="./assets/login.css?v=<?php echo time(); ?>">
</head>
<body>
    <h1 class="logo">marinmart</h1>
    <div class="container">
        <div class="form-wrapper">
            <form id="login-form" action="fetch_login.php" method="post">
                <h1>LOG IN</h1>
                <label for="user-type" id="user-type">User Type:</label>
                <select name="user-type" id="user-type">
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="user" selected>User</option>
                </select>
                <div class="input-container">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-container">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account yet? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>

    <footer>
        © 2024 Marinmart. All rights reserved.
    </footer>

</body>
</html>
