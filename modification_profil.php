<?php
session_start();
require "connexiondata.php";
$pdo = connexion();

//if user is connected 
$user_id = $_SESSION['user_id'] ?? null;
// if not 
if (!$user_id) {
    header("Location: connexion.php");
    exit;
}

//just a user info
$stmt = $pdo->prepare("SELECT username, email, tel, address FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $address = $_POST['address'] ?? '';
    // update step 
    $update_stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, tel = ?, address = ? WHERE id = ?");
    $update_stmt->execute([$username, $email, $tel, $address, $user_id]);

    header("Location: contact.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mes informations</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="p-5 mt-5">
    <h1 class="text-center">Modifier mes informations</h1>

    <form method="POST" class="mt-4">
        <label class="form-label mt-2">Nom d'utilisateur</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label class="form-label mt-2">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label class="form-label mt-2">Téléphone</label>
        <input type="tel" name="tel" class="form-control" value="<?= htmlspecialchars($user['tel']) ?>">

        <label class="form-label mt-2">Adresse</label>
        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>">

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="contact.php" class="btn btn-danger">Annuler</a>
        </div>
    </form>
</div>

<?php include "footer.php"; ?>
</body>
</html>
