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
    $sql = "INSERT INTO categorias (nombre) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre]);
}

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);
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

    <div class="content">
        <div class="container">
            <h1 class="mb-4">Gestión de Categorías</h1>

            <!-- Formulario para añadir categoría -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Añadir Nueva Categoría</h5>
                    <form method="POST" action="categorias.php">
                        <div class="mb-3">
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre de la categoría" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Añadir Categoría</button>
                    </form>
                </div>
            </div>

            <!-- Lista de categorías -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Categorías Existentes</h5>
                    <ul class="list-group">
                        <?php foreach ($categorias as $categoria): ?>
                            <li class="list-group-item bg-light"><?php echo htmlspecialchars($categoria['nombre']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>