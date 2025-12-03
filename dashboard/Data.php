<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "admin_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Erreur: " . mysqli_connect_error());
}
?>
