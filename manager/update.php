<?php
// The update process
include('../db/connection.php');

// Set custom exception handler
set_exception_handler(function($exception) {
    echo "<script>alert('Error: " . addslashes($exception->getMessage()) . "'); window.history.back();</script>";
    exit();
});

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = htmlspecialchars(trim($_POST['table']));
    $id = htmlspecialchars(trim($_POST['id']));

    $updateColumns = "";
    $bindValueArray = [];
    $bindValueTypes = "";

    foreach ($_POST as $key => $value) {
        if ($key != 'table' && $key != 'id' && $key != 'username' && $key != 'password') {
            $updateColumns .= "`$key` = ?, ";
            $bindValueArray[] = htmlspecialchars(trim($value));

            if ($key == 'Price') {
                $bindValueTypes .= 'd';
            } else {
                $bindValueTypes .= 's';
            }
        }
    }

    $updateColumns = rtrim($updateColumns, ", ");
    $sql = "UPDATE `" . $table . "` SET " . $updateColumns . " WHERE `" . $table . "ID` = ?";
    $stmt = mysqli_prepare($conn, $sql);

    array_push($bindValueArray, $id);
    $bindValueTypes .= 'i';

    // Check for duplicates before updating
    $checkDuplicateSql = "";
    $checkDuplicateStmt = null;
    if ($table == 'supplier') {
        $duplicateChecks = [
            ["`SupplierName` = ?", $_POST['SupplierName']],
            ["`ContactPerson` = ?", $_POST['ContactPerson']],
            ["`ContactNumber` = ?", $_POST['ContactNumber']]
        ];

        foreach ($duplicateChecks as $check) {
            $checkDuplicateSql = "SELECT COUNT(*) FROM `supplier` WHERE " . $check[0] . " AND `SupplierID` != ?";
            $checkDuplicateStmt = mysqli_prepare($conn, $checkDuplicateSql);
            mysqli_stmt_bind_param($checkDuplicateStmt, 'si', $check[1], $id);
            mysqli_stmt_execute($checkDuplicateStmt);
            mysqli_stmt_bind_result($checkDuplicateStmt, $count);
            mysqli_stmt_fetch($checkDuplicateStmt);

            if ($count > 0) {
                echo "<script>alert('Duplicate entry for supplier found. Update failed.'); window.history.back();</script>";
                mysqli_stmt_close($checkDuplicateStmt);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit;
            }

            mysqli_stmt_close($checkDuplicateStmt);
        }
    } elseif ($table == 'users') {
        $checkDuplicateSql = "SELECT COUNT(*) FROM `users` WHERE `username` = ? AND `user_id` != ?";
        $checkDuplicateStmt = mysqli_prepare($conn, $checkDuplicateSql);
        mysqli_stmt_bind_param($checkDuplicateStmt, 'si', $_POST['username'], $id);
        mysqli_stmt_execute($checkDuplicateStmt);
        mysqli_stmt_bind_result($checkDuplicateStmt, $count);
        mysqli_stmt_fetch($checkDuplicateStmt);

        if ($count > 0) {
            echo "<script>alert('Duplicate entry for username found. Update failed.'); window.history.back();</script>";
            mysqli_stmt_close($checkDuplicateStmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            exit;
        }

        mysqli_stmt_close($checkDuplicateStmt);
    } elseif ($table == 'category') {
        $checkDuplicateSql = "SELECT COUNT(*) FROM `category` WHERE `CategoryName` = ? AND `CategoryID` != ?";
        $checkDuplicateStmt = mysqli_prepare($conn, $checkDuplicateSql);
        mysqli_stmt_bind_param($checkDuplicateStmt, 'si', $_POST['CategoryName'], $id);
        mysqli_stmt_execute($checkDuplicateStmt);
        mysqli_stmt_bind_result($checkDuplicateStmt, $count);
        mysqli_stmt_fetch($checkDuplicateStmt);

        if ($count > 0) {
            echo "<script>alert('Duplicate entry for category found. Update failed.'); window.history.back();</script>";
            mysqli_stmt_close($checkDuplicateStmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            exit;
        }

        mysqli_stmt_close($checkDuplicateStmt);
    } elseif ($table == 'product') {
        $checkDuplicateSql = "SELECT COUNT(*) FROM `product` WHERE `ProductName` = ? AND `ProductID` != ?";
        $checkDuplicateStmt = mysqli_prepare($conn, $checkDuplicateSql);
        mysqli_stmt_bind_param($checkDuplicateStmt, 'si', $_POST['ProductName'], $id);
        mysqli_stmt_execute($checkDuplicateStmt);
        mysqli_stmt_bind_result($checkDuplicateStmt, $count);
        mysqli_stmt_fetch($checkDuplicateStmt);

        if ($count > 0) {
            echo "<script>alert('Duplicate entry for product found. Update failed.'); window.history.back();</script>";
            mysqli_stmt_close($checkDuplicateStmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            exit;
        }

        mysqli_stmt_close($checkDuplicateStmt);
    }

    mysqli_stmt_bind_param($stmt, $bindValueTypes, ...$bindValueArray);

    if (mysqli_stmt_execute($stmt)) {
        if ($table == 'supplier' && isset($_POST['user_id']) && isset($_POST['username']) && isset($_POST['password'])) {
            $userID = htmlspecialchars(trim($_POST['user_id']));
            $username = htmlspecialchars(trim($_POST['username']));
            $password = htmlspecialchars(trim($_POST['password']));

            $userSql = "UPDATE `users` SET `username` = ?, `password` = ? WHERE `user_id` = ?";
            $userStmt = mysqli_prepare($conn, $userSql);
            mysqli_stmt_bind_param($userStmt, 'ssi', $username, $password, $userID);

            if (mysqli_stmt_execute($userStmt)) {
                echo "<script>alert('Record updated successfully.'); window.location.href = 'tables.php';</script>";
            } else {
                echo "<script>alert('Error updating user record: " . addslashes(mysqli_error($conn)) . "'); window.history.back();</script>";
            }
            mysqli_stmt_close($userStmt);
        } else {
            echo "<script>alert('Record updated successfully.'); window.location.href = 'tables.php';</script>";
        }
    } else {
        echo "<script>alert('Error updating record: " . addslashes(mysqli_error($conn)) . "'); window.history.back();</script>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Invalid request method.'); window.location.href = 'tables.php';</script>";
}

mysqli_close($conn);
?>


