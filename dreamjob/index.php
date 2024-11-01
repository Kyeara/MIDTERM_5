<?php
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not authenticated
    exit();
}

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch user details
$user = fetchUserById($pdo, $userId); // Assume you have a function to fetch user details

// Fetch all brands
$brands = fetchAllBrands($pdo);

// Fetch products along with the updated by user and added by user
$sql = "SELECT p.Product_ID, p.Product_Type, p.Product_Name, p.Price, p.Quantity, p.Date_Created, p.last_updated, 
        u.Username AS Updated_By, b.Designer_Brand, addedBy.Username AS Added_By
        FROM Product p 
        LEFT JOIN Users u ON p.Updated_By = u.User_ID
        LEFT JOIN Users addedBy ON p.added_by = addedBy.User_ID
        LEFT JOIN Brand b ON p.Brand_ID = b.Brand_ID"; // Join to get the username of the user who added the product

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$successMessage = '';
$errorMessage = '';

// Handling form submission for adding a brand
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insertNewBrandBtn'])) {
    $designerBrand = trim($_POST['designerBrand']);
    $dateFounded = trim($_POST['dateFounded']);
    $userId = $_SESSION['user_id']; // Get the logged-in user ID

    if (!empty($designerBrand) && !empty($dateFounded)) {
        $brandInserted = insertBrand($pdo, $designerBrand, $dateFounded, $userId);
        if ($brandInserted) {
            $successMessage = "Brand added successfully!";
            $brands = fetchAllBrands($pdo); // Refresh brand list
        } else {
            $errorMessage = "Failed to add brand.";
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}

// Handling form submission for adding a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insertNewProductBtn'])) {
    $brandID = trim($_POST['brandID']);
    $productType = trim($_POST['productType']);
    $productName = trim($_POST['productName']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $userId = $_SESSION['user_id']; // Get the logged-in user ID

    if (!empty($brandID) && !empty($productType) && !empty($productName) && 
        $price >= 0 && $quantity >= 0) {
        
        $productInserted = insertProduct($pdo, $brandID, $productType, $productName, $price, $quantity, $userId);
        if ($productInserted) {
            $successMessage = "Product added successfully!";
            // Fetch the latest products list again, including 'Added_By' and 'Updated_By'
            $sql = "SELECT p.Product_ID, p.Product_Type, p.Product_Name, p.Price, p.Quantity, p.Date_Created, p.last_updated, 
                    u.Username AS Updated_By, b.Designer_Brand, addedBy.Username AS Added_By
                    FROM Product p 
                    LEFT JOIN Users u ON p.Updated_By = u.User_ID
                    LEFT JOIN Users addedBy ON p.added_by = addedBy.User_ID
                    LEFT JOIN Brand b ON p.Brand_ID = b.Brand_ID"; // Join to get the username of the user who added the product

            $stmt = $pdo->query($sql);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Refresh product list with usernames
        } else {
            $errorMessage = "Failed to add product.";
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}

// Handle logout
if (isset($_POST['logoutBtn'])) {
    session_destroy(); // Destroy the session
    header('Location: login.php'); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the external CSS file -->
</head>
<body>
    <div class="header">
        <h1>Hello, welcome <?php echo htmlspecialchars($user['Username']); ?>!</h1> <!-- Display current user's name -->
        
        <!-- Logout Button -->
        <form action="" method="POST" style="display:inline;">
            <button type="submit" name="logoutBtn" class="small-button" onclick="return confirm('Are you sure you want to logout?');">Logout</button>
        </form>
    </div>

    <h3>Add New Brand</h3>
    <?php if ($successMessage) echo "<div class='message success'>$successMessage</div>"; ?>
    <?php if ($errorMessage) echo "<div class='message error'>$errorMessage</div>"; ?>

    <form action="" method="POST">
        <p><label for="designerBrand">Designer Brand</label> <input type="text" name="designerBrand" required></p>
        <p><label for="dateFounded">Date Founded</label> <input type="date" name="dateFounded" required></p>
        <p><input type="submit" name="insertNewBrandBtn" value="Add Brand"></p>
    </form>

    <h3>Add New Product</h3>
    <form action="" method="POST">  
        <p>
            <label for="brandID">Designer Brand</label>
            <select name="brandID" required>
                <option value="">Select a Brand</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?php echo htmlspecialchars($brand['Brand_ID']); ?>"><?php echo htmlspecialchars($brand['Designer_Brand']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p><label for="productType">Product Type</label> <input type="text" name="productType" required></p>
        <p><label for="productName">Product Name</label> <input type="text" name="productName" required></p>
        <p><label for="price">Price</label> <input type="number" step="0.01" name="price" required></p>
        <p><label for="quantity">Quantity</label> <input type="number" name="quantity" required></p>
        <p><input type="submit" name="insertNewProductBtn" value="Add Product"></p>
    </form>

    <h3>Product List</h3>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Designer Brand</th>
            <th>Product Type</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Added By</th>
            <th>Date Added</th> <!-- New column for Date Added -->
            <th>Last Updated</th>
            <th>Updated By</th> <!-- New column for Updated By -->
            <th>Action</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['Product_ID']); ?></td>
            <td><?php echo htmlspecialchars($product['Designer_Brand']); ?></td>
            <td>
                <?php
                // Safely access 'Product_Type'
                echo (isset($product['Product_Type']) ? htmlspecialchars($product['Product_Type']) : 'N/A');
                ?>
            </td>
            <td><?php echo htmlspecialchars($product['Product_Name']); ?></td>
            <td><?php echo htmlspecialchars($product['Price']); ?></td>
            <td><?php echo htmlspecialchars($product['Quantity']); ?></td>
            <td>
                <?php
                // Display the username of the person who added the product
                echo isset($product['Added_By']) ? htmlspecialchars($product['Added_By']) : 'N/A';
                ?>
            </td>
            <td><?php echo htmlspecialchars($product['Date_Created']); ?></td> <!-- Display Date Added -->
            <td>
                <?php
                // Safely access 'last_updated'
                echo isset($product['last_updated']) ? htmlspecialchars($product['last_updated']) : 'Not Updated Yet';
                ?>
            </td>
            <td>
                <?php
                // Display the username of the person who last updated the product
                echo isset($product['Updated_By']) ? htmlspecialchars($product['Updated_By']) : 'N/A';
                ?>
            </td>
            <td>
                <a href="editProduct.php?product_id=<?php echo $product['Product_ID']; ?>">Edit</a>
                <!-- Delete Form -->
                <form action="core/handleForms.php" method="POST" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                    <button type="submit" name="deleteProductBtn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
