<?php
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=your_database", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Example values (normally from form + session)
$poll_id   = $_POST['poll_id'];
$option_id = $_POST['option_id'];
$user_id   = $_SESSION['user_id']; // user must be logged in

try {
    $stmt = $pdo->prepare("INSERT INTO votes (poll_id, user_id, option_id) VALUES (?, ?, ?)");
    $stmt->execute([$poll_id, $user_id, $option_id]);

    echo "Vote submitted successfully!";
} catch (PDOException $e) {

    // 1062 = duplicate entry (because of UNIQUE constraint)
    if ($e->errorInfo[1] == 1062) {
        echo "You have already voted in this poll.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
