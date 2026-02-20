<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/polls.php";

startAppSession();
requireRole('admin');

$message = "";

if (isset($_POST['create'])) {
    $question = trim($_POST['question'] ?? '');
    $rawOptions = $_POST['options'] ?? [];
    $options = [];

    foreach ($rawOptions as $option) {
        $optionText = trim((string)$option);
        if ($optionText !== '') {
            $options[] = $optionText;
        }
    }

    if ($question !== '' && count($options) >= 2) {
        createPoll($question, $options);
        $message = "Poll created successfully.";
    } else {
        $message = "Enter a question and at least 2 options.";
    }
}
?>

<h2>Create Poll</h2>
<?php if ($message !== ""): ?>
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<form method="POST">
    Question:<br>
    <input type="text" name="question" required><br><br>

    Option 1:<br>
    <input type="text" name="options[]" required><br><br>

    Option 2:<br>
    <input type="text" name="options[]" required><br><br>

    Option 3:<br>
    <input type="text" name="options[]" required><br><br>

    <input type="submit" name="create" value="Create Poll">
</form>

<a href="admin_dashboard.php">Back</a>
