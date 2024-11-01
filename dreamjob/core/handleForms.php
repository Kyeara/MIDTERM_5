<?php
session_start();
require_once 'dbConfig.php'; 
require_once 'models.php'; 

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login page
    exit();
}

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if we are inserting a new product
    if (isset($_POST['insertNewProductBtn'])) {
        $brandId = $_POST['brandID'];
        $productType = $_POST['productType'];
        $productName = $_POST['productName'];
        $price = (float)$_POST['price']; // Cast to float
        $quantity = (int)$_POST['quantity']; // Cast to int

        // Call the insertProduct function
        insertProduct($pdo, $brandId, $productType, $productName, $price, $quantity, $userId);
        header("Location: ../index.php?message=Product added successfully");
        exit();

    // Check if we are updating an existing product
    } elseif (isset($_POST['updateProductBtn'])) {
        if (isset($_POST['productId']) && isset($_POST['brandID'])) {
            $productId = $_POST['productId'];
            $brandId = $_POST['brandID']; // Corrected to use 'brandID'
            $productType = $_POST['productType'];
            $productName = $_POST['productName'];
            $price = (float)$_POST['price']; // Cast to float
            $quantity = (int)$_POST['quantity']; // Cast to int

            if (updateProduct($pdo, $productId, $brandId, $productType, $productName, $price, $quantity, $userId)) {
                header('Location: ../index.php?message=Product updated successfully');
                exit();
            } else {
                echo "Failed to update product.";
            }
        } else {
            echo "Product ID or Brand ID not set.";
        }

    // Check if we are inserting a new brand
    } elseif (isset($_POST['insertNewBrandBtn'])) {
        $designerBrand = $_POST['designerBrand'];
        $dateFounded = $_POST['dateFounded'];

        // Call the insertBrand function
        insertBrand($pdo, $designerBrand, $dateFounded, $userId);
        header("Location: ../index.php?message=Brand added successfully");
        exit();
    }
}

// Handle deletion of products
if (isset($_POST['deleteProductBtn'])) {
    if (isset($_POST['product_id'])) { // Changed to 'product_id' to match the input name from index.php
        $productId = $_POST['product_id'];
        if (deleteProduct($pdo, $productId)) {
            header('Location: ../index.php?message=Product deleted successfully');
            exit();
        } else {
            echo "Failed to delete product.";
        }
    } else {
        echo "Product ID not set for deletion.";
    }
}

// Handle deletion of brands
if (isset($_POST['deleteBrandBtn'])) {
    if (isset($_POST['brand_id'])) {
        $brandId = $_POST['brand_id'];
        if (deleteBrand($pdo, $brandId)) {
            header('Location: ../index.php?message=Brand deleted successfully');
            exit();
        } else {
            echo "Failed to delete brand.";
        }
    } else {
        echo "Brand ID not set for deletion.";
    }
}
?>
