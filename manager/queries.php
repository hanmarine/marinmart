<?php
include('../db/admin_manager.php');
?>
<!DOCTYPE html>
<!-- View Queries -->
<html>
<head>
  <title>Queries | Marinmart</title>
  <link rel="stylesheet" href="../assets/admin.css?v=<?php echo time(); ?>">
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

  <script>
    function loadQuery(queryType, page = 1) {
      fetch(`fetch_query.php?type=${queryType}&page=${page}`)
        .then(response => response.text())
        .then(data => {
          document.getElementById('query-container').innerHTML = data;
        });
    }

    function loadPage(queryType, page) {
      loadQuery(queryType, page);
    }

    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      const queryType = params.get('type') || 'complete';
      const page = params.get('page') || 1;
      loadQuery(queryType, page);
    });

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
  </script>
</body>
</html>