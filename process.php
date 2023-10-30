<?php
include('db.php');

$selected_users = isset($_POST['users']) ? explode(',', $_POST['users']) : [];

// Ajoutez cette vérification au début de votre fichier process.php
if (empty($selected_users)) {
    echo "Aucun utilisateur n'a été sélectionné. Veuillez sélectionner au moins un utilisateur.";
    exit;
}

try {
    $file_content = "";

    foreach ($selected_users as $selected_user) {
        if (strpos($selected_user, 'contact_') === 0) {
            $contact = str_replace('contact_', '', $selected_user);
            $sql = "SELECT c.id, c.name AS computer_name
                    FROM glpi_computers c
                    WHERE c.contact = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $contact);
            $user_name = $contact; // Utilisez la valeur du contact ici

        } else {
            $sql = "SELECT c.id, c.name AS computer_name
                    FROM glpi_computers c
                    WHERE c.users_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $selected_user);
            $user_name = fetch_user_name_by_id($selected_user); // Utilisez la fonction pour les utilisateurs ici
        }

        $stmt->execute();
        $computers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (!empty($computers)) {
            $file_content .= "Liste des ordinateurs de l'utilisateur '$user_name':\n";
            foreach ($computers as $computer) {
                $line = $computer['computer_name'] . " (ID: " . $computer['id'] . ")\n";
                $file_content .= $line;
            }
            $file_content .= "\n";
        }
    }

    if (empty($file_content)) {
        echo "Aucun ordinateur n'a été trouvé pour les utilisateurs sélectionnés.";
    } else {
        $file_path = "computers.txt";
        file_put_contents($file_path, $file_content);

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
function fetch_user_name_by_id($user_id)
{
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