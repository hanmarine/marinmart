<?php 
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
?>