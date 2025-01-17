<?php
include('../db/connection.php');


$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Validate page as integer
$limit = 10;
$start = ($page - 1) * $limit;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';


// Search functionality
$searchSql = '';
$params = [];
$types = '';


if (!empty($search)) {
    $searchSql = "WHERE CategoryName LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}


// Prepared statements for total and category data
$totalSql = "SELECT COUNT(*) as total FROM category $searchSql";
$totalStmt = $conn->prepare($totalSql);
if (!empty($params)) {
    $totalStmt->bind_param($types, ...$params);
}
$totalStmt->execute();
$totalStmt->bind_result($totalCategories);
$totalStmt->fetch();
$totalStmt->close();


$totalPages = ceil($totalCategories / $limit);


$categorySql = "SELECT CategoryID, CategoryName FROM category $searchSql LIMIT ?, ?";
$categoryStmt = $conn->prepare($categorySql);


$params[] = $start;
$params[] = $limit;
$types .= 'ii';


$categoryStmt->bind_param($types, ...$params);
$categoryStmt->execute();
$categoryStmt->bind_result($categoryID, $categoryName);


if (!$categoryStmt->execute()) {
    echo "Error fetching category data: " . mysqli_error($conn);
    exit;
}
?>


<div class="product_table">
    <table>
        <thead>
            <tr>
                <th>Category ID</th>
                <th>Category Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($categoryStmt->fetch()) : ?>
            <tr>
                <td><?php echo $categoryID; ?></td>
                <td><?php echo $categoryName; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


<div class="pages">
    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <button onclick="loadPage('category', <?php echo $i; ?>)"><?php echo $i; ?></button>
    <?php endfor; ?>
</div>


<?php
$categoryStmt->close();
mysqli_close($conn);
?>