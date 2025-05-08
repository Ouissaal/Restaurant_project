<?php
session_start();
require "connexiondata.php";
$pdo = connexion();

// same if user is connected ! and if not 
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: connexion.php"); 
    exit;
}


$stmt = $pdo->prepare("SELECT username, email, address, tel FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(); 

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>

    <h1 class="mt-5 p-5 text-danger">Mon Compte</h1>

    <div class="card shadow p-4 mx-auto" style="width:400px;">
        <h2>Mes Informations</h2>
        <p><b>Nom d'utilisateur :</b> <?= htmlspecialchars($user['username']) ?></p>
        <p><b>Email :</b> <?= htmlspecialchars($user['email']) ?></p>
        <p><b>Adresse :</b> <?= htmlspecialchars($user['address']) ?></p>
        <p><b>Téléphone :</b> <?= htmlspecialchars($user['tel']) ?></p>
        <br>
        <button type="button" class="btn btn-primary w-100">
            <a href="modification_profil.php" class="text-white text-decoration-none ">Modifier mes informations</a>
        </button>
    </div>
    </div>

<script src="./bootstrap/js/bootstrap.bundle.js"></script>
</body>
<?php include "footer.php"; ?>
</html>
