<?php
include('../db/userlog.php');

// Fetch user information
$user_id = (int)$_SESSION['user_id']; // Validate user ID as integer

$stmt = mysqli_prepare($conn, "SELECT u.username, s.ContactPerson, s.ContactNumber FROM users u JOIN supplier s ON u.user_id = s.user_id WHERE u.user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind user ID as integer
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
  echo "Error fetching user data: " . mysqli_error($conn);
  exit;
}

$userInfo = mysqli_fetch_assoc($result);

$username = $userInfo['username'];
$contactPerson = $userInfo['ContactPerson'];
$contactNumber = $userInfo['ContactNumber'];

function formatPrice($price) {
  return '$' . number_format($price, 2);
}

function formatContactNumber($number) {
  return preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1) $2-$3", $number);
}

// Query to fetch supplier details based on validated user ID
$stmt = mysqli_prepare($conn, "SELECT SupplierID, ContactPerson, ContactNumber FROM supplier WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind user ID as integer
mysqli_stmt_execute($stmt);
$supplierResult = mysqli_stmt_get_result($stmt);

if (!$supplierResult) {
  echo "Error fetching supplier data: " . mysqli_error($conn);
  exit;
}

$supplierData = mysqli_fetch_assoc($supplierResult);

// Extract contact person name and phone number
$contactPerson = "";
$contactNumber = "";
$supplierID = null;
if ($supplierData) {
  $supplierID = $supplierData['SupplierID'];
  $contactPerson = $supplierData['ContactPerson'];
  $contactNumber = $supplierData['ContactNumber'];
}

// Assuming SupplierName is a column in the supplier table
$stmt = mysqli_prepare($conn, "SELECT SupplierName FROM supplier WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind user ID as integer
mysqli_stmt_execute($stmt);
$supplierNameResult = mysqli_stmt_get_result($stmt);
$supplierNameData = mysqli_fetch_assoc($supplierNameResult);

// Query to fetch user information (adjust based on your database schema)
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username); // Bind username as string
mysqli_stmt_execute($stmt);
$userInfoResult = mysqli_stmt_get_result($stmt);

if (!$userInfoResult) {
  echo "Error fetching data: " . mysqli_error($conn);
  exit;
}

// Fetch user data from results
$userInfo = mysqli_fetch_assoc($userInfoResult);

// Close prepared statements
mysqli_stmt_close($stmt);

// Close result sets (optional, can be done automatically by garbage collector)
mysqli_free_result($result);
mysqli_free_result($supplierResult);
mysqli_free_result($supplierNameResult);
mysqli_free_result($userInfoResult);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Marinmart</title>
    <link rel="stylesheet" href="../assets/css/profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/navbar.css?v=<?php echo time(); ?>">
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
                <p>@<?php echo $userInfo['username']; ?></p>
            </div>
        </div>

        <div class="info-card">
            <h2>Account Information</h2>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Information</th>
                </tr>
                <tr>
                    <td>Supplier's Name:</td>
                    <td><?php echo $contactPerson; ?></td>
                </tr>
                <tr>
                    <td>Company:</td>
                    <td><?php echo $supplierNameData['SupplierName']; ?></td>
                </tr>
                <tr>
                    <td>Contact Number:</td>
                    <td><?php echo formatContactNumber($contactNumber); ?></td>
                </tr>
            </table>
        </div>

        <div class="info-card">
            <h2>Products Supplied</h2>
            <table>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                </tr>
                <?php
                // Query to fetch products owned by the supplier
                $productQuery = "SELECT p.ProductID, p.ProductName, c.CategoryName, p.Price 
                                 FROM product p 
                                 JOIN category c ON p.CategoryID = c.CategoryID 
                                 WHERE p.SupplierID = $supplierID";
                $productResult = mysqli_query($conn, $productQuery);

                // Check if any products are found
                if (mysqli_num_rows($productResult) > 0) {
                    // Loop through each product and display information in a table row
                    while ($productData = mysqli_fetch_assoc($productResult)) {
                        echo "<tr>";
                        echo "<td>" . $productData['ProductID'] . "</td>";
                        echo "<td>" . $productData['ProductName'] . "</td>";
                        echo "<td>" . $productData['CategoryName'] . "</td>";
                        echo "<td>" . formatPrice($productData['Price']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // Display "No Products Found" message if no products are found
                    echo "<tr><td colspan='4'>No Products Found</td></tr>";
                }
                ?>
            </table>
        </div>
    </main>

    <footer id="footer"></footer>
    <script src="../assets/js/mobileNav.js"></script>
    <script src="../assets/js/footer.js"></script>
    <script src="../assets/js/dropdown.js"></script>
</body>
</html>