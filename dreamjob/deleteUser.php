<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Start the session to check if the user is logged in
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login page
    exit();
}

// Fetch the user information by ID
if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $user = fetchUserById($pdo, $userId);

    if ($user) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <style>
        body {
            font-family: "Arial";
        }
        input {
            font-size: 1.2em;
            height: 40px;
            width: 200px;
        }
    </style>
</head>
<body>
    <h1>Are you sure you want to delete this user?</h1>
    <div>
        <p>Username: <?php echo htmlspecialchars($user['Username']); ?></p>
        <p>Date Created: <?php echo htmlspecialchars($user['Created_At']); ?></p>

        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['User_ID']); ?>">
            <button type="submit" name="deleteUserBtn">Delete User</button>
        </form>

        <a href="index.php">Cancel</a>
    </div>
</body>
</html>
<?php 
    } else {
        echo "User not found.";
    }
} else {
    echo "Invalid request.";
}
?>
