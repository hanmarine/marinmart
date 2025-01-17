<?php 
include('connection.php');

session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>