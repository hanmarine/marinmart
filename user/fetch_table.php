<?php
include('../db/connection.php');

$page = isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT) ? $_GET['page'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Search functionality
$searchSql = '';
$params = [];
$types = '';

if (!empty($search)) {
    $searchSql = "WHERE p.ProductName LIKE ? OR s.SupplierName LIKE ? OR c.CategoryName LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

// Prepared statements for total and product data
$totalSql = "SELECT COUNT(*) as total FROM product p
             INNER JOIN supplier s ON p.SupplierID = s.SupplierID
             INNER JOIN category c ON p.CategoryID = c.CategoryID
             $searchSql";
$totalStmt = $conn->prepare($totalSql);
if (!empty($params)) {
    $totalStmt->bind_param($types, ...$params);
}
$totalStmt->execute();
$totalStmt->bind_result($totalProducts);
$totalStmt->fetch();
$totalStmt->close();

$totalPages = ceil($totalProducts / $limit);

$productSql = "SELECT p.ProductID, p.ProductName, s.SupplierName, c.CategoryName, p.Price
               FROM product p
               INNER JOIN supplier s ON p.SupplierID = s.SupplierID
               INNER JOIN category c ON p.CategoryID = c.CategoryID
               $searchSql
               LIMIT ?, ?";
$productStmt = $conn->prepare($productSql);

$params[] = $start;
$params[] = $limit;
$types .= 'ii';

$productStmt->bind_param($types, ...$params);
$productStmt->execute();
$productStmt->bind_result($productID, $productName, $supplierName, $categoryName, $price);

if (!$productStmt->execute()) {
    echo "Error fetching product data: " . mysqli_error($conn);
    exit;
}
?>

<div class="product_table">
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Supplier</th>
                <th>Category</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($productStmt->fetch()) : ?>
            <tr>
                <td><?php echo $productID; ?></td>
                <td><?php echo $productName; ?></td>
                <td><?php echo $supplierName; ?></td>
                <td><?php echo $categoryName; ?></td>
                <td><?php echo formatPrice($price); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="pages">
    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <button onclick="loadPage('product', <?php echo $i; ?>)"><?php echo $i; ?></button>
    <?php endfor; ?>
</div>

<?php
$productStmt->close();
mysqli_close($conn);
?>