<?php
include('db.php');

// Récupérer les données du formulaire et nettoyer
$selected_users = isset($_POST['users']) ? $_POST['users'] : [];

// Validation des données
if (empty($selected_users)) {
    echo "Aucun utilisateur n'a été sélectionné.";
    exit; // Arrêter l'exécution du script
}

try {
    // Initialisation du contenu du fichier texte
    $file_content = "";

    // Parcourir la liste des utilisateurs sélectionnés
    foreach ($selected_users as $selected_user) {
        // Vérifier si l'utilisateur a un ID valide
        if (!is_numeric($selected_user)) {
            continue; // Ignorer les valeurs non numériques
        }

        // Construire la requête SQL pour récupérer les ordinateurs de l'utilisateur sélectionné
        $sql = "SELECT c.id, c.name AS computer_name
                FROM glpi_computers c
                WHERE (c.users_id = ? OR (c.users_id IS NULL AND ? = 0))";

        // Exécution de la requête en utilisant un paramètre pour l'ID de l'utilisateur
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $selected_user, $selected_user); // "i" indique que c'est un entier (ID d'utilisateur)
        $stmt->execute();
        $computers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Récupérer le nom de l'utilisateur
        $user_name = fetch_user_name_by_id($selected_user);

        // Vérifier si des ordinateurs ont été trouvés pour cet utilisateur
        if (!empty($computers)) {
            // Ajouter le nom de l'utilisateur et la liste des ordinateurs dans le contenu du fichier
            $file_content .= "Liste des ordinateurs de l'utilisateur '$user_name':\n";
            foreach ($computers as $computer) {
                $line = $computer['computer_name'] . " (ID: " . $computer['id'] . ")\n";
                $file_content .= $line;
            }
            $file_content .= "\n"; // Ajouter une ligne vide pour séparer les utilisateurs
        }
    }

    // Vérifier si des données ont été trouvées
    if (empty($file_content)) {
        echo "Aucun ordinateur n'a été trouvé pour les utilisateurs sélectionnés.";
    } else {
        // Génération du fichier texte
        $file_path = "computers.txt";
        file_put_contents($file_path, $file_content);

        // Forcer le téléchargement
        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        flush();
        readfile($file_path);
        exit;
    }
} catch (Exception $e) {
    echo "Une erreur s'est produite : " . $e->getMessage();
}

// Fonction pour récupérer le nom de l'utilisateur par son ID
function fetch_user_name_by_id($user_id) {
    // Inclure le fichier de configuration de la base de données
    include('db.php');

    try {
        // Construire la requête SQL pour récupérer le nom de l'utilisateur par son ID
        $sql = "SELECT name FROM glpi_users WHERE id = ?";

        // Exécution de la requête en utilisant un paramètre pour l'ID de l'utilisateur
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id); // "i" indique que c'est un entier (ID d'utilisateur)
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier si un résultat a été trouvé
        if ($row = $result->fetch_assoc()) {
            // Récupérer le nom de l'utilisateur depuis le résultat de la requête
            $user_name = $row['name'];
            return $user_name;
        } else {
            return "Utilisateur inconnu"; // Si l'ID d'utilisateur n'est pas trouvé dans la base de données
        }
    } catch (Exception $e) {
        return "Erreur lors de la récupération du nom d'utilisateur : " . $e->getMessage();
    } finally {
        // Fermeture de la connexion à la base de données
        $conn->close();
    }
}
?>
