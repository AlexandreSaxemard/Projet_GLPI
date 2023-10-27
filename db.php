<?php
$servername = 'localhost';
$username = 'glpi_test';
$password = '2j1l1p';
$dbname = 'glpi';

// On établit la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// On vérifie la connexion
if ($conn->connect_error) {
  die('Erreur : ' . $conn->connect_error);
}
// echo 'Connexion réussie à la base de données ' . $dbname;
?>
