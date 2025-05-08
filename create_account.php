<?php
require 'connexiondata.php'; 
$pdo = connexion();

$error = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    
    if (empty($nom) || empty($email) || empty($password) || empty($password2)) {
        $error = "Tous les champs sont obligatoires.";
    } 
    elseif ($password !== $password2) { 
        $error = "Les mots de passe ne correspondent pas.";
    } 
    else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert step 
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$nom, $email, $hashedPassword]);
            setcookie("nom", htmlspecialchars($nom), time() + 3600, "/"); // cookie 1h
            header("Location: connexion.php");
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>


<body>

<div class="container p-5">
    <!-- error msg -->
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
<div class="container p-5 " style="max-width: 500px;">
    <h5 class="bg-primary  text-center p-3 mb-0 "> → Créer un compte</h5>
    <form method="POST" action="create_account.php" class="p-4 rounded shadow <?php echo $theme === 'dark' ? 'text-white bg-dark' : 'text-dark'; ?>">
        <label for="nom" class="form-label mt-3">Nom d'utilisateur :</label>
        <input type="text" name="nom" class="form-control" required>

        <label for="email" class="form-label mt-3">Adresse e-mail :</label>
        <input type="email" name="email" class="form-control" required>

        <label for="password" class="form-label mt-3">Mot de passe :</label>
        <input type="password" name="password" class="form-control" required>

        <label for="password2" class="form-label mt-3">Confirmer le mot de passe :</label>
        <input type="password" name="password2" class="form-control" required>

        <div class="text-center mt-4">
            <button type="submit" name="submit" class="btn btn-primary mb-3">S'inscrire</button>
            <p class=" text-primary">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
        </div>
    </form>
</div>
    </div>
<?php include "footer.php" ?>
</body>

