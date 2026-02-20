<?php
session_start();

if($_SESSION['role'] != 'admin'){
    header("Location: index.php");
}
?>

<h2>Welcome Admin</h2>
<a href="logout.php">Logout</a>
