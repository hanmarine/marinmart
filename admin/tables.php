<?php
include('../db/admin_manager.php');
?>
<!DOCTYPE html>
<!-- View Tables -->
<html>
<head>
  <title>Tables | Marinmart</title>
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

  <script>
    let currentTable = 'product';

    function setTable(tableName) {
      currentTable = tableName;
      loadTable(currentTable);
    }

    function loadTable(tableName, page = 1, searchQuery = '') {
      fetch(`fetch_table.php?table=${tableName}&page=${page}&search=${searchQuery}`)
      .then(response => response.text())
      .then(data => {
        document.getElementById('table-container').innerHTML = data;
      });
    }

    function loadPage(page) {
      const searchQuery = document.getElementById('searchInput').value;
      loadTable(currentTable, page, searchQuery);
    }

    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      currentTable = params.get('table') || 'product';
      const page = params.get('page') || 1;
      loadTable(currentTable, page);
    });

    document.getElementById('searchInput').addEventListener('input', () => {
      loadTable(currentTable, 1, document.getElementById('searchInput').value);
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

