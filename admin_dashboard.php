<?php
require_once __DIR__ . "/includes/auth.php";

startAppSession();
requireRole('admin');
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h2>Welcome Admin</h2>
<a href="create_poll.php">Create Poll</a><br/>
<a href="results.php">Results</a><br/>
<a href="logout.php">Logout</a><br/>


</body>
</html>

