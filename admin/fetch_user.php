<?php
include('../db/connection.php');

$tableName = $_GET['table']; 
$page = $_GET['page'] ?? 1; 
$searchQuery = $_GET['search'] ?? ''; 
$limit = 10; 
$offset = ($page - 1) * $limit; 

$userTypeFilter = "";
if ($tableName === "user_supplier") {
  $userTypeFilter = " WHERE u.role = 'user'";
}

$sql = "SELECT u.user_id, u.username, u.password, s.SupplierID, s.SupplierName, s.ContactPerson, s.ContactNumber 
        FROM users u
        LEFT JOIN supplier s ON u.user_id = s.user_id" . $userTypeFilter;

if ($searchQuery) {
  $sql .= " AND (s.SupplierName LIKE '%$searchQuery%' OR u.username LIKE '%$searchQuery%')";
}

$sql .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

$totalRecords = 0;
if ($result) {
  $totalRecords = mysqli_num_rows(mysqli_query($conn, "SELECT COUNT(*) FROM users u LEFT JOIN supplier s ON u.user_id = s.user_id" . $userTypeFilter));
}

$totalPages = ceil($totalRecords / $limit);

$data = array(
  "data" => array(),
  "pagination" => array(
    "total_pages" => $totalPages,
    "current_page" => $page
  )
);

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $data["data"][] = $row;
  }
} else {
  $data["data"] = [];
}

echo json_encode($data);

mysqli_close($conn);
?>