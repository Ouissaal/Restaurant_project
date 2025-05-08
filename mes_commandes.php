<?php
session_start();
require "connexiondata.php";

$pdo = connexion();

// if user not connected 
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT c.id AS commande_id, c.date_commande, c.total AS total_commande, cd.product_id, cd.quantite, cd.total, cd.status, cd.payment_method, p.product_name, p.product_image FROM commandes c  JOIN commandes_details cd ON c.id = cd.commande_id  JOIN products p ON cd.product_id = p.id  WHERE c.user_id = ?  ORDER BY c.date_commande DESC");
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll();

// Regrouper les commandes par ID

$commandes_par_id = [];
foreach ($commandes as $commande) {
    $id = $commande['commande_id'];
    if (!isset($commandes_par_id[$id])) {
        $commandes_par_id[$id] = [
            'date' => $commande['date_commande'],
            'total' => $commande['total_commande'],
            'produits' => []
        ];
    }
    $commandes_par_id[$id]['produits'][] = $commande;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5 p-5">
    <h2 class="mb-4 mt-5">Mes Commandes</h2>
<!-- si aucun commande -->
    <?php if (empty($commandes_par_id)): ?>
        <div class="alert alert-info">Vous n'avez passé aucune commande pour le moment.</div>
        <a href="acceuil.php" class="btn btn-outline-primary">Faire un achat</a>
    <?php else: ?>

    
        <?php foreach ($commandes_par_id as $id_commande => $details): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Commande n°<?= $id_commande ?> | Date : <?= $details['date'] ?> | Total : <?= number_format($details['total'], 2) ?> MAD
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Paiement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details['produits'] as $prod): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($prod['product_image']) ?>" width="50" height="50" class="me-2">
                                        <?= htmlspecialchars($prod['product_name']) ?>
                                    </td>
                                    <td><?= $prod['quantite'] ?></td>
                                    <td><?= number_format($prod['total'], 2) ?> MAD</td>
                                    <td class="bg-success"><?= htmlspecialchars($prod['status']) ?></td>
                                    <td><?= htmlspecialchars($prod['payment_method']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include "footer.php"; ?>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
