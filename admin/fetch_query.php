<?php
// Fetching queries
include('../db/connection.php');

$query_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';
$page = isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT) ? $_GET['page'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

switch ($query_type) {
    case 'complete':
        $query = "SELECT p.ProductName, c.CategoryName, s.SupplierName, p.Price
                    FROM product p
                    JOIN category c ON p.CategoryID = c.CategoryID
                    JOIN supplier s ON p.SupplierID = s.SupplierID
                    ORDER BY p.ProductName";
        break;
    case 'product_supplier':
        $query = "SELECT s.SupplierName, p.ProductName, p.Price
                    FROM product p
                    JOIN supplier s ON p.SupplierID = s.SupplierID
                    ORDER BY s.SupplierName";
        break;
    case 'product_category':
        $query = "SELECT c.CategoryName, p.ProductName, p.Price
                    FROM product p
                    JOIN category c ON p.CategoryID = c.CategoryID
                    ORDER BY c.CategoryName";
        break;
    default:
        echo "Invalid query type";
        exit;
}

$paginated_query = $query . " LIMIT ?, ?";
$stmt = $conn->prepare($paginated_query);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

if ($query_type == 'complete') {
    echo "<h3>COMPLETE PRODUCT QUERY</h3>";
} elseif ($query_type == 'product_supplier') {
    echo "<h3>PRODUCT-SUPPLIER QUERY</h3>";
} else {
    echo "<h3>PRODUCT-CATEGORY QUERY</h3>";
}

if ($result->num_rows > 0) {
    echo "<table>";
    $row = $result->fetch_assoc();
    echo "<tr>";
    foreach ($row as $key => $value) {
        $header = ucfirst(str_replace(['ID', 'Name'], [' ID', ' Name'], $key));
        echo "<th>$header</th>";
    }
    echo "</tr>";

    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            if($key == 'Price'){
                echo "<td>". formatPrice($value). "</td>";
            } else {
                echo "<td>$value</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";

    $count_query = "SELECT COUNT(*) as total FROM ($query) as count_table";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_row = $count_result->fetch_assoc();
    $total_pages = ceil($total_row['total'] / $limit);

    echo "<div class='pages'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<button onclick=\"loadPage('$query_type', $i)\">$i</button> ";
    }
    echo "</div>";
} else {
    echo "0 results";
}

$stmt->close();
$conn->close();
?>


