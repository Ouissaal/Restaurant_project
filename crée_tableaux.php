<?php 

$pdo = new PDO('mysql:host=localhost;dbname=project_food', 'root', '');

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address varchar(100) NOT NULL,
    tel INT(10)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    product_image VARCHAR(255) NOT NULL,
    stock INT(6)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_commande DATE NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    total DECIMAL(10, 2) NOT NULL
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS commandes_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    product_id INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    quantite INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(255) NOT NULL,
    payment_method VARCHAR(255) NOT NULL
)";
$pdo->exec($sql);




?>