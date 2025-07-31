<?php
include('../db/userlog.php');

// Fetch user information
$user_id = $_SESSION['user_id'];


// Prepare the query
$query = "SELECT u.username, s.ContactPerson, s.ContactNumber FROM users u JOIN supplier s ON u.user_id = s.user_id WHERE u.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result->fetch_assoc();


$username = $userInfo['username'];
$contactPerson = $userInfo['ContactPerson'];
$contactNumber = $userInfo['ContactNumber'];


// Update user information
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_username'])) {
        $new_username = trim($_POST['username']);


        // Prepare the check query
        $checkQuery = "SELECT user_id FROM users WHERE username=?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $checkResult = $stmt->get_result();


        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Username already exists. Please choose a different username.'); window.location.href = '../user/settings.php';</script>";
        } else {
            // Prepare the update query
            $updateQuery = "UPDATE users SET username=? WHERE user_id=?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $new_username, $user_id);
            $stmt->execute();


            echo "<script>alert('Username updated successfully.'); window.location.href = '../user/settings.php';</script>";
        }
    }


    if (isset($_POST['update_password'])) {
        $current_pw = $_POST['current_pw'];
        $new_pw = $_POST['new_pw'];
        $confirm_pw = $_POST['confirm_pw'];
        $errors = [];  


        // Prepare the check query
        $checkQuery = "SELECT password FROM users WHERE user_id=?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $checkResult = $stmt->get_result()->fetch_assoc();


        if (!$checkResult) {
            $errors[] = "Database error: Unable to retrieve user information.";
        } else if ($current_pw != $checkResult['password']) {
            $errors[] = "Incorrect current password.";
        }


        // Check if new and confirm password match
        if ($new_pw !== $confirm_pw) {
            $errors[] = "New passwords do not match.";
        }


        // If no errors, update password
        if (empty($errors)) {
            $updateQuery = "UPDATE users SET password=? WHERE user_id=?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $new_pw, $user_id);
            $stmt->execute();


            echo "<script>alert('Password updated successfully.'); window.location.href = '../user/settings.php';</script>";
        } else {
            echo "<script>alert('" . implode("\\n", $errors) . "'); window.location.href = '../user/settings.php';</script>";
        }
    }


    if (isset($_POST['update_info'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $contact_number = trim($_POST['contact_number']);


        // Validate input
        if (empty($first_name) || empty($last_name) || empty($contact_number)) {
            echo "<script>alert('Please fill in all required fields.');</script>";
            exit();
        }


        $full_name = $first_name . ' ' . $last_name;


        // Check for existing ContactPerson and ContactNumber
        $checkPerson = "SELECT * FROM supplier WHERE ContactPerson=? AND SupplierID != ?";
        $stmt = $conn->prepare($checkPerson);
        $stmt->bind_param("si", $full_name, $user_id);
        $stmt->execute();
        $checkRes1 = $stmt->get_result();


        $checkNumber = "SELECT * FROM supplier WHERE ContactNumber=? AND SupplierID != ?";
        $stmt = $conn->prepare($checkNumber);
        $stmt->bind_param("si", $contact_number, $user_id);
        $stmt->execute();
        $checkRes2 = $stmt->get_result();


        if ($checkRes1->num_rows > 0) {
            echo "<script>alert('[ERROR] This contact person already exists for another supplier.'); window.location.href = '../user/settings.php';</script>";
        } else if ($checkRes2->num_rows > 0) {
            echo "<script>alert('[ERROR] This contact number already exists for another supplier.'); window.location.href = '../user/settings.php';</script>";
        } else {
            // Prepare the update query
            $updateQuery = "UPDATE supplier SET ContactPerson=?, ContactNumber=? WHERE SupplierID=?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ssi", $full_name, $contact_number, $user_id);
            $stmt->execute();


            if ($stmt->affected_rows > 0) { // Check if update was successful
                echo "<script>alert('Information updated successfully.'); window.location.href = '../user/settings.php';</script>";
            } else {
                echo "<script>alert('[ERROR] Information update failed.'); window.location.href = '../user/settings.php';</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Marinmart</title>
    <link rel="stylesheet" href="../assets/css/profile.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="header">
        <?php include '../functions/navbar.php' ?>
    </header>

    <main class="main">
        <div class="user-info">
            <img src="../assets/profile.png" alt="User Profile Image" class="user-image">
            <div class="user-details">
                <h2><?php echo $contactPerson ?> (user)</h2>
                <p>@<?php echo $username; ?></p>
            </div>
        </div>

        <div class="form-container">
            <form method="POST">
                <div class="form-section">
                    <h3>Change Username</h3>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                    <button type="submit" name="update_username">Save Changes</button>
                </div>
            </form>
            
            <form method="POST">
                <div class="form-section">
                    <h3>Change Password</h3>
                    <label for="current_pw">Current Password:</label>
                    <input type="password" id="current_pw" name="current_pw" required>
                    <label for="new_pw">New Password:</label>
                    <input type="password" id="new_pw" name="new_pw" required>
                    <label for="confirm_pw">Confirm Password:</label>
                    <input type="password" id="confirm_pw" name="confirm_pw" required>
                    <button type="submit" name="update_password">Save Changes</button>
                </div>
            </form>

            <form method="POST">
                <div class="form-section">
                    <h3>Change Information</h3>
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo explode(' ', $contactPerson)[0]; ?>" required>
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo explode(' ', $contactPerson)[1]; ?>" required>
                    <label for="contact_number">Contact Number:</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?php echo $contactNumber; ?>" required>
                    <button type="submit" name="update_info">Save Changes</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        © 2024 Marinmart, All rights reserved.
    </footer>
    
    <script src="../assets/js/dropdown.js"></script>
</body>
</html>

