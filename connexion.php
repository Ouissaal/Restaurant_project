<?php
require 'connexiondata.php';
$pdo = connexion();

$saved_email = $_COOKIE['email_user'] ?? '';
$saved_password = $_COOKIE['password_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];

        if ($remember) {
            setcookie('email_user', $email, time() + (7 * 24 * 60 * 60));
            setcookie('password_user', '', time() - 3600);
        } else {
            setcookie('email_user', '', time() - 3600);
            setcookie('password_user', '', time() - 3600);
        }

        header('Location: acceuil.php'); 
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
    <title>connexion</title>
</head>
<body>
<div class="conatiner p-5">
    <?php if (isset($error)): ?>
        <p class='alert alert-danger'><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
<div class="card mt-5  mx-auto" style="max-width:400px;">
    <h5 class="bg-primary text-light text-center p-4  mb-0">Connexion</h5>
    <form method="POST" action="" class="p-4 shadow <?php echo $theme === 'dark' ? 'text-white bg-dark' : 'text-dark'; ?>">
            <label class="form-label">Email:</label>
            <input type="email"  class="form-control" name="email" value="<?php echo htmlspecialchars($saved_email); ?>" required>
        
            <label class="form-label mt-2">Mot de passe:</label>
            <input type="password" class="form-control"  name="password" required>
       
            <label class="form-label mt-2"><input type="checkbox" class="form-checkbox" name="remember" id="remember" <?php if ($saved_email) echo 'checked'; ?>> Se souvenir de moi</label><br>
        
        <button type="submit" class="btn btn-primary w-100 mt-2">Se connecter</button>
        <p class="text-center mt-2 "><a href="create_account.php" style="text-decoration: none;">Crée un compte</a></p>
    </form>
   
   

    </div>
    </div>

<?php include "footer.php" ?>
</body>
</html>