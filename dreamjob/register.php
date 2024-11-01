<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registerBtn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (registerUser($pdo, $username, $password)) {
        header('Location: login.php'); // Redirect to login after successful registration
        exit();
    } else {
        $errorMessage = "Registration failed. Username may already be taken.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Register</h2>
    <?php if (isset($errorMessage)) echo "<div class='error'>$errorMessage</div>"; ?>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <input type="submit" name="registerBtn" value="Register">
    </form>
    <p><a href="login.php">Login</a></p>
</body>
</html>
