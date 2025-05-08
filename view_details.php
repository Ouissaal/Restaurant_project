<?php
require 'connexiondata.php';
$pdo = connexion();

// Vérifier si l'ID du produit est fourni
if (!isset($_GET['id'])) {
    echo "Produit non trouvé.";
    exit();
}

$product_id = $_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$produit = $stmt->fetch(); 


if (!$produit) { 
    echo "Produit introuvable.";
    exit();
}

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}


if (isset($_POST['add_to_cart'])) {
    $quantite = $_POST['quantite'];
    
    $_SESSION['panier'][$produit['id']] = [
        'product_name' => $produit['product_name'],
        'product_price' => $produit['product_price'],
        'product_image' => $produit['product_image'],
        'quantite' => $quantite
    ];
    
    $message = "Produit ajouté au panier.";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produit Détails</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <img src="<?= htmlspecialchars($produit['product_image']) ?>" alt="<?= htmlspecialchars($produit['product_name']) ?>" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($produit['product_name']) ?></h2>
            <h4 class="text-primary mb-3"><?= htmlspecialchars($produit['product_price']) ?> MAD</h4>
            <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($produit['description'])) ?></p>

            <form method="post" class="row">
                
                    <label for="quantite" class="form-label">Quantité</label>
                <div class="col-md-3">
                    <input type="number" id="quantite" name="quantite" value="1" min="1" max="99" class="form-control" required>
                </div>
                <div class="col-md-9">
                <button type="submit" name="add_to_cart" class="btn btn-success">Ajouter au panier</button>
                </div>
            </form>

            <div class="mt-3 alert alert-info"> ! Stock disponible: <?= htmlspecialchars($produit['stock']) ?> unités</div>
        </div>
    </div>

    <hr class="my-5">

    <h4>Produits similaires :</h4>
    <div class="row">
        <?php
        $stmt_similar = $pdo->prepare("SELECT * FROM products WHERE id != ? LIMIT 3"); // ici j'ajoute limit 3 pour afficher seulement trois produits pas plus
        $stmt_similar->execute([$produit['id']]);
        $similaires = $stmt_similar->fetchAll();

        foreach ($similaires as $similaire): ?>
            <div class="col-md-4">
                <div class="card p-2" style="width:400px; height:500px;">
                    <img src="<?= htmlspecialchars($similaire['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($similaire['product_name']) ?>" style="min-width:300px;min-height:300px;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($similaire['product_name']) ?></h5>
                        <p class="card-text text-primary"><?= htmlspecialchars($similaire['product_price']) ?> MAD</p>
                        <a href="view_details.php?id=<?= $similaire['id'] ?>" class="btn btn-outline-primary">Voir détails</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
