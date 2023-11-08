<?php
// Inclusion du fichier de configuration de la base de données
include('db.php');

// Récupération des utilisateurs sélectionnés depuis le formulaire et conversion en tableau
$selected_users = isset($_POST['users']) ? explode(',', $_POST['users']) : [];

// Vérifier si des utilisateurs ont été sélectionnés
if (empty($selected_users)) {
    echo "Aucun utilisateur n'a été sélectionné. Veuillez sélectionner au moins un utilisateur.";
    exit;
}

try {
    // Initialisation du contenu du fichier texte
    $file_content = "";

    // On parcourt la liste des utilisateurs sélectionnés
    foreach ($selected_users as $selected_user) {

        // On vérifie si l'élément est un contact en cherchant la chaîne 'contact_'
        if (strpos($selected_user, 'contact_') === 0) {

            // Suppression de 'contact_' pour obtenir seulement le nom du contact
            $contact = str_replace('contact_', '', $selected_user);

            // Récupère les informations de l'ordinateur pour un contact
            $sql = "SELECT c.id, c.name AS computer_name, c.serial, c.date_mod, c.date_creation 
                    FROM glpi_computers c
                    WHERE c.contact = ? AND c.users_id = 0";

            // Prépare la requête SQL
            $stmt = $conn->prepare($sql);

            $stmt->bind_param("s", $contact);

            $user_name = $contact; // Utilise le nom du contact comme nom d'utilisateur
        } else {
            // Sinon alors c'est un utilisateur + récupération des informations correspondantes
            $sql = "SELECT c.id, c.name AS computer_name, c.serial, c.date_mod, c.date_creation 
                    FROM glpi_computers c
                    WHERE c.users_id = ?";

            // Préparation de la requête SQL
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $selected_user);
            $user_name = fetch_user_name_by_id($selected_user); // Utilise une fonction pour récupérer le nom d'utilisateur
        }

        // Exécute la requête SQL
        $stmt->execute();

        // Récupère les résultats dans un tableau associatif
        $computers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Si des ordinateurs ont été trouvés pour cet utilisateur/contact
        if (!empty($computers)) {

            // Ajoute le nom de l'utilisateur au contenu du fichier
            $file_content .= "Liste des ordinateurs de l'utilisateur '$user_name':\n\n";

            // Parcours la liste des ordinateurs
            foreach ($computers as $computer) {

                // Construit une ligne de texte pour chaque ordinateur
                $line = "Nom de l'ordinateur : " . $computer['computer_name'] . ", ";

                // Ajoute les autres informations si elles existent
                if (isset($computer['serial'])) {
                    $line .= "Numéro de série : " . $computer['serial'] . ", ";
                }
                if (isset($computer['date_mod'])) {
                    $line .= "Date de modification : " . $computer['date_mod'] . ", ";
                }
                if (isset($computer['date_creation'])) {
                    $line .= "Date de création : " . $computer['date_creation'];
                }

                $line .= "\n";

                $file_content .= $line;
            }

            // Ajoute une ligne vide pour séparer les utilisateurs/contacts
            $file_content .= "\n"; 
        }
    }

    // Vérification si des données ont été trouvées
    if (empty($file_content)) {
        echo "Aucun ordinateur n'a été trouvé pour les utilisateurs/contacts sélectionnés.";
    } else {

        // Génère le fichier texte
        $file_path = "computers.txt";

        // Écrit le contenu du fichier
        file_put_contents($file_path, $file_content);

        // Configure les en-têtes pour forcer le téléchargement du fichier
        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Envoie le contenu du fichier au navigateur et termine le script
        flush();
        readfile($file_path);
        exit;
    }
} catch (Exception $e) {
    // Affiche un message d'erreur en cas d'exception
    echo "Une erreur s'est produite : " . $e->getMessage();
}

// Fonction pour récupérer le nom de l'utilisateur en fonction de son ID
function fetch_user_name_by_id($user_id)
{
    // Inclut à nouveau le fichier de configuration de la base de données
    include('db.php');

    try {
        // Construit la requête SQL pour récupérer le nom de l'utilisateur par son ID
        $sql = "SELECT name FROM glpi_users WHERE id = ?";

        // Prépare la requête SQL
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id); // "i" indique que c'est un entier (ID d'utilisateur)
        $stmt->execute();
        $result = $stmt->get_result();

        // Si un résultat est trouvé, renvoie le nom de l'utilisateur
        if ($row = $result->fetch_assoc()) {
            $user_name = $row['name'];
            return $user_name;
        } else {
            // Si aucun résultat n'est trouvé, renvoie "Utilisateur inconnu"
            return "Utilisateur inconnu";
        }
    } catch (Exception $e) {
        // En cas d'erreur, renvoie un message d'erreur
        return "Erreur lors de la récupération du nom d'utilisateur : " . $e->getMessage();
    } finally {
        // Ferme la connexion à la base de données
        $conn->close();
    }
}
?>