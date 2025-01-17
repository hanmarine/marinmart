<?php
// The addition process
include('../db/admin_manager.php');

function generateCategoryID($conn) {
  $stmt = $conn->prepare("SHOW TABLE STATUS LIKE 'category'");
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['Auto_increment'];
}

function generateSupplierID($conn) {
  $stmt = $conn->prepare("SHOW TABLE STATUS LIKE 'supplier'");
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['Auto_increment'];
}

function generateProductID($conn) {
  $stmt = $conn->prepare("SHOW TABLE STATUS LIKE 'product'");
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['Auto_increment'];
}

function generateUserID($conn) {
  $stmt = $conn->prepare("SHOW TABLE STATUS LIKE 'users'");
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['Auto_increment'];
}

function addProduct($conn, $productID, $productName, $categoryID, $supplierID, $price) {
  $checkProductQuery = "SELECT * FROM product WHERE ProductName = ?";
  $stmt = $conn->prepare($checkProductQuery);
  $stmt->bind_param("s", $productName);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if ($result->num_rows > 0) {
    echo "<script>alert('[ERROR] This product already exists.'); window.location.href = '../admin/add_form.php';</script>";
    exit();
  }

  $stmt = $conn->prepare("INSERT INTO product (ProductID, ProductName, CategoryID, SupplierID, Price) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("isiid", $productID, $productName, $categoryID, $supplierID, $price);
  $stmt->execute();
  $stmt->close();
}

function addSupplier($conn, $supplierID, $contactPerson, $supplierName, $contactNumber, $username, $password) {
  // Check for duplicate supplier name
  $checkSupplierNameQuery = "SELECT * FROM supplier WHERE SupplierName = ?";
  $stmt = $conn->prepare($checkSupplierNameQuery);
  $stmt->bind_param("s", $supplierName);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if ($result->num_rows > 0) {
    echo "<script>alert('[ERROR] This company already exists.'); window.location.href = '../admin/add_form.php';</script>";
    exit();
  }

  // Check for duplicate contact person
  $checkContactPersonQuery = "SELECT * FROM supplier WHERE ContactPerson = ?";
  $stmt = $conn->prepare($checkContactPersonQuery);
  $stmt->bind_param("s", $contactPerson);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if ($result->num_rows > 0) {
    echo "<script>alert('[ERROR] This person already exists for another supplier.'); window.location.href = '../admin/add_form.php';</script>";
    exit();
  }

  // Check for duplicate contact number
  $checkContactNumberQuery = "SELECT * FROM supplier WHERE ContactNumber = ?";
  $stmt = $conn->prepare($checkContactNumberQuery);
  $stmt->bind_param("s", $contactNumber);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if ($result->num_rows > 0) {
    echo "<script>alert('[ERROR] This number already exists for another supplier.'); window.location.href = '../admin/add_form.php';</script>";
    exit();
  }

  // Check for duplicate username **before** inserting into users table
  $checkUsernameQuery = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($checkUsernameQuery);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if ($result->num_rows > 0) {
    echo "<script>alert('[ERROR] This username already exists.'); window.location.href = '../admin/add_form.php';</script>";
    exit();
  }

  // If no duplicates, proceed with insertion
  $stmt = $conn->prepare("INSERT INTO users (user_id, username, password) VALUES (NULL, ?, ?)");  // Use NULL for auto-increment
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();

  // **Get the newly inserted user ID**
  $userID = $stmt->insert_id;
  $stmt->close();

  // **Then, insert into supplier table with the retrieved user ID**
  $stmt = $conn->prepare("INSERT INTO supplier (SupplierID, ContactPerson, SupplierName, ContactNumber, user_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $supplierID, $contactPerson, $supplierName, $contactNumber, $userID);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Supplier added successfully.'); window.location.href='{$_SERVER['PHP_SELF']}';</script>";
}

function addCategory($conn, $categoryName) {
  $checkCategoryQuery = "SELECT * FROM category WHERE CategoryName = ?";
  $stmt = $conn->prepare($checkCategoryQuery);
  $stmt->bind_param("s", $categoryName);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if ($result->num_rows > 0) {
    echo "<script>alert('[ERROR] This category already exists.'); window.location.href = '../admin/add_form.php';</script>";
    exit();
  }

  $categoryID = generateCategoryID($conn);
  $stmt = $conn->prepare("INSERT INTO category (CategoryID, CategoryName) VALUES (?, ?)");
  $stmt->bind_param("is", $categoryID, $categoryName);
  $stmt->execute();
  $stmt->close();
}

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
  <link rel="stylesheet" href="../assets/admin.css?v=<?php echo time(); ?>">
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
            
  <script>
    function showProductForm() {
      document.getElementById("productForm").style.display = "block";
      document.getElementById("supplierForm").style.display = "none";
      document.getElementById("categoryForm").style.display = "none";
    }

    function showSupplierForm() {
      document.getElementById("productForm").style.display = "none";
      document.getElementById("supplierForm").style.display = "block";
      document.getElementById("categoryForm").style.display = "none";
    }

    function showCategoryForm() {
      document.getElementById("productForm").style.display = "none";
      document.getElementById("supplierForm").style.display = "none";
      document.getElementById("categoryForm").style.display = "block";
    }

    // Dropdown Toggle
    const userDropdownToggle = document.querySelector('.user-dropdown-toggle');
    const userDropdown = document.querySelector('.user-dropdown');

    userDropdownToggle.addEventListener('click', function() {
        userDropdownToggle.classList.toggle('active');
    });

    // Close the dropdown if clicked outside
    document.addEventListener('click', function(event) {
        if (!userDropdownToggle.contains(event.target) && userDropdown.classList.contains('active')) {
        userDropdownToggle.classList.remove('active');
        }
    });

    // Password Toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      togglePassword.src = 'https://img.icons8.com/pastel-glyph/64/26344b/surprise--v2.png'; // Change image source when password is visible
    } else {
      passwordInput.type = 'password';
      togglePassword.src = 'https://img.icons8.com/ios/50/26344b/closed-eye.png'; // Change image source when password is hidden
    }
    });
  </script>
</body>
</html>