<?php
include('../db/userlog.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Marinmart</title>
    <link rel="stylesheet" href="../assets/css/product_table.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/navbar.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="header">
        <?php include '../functions/navbar.php' ?>
    </header>

    <main class="main">
        <h2 style="text-align: center; color: #246af3;">CATEGORIES</h2>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search..." onkeyup="searchTable()">
        </div>
        <div id="table-container"></div>
    </main>

    <footer id="footer"></footer>
    <script src="../assets/js/mobileNav.js"></script>
    <script src="../assets/js/footer.js"></script>
    <script src="../assets/js/dropdown.js"></script>
    <script src="../assets/js/category.js"></script>
</body>
</html>