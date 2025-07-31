<?php 
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
?>