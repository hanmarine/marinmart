<?php
// The update form
include('../db/session.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['table']) && isset($_POST['id'])) {
        $table = htmlspecialchars(trim($_POST['table']));
        $id = htmlspecialchars(trim($_POST['id']));
        $columns = array();
        $values = array();

        foreach ($_POST as $key => $value) {
            if ($key != 'table' && $key != 'id') {
                $columns[] = "`$key`=?";
                $values[] = htmlspecialchars(trim($value));
            }
        }

        $values[] = $id;
        $sql = "UPDATE `". $table. "` SET ". implode(", ", $columns) . " WHERE `". $table. "ID`=?";
        $stmt = mysqli_prepare($conn, $sql);
        $types = str_repeat('s', count($values) - 1) . 'i';
        mysqli_stmt_bind_param($stmt, $types, ...$values);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Record updated successfully.');</script>";
        } else {
            echo "<script>alert('Error updating record: ". mysqli_error($conn). "'); window.history.back();</script>";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    if (isset($_GET['table']) && isset($_GET['id'])) {
        $table = htmlspecialchars(trim($_GET['table']));
        $id = htmlspecialchars(trim($_GET['id']));

        $sql = "SELECT * FROM `". $table. "` WHERE `". $table. "ID` =?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $data = json_encode($row);
            } else {
                echo "<script>alert('Record not found.'); window.history.back();</script>";
            }
            mysqli_free_result($result);
        } else {
            echo "<script>alert('Error fetching record: ". mysqli_error($conn). "'); window.history.back();</script>";
        }
        mysqli_stmt_close($stmt);

        $categories = [];
        $categoryQuery = "SELECT CategoryID, CategoryName FROM category ORDER BY CategoryName";
        $categoryResult = mysqli_query($conn, $categoryQuery);
        while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
            $categories[$categoryRow['CategoryID']] = $categoryRow['CategoryName'];
        }
        mysqli_free_result($categoryResult);

        $suppliers = [];
        $supplierQuery = "SELECT SupplierID, SupplierName FROM supplier ORDER BY SupplierName";
        $supplierResult = mysqli_query($conn, $supplierQuery);
        while ($supplierRow = mysqli_fetch_assoc($supplierResult)) {
            $suppliers[$supplierRow['SupplierID']] = $supplierRow['SupplierName'];
        }
        mysqli_free_result($supplierResult);
    } else {
        echo "<script>alert('Invalid request.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Record</title>
  <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../assets/css/navbar.css?v=<?php echo time(); ?>">
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
    <?php if (isset($data)) : ?>
      <h2 id="update_heading">Update <?php echo ucfirst($table); ?></h2>
      <div id="update_form">
        <form action="../functions/update.php" method="post">
          <input type="hidden" name="table" value="<?php echo $table; ?>">
          <input type="hidden" name="id" value="<?php echo $id; ?>">


          <?php
          $row = json_decode($data, true);
          foreach ($row as $key => $value) {
            $primaryKeyMap = array(
              "product" => "ProductID",
              "category" => "CategoryID",
              "supplier" => "SupplierID"
            );


            if ($key != $primaryKeyMap[$table]) {
              $label = ucfirst($key);
              if ($key == 'ProductName' OR $key == 'CategoryName') {
                $label = ucwords(str_replace('Name', ' Name', $key));
              } elseif ($key == 'ContactPerson') {
                $label = ucwords(str_replace("ContactPerson", "Supplier's Name", $key));
              } elseif ($key == 'SupplierName') {
                $label = ucwords(str_replace('SupplierName', 'Company', $key));
              } elseif ($key == 'ContactNumber') {
                $label = ucwords(str_replace('Number', ' Number', $key));
              } elseif ($key == 'CategoryID') {
                $label = ucwords(str_replace('CategoryID', 'Category', $key));
              } elseif ($key == 'SupplierID') {
                $label = ucwords(str_replace('SupplierID', 'Supplier', $key));
              } elseif ($key == 'user_id') {
                $label = ucwords(str_replace('user_id', 'User ID', $key));
              }


              if ($key == 'user_id') {
                echo "<label for='$key'>$label: </label>";
                echo "<span id='$key'> $value</span>";
                echo "<input type='hidden' name='$key' value='$value'>";
                echo "<br>";
              } else {
                echo "<label for='$key'>$label: </label>";
                if ($key == 'Price') {
                  echo "<input type='number' name='$key' id='$key' value='$value' step='.01' required>";
                } elseif ($key == 'CategoryID') {
                  echo "<select name='$key' id='$key' required>";
                  foreach ($categories as $catID => $catName) {
                    $selected = $value == $catID ? "selected" : "";
                    echo "<option value='$catID' $selected>$catName</option>";
                  }
                  echo "</select>";
                } elseif ($key == 'SupplierID') {
                  echo "<select name='$key' id='$key' required>";
                  foreach ($suppliers as $supID => $supName) {
                    $selected = $value == $supID ? "selected" : "";
                    echo "<option value='$supID' $selected>$supName</option>";
                  }
                  echo "</select>";
                } elseif ($key == 'ContactNumber') {
                  echo "<input type='number' name='$key' id='$key' value='$value' size='20' required>";
                  echo "<br>";
                } else {
                  echo "<input type='text' name='$key' id='$key' value='$value' size='50' required>";
                }
                echo "<br>";
              }
            } else {
              echo "<label for='$key'>". ucfirst(str_replace('ID', ' ID', $key)).": </label>";
              echo "<span id='$key'> $value</span>";
              echo "<br>";
            }
          }


          // Fetch user details
          if ($table == 'supplier' && isset($row['user_id'])) {
            $userID = $row['user_id'];
            $userQuery = "SELECT username, password FROM users WHERE user_id = ?";
            $userStmt = mysqli_prepare($conn, $userQuery);
            mysqli_stmt_bind_param($userStmt, 'i', $userID);


            if (mysqli_stmt_execute($userStmt)) {
              $userResult = mysqli_stmt_get_result($userStmt);
              $userRow = mysqli_fetch_assoc($userResult);
              if ($userRow) {
                echo "<label for='username'>Username: </label>";
                echo "<input type='text' name='username' id='username' value='{$userRow['username']}' required>";
                echo "<br>";
                echo "<label for='password'>Password: </label>";
                echo "<input type='password' name='password' id='password' value='{$userRow['password']}' required>";
                echo "<img src='https://img.icons8.com/ios/50/26344b/closed-eye.png' alt='Show Password' id='togglePassword' class='togglePassword'>";
                echo "<br>";
              }
              mysqli_free_result($userResult);
            }
            mysqli_stmt_close($userStmt);
          }
          ?>
          <br>
          <input type="submit" id="submit_button" value="Submit">
          <button type="button" onclick="javascript:history.back()">Back</button>
        </form>
      </div>
    <?php endif; ?>
  </main>

  <footer id="footer"></footer>
  <script src="../assets/js/mobileNav.js"></script>
  <script src="../assets/js/footer.js"></script>
  <script src="../assets/js/dropdown.js"></script>
  <script src="../assets/js/passwordToggle.js"></script>
</body>
</html>



