<?php
require "connexiondata.php";
$pdo = connexion();

if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}


$total = 0;
foreach ($_SESSION['panier'] as $id => $produit) {
    if (isset($produit['product_price'], $produit['quantite'])) {
        $total += $produit['product_price'] * $produit['quantite'];
    }
}

$success_message = '';
$error_message = '';


$user_data = null;
if (isset($_SESSION['user_id'])) {                           // Récupérer les données utilisateur si connecté
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données du formulaire
    $required_fields = ['nom', 'email', 'adresse', 'ville', 'code_postal', 'pays', 'payment_method'];
    $all_fields_present = true;
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $all_fields_present = false;
            $error_message = "Veuillez remplir tous les champs obligatoires.";
            break;
        }
    }
    
    if ($all_fields_present) {
        try {
            // Début de la transaction
            $pdo->beginTransaction();
            
            // Récupérer l'ID utilisateur ou rediriger vers la page de connexion si non connecté
            if (!isset($_SESSION['user_id'])) {
                // Stocker les données du formulaire dans la session
                $_SESSION['payment_data'] = $_POST;
                $_SESSION['return_to'] = 'paiment.php';
                header('Location: connexion.php');
                exit;
            }
            
            $user_id = $_SESSION['user_id'];
            
            // Créer la commande dans la table commandes
            $stmt_commande = $pdo->prepare("INSERT INTO commandes (user_id, date_commande, total) VALUES (?, NOW(), ?)");
            if (!$stmt_commande->execute([$user_id, $total])) {
                throw new Exception("Erreur lors de la commande : " . implode(" - ", $stmt_commande->errorInfo()));
            }
            
            $commande_id = $pdo->lastInsertId();
            
            //Enregistrer les articles de la commande dans commandes_details
            $stmt_ligne = $pdo->prepare("INSERT INTO commandes_details (commande_id, product_id, user_id, quantite, total, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            // Formater l'adresse de livraison
            $shipping_address = $_POST['adresse'] . ', ' . $_POST['ville'] . ', ' . $_POST['code_postal'] . ', ' . $_POST['pays'];
            
            foreach ($_SESSION['panier'] as $id => $produit) {
                if (isset($produit['quantite'], $produit['product_price'])) {
                    $sous_total = $produit['quantite'] * $produit['product_price'];
                    
                    if (!$stmt_ligne->execute([
                        $commande_id,
                        $id,
                        $user_id,
                        $produit['quantite'],
                        $sous_total,
                        'en traitement',
                        $_POST['payment_method']
                    ])) {
                        throw new Exception("Erreur lors de l'ajout des détails : " . implode(" - ", $stmt_ligne->errorInfo()));
                    }
                    
                   
                    $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $stmt_stock->execute([$produit['quantite'], $id]);
                }
            }
            
            // Ajouter l'adresse de livraison au profil utilisateur si non définie
            if (empty($user_data['address'])) {
                $stmt_user = $pdo->prepare("UPDATE users SET address = ? WHERE id = ?");
                $stmt_user->execute([$_POST['adresse'], $user_id]);
            }
            
            // Valider la transaction
            $pdo->commit();
            
            // Vider le panier
            $_SESSION['panier'] = [];
            
            $success_message = "Votre commande a été traitée avec succès! Numéro de commande: " . $commande_id;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Une erreur est survenue lors du traitement de votre commande: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - Fastfood Express</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
</head>

<body class="d-flex">
    <div class="container d-flex justify-content-between p-5 mt-5">
        
        <?php if ($success_message): ?>
            <div class="col-12 mt-5">
                <div class="alert alert-success mt-5">
                    <h4>Commande confirmée</h4>
                    <p><?= htmlspecialchars($success_message) ?></p>
                    <a href="acceuil.php" class="btn btn-primary mt-3">Retour à l'accueil</a>
                </div>
            </div>
        <?php else: ?>
            <div class="p-5 mt-5 col-md-9">
                <h1 class="mt-5">Paiement</h1>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>

                <div class="card mt-3 mx-auto p-2">
                    <h2 class="text-light bg-primary p-2">Informations de livraison</h2>
                    <form method="POST" action="">
                        <label for="nom" class="form-label mt-2">Nom Complet</label>
                        <input type="text" class="form-control" name="nom" value="<?= $user_data ? htmlspecialchars($user_data['name'] ?? '') : '' ?>" required>

                        <label for="email" class="form-label mt-2">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= $user_data ? htmlspecialchars($user_data['email'] ?? '') : '' ?>" required>

                        <label for="adresse" class="form-label mt-2">Adresse</label>
                        <input type="text" class="form-control" name="adresse" value="<?= $user_data ? htmlspecialchars($user_data['address'] ?? '') : '' ?>" required>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" name="ville" value="<?= $user_data ? htmlspecialchars($user_data['city'] ?? '') : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="code_postal" class="form-label">Code Postal</label>
                                <input type="text" class="form-control" name="code_postal" value="<?= $user_data ? htmlspecialchars($user_data['postal_code'] ?? '') : '' ?>" required>
                            </div>
                        </div>

                        <label for="pays" class="form-label">Pays</label>
                        <select name="pays" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            <option value="france" <?= $user_data && ($user_data['country'] ?? '') == 'france' ? 'selected' : '' ?>>France</option>
                            <option value="morocco" <?= $user_data && ($user_data['country'] ?? '') == 'morocco' ? 'selected' : '' ?>>Maroc</option>
                        </select>

                        <hr>
                        <h5>Méthode de Paiement</h5>
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" id="credit" name="payment_method" value="Carte de crédit" required>
                            <label for="credit" class="form-check-label">Carte de crédit</label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" id="paypal" name="payment_method" value="Paypal">
                            <label for="paypal" class="form-check-label">Paypal</label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" id="virement" name="payment_method" value="Virement bancaire">
                            <label for="virement" class="form-check-label">Virement bancaire</label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" id="livraison" name="payment_method" value="Paiement à la livraison">
                            <label for="livraison" class="form-check-label">Paiement à la livraison</label>
                        </div>

                        <div id="credit-card-details" class="mt-3 card p-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="card_number" class="form-label">Numéro de carte</label>
                                    <input type="text" class="form-control" name="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label for="expiry" class="form-label">Date d'expiration</label>
                                    <input type="text" class="form-control" name="expiry" placeholder="MM/YY">
                                </div>
                                <div class="col-md-6">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" name="cvv" placeholder="123">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary w-100">Payer <?= number_format($total, 2) ?> MAD</button>
                    </form>
                </div>
            </div>

            <div class="col-md-3 mt-5 pt-5">
                <div class="card">
                    <h4 class="text-light bg-primary p-2">Votre commande</h4>
                    <div class="p-3">
                        <h6>Résumé</h6>
                        <hr>
                        <?php foreach ($_SESSION['panier'] as $id => $produit): ?>
                            <?php if (isset($produit['product_name'], $produit['product_price'], $produit['quantite'])): ?>
                                <div class="d-flex justify-content-between">
                                    <span><?= htmlspecialchars($produit['product_name']) ?> x<?= $produit['quantite'] ?></span>
                                    <span><?= number_format($produit['product_price'] * $produit['quantite'], 2) ?> MAD</span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <hr>
                        <h6 class="d-flex justify-content-between">
                            <span>Total:</span>
                            <span><?= number_format($total, 2) ?> MAD</span>
                        </h6>
                        <a href="panier.php" class="btn btn-outline-secondary w-100 mt-3">Modifier le panier</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include "footer.php" ?>
    
    <script>
        // Afficher/masquer les détails de carte de crédit selon la méthode de paiement
        document.addEventListener('DOMContentLoaded', function() {
            const creditRadio = document.getElementById('credit');
            const cardDetails = document.getElementById('credit-card-details');
            
            function toggleCardDetails() {
                if (creditRadio.checked) {
                    cardDetails.style.display = 'block';
                } else {
                    cardDetails.style.display = 'none';
                }
            }
            
            // État initial
            toggleCardDetails();
            
            // Ajouter des écouteurs d'événements à tous les boutons radio de méthode de paiement
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            paymentRadios.forEach(radio => {
                radio.addEventListener('change', toggleCardDetails);
            });
        });
    </script>
    
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
