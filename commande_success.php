<?php
session_start();
$message = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande réussie</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php else: ?>
            <div class="alert alert-info">Aucune commande enregistrée.</div>
        <?php endif; ?>
        <a href="acceuil.php" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</body>
</html>
