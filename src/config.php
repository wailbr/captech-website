<?php
$servername = "localhost";
$username = "root";
$password = "Dusty126$";
$dbname = "gestion_projets";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>