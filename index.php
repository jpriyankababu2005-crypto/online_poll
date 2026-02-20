<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/polls.php";

startAppSession();

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = findUserByUsername($username);

    if ($user && isPasswordValid($password, $user['password'] ?? '')) {
        loginUser($user);

        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
            exit();
        }

        header("Location: user_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
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
<?php if ($error !== ""): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<form method="POST">
    Username:<br>
    <input type="text" name="username" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="login" value="Login">
</form>

</body>
</html>
