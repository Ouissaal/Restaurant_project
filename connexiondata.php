<?php 
function connexion(){
    try{
        $conn = new PDO('mysql:host=localhost;dbname=project_food', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } 
    catch(PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}


// Only start session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser le panier 
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$pdo = connexion();

//thème light/dark 
if (isset($_POST['theme'])) {
    $_SESSION['theme'] = $_POST['theme'];
}
$theme = $_SESSION['theme'] ?? 'light';


$nombre_articles = count($_SESSION['panier']);

$stmt = $pdo->query('SELECT * FROM products');
$produits = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Fastfood Express</title>
  <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
  <style>
          body.dark {
             background-color: #121212; color: white;
             }
          body.light { 
            background-color: #ffffff; color: black; 
          }

  </style>
</head>
<body class="<?= $theme ?>">

<header class="mb-5">
  <nav class="navbar navbar-expand-lg fixed-top p-3 <?= $theme === 'dark' ? 'bg-dark' : 'bg-light'; ?>">
    <h3 class="navbar-brand <?= $theme === 'dark' ? 'text-white' : 'text-dark'; ?>">FASTFOOD EXPRESS</h3>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>" href="acceuil.php">Accueil</a>
        </li>
        <li class="nav-item me-4">
          <a class="nav-link <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>" href="contact.php">Contact</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
       
        <?php if (isset($_SESSION['user_id'])): ?> 
           <!-- if user is connected -->
          <div class="dropdown ms-4">
          <button class="btn  dropdown-toggle <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"> Mon compte</button>

          <ul class="dropdown-menu">
            <li><a class="dropdown-item <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>" href="mon_compte.php" >Mon profil</a></li>
            <li><a class="dropdown-item <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>" href="mes_commandes.php">Mes commandes</a></li>
            <li><a class="dropdown-item text-danger" href="déconnexion.php">Déconnexion</a></li>
          </ul>
        </div>
         <!-- if not  -->
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>" href="connexion.php">Connexion</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>" href="create_account.php">Inscription</a>
          </li>
        <?php endif; ?>

        <!-- Panier && badge -->
        <li class="nav-item">
          <a href="panier.php" class="nav-link <?= $theme === 'dark' ? 'text-secondary' : 'text-dark'; ?>">
            Panier <span class="badge bg-danger"><?= $nombre_articles ?></span>
          </a>
        </li>

        <!-- théme switch btn : -->
        <li class="nav-item d-flex align-items-center ms-3">
          <form method="POST" class="d-flex align-items-center">
            <input type="hidden" name="theme" value="<?= $theme === 'dark' ? 'light' : 'dark'; ?>">
            <div class="form-check form-switch  ms-auto">
              <input class="form-check-input" type="submit" id="themeSwitch">
              <label class="form-check-label ms-2" for="themeSwitch">
                <?= $theme === 'dark' ? ' Clair' : 'Sombre'; ?>
              </label>
            </div>
          </form>
        </li>
      </ul>
    </div>
  </nav>
</header>


<script src="bootstrap/js/bootstrap.bundle.js"></script>