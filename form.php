<!DOCTYPE html>
<html>

<head>
    <title>Formulaire d'inventaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Formulaire d'inventaire</h1>
        <form method="POST" id="mainForm">
            <?php include("db.php"); ?>

            <div class="mb-3">
                <label class="form-label">Rechercher des utilisateurs pour obtenir la liste de leur(s) ordinateur(s)</label>
                <div id="selectedUsers">
                    <?php include("get_users.php"); ?>
                </div>
            </div>

            <button type="button" id="selectAllButton" class="btn btn-secondary">Sélectionner tout</button>

            <!-- Ajout du choix du format -->
            <div class="mb-3">
                <label class="form-label">Format du fichier :</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="fileType" id="excel" value="excel">
                    <label class="form-check-label" for="excel">Excel</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="fileType" id="text" value="text">
                    <label class="form-check-label" for="text">Texte</label>
                </div>
            </div>

            <input type="hidden" name="users" id="selectedUsersInput">
            <button type="submit" id="submitButton" class="btn btn-primary">Télécharger</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script src="js/script.js"></script>
</body>

</html>