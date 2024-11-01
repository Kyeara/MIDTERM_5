CREATE TABLE Users (
    User_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Brand (
    Brand_ID INT AUTO_INCREMENT PRIMARY KEY,
    Designer_Brand VARCHAR(50) NOT NULL,
    Date_Founded DATE,
    added_by INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES Users(User_ID) ON DELETE SET NULL
);

CREATE TABLE Product (
    Product_ID INT AUTO_INCREMENT PRIMARY KEY,
    Brand_ID INT,
    Product_Type VARCHAR(50),
    Product_Name VARCHAR(50),
    Price INT,
    Quantity INT,
    Date_Created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by INT,
    Updated_By INT,  -- New column for user who last updated the product
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (Brand_ID) REFERENCES Brand(Brand_ID) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES Users(User_ID) ON DELETE SET NULL,
    FOREIGN KEY (Updated_By) REFERENCES Users(User_ID) ON DELETE SET NULL  -- Foreign key constraint
);

-- Additional statement to ensure the last_updated field is included
ALTER TABLE Product ADD last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
