<?php
session_start();
include "Data.php";

if(isset($_POST["login"])){

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);

        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        header("Location: dashboard.php");
        exit();
    } else {
    $error = "Nom d'utilisateur ou mot de passe incorrect";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-box">
<h2>Login Admin</h2>

<form method="POST">
    <input type="text" name="username" placeholder="Nom" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit" name="login">Se connecter</button>
</form>

<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>

</body>
</html>

