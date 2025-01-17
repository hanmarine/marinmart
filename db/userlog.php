<?php 
include('../db/connection.php');
// Session handling to retrieve username (replace with your session handling logic)
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Get the logged-in user ID (assuming it's stored in a session variable)
$loggedInUserID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user ID is available
if (!$loggedInUserID) {
    // Redirect to login or handle the case where user ID is not found
    echo "Error: User ID not found in session.";
    exit;
}

// Query to fetch supplier details based on user ID
$supplierQuery = "SELECT ContactPerson FROM supplier WHERE user_id = $loggedInUserID";

$supplierResult = mysqli_query($conn, $supplierQuery);

if (!$supplierResult) {
    echo "Error fetching supplier data: " . mysqli_error($conn);
    exit;
}

$supplierData = mysqli_fetch_assoc($supplierResult);

// Extract contact person name (assuming it's in the 'ContactPerson' column)
$contactPerson = "";
if ($supplierData) {
    $contactPerson = $supplierData['ContactPerson'];
}
?>