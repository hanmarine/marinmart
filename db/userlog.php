<?php 
include('connection.php');
include('session.php');

$loggedInUserID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user ID is available
if (!$loggedInUserID) {
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