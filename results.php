<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/polls.php";

startAppSession();
requireRole('admin');

$pollId = oid($_GET['id'] ?? '');

if ($pollId === null) {
    $latestPoll = getLatestPoll();
    $pollId = $latestPoll['_id'] ?? null;
}

$results = $pollId ? getPollResults($pollId) : [];
?>

<h2>Poll Results</h2>

<?php if (empty($results)): ?>
    <p>No poll results available.</p>
<?php endif; ?>

<?php foreach ($results as $row): ?>
    <p>
        <?php echo htmlspecialchars($row['option_text']); ?> :
        <strong><?php echo $row['total']; ?> votes</strong>
    </p>
<?php endforeach; ?>
<a href="admin_dashboard.php">Back to dashboard</a><br/>
