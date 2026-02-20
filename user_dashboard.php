<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/polls.php";

startAppSession();
requireRole('user');

$userId = getSessionUserId();
if ($userId === null) {
    logoutUser();
    header("Location: index.php");
    exit();
}

$feedback = "";
$poll = getActivePoll();
$pollId = $poll['_id'] ?? null;

if (isset($_POST['vote']) && $pollId) {
    $optionId = oid($_POST['option_id'] ?? '');
    if ($optionId === null) {
        $feedback = "Invalid vote option.";
    } else {
        $feedback = castVote($pollId, $optionId, $userId);
    }
}

if ($poll) {
    $alreadyVoted = hasUserVoted($pollId, $userId);

    echo "<h2>" . htmlspecialchars((string)$poll['question'], ENT_QUOTES, 'UTF-8') . "</h2>";
    if ($feedback !== "") {
        echo "<p>" . htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8') . "</p>";
    }

    if ($alreadyVoted) {
        echo "<p><b>You have already voted for this poll.</b></p>";
    } else {
        $options = getPollOptions($pollId);
?>

<form method="POST">
<?php foreach ($options as $row): ?>
    <input type="radio" name="option_id"
        value="<?php echo htmlspecialchars(objectIdToString($row['_id']), ENT_QUOTES, 'UTF-8'); ?>" required>
    <?php echo htmlspecialchars((string)$row['option_text'], ENT_QUOTES, 'UTF-8'); ?>
    <br>
<?php endforeach; ?>
<br>
<input type="submit" name="vote" value="Vote">
</form>

<?php
    }
} else {
    echo "<h3>No Active Poll Available</h3>";
}
?>

<br><br>
<a href="logout.php">Logout</a>
