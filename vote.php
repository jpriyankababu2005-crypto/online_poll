<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/polls.php";

startAppSession();

if (!isset($_SESSION['user_id'])) {
    echo "Please login first.";
    exit();
}

echo handleVoteRequest(
    $_POST['poll_id'] ?? '',
    $_POST['option_id'] ?? '',
    $_SESSION['user_id'] ?? ''
);
?>