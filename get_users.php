<?php
// Inclure le fichier de configuration de la base de données
include('db.php');

// Requête pour récupérer les utilisateurs avec leurs contacts depuis la table glpi_users
$sql = "SELECT DISTINCT u.id, u.name, c.contact
        FROM glpi_users u
        LEFT JOIN glpi_computers c ON u.id = c.users_id
        UNION
        SELECT NULL AS id, NULL AS name, c.contact
        FROM glpi_computers c
        WHERE c.users_id = 0";

// Préparation de la requête SQL
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

// Fermer la connexion à la base de données
$stmt->close();

// Afficher les utilisateurs dans le format souhaité
echo '<select multiple class="form-select" id="userSelect" style="width: 100%;">';
foreach ($users as $user) {
    $id = $user['id'] ?? 'contact_' . $user['contact'];
    echo '<option value="' . $id . '">' . $user['name'] . ' - ' . $user['contact'] . '</option>';
}
echo '</select>';
