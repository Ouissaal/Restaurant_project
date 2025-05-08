<?php
require "connexiondata.php";
$pdo = connexion();

?>
<!-- just a conatct page -->
<body>
<div class="contanier-fluid mt-5 p-5 ">
    <div class="row p-5">

        <div class="col-md-6 shadow p-3 rounded">
            <h1 class="text-info">Contactez-nous</h1>
            <form action="">
                <label for="" class="form-label mt-3">Nom</label>
                <input type="text" class="form-control">
                <label for="" class="form-label mt-3">Email</label>
                <input type="email" class="form-control">

                <label for="" class="form-label mt-3">Message</label>
                <textarea name="" id=""  class="form-control"></textarea><br>
                <button type="submit" class="btn btn-info w-100">Envoyer</button>
            </form>
        </div>
        <div class="col-md-6 p-5">
            <div class="card">
                <h5 class="card-title  p-2">Information de contact</h5>
                <div class="card-body text-secondary">
                    <li>123 Rue 130 TAZA ,Maroc</li>
                    <li>212 1 23 45 67 89</li>
                    <li>contact@luxeparfum.com</li>
                    <hr>

                </div>
                <h5 class="card-title p-2">Heures d'overture</h5>
                <div class="card-body text-secondary">
                    <li>Lundi - Vendredi : 9h-18h</li>
                    <li>Samedi : 10h -17h</li>
                    <li>Dimanche: Fermé</li>
                    <hr>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require "footer.php";?>
</body>
