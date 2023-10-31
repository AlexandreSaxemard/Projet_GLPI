<?php
// Inclure le fichier de configuration de la base de données
include('db.php');

// Récupérer les utilisateurs sélectionnés depuis le formulaire et les convertir en un tableau
$selected_users = isset($_POST['users']) ? explode(',', $_POST['users']) : [];

// Vérifier si des utilisateurs ont été sélectionnés
if (empty($selected_users)) {
    echo "Aucun utilisateur n'a été sélectionné. Veuillez sélectionner au moins un utilisateur.";
    exit;
}

try {
    // Initialisation du contenu du fichier texte
    $file_content = "";

    // Parcourir la liste des utilisateurs sélectionnés
    foreach ($selected_users as $selected_user) {
        // Vérifier si l'utilisateur est un contact en vérifiant s'il commence par 'contact_'
        if (strpos($selected_user, 'contact_') === 0) {
            // Si c'est un contact, récupérer le nom du contact (en supprimant 'contact_')
            $contact = str_replace('contact_', '', $selected_user);

            // Construire la requête SQL pour récupérer les ordinateurs liés à ce contact
            $sql = "SELECT c.id, c.name AS computer_name
                    FROM glpi_computers c
                    WHERE c.contact = ?";

            // Préparation de la requête SQL avec un paramètre pour le nom du contact
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $contact);
            $user_name = $contact; // Utilisez la valeur du contact comme nom d'utilisateur

        } else {
            // Si c'est un utilisateur, récupérer les ordinateurs liés à cet utilisateur
            $sql = "SELECT c.id, c.name AS computer_name, c.serial, c.date_mod, c.date_creation
                    FROM glpi_computers c
                    WHERE c.users_id = ?";

            // Préparation de la requête SQL avec un paramètre pour l'ID de l'utilisateur
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $selected_user);
            $user_name = fetch_user_name_by_id($selected_user); // Utilisez la fonction pour récupérer le nom de l'utilisateur
        }

        // Exécution de la requête SQL
        $stmt->execute();

        // Récupération des résultats de la requête sous forme de tableau associatif
        $computers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Vérifier si des ordinateurs ont été trouvés pour cet utilisateur/contact
        if (!empty($computers)) {
            // Ajouter le nom de l'utilisateur/contact et la liste des ordinateurs au contenu du fichier
            $file_content .= "Liste des ordinateurs de l'utilisateur '$user_name':\n\n";
            foreach ($computers as $computer) {
                $line = "Nom de l'ordinateur : " . $computer['computer_name'] . "\n";
                $line .= "Numéro de série : " . $computer['serial'] . "\n";
                $line .= "Date de modification : " . $computer['date_mod'] . "\n";
                $line .= "Date de création : " . $computer['date_creation'] . "\n";
                $file_content .= $line;
            }
            $file_content .= "\n"; // Ajouter une ligne vide pour séparer les utilisateurs/contacts
        }
    }

    // Vérifier si des données ont été trouvées
    if (empty($file_content)) {
        echo "Aucun ordinateur n'a été trouvé pour les utilisateurs/contacts sélectionnés.";
    } else {
        // Génération du fichier texte
        $file_path = "computers.txt";
        file_put_contents($file_path, $file_content);

        // Configurer les en-têtes pour forcer le téléchargement du fichier
        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Envoyer le contenu du fichier au navigateur et terminer le script
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

        // Préparation de la requête SQL avec un paramètre pour l'ID de l'utilisateur
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
