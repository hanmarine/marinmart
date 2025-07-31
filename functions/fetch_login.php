<?php
session_start();

include '../db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $user_type = $conn->real_escape_string($_POST['user-type']);

    $sql = "SELECT user_id, username, password, role FROM users WHERE username = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Direct comparison if passwords are not hashed (not recommended)
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            switch ($row['role']) {
                case 'admin':
                    header("Location: ../admin/dashboard.php");
                    break;
                case 'manager':
                    header("Location: ../manager/dashboard.php");
                    break;
                case 'user':
                    header("Location: ../user/dashboard.php");
                    break;
                default:
                    echo "<script>alert('Invalid role.'); window.location.href = './login.php';</script>";
                    break;
            }
        } else {
            echo "<script>alert('Incorrect username or password.'); window.location.href = './login.php';</script>";
        }
    } else {
        echo "<script>alert('Incorrect username or password.'); window.location.href = './login.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
