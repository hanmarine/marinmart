<?php
include('../db/session.php');
?>
<!DOCTYPE html>
<!-- View Tables -->
<html>
<head>
  <title>Tables | Marinmart</title>
  <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../assets/css/navbar.css?v=<?php echo time(); ?>">
</head>
<body>
  <header class="header">
      <?php include '../functions/navbar.php' ?>
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
  
  <footer id="footer"></footer>
  <script src="../assets/js/mobileNav.js"></script>
  <script src="../assets/js/footer.js"></script>
  <script src="../assets/js/dropdown.js"></script>
  <script src="../assets/js/table.js"></script>
</body>
</html>

