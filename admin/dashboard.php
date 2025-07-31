<?php
include('../db/session.php');
require_once '../functions/fetch_dashboard.php';
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Marinmart</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/navbar.css?v=<?php echo time(); ?>">
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <?php include '../functions/loadCharts.php'; ?>
</head>
<body>
    <header class="header">
        <?php include '../functions/navbar.php' ?>
    </header>

    <main class="main">
        <div class="total-card">
            <img width="40" height="40" src="https://img.icons8.com/ios-filled/100/246af3/box--v1.png" alt="box--v1"/>
            <br>TOTAL SUPPLIERS:<br>
            <div class="total-count"><?php echo $totalSuppliers; ?></div>
        </div>
        <div class="total-card">
            <img width="40" height="40" src="https://img.icons8.com/ios-filled/40/246af3/fast-moving-consumer-goods.png" alt="fast-moving-consumer-goods"/>    
            <br>TOTAL PRODUCTS:<br>
            <div class="total-count"><?php echo $totalProducts; ?></div>
        </div>
        <div class="total-card">
            <img width="40" height="40" src="https://img.icons8.com/material/40/246af3/diversity.png" alt="diversity"/>    
            <br>TOTAL CATEGORIES:<br>
            <div class="total-count"><?php echo $totalCategories; ?></div>
        </div>
        <div class="chart-card" id="chart1">Loading chart...</div>
        <div class="chart-card" id="chart2">Loading chart...</div>
    </main>

    <footer id="footer"></footer>
    <script src="../assets/js/dropdown.js"></script>
    <script src="../assets/js/mobileNav.js"></script>
    <script src="../assets/js/footer.js"></script>
</body>
</html>

