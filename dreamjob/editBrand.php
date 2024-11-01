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

// Initialize brand variable
$brand = null;

// Check if a brand ID was provided
if (isset($_GET['brand_id'])) {
    $brand = getBrandById($pdo, $_GET['brand_id']);
    if (!$brand) {
        die("Brand not found!");
    }
} else {
    die("Invalid brand ID.");
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['brand_id']) && !empty($_POST['brand_id'])) {
        $brandId = $_POST['brand_id'];
        $designerBrand = $_POST['designerBrand'];
        $dateFounded = $_POST['dateFounded'];
        $userId = $_SESSION['user_id']; // Get the logged-in user ID

        // Call the update function
        $success = updateBrand($pdo, $brandId, $designerBrand, $dateFounded, $userId);

        if ($success) {
            // Redirect or display success message
            header("Location: index.php"); // Redirect to the brand list
            exit();
        } else {
            echo "Error updating brand.";
        }
    } else {
        echo "Brand ID not set.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand</title>
    <link rel="stylesheet" href="styles.css"> 
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        input {
            font-size: 1em;
            height: 30px;
            width: 200px;
        }
    </style>
</head>
<body>
    <h3>Edit Brand Information</h3>
    <form action="" method="POST"> <!-- Change action to current file to handle POST -->
        <input type="hidden" name="brand_id" value="<?php echo htmlspecialchars($brand['Brand_ID']); ?>">
        <p>
            <label for="designerBrand">Designer Brand</label>
            <input type="text" name="designerBrand" value="<?php echo htmlspecialchars($brand['Designer_Brand']); ?>" required>
        </p>
        <p>
            <label for="dateFounded">Date Founded</label>
            <input type="date" name="dateFounded" value="<?php echo htmlspecialchars($brand['Date_Founded']); ?>" required>
        </p>
        <p><input type="submit" name="updateBrandBtn" value="Update"></p>
    </form>
    <p><a href="index.php">Back to Brand List</a></p>
</body>
</html>
