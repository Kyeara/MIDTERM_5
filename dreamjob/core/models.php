<?php

// User Functions
function registerUser($pdo, $username, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO Users (Username, Password) VALUES (:username, :password)");
    return $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
}

function loginUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['Password'])) {
        return $user; // Return user data on successful login
    }
    return false; // Login failed
}

function logoutUser() {
    session_start();
    session_unset();
    session_destroy();
}

// Fetch user by ID
function fetchUserById($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT Username FROM Users WHERE User_ID = :userId");
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Product Functions
function insertProduct($pdo, $brandID, $productType, $productName, $price, $quantity, $userId) {
    $sql = "INSERT INTO Product (Brand_ID, Product_Type, Product_Name, Price, Quantity, Date_Created, last_updated, added_by, Updated_By) 
            VALUES (:brandID, :productType, :productName, :price, :quantity, NOW(), NOW(), :addedBy, :updatedBy)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':brandID', $brandID);
    $stmt->bindParam(':productType', $productType);
    $stmt->bindParam(':productName', $productName);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':addedBy', $userId); 
    $stmt->bindParam(':updatedBy', $userId); 

    return $stmt->execute();
}

function fetchAllProducts($pdo) {
    $stmt = $pdo->prepare("SELECT p.Product_ID, p.Product_Type, p.Product_Name, p.Price, p.Quantity, 
                               b.Designer_Brand, u.Username, p.Date_Created, p.last_updated 
                        FROM Product p 
                        JOIN Brand b ON p.Brand_ID = b.Brand_ID 
                        LEFT JOIN Users u ON p.added_by = u.User_ID");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateProduct($pdo, $productId, $brandId, $productType, $productName, $price, $quantity, $userId) {
    $sql = "UPDATE Product SET 
                Brand_ID = ?, 
                Product_Type = ?, 
                Product_Name = ?, 
                Price = ?, 
                Quantity = ?, 
                Updated_By = ? 
            WHERE Product_ID = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$brandId, $productType, $productName, $price, $quantity, $userId, $productId]);
}

function deleteProduct($pdo, $productId) {
    $stmt = $pdo->prepare("DELETE FROM Product WHERE Product_ID = :productId");
    if ($stmt->execute(['productId' => $productId])) {
        return true; // Successfully deleted
    } else {
        // Log the error
        $errorInfo = $stmt->errorInfo();
        echo "Error deleting product: " . htmlspecialchars($errorInfo[2]);
        return false; // Deletion failed
    }
}

function getProductById($pdo, $productId) {
    $stmt = $pdo->prepare("SELECT * FROM Product WHERE Product_ID = :productId");
    $stmt->execute(['productId' => $productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Brand Functions
function insertBrand($pdo, $designerBrand, $dateFounded, $userId) {
    $stmt = $pdo->prepare("INSERT INTO Brand (Designer_Brand, Date_Founded, added_by) 
                            VALUES (:designerBrand, :dateFounded, :userId)");
    return $stmt->execute([
        'designerBrand' => $designerBrand,
        'dateFounded' => $dateFounded,
        'userId' => $userId,
    ]);
}

function fetchAllBrands($pdo) {
    $stmt = $pdo->prepare("SELECT b.*, u.Username FROM Brand b LEFT JOIN Users u ON b.added_by = u.User_ID");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBrandById($pdo, $brandId) {
    $stmt = $pdo->prepare("SELECT * FROM Brand WHERE Brand_ID = :brandId");
    $stmt->execute(['brandId' => $brandId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateBrand($pdo, $brandId, $designerBrand, $dateFounded, $userId) {
    $stmt = $pdo->prepare("UPDATE Brand SET Designer_Brand = :designerBrand, Date_Founded = :dateFounded, added_by = :userId 
                            WHERE Brand_ID = :brandId");
    return $stmt->execute([
        'designerBrand' => $designerBrand,
        'dateFounded' => $dateFounded,
        'userId' => $userId,
        'brandId' => $brandId,
    ]);
}

function deleteBrand($pdo, $brandId) {
    $stmt = $pdo->prepare("DELETE FROM Brand WHERE Brand_ID = :brandId");
    if ($stmt->execute(['brandId' => $brandId])) {
        return true; // Successfully deleted
    } else {
        // Log the error
        $errorInfo = $stmt->errorInfo();
        echo "Error deleting brand: " . htmlspecialchars($errorInfo[2]);
        return false; // Deletion failed
    }
}   
?>
