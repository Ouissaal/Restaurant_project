<?php
session_start();
require "connexiondata.php";

$pdo = connexion();
$total = 0;
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['error']);


if (!empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $id => $produit) {
        
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?"); // Check if product exists in database
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if (!$product) { // if Product doesn't exist than  remove it from the cart/panier 
            unset($_SESSION['panier'][$id]);
            if (!isset($_SESSION['error'])) {
                $_SESSION['error'] = "Un ou plusieurs produits ont été retirés car ils n'existent plus.";
            }
        } else {
            // Update product info from database
            $_SESSION['panier'][$id]['product_name'] = $product['product_name'];
            $_SESSION['panier'][$id]['product_price'] = $product['product_price'];
            $_SESSION['panier'][$id]['product_image'] = $product['product_image'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - Fastfood Express</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 " style="margin-top:100px;">Votre panier</h2>

    <?php if ($errorMessage): ?><!-- if Product doesn't exist -->
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    
    <?php if (!empty($_SESSION['panier'])): ?>
        <form action="mettre_a_jour_panier.php" method="post">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['panier'] as $id => $produit): ?>
                        <?php
                        if (!isset($produit['product_price'], $produit['quantite'])) continue;
                        $nom = $produit['product_name'] ?? 'Produit inconnu';
                        $prix = $produit['product_price'];
                        $qte = $produit['quantite'];
                        $img = $produit['product_image'] ?? 'default.png';
                        $sous_total = $prix * $qte;
                        $total += $sous_total;
                        ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($img) ?>" width="50" height="50" class="me-2">
                                <?= htmlspecialchars($nom) ?>
                            </td>
                            <td><?= number_format($prix, 2) ?> MAD</td>
                            <td>
                                <input type="number" name="quantites[<?= $id ?>]" value="<?= $qte ?>" min="1" class="form-control" style="width: 70px; margin: auto;">
                            </td>
                            <td><?= number_format($sous_total, 2) ?> MAD</td>
                            <td>
                                <a href="supprimer_du_panier.php?id=<?= $id ?>" class="btn btn-danger btn-sm">
                                    X
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td><strong><?= number_format($total, 2) ?> MAD</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="d-flex justify-content-between mt-3">
                <a href="acceuil.php" class="btn btn-outline-primary">Continuer vos achats</a>
                <div>
                    <button type="submit" class="btn btn-primary">Mettre à jour le panier</button>
                    <a href="vider_panier.php" class="btn btn-outline-danger">Vider le panier</a>
                    <a href="passer_commande.php" class="btn btn-success">Passer la commande</a>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-info">Votre panier est vide.</div>
        <a href="acceuil.php" class="btn btn-outline-primary">Continuer vos achats</a>
    <?php endif; ?>
</div>

<?php require "footer.php" ?>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>