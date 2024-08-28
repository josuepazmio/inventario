<?php
include '../header.php';
require '../db.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];

    $sql = "INSERT INTO personal (nombre) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre]);
}

$personal = $pdo->query("SELECT * FROM personal")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #dee2e6;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }
    </style>
</head>
<body>
    <?php include 'admin_menu.php'; ?>

    <div class="content">  <div class="container mt-4">
        <h1 class="mb-4">Personal</h1>
        
        <!-- Formulario de Añadir Personal -->
        <form method="POST" action="personal.php" class="mb-4">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Personal</label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre del personal" required>
            </div>
            <button type="submit" class="btn btn-primary">Añadir Personal</button>
        </form>

        <!-- Lista de Personal -->
        <ul class="list-group">
            <?php foreach ($personal as $persona): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($persona['nombre']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>