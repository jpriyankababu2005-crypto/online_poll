<?php
require_once __DIR__ . "/includes/auth.php";

startAppSession();
logoutUser();
header("Location: index.php");
exit();
?>
