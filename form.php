<!DOCTYPE html>
<html>

<head>
    <title>Formulaire d'inventaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css"> <!-- Lien vers le fichier CSS -->
</head>

<body>
    <div class="container mt-5">
        <h1>Formulaire d'inventaire</h1>
        <form action="process.php" method="POST">
            <?php include("db.php"); ?>

            <div class="mb-3">
                <label class="form-label">Rechercher des utilisateurs ou contacts:</label>
                <input type="text" id="userSearch" class="form-control" placeholder="Chercher un utilisateur ou un contact...">
            </div>

            <div class="mb-3">
                <label class="form-label">Utilisateurs sélectionnés:</label>
                <div id="selectedUsers">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Utilisateurs sélectionnés:</label>
                <div id="selectedUsers">
                    <!-- Liste des cases à cocher générée depuis get_users.php -->
                    <?php include("get_users.php"); ?>
                </div>
            </div>

            <input type="hidden" name="users" id="selectedUsersInput">
            <button type="submit" id="submitButton" class="btn btn-primary">Rechercher</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script> <!-- Lien vers le fichier JavaScript -->
</body>

</html>