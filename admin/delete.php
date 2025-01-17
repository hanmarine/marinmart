<?php
include('../db/connection.php');

if (isset($_GET['table']) && isset($_GET['id'])) {
  $table = $_GET['table'];
  $id = $_GET['id'];

  try {
    if ($table == 'supplier') {
      // Get the user_id associated with the supplier
      $userQuery = "SELECT user_id FROM supplier WHERE SupplierID = ?";
      $stmt = mysqli_prepare($conn, $userQuery);
      mysqli_stmt_bind_param($stmt, 'i', $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $userId);
      mysqli_stmt_fetch($stmt);
      mysqli_stmt_close($stmt);

      // Start a transaction for safe deletion
      mysqli_autocommit($conn, FALSE);
      mysqli_begin_transaction($conn);

      // Delete the supplier
      $supplierQuery = "DELETE FROM supplier WHERE SupplierID = ?";
      $stmt = mysqli_prepare($conn, $supplierQuery);
      mysqli_stmt_bind_param($stmt, 'i', $id);

      if (mysqli_stmt_execute($stmt)) {
        // Delete the user if user_id is not null
        if (!empty($userId)) {
          $userQuery = "DELETE FROM users WHERE user_id = ?";
          $stmt = mysqli_prepare($conn, $userQuery);
          mysqli_stmt_bind_param($stmt, 'i', $userId);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);
        }
        // Commit the transaction if successful
        mysqli_commit($conn);
        echo "<script>alert('Record deleted successfully.'); window.location.href = '../admin/tables.php';</script>";
      } else {
        // Rollback the transaction on error
        mysqli_rollback($conn);
        throw new Exception(mysqli_error($conn));
      }
    } else {
      // Delete for other tables
      $sql = "DELETE FROM `" . $table . "` WHERE `" . $table . "ID` = ?";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, 'i', $id);

      if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Record deleted successfully.'); window.location.href = '../admin/tables.php';</script>";
      } else {
        throw new Exception(mysqli_error($conn));
      }
    }
    mysqli_stmt_close($stmt);
  } catch (Exception $e) {
    $errorMessage = addslashes($e->getMessage());

    // Check if the error is a foreign key constraint violation
    if (strpos($errorMessage, 'CONSTRAINT') !== false) {
      echo "<script>alert('Error deleting record: Cannot delete parent row due to foreign key constraints.'); window.location.href = '../admin/tables.php';</script>";
    } else {
      echo "<script>alert('Error deleting record: $errorMessage'); window.location.href = '../admin/tables.php';</script>";
    }
  } finally {
    // Always rollback and commit at the end, depending on the exception
    mysqli_autocommit($conn, TRUE);
  }
} else {
  echo "<script>alert('Invalid request.'); window.location.href = '../admin/tables.php';</script>";
}

mysqli_close($conn);
?>
