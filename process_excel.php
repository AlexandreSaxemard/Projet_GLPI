<?php
// Inclusion des fichiers nécessaires
include('db.php');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Récupération des utilisateurs sélectionnés depuis le formulaire et conversion en tableau
$selected_users = isset($_POST['users']) ? explode(',', $_POST['users']) : [];

// Vérifier si des utilisateurs ont été sélectionnés
if (empty($selected_users)) {
    echo "Aucun utilisateur n'a été sélectionné. Veuillez sélectionner au moins un utilisateur.";
    exit;
}

// Création d'un nouveau fichier Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configuration des en-têtes du fichier Excel
$sheet->setCellValue('A1', 'Nom de l\'utilisateur');
$sheet->setCellValue('B1', 'Nom de l\'ordinateur');
$sheet->setCellValue('C1', 'Numéro de série');
$sheet->setCellValue('D1', 'Date de modification');
$sheet->setCellValue('E1', 'Date de création');

// On commence à écrire les données à la ligne 2
$row = 2;

try {

    // On parcourt la liste des utilisateurs sélectionnés
    foreach ($selected_users as $selected_user) {

        // Variable pour suivre si le nom a déjà été écrit
        $user_name_written = false;  

        // On vérifie si l'élément est un contact en cherchant la chaîne 'contact_'
        if (strpos($selected_user, 'contact_') === 0) {

            // Suppression de 'contact_' pour obtenir seulement le nom du contact
            $contact = str_replace('contact_', '', $selected_user);

            // Récupère les informations de l'ordinateur pour un contact
            $sql = "SELECT c.id, c.name AS computer_name, c.serial, c.date_mod, c.date_creation 
                    FROM glpi_computers c
                    WHERE c.contact = ? AND c.users_id = 0";

            // Préparation de la requête SQL
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $contact);
            $user_name = $contact;
        } else {

            // Sinon alors c'est un utilisateur + récupération des informations correspondantes
            $sql = "SELECT c.id, c.name AS computer_name, c.serial, c.date_mod, c.date_creation 
                    FROM glpi_computers c
                    WHERE c.users_id = ?";

            // Préparation de la requête SQL
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $selected_user);
            $user_name = fetch_user_name_by_id($selected_user);
        }

        $stmt->execute();
        $computers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Si un nom d'utilisateur a été trouvé
        if (!empty($computers)) {
            foreach ($computers as $computer) {
                if (!$user_name_written) {
                    $sheet->setCellValue('A' . $row, $user_name);
                    $user_name_written = true;
                }
                $sheet->setCellValue('B' . $row, $computer['computer_name']);
                $sheet->setCellValue('C' . $row, $computer['serial']);
                $sheet->setCellValue('D' . $row, $computer['date_mod']);
                $sheet->setCellValue('E' . $row, $computer['date_creation']);
                $row++;
            }
        }
    }

    // Création d'un objet Writer pour enregistrer le fichier Excel
    $writer = new Xlsx($spreadsheet);

    // Chemin du fichier
    $file_path = 'computers.xlsx';

    // Enregistrement du fichier
    $writer->save($file_path);

    // Téléchargement du fichier
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: '.filesize($file_path));

    // Envoi du fichier
    flush();
    readfile($file_path);
    exit;

} catch (Exception $e) {

    // Afficher l'erreur
    echo "Une erreur s'est produite : " . $e->getMessage();

}

// Fonction pour récupérer le nom de l'utilisateur en fonction de son ID
function fetch_user_name_by_id($user_id)
{

    // Inclusion du fichier de connexion à la base de données
    include('db.php');

    // Récupération du nom de l'utilisateur
    $sql = "SELECT name FROM glpi_users WHERE id = ?";

    // Préparation de la requête SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si un nom d'utilisateur a été trouvé
    if ($row = $result->fetch_assoc()) {
        return $row['name'];
    } else {
        return "Utilisateur inconnu";
    }
}
?>
