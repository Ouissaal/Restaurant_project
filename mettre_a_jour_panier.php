<?php
session_start();
require "connexiondata.php";
$pdo = connexion();

// Check if the cart exists in session
if (!empty($_SESSION['panier'])) {
    // Loop through each product in the cart and update its quantity
    foreach ($_POST['quantites'] as $id => $quantite) {
        if (isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id]['quantite'] = $quantite;
        }
    }
}


header("Location: panier.php");
exit;
?>
