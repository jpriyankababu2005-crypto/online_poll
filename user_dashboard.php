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
$rawPollId = trim((string)($_GET['poll_id'] ?? ''));
$selectedPollId = oid($rawPollId);
$poll = $selectedPollId ? getPollById($selectedPollId) : null;

if ($rawPollId !== '' && $selectedPollId === null) {
    $feedback = "Invalid poll id.";
} elseif ($selectedPollId !== null && !$poll) {
    $feedback = "Poll not found.";
}

if (isset($_POST['vote']) && $poll) {
    $optionId = oid($_POST['option_id'] ?? '');
    if ($optionId === null) {
        $feedback = "Invalid vote option.";
    } else {
        $feedback = castVote($selectedPollId, $optionId, $userId);
    }
}

$polls = getAvailablePolls();
echo "<h2>Available Polls</h2>";

$hasPolls = false;
foreach ($polls as $item) {
    $hasPolls = true;
    $itemId = objectIdToString($item['_id']);
    $takenText = hasUserVoted($item['_id'], $userId) ? " (Already taken)" : "";
    echo "<p><a href=\"user_dashboard.php?poll_id=" . htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') . "\">"
        . htmlspecialchars((string)$item['question'], ENT_QUOTES, 'UTF-8') . "</a>"
        . htmlspecialchars($takenText, ENT_QUOTES, 'UTF-8') . "</p>";
}

if (!$hasPolls) {
    echo "<p>No polls available right now.</p>";
}

echo "<p><a href=\"user_dashboard.php\">Show all available polls</a></p>";

if ($feedback !== "") {
    echo "<p>" . htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8') . "</p>";
}

if ($poll) {
    $alreadyVoted = hasUserVoted($selectedPollId, $userId);

    echo "<h3>" . htmlspecialchars((string)$poll['question'], ENT_QUOTES, 'UTF-8') . "</h3>";

    if ($alreadyVoted) {
        echo "<p><b>Poll already taken.</b></p>";
    } else {
        $options = getPollOptions($selectedPollId);
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
}
?>

<br><br>
<a href="logout.php">Logout</a>
