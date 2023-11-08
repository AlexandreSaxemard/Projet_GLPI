<?php

// Paramètres de connexion à la base de données
$servername = '10.249.64.102';
$username = 'glpi';
$password = '2j1l1p';
$dbname = 'glpi';
$port = 3306;

// On établit la connexion
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// On vérifie la connexion
if ($conn->connect_error) {
  die('Erreur : ' . $conn->connect_error);
}
// echo 'Connexion réussie à la base de données ' . $dbname;
?>
