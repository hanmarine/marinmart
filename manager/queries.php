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
        <h1 class="logo">marinmart</h1>
        <nav class="nav">
          <a href="dashboard.php">DASHBOARD</a>
          <a href="tables.php">TABLES</a>
          <a href="queries.php">QUERIES</a>
        </nav>
        <div class="profile">
            <a href="#" class="user-dropdown-toggle">
                <img src="../assets/user.png" alt="profile icon" height="40px" width="40px">
            </a>
            <div class="user-dropdown">
                <p>Hi, <?php echo $username; ?> (manager)</p>
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

  <script src="../assets/js/dropdown.js"></script>
  <script src="../assets/js/query.js"></script>
</body>
</html>