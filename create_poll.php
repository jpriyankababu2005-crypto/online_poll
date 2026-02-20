<?php
session_start();
include("config.php");

if($_SESSION['role'] != 'admin'){
    header("Location: index.php");
}

if(isset($_POST['create'])){

    $question = $_POST['question'];

    mysqli_query($conn, "INSERT INTO polls (question,status) VALUES ('$question','active')");
    $poll_id = mysqli_insert_id($conn);

    foreach($_POST['options'] as $option){
        mysqli_query($conn, "INSERT INTO options (poll_id, option_text) VALUES ('$poll_id','$option')");
    }

    echo "Poll Created Successfully!";
}
?>

<h2>Create Poll</h2>

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
