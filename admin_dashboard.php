<?php
session_start();

if($_SESSION['role'] != 'admin'){
    header("Location: index.php");
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Welcome Admin</h2>
<a href="create_poll.php">Create Poll</a><br/>
<a href="results.php">Results</a><br/>
<a href="logout.php">Logout</a><br/>


</body>
</html>

