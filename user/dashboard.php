<?php
include('../db/userlog.php');

// Initialize total counts
$totalSuppliers = 0;
$totalCategories = 0;
$totalProducts = 0;

// Queries to fetch data
$totalSuppliersQuery = "SELECT COUNT(*) AS total_suppliers FROM supplier";
$totalCategoriesQuery = "SELECT COUNT(*) AS total_categories FROM category";
$totalProductsQuery = "SELECT COUNT(*) AS total_products FROM product";

// Prepare and execute the total suppliers query
if ($stmt = mysqli_prepare($conn, $totalSuppliersQuery)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $totalSuppliers);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing total suppliers query: " . mysqli_error($conn);
    exit;
}

// Prepare and execute the total categories query
if ($stmt = mysqli_prepare($conn, $totalCategoriesQuery)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $totalCategories);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing total categories query: " . mysqli_error($conn);
    exit;
}

// Prepare and execute the total products query
if ($stmt = mysqli_prepare($conn, $totalProductsQuery)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $totalProducts);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing total products query: " . mysqli_error($conn);
    exit;
}

// Query to get top 5 categories and group the rest into 'Others'
$categoriesQuery = "
    SELECT CategoryName, COUNT(ProductID) AS product_count
    FROM category
    LEFT JOIN product ON category.CategoryID = product.CategoryID
    GROUP BY CategoryName
    ORDER BY product_count DESC
    LIMIT 5";

$otherCategoriesQuery = "
    SELECT COUNT(ProductID) AS product_count
    FROM category
    LEFT JOIN product ON category.CategoryID = product.CategoryID
    WHERE CategoryName NOT IN (SELECT CategoryName FROM ($categoriesQuery) AS top_categories)";

// Prepare and execute the categories query
if ($stmt = mysqli_prepare($conn, $categoriesQuery)) {
    mysqli_stmt_execute($stmt);
    $categoriesResult = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing categories query: " . mysqli_error($conn);
    exit;
}

// Prepare and execute the other categories query
if ($stmt = mysqli_prepare($conn, $otherCategoriesQuery)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $otherCategoriesCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing other categories query: " . mysqli_error($conn);
    exit;
}

// Query to get top 5 suppliers and group the rest into 'Others'
$suppliersQuery = "
    SELECT SupplierName, COUNT(ProductID) AS product_count
    FROM supplier
    LEFT JOIN product ON supplier.SupplierID = product.SupplierID
    GROUP BY SupplierName
    ORDER BY product_count DESC
    LIMIT 5";

$otherSuppliersQuery = "
    SELECT COUNT(ProductID) AS product_count
    FROM supplier
    LEFT JOIN product ON supplier.SupplierID = product.SupplierID
    WHERE SupplierName NOT IN (SELECT SupplierName FROM ($suppliersQuery) AS top_suppliers)";

// Prepare and execute the suppliers query
if ($stmt = mysqli_prepare($conn, $suppliersQuery)) {
    mysqli_stmt_execute($stmt);
    $suppliersResult = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing suppliers query: " . mysqli_error($conn);
    exit;
}

// Prepare and execute the other suppliers query
if ($stmt = mysqli_prepare($conn, $otherSuppliersQuery)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $otherSuppliersCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing other suppliers query: " . mysqli_error($conn);
    exit;
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Marinmart</title>
    <link rel="stylesheet" href="../assets/dashboard.css?v=<?php echo time(); ?>">
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var categoryData = google.visualization.arrayToDataTable([
                ['Category', 'Number'],
                <?php while ($row = mysqli_fetch_assoc($categoriesResult)) { ?>
                    ['<?php echo $row['CategoryName']; ?>', <?php echo $row['product_count']; ?>],
                <?php } ?>
                ['Others', <?php echo $otherCategoriesCount; ?>]
            ]);

            var supplierData = google.visualization.arrayToDataTable([
                ['Supplier', 'Number'],
                <?php while ($row = mysqli_fetch_assoc($suppliersResult)) { ?>
                    ['<?php echo $row['SupplierName']; ?>', <?php echo $row['product_count']; ?>],
                <?php } ?>
                ['Others', <?php echo $otherSuppliersCount; ?>]
            ]);

            var o1 = {
                title: 'TOP 5 CATEGORIES', 
                titleTextStyle: {
                    color: '#246af3' 
                },
                is3D: true,
                backgroundColor: 'transparent',
            };

            var o2 = {
                title: 'TOP 5 SUPPLIERS', 
                titleTextStyle: {
                    color: '#246af3' 
                },
                is3D: true,
                backgroundColor: 'transparent',
            };

            var chart1 = new google.visualization.PieChart(document.getElementById('chart1'));
            chart1.draw(categoryData, o1);

            var chart2 = new google.visualization.PieChart(document.getElementById('chart2'));
            chart2.draw(supplierData, o2);
        }
    </script>
</head>
<body>
    <header class="header">
        <h1 class="logo">marinmart</h1>
        <nav class="nav">
            <a href="dashboard.php">DASHBOARD</a>
            <a href="products.php">PRODUCTS</a>
            <a href="categories.php">CATEGORIES</a>
        </nav>
        <div class="profile">
            <a href="#" class="user-dropdown-toggle">
                <img src="../assets/user.png" alt="profile icon" height="40px" width="40px">
            </a>
            <div class="user-dropdown">
                <p>Hi, <?php echo $contactPerson ? $contactPerson : $username; ?> (user)</p>
                <a href="profile.php">Profile</a>
                <a href="settings.php">Settings</a>
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
    
    <footer>
        © 2024 Marinmart, All rights reserved.
    </footer>

    <script>
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

