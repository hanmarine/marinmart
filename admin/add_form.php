<?php
// The addition process
include('../db/session.php');
require_once '../functions/add_form_functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    if (isset($_POST['product_submit'])) {
      $productID = $_POST['productID'];
      $productName = $_POST['productName'];
      $categoryID = $_POST['categoryID'];
      $supplierID = $_POST['supplierID'];
      $price = $_POST['price'];

      // Check if all fields are provided
      if (!empty($productName) && !empty($categoryID) && !empty($supplierID) && !empty($price)) {
        addProduct($conn, $productID, $productName, $categoryID, $supplierID, $price);
        echo "<script>alert('Product added successfully.'); window.location.href='{$_SERVER['PHP_SELF']}';</script>";
      } else {
        echo "<script>alert('All fields are required.');</script>";
      }
    } elseif (isset($_POST['supplier_submit'])) {
      $supplierID = $_POST['supplierID'];
      $contactPerson = $_POST['contactPerson'];
      $supplierName = $_POST['supplierName'];
      $contactNumber = $_POST['contactNumber'];
      $username = $_POST['username'];
      $password = $_POST['password'];

      // Check if all fields are provided
      if (!empty($contactPerson) && !empty($supplierName) && !empty($contactNumber) && !empty($username) && !empty($password)) {
        addSupplier($conn, $supplierID, $contactPerson, $supplierName, $contactNumber, $username, $password);
        echo "<script>alert('Supplier added successfully.'); window.location.href='{$_SERVER['PHP_SELF']}';</script>";
      } else {
        echo "<script>alert('All fields are required.');</script>";
      }
    } elseif (isset($_POST['add_category'])) {
      $categoryName = $_POST['categoryName'];

      // Check if category name is provided
      if (!empty($categoryName)) {
        addCategory($conn, $categoryName);
        echo "<script>alert('Category added successfully.'); window.location.href='{$_SERVER['PHP_SELF']}';</script>";
      } else {
        echo "<script>alert('Category name is required.');</script>";
      }
    }
  } catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
      echo "<script>alert('Duplicate entry: {$e->getMessage()}');</script>";
    } else {
      echo "<script>alert('Error: {$e->getMessage()}');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create | Marinmart</title>
  <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
  <header class="header">
    <h1 class="logo">marinmart</h1>
    <nav class="nav">
      <a href="dashboard.php">DASHBOARD</a>
      <a href="add_form.php">CREATE</a>
      <a href="tables.php">TABLES</a>
      <a href="queries.php">QUERIES</a>
    </nav>
    <div class="profile">
      <a href="#" class="user-dropdown-toggle">
        <img src="../assets/user.png" alt="profile icon" height="40px" width="40px">
      </a>
      <div class="user-dropdown">
        <p>Hi, <?php echo $username; ?> (admin)</p>
        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?logout=true'; ?>">Logout</a>
        <?php
        if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
          session_destroy();
          header('Location: ../index.php');
          exit;
        }
        ?>
      </div>
    </div>
  </header>
  <main class="main">
    <div class="container">
      <div class="buttons">
        <button onclick="showProductForm()">Product</button>
        <button onclick="showSupplierForm()">Supplier</button>
        <button onclick="showCategoryForm()">Category</button>
      </div>
      <div class="form">
        <!-- Category Form -->
        <form id="categoryForm" method="post" action="" style="display: none;">
          <h3>CREATE CATEGORY:</h3>
          <label for="categoryID">Category ID:</label>
          <input type="text" id="categoryID" name="categoryID" readonly value="<?php echo generateCategoryID($conn);?>">
          <br><br>
          <label for="categoryName">Category Name:</label>
          <input type="text" id="categoryName" name="categoryName" placeholder="e.g. Fisheries" required><br>
          <br>
          <button type="submit" name="add_category">Submit</button>
          <button type="reset">Reset</button>
        </form>

        <!-- Product Form -->
        <form id="productForm" method="post" action="">
          <h3>CREATE PRODUCT:</h3>
          <label for="productID">Product ID:</label>
          <input type="text" id="productID" name="productID" readonly value="<?php echo generateProductID($conn);?>">
          <br><br>
          <label for="productName">Product Name:</label>
          <input type="text" id="productName" name="productName" placeholder="e.g. Pizza" required><br>
          <label for="categoryID">Category:</label>
          <select id="categoryID" name="categoryID" required>
            <?php
            $categoryQuery = "SELECT * FROM category ORDER BY CategoryName ASC";
            $categoryResult = $conn->query($categoryQuery);
            while ($row = $categoryResult->fetch_assoc()) {
              echo "<option value='{$row['CategoryID']}'>{$row['CategoryName']}</option>";
            }
            ?>
          </select><br>
          <label for="supplierID">Supplier:</label>
          <select id="supplierID" name="supplierID" required>
            <?php
            $supplierQuery = "SELECT * FROM supplier ORDER BY SupplierName ASC";
            $supplierResult = $conn->query($supplierQuery);
            while ($row = $supplierResult->fetch_assoc()) {
              echo "<option value='{$row['SupplierID']}'>{$row['SupplierName']}</option>";
            }
            ?>
          </select><br>
          <label for="price">Price:</label>
          <input type="number" id="price" name="price" step=".01" placeholder="e.g. 1234.56" required><br>
          <br>
          <button type="submit" name="product_submit">Submit</button>
          <button type="reset">Reset</button>
        </form>

        <!-- Supplier Form -->
        <form id="supplierForm" method="post" action="" style="display: none;">
          <h3>CREATE SUPPLIER:</h3>
          <label for="supplierID">Supplier's ID:</label>
          <input type="text" id="supplierID" name="supplierID" readonly value="<?php echo generateSupplierID($conn);?>">
          <br>
          <label for="supplierID">User ID:</label>
          <input type="text" id="userID" name="userID" readonly value="<?php echo generateUserID($conn);?>">
          <br>
          <label for="contactPerson">Supplier's Name:</label>
          <input type="text" id="contactPerson" name="contactPerson" placeholder="e.g. John Doe" required><br>
          <label for="supplierName">Company:</label>
          <input type="text" id="supplierName" name="supplierName" placeholder="e.g. Wonka Industries" required><br>
          <label for="contactNumber">Contact Number:</label>
          <input type="tel" id="contactNumber" name="contactNumber" placeholder="e.g. 1234567890" pattern="[0-9]{10}" maxlength="10" required><br>
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" placeholder="e.g. johndoe" required><br>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" placeholder="e.g. yourpassword" required>
          <img src='https://img.icons8.com/ios/50/26344b/closed-eye.png' alt='Show Password' id='togglePassword' class='togglePassword'>
          <br>
          <button type="submit" name="supplier_submit">Submit</button>
          <button type="reset">Reset</button>
        </form>
      </div>
    </div>
  </main>

  <footer>
      © 2024 Marinmart, All rights reserved.
  </footer>
  
  <script src="../assets/js/addForms.js"></script>
  <script src="../assets/js/dropdown.js"></script>
  <script src="../assets/js/passwordToggle.js"></script>
</body>
</html>