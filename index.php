<?php
session_start();
include("config.php");

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin'){
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }

    } else {
        echo "<script>alert('Invalid Username or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form method="POST">
    Username:<br>
    <input type="text" name="username" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="login" value="Login">
</form>

</body>
</html>
