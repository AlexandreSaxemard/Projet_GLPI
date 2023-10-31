<?php
// Inclure les fichiers nécessaires
include('db.php');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$selected_users = isset($_POST['users']) ? explode(',', $_POST['users']) : [];

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
    foreach ($selected_users as $selected_user) {
        $user_name_written = false;  // Variable pour suivre si le nom a déjà été écrit

        if (strpos($selected_user, 'contact_') === 0) {
            $contact = str_replace('contact_', '', $selected_user);
            $sql = "SELECT c.id, c.name AS computer_name, c.serial, c.date_mod, c.date_creation 
                    FROM glpi_computers c
                    WHERE c.contact = ? AND c.users_id = 0";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $contact);
            $user_name = $contact;
        } else {
            $sql = "SELECT c.id, c.name AS computer_name, c.serial, c.date_mod, c.date_creation 
                    FROM glpi_computers c
                    WHERE c.users_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $selected_user);
            $user_name = fetch_user_name_by_id($selected_user);
        }

        $stmt->execute();
        $computers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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

    $writer = new Xlsx($spreadsheet);
    $file_path = 'computers.xlsx';
    $writer->save($file_path);

    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: '.filesize($file_path));
    flush();
    readfile($file_path);
    exit;
} catch (Exception $e) {
    echo "Une erreur s'est produite : " . $e->getMessage();
}

function fetch_user_name_by_id($user_id)
{
    include('db.php');
    $sql = "SELECT name FROM glpi_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['name'];
    } else {
        return "Utilisateur inconnu";
    }
}
?>
