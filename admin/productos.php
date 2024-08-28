<?php
include '../header.php';
require '../db.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'];
        $cantidad = $_POST['cantidad'];

        $sql = "INSERT INTO productos (nombre, categoria, cantidad) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $categoria, $cantidad]);
    }

    if (isset($_POST['update_stock'])) {
        $producto_id = $_POST['producto_id'];
        $nueva_cantidad = $_POST['nueva_cantidad'];

        $sql = "UPDATE productos SET cantidad = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nueva_cantidad, $producto_id]);
    }
}

$productos = $pdo->query("SELECT productos.*, categorias.nombre AS categoria_nombre 
    FROM productos 
    JOIN categorias ON productos.categoria = categorias.id")->fetchAll(PDO::FETCH_ASSOC);

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);

// Filtrar productos con stock <= 10
$productos_bajos_stock = array_filter($productos, function($producto) {
    return $producto['cantidad'] <= 10;
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
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
            <h1 class="mb-4">Gestión de Productos</h1>

            <!-- Formulario para añadir producto -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Añadir Nuevo Producto</h5>
                    <form method="POST" action="productos.php">
                        <input type="hidden" name="add_product">
                        <div class="mb-3">
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre del producto" required>
                        </div>
                        <div class="mb-3">
                            <select name="categoria" class="form-select" required>
                                <option value="" disabled selected>Selecciona la Categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="number" name="cantidad" class="form-control" placeholder="Cantidad" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Añadir Producto</button>
                    </form>
                </div>
            </div>

            <!-- Lista de productos -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Productos Existentes</h5>
                    <ul class="list-group">
                        <?php foreach ($productos as $producto): ?>
                            <li class="list-group-item bg-light">
                                <?php echo htmlspecialchars($producto['nombre']); ?> - 
                                <?php echo htmlspecialchars($producto['categoria_nombre']); ?> - 
                                <?php echo htmlspecialchars($producto['cantidad']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Productos con bajo stock -->
            <?php if (!empty($productos_bajos_stock)): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Actualizar Stock de Productos con Bajo Stock</h5>
                        <form method="POST" action="productos.php">
                            <div class="mb-3">
                                <select name="producto_id" class="form-select" required>
                                    <option value="" disabled selected>Selecciona el Producto</option>
                                    <?php foreach ($productos_bajos_stock as $producto): ?>
                                        <option value="<?php echo $producto['id']; ?>">
                                            <?php echo htmlspecialchars($producto['nombre']); ?> - 
                                            <?php echo htmlspecialchars($producto['cantidad']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input type="number" name="nueva_cantidad" class="form-control" placeholder="Nueva Cantidad" required>
                            </div>
                            <button type="submit" name="update_stock" class="btn btn-primary">Actualizar Stock</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
