<?php 
require 'connexiondata.php';
$pdo = connexion();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
} 

else {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT username, email, address FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $nom = $user['username'];
        $adresse_initiale = $user['address'];  
    }
}

if (isset($_POST['Ajouter'])) { // if user click on "ajouter"
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    
   
    if (isset($_SESSION['panier'][$product_id])) {  // Check if the product is already in the cart
        
        $_SESSION['panier'][$product_id]['quantite']++; // If already in cart, increment quantity
    }
     else {
       
        $_SESSION['panier'][$product_id] = [   // If not in cart, add it with quantity 1
            'product_name' => $product_name,
            'product_price' => floatval($product_price),
            'product_image' => $product_image,
            'quantite' => 1
        ];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}


$stmt = $pdo->query('SELECT * FROM products');
$produits = $stmt->fetchAll();


$nombre_articles = count($_SESSION['panier']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container p-5">
        <h2 class="text-center mt-5 mb-4">VOS PRODUITS</h2>
        <div class="row">
            <?php foreach ($produits as $produit): ?>
                <div class="col-md-4 d-flex justify-content-center mb-4">

                    <div class="card shadow p-1" style="width: 100%; max-width: 370px;">

                       
                        <img src="<?= htmlspecialchars($produit['product_image'] ?? 'product_img/default.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['product_name']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            
                          
                            <h5 class="card-title"><?= htmlspecialchars($produit['product_name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($produit['description']) ?></p>
                            <p class="card-text text-primary fw-bold"><?= number_format($produit['product_price'], 2) ?> MAD</p>

                       
                            <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                                <input type="hidden" name="product_id" value="<?= $produit['id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($produit['product_name']) ?>">
                                <input type="hidden" name="product_price" value="<?= htmlspecialchars($produit['product_price']) ?>">
                                <input type="hidden" name="product_image" value="<?= htmlspecialchars($produit['product_image']) ?>">
                                
                                <div class="d-flex">
                                    <a href="view_details.php?id=<?= urlencode($produit['id']) ?>" class="btn btn-outline-primary me-2">Voir détails</a>
                                    <button type="submit" name="Ajouter" class="btn btn-primary flex-grow-1">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require "footer.php" ?>
    
   <script src="./bootstrap/js/bootstrap.bundle.js"></script>
</body>
</html>