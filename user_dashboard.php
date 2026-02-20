<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'user'){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get active poll
$poll_query = mysqli_query($conn, "SELECT * FROM polls WHERE status='active' LIMIT 1");
$poll = mysqli_fetch_assoc($poll_query);

if($poll){

    $poll_id = $poll['id'];
    echo "<h2>".$poll['question']."</h2>";

    // Check if user already voted
    $check_vote = mysqli_query($conn, 
        "SELECT * FROM votes WHERE poll_id='$poll_id' AND user_id='$user_id'");

    if(mysqli_num_rows($check_vote) > 0){
        echo "<p><b>You have already voted for this poll.</b></p>";
    } else {
?>

<form method="POST">
<?php
$options = mysqli_query($conn, 
    "SELECT * FROM options WHERE poll_id='$poll_id'");

while($row = mysqli_fetch_assoc($options)){
?>
    <input type="radio" name="option_id" 
        value="<?php echo $row['id']; ?>" required>
    <?php echo $row['option_text']; ?>
    <br>
<?php } ?>

<br>
<input type="submit" name="vote" value="Vote">
</form>

<?php
    }
} else {
    echo "<h3>No Active Poll Available</h3>";
}

// When user submits vote
if(isset($_POST['vote'])){
    $option_id = $_POST['option_id'];

    mysqli_query($conn, 
        "INSERT INTO votes (poll_id, option_id, user_id) 
        VALUES ('$poll_id','$option_id','$user_id')");

    echo "<p>Vote Submitted Successfully!</p>";
}
?>

<br><br>
<a href="logout.php">Logout</a>
