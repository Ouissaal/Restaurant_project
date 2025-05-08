<?php
session_start();
require "connexiondata.php";
$pdo = connexion();

// if user not connected ->connexion.php to connect 
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (empty($_SESSION['panier'])) {
    $_SESSION['error'] = "Votre panier est vide.";
    header("Location: panier.php");
    exit();
}


header("Location: paiment.php");
exit();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Redirection</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
    <meta http-equiv="refresh" content="2;url=paiment.php">
</head>
<style>
    .alert{
        margin-top:200px;
        margin-bottom:200px;
        width:450px;
    }
</style>
<body class="mx-auto">
<div class="container mt-5 ">
    <div class="alert alert-info">
        <h4 class="alert-heading ">Redirection vers le paiement...</h4>
        <p>Vous allez être redirigé vers la page de paiement.</p>
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
