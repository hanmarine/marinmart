<?php
include('../db/connection.php');

$page = isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT) ? $_GET['page'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

function formatPrice($price) {
  return '$' . number_format($price, 2);
}

function formatContactNumber($number) {
  return preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1) $2-$3", $number);
}

function createTable($conn, $tableName, $start, $limit, $search) {
  $searchSql = '';
  $params = [];
  $types = '';

  if (!empty($search)) {
    if ($tableName == 'product') {
      $searchSql = "WHERE ProductName LIKE ?";
      $params[] = "%$search%";
      $types .= 's';
    } elseif ($tableName == 'supplier') {
      $searchSql = "WHERE SupplierName LIKE ? OR ContactPerson LIKE ? OR ContactNumber LIKE ?";
      $params[] = "%$search%";
      $params[] = "%$search%";
      $params[] = "%$search%";
      $types .= 'sss';
    } elseif ($tableName == 'category') {
      $searchSql = "WHERE CategoryName LIKE ?";
      $params[] = "%$search%";
      $types .= 's';
    }
  }

  if ($tableName == 'supplier') {
    $sql = "SELECT s.*, u.username, u.password FROM supplier s LEFT JOIN users u ON s.user_id = u.user_id $searchSql LIMIT ?, ?";
  } else {
    $sql = "SELECT * FROM $tableName $searchSql LIMIT ?, ?";
  }

  $params[] = $start;
  $params[] = $limit;
  $types .= 'ii';

  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($tableName == 'product') {
    echo "<h3>PRODUCT TABLE</h3>";
  } elseif ($tableName == 'supplier') {
    echo "<h3>SUPPLIER TABLE</h3>";
  } else {
    echo "<h3>CATEGORY TABLE</h3>";
  }

  if ($result->num_rows > 0) {
    echo "<table>";
    $row = $result->fetch_assoc();
    echo "<tr>";
    foreach ($row as $key => $value) {
      $header = ucfirst(str_replace(['ID', 'Name'], [' ID', ' Name'], $key));
      $header = ucfirst(str_replace(['_id', 'Name'], [' ID', ' Name'], $key));
      $header = str_replace(['Contact', 'Number'], ['Contact', ' No.'], $header);
      $header = str_replace(['Contact', 'Person'], ['Contact', ' Person'], $header);
      echo "<th>$header</th>";
    }
    echo "<th>Actions</th>";
    echo "</tr>";

    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      $primaryKey = $row[ucfirst($tableName). 'ID'];

      foreach ($row as $key => $value) {
        if ($tableName == 'product' && $key == 'Price') {
          echo "<td>" . formatPrice($value) . "</td>";
        } elseif ($tableName == 'supplier' && $key == 'ContactNumber') {
          echo "<td>" . formatContactNumber($value) . "</td>";
        } else {
          echo "<td>$value</td>";
        }
      }
      echo "<td>";
      echo "<a id='edit' href='update_form.php?table=$tableName&id=". urlencode($primaryKey). "'>
      <img width='15' height='15' src='https://img.icons8.com/material-rounded/24/FFFFFF/edit--v1.png' alt='edit--v1'/>
      </a>";
      echo "</td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "No results found.";
  }
  $stmt->close();
}

if (isset($_GET['table'])) {
  $table = htmlspecialchars($_GET['table']);

  if ($table == 'supplier' || $table == 'category' || $table == 'product') {
    $searchSql = '';
    $params = [];
    $types = '';

    if (!empty($search)) {
      if ($table == 'product') {
        $searchSql = "WHERE ProductName LIKE ?";
        $params[] = "%$search%";
        $types .= 's';
      } elseif ($table == 'supplier') {
        $searchSql = "WHERE SupplierName LIKE ? OR ContactPerson LIKE ? OR ContactNumber LIKE ?";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'sss';
      } elseif ($table == 'category') {
        $searchSql = "WHERE CategoryName LIKE ?";
        $params[] = "%$search%";
        $types .= 's';
      }
    }

    $sql = "SELECT COUNT(*) as total FROM $table $searchSql";
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
      $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total = $row['total'];
    $pages = ceil($total / $limit);
    $stmt->close();

    createTable($conn, $table, $start, $limit, $search);

    echo "</div>";
    echo "<div class='pages'>";
    for ($i = 1; $i <= $pages; $i++) {
      echo "<button onclick=\"loadPage($i)\">$i</button> ";
    }
    echo "</div>";
  } else {
    echo "Invalid table name";
  }
}

mysqli_close($conn);
?>