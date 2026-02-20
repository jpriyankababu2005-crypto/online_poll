<?php
include "db.php";

$option_id = $_POST['option_id'];

$conn->query("UPDATE options SET votes = votes + 1 WHERE id=$option_id");

echo "Thank you for voting!";
?>
