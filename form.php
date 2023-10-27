<!DOCTYPE html>
<html>

<head>
    <title>Formulaire d'inventaire</title>
    <!-- CDN de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Formulaire d'Inventaire</h1>
        <form action="process.php" method="POST">
            <?php include("db.php"); ?>

            <div class="mb-3">
                <label class="form-label">Utilisateurs:</label>
                <select multiple class="form-select" name="users[]">
                    <?php include("get_users.php"); ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>
</body>

</html>
