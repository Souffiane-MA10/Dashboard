<?php

session_start();
// Si l'utilisateur n'est pas connecté
if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit();
}

/*f($_SESSION["role"] != "admin"){
    echo "Accès refusé !";
    exit();
}
*/
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard Admin</title>
<link rel="stylesheet" href="style.css">
     

</head>
<body>

  <div class="dashboard">
    
<h1>Bienvenue : <?php echo $_SESSION["username"]; ?></h1>

    <a href="../Bulltine/calculatric.php">Calculatrice</a>
    <a href="../Bulltine/index.html">Bulltine de Paie</a>
    <a href="logout.php">Déconnexion</a>

</div>

</body>
</html>
