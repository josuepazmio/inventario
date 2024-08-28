<?php
include '../header.php';
require '../db.php';
session_start();

if ($_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Usuario</title>
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
    <div class="sidebar">
        <h4>Menú de Usuario</h4>
        <div class="list-group">
            <a href="asignaciones.php" class="list-group-item list-group-item-action">Asignar Producto</a>
            <a href="lista_asignaciones.php" class="list-group-item list-group-item-action">Lista de Asignaciones</a>
            <a href="../logout.php" class="list-group-item list-group-item-action">Cerrar Sesión</a>
            
        </div>
    </div>

    <div class="content">
        <!-- Contenido de la página -->
        <h1 class="mb-4">Bienvenido al Panel de Usuario</h1>
        <p>Selecciona una opción del menú para comenzar.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
