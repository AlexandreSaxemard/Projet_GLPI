<?php
// Inclure le fichier de configuration de la base de données
include('db.php');

// Construire la requête SQL pour récupérer tous les ordinateurs
$sql = "SELECT * FROM glpi_computers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$computers = $result->fetch_all(MYSQLI_ASSOC);

// Afficher les ordinateurs dans le formulaire
foreach ($computers as $computer) {
    echo '<div class="form-check">
            <input class="form-check-input" type="checkbox" value="' . $computer['id'] . '" id="' . $computer['id'] . '" name="computers[]">
            <label class="form-check-label" for="' . $computer['id'] . '">' . $computer['name'] . '</label>
          </div>';
}

// Fermer la connexion à la base de données
$stmt->close();
?>
