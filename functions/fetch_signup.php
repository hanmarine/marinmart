<?php
include('../db/connection.php'); // Assuming your connection file path

// Extract user data from POST request with input validation
$firstName = htmlspecialchars(trim($_POST['first-name']));
$lastName = htmlspecialchars(trim($_POST['last-name']));
$company = htmlspecialchars(trim($_POST['company']));
$username = htmlspecialchars(trim($_POST['username']));
$contactNumber = htmlspecialchars(trim($_POST['contact-number']));
$password = htmlspecialchars(trim($_POST['password']));
$confirmPassword = htmlspecialchars(trim($_POST['confirm-password']));

// Basic validation
if (empty($firstName) || empty($lastName) || empty($company) || empty($username) || empty($contactNumber) || empty($password) || empty($confirmPassword)) {
    echo "<script>alert('Please fill out all required fields.'); window.location.href='login.php';</script>";
    exit;
}

if ($password !== $confirmPassword) {
    echo "<script>alert('Passwords do not match.'); window.location.href='login.php';</script>";
    exit;
}

// Check for duplicate username
$usernameCheckQuery = "SELECT COUNT(*) AS username_count FROM users WHERE username = ?";
$stmt = $conn->prepare($usernameCheckQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$usernameResult = $stmt->get_result();
$usernameData = $usernameResult->fetch_assoc();

if ($usernameData['username_count'] > 0) {
    echo "<script>alert('[ERROR] Username already exists.'); window.location.href='signup.php';</script>";
    exit;
}
$stmt->close();

// Duplicate contact person
$supplierCP = "SELECT COUNT(*) AS duplicate_count FROM supplier WHERE ContactPerson = ?";
$stmt = $conn->prepare($supplierCP);
$contactPerson = "$firstName $lastName";
$stmt->bind_param("s", $contactPerson);
$stmt->execute();
$CPResult = $stmt->get_result();
$CPData = $CPResult->fetch_assoc();

if ($CPData['duplicate_count'] > 0) {
    echo "<script>alert('[ERROR] This person already exists.'); window.location.href='./signup.php';</script>";
    exit;
}
$stmt->close();

// Duplicate company
$supplierSN = "SELECT COUNT(*) AS duplicate_count FROM supplier WHERE SupplierName = ?";
$stmt = $conn->prepare($supplierSN);
$stmt->bind_param("s", $company);
$stmt->execute();
$SNResult = $stmt->get_result();
$SNData = $SNResult->fetch_assoc();

if ($SNData['duplicate_count'] > 0) {
    echo "<script>alert('[ERROR] This company already exists.'); window.location.href='./signup.php';</script>";
    exit;
}
$stmt->close();

// Duplicate number
$supplierNo = "SELECT COUNT(*) AS duplicate_count FROM supplier WHERE ContactNumber = ?";
$stmt = $conn->prepare($supplierNo);
$stmt->bind_param("s", $contactNumber);
$stmt->execute();
$NumberResult = $stmt->get_result();
$NumberData = $NumberResult->fetch_assoc();

if ($NumberData['duplicate_count'] > 0) {
    echo "<script>alert('[ERROR] This number already exists.'); window.location.href='./signup.php';</script>";
    exit;
}
$stmt->close();

// Insert user data
$userInsertQuery = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($userInsertQuery);
$stmt->bind_param("ss", $username, $password);
$userResult = $stmt->execute();

if (!$userResult) {
    echo "Error registering user: " . $stmt->error;
    exit;
}

// Get the newly inserted user ID (assuming auto-increment on `users.id`)
$user_id = $stmt->insert_id;
$stmt->close();

// Insert supplier data (associated with the user) into `supplier` table
$supplierInsertQuery = "INSERT INTO supplier (ContactPerson, SupplierName, ContactNumber, user_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($supplierInsertQuery);
$stmt->bind_param("sssi", $contactPerson, $company, $contactNumber, $user_id);
$supplierResult = $stmt->execute();

if (!$supplierResult) {
    echo "Error registering supplier: " . $stmt->error;
    exit;
}
$stmt->close();

// Success message
echo "<script>alert('Registration successful, you are now redirecting to login page.'); window.location.href='login.php';</script>"; // Redirect to success page

// Close connection
$conn->close();
?>