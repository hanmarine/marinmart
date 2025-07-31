<?php
include('../db/session.php');
?>
<!DOCTYPE html>
<!-- View Queries -->
<html>
<head>
  <title>Queries | Marinmart</title>
  <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
  <header class="header">
      <?php include '../functions/navbar.php' ?>
  </header>
  <main class="main">
    <div class="container">
      <div class="buttons">
        <button onclick="loadQuery('complete')">Complete Product Query</bu>
        <button onclick="loadQuery('product_supplier')">Product-Supplier Query</b>  
        <button onclick="loadQuery('product_category')">Product-Category Query</b>
      </div>
      <div id="query-container"></div>
    </div>
  </main>

  <footer>
        © 2024 Marinmart, All rights reserved.
  </footer>

  <script src="../assets/js/query.js"></script>
  <script src="../assets/js/dropdown.js"></script>
</body>
</html>