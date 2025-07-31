<?php
include('../db/session.php');
?>
<!DOCTYPE html>
<!-- View Tables -->
<html>
<head>
  <title>Tables | Marinmart</title>
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
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search..." onkeyup="searchTable()">
    </div>
    <div class="container">
      <div class="buttons">
        <button onclick="setTable('product')">Product</button>
        <button onclick="setTable('supplier')">Supplier</button>  
        <button onclick="setTable('category')">Category</button>
      </div>
      <div id="table-container"></div>
      <div id="pagination"></div>
    </div>
  </main>
  
  <footer>
      © 2024 Marinmart, All rights reserved.
  </footer>
      
  <script src="../assets/js/table.js"></script>
  <script src="../assets/js/dropdown.js"></script>
</body>
</html>

