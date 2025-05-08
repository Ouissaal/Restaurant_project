<?php 

try {
    $pdo = new PDO('mysql:host=localhost;', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
    $sql = "CREATE DATABASE IF NOT EXISTS project_food";
    $pdo->exec($sql);

    echo "base de données créée avec succès";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
