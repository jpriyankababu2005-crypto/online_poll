<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=poll_system", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$poll_id = $_GET['id']; // example: results.php?poll_id=1

$stmt = $pdo->prepare("
    SELECT options.option_text, COUNT(votes.id) AS total
    FROM options
    LEFT JOIN votes ON options.id = votes.option_id
    WHERE options.poll_id = ?
    GROUP BY options.id
");

$stmt->execute([$poll_id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Poll Results</h2>

<?php foreach ($results as $row): ?>
    <p>
        <?php echo htmlspecialchars($row['option_text']); ?> :
        <strong><?php echo $row['total']; ?> votes</strong>
    </p>
<?php endforeach; ?>
