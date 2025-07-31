CREATE DATABASE marinmart;

-- Creating user table
CREATE TABLE `users` (
    `users_id` INT(11) NOT NULL AUTO_INCREMENT,
    `usersname` VARCHAR(50) NOT NULL,
    `password` VARCHAR(50) NOT NULL,
	`role` ENUM('admin', 'manager', 'users') NOT NULL,
    PRIMARY KEY (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Creating supplier table
CREATE TABLE `supplier` (
  `SupplierID` INT(11) NOT NULL AUTO_INCREMENT,
  `ContactPerson` VARCHAR(50) NOT NULL,
  `SupplierName` VARCHAR(50) NOT NULL,
  `ContactNumber` VARCHAR(11) NOT NULL,
  `users_id` INT(11) NOT NULL,
  PRIMARY KEY (`SupplierID`),
  CONSTRAINT `FK_supplier_users` FOREIGN KEY (`users_id`) REFERENCES `users`(`users_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Creating category table
CREATE TABLE `category` (
  `CategoryID` INT(11) NOT NULL AUTO_INCREMENT,
  `CategoryName` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Creating product table
CREATE TABLE `product` (
  `ProductID` INT(11) NOT NULL AUTO_INCREMENT,
  `ProductName` VARCHAR(50) NOT NULL,
  `CategoryID` INT(11) NOT NULL,
  `SupplierID` INT(11) NOT NULL,
  `Price` INT(11) NOT NULL,
  PRIMARY KEY (`ProductID`),
  CONSTRAINT `FK_product_category` FOREIGN KEY (`CategoryID`) REFERENCES `category`(`CategoryID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_product_supplier` FOREIGN KEY (`SupplierID`) REFERENCES `supplier`(`SupplierID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample values for the `users` table
INSERT INTO `users` (`usersname`, `password`, `role`) VALUES
('admin_users', 'adminpass123', 'admin'),
('manager_bob', 'bobpass', 'manager'),
('regular_jane', 'janepass', 'users');

-- Sample values for the `category` table
INSERT INTO `category` (`CategoryName`) VALUES
('Electronics'),
('Clothing'),
('Books');

-- Sample values for the `supplier` table
('Alice Smith', 'Tech Distributors Inc.', '09123456789', 1),
('John Doe', 'Fashion Forward Co.', '09234567890', 2),
('Emily White', 'Knowledge Hub Ltd.', '09345678901', 1);

-- Sample values for the `product` table
INSERT INTO `product` (`ProductName`, `CategoryID`, `SupplierID`, `Price`) VALUES
('Laptop Pro X', 1, 1, 1200),
('Summer Dress', 2, 2, 50),
('The Great Novel', 3, 3, 25);

