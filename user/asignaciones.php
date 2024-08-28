<?php
include '../header.php';
require '../db.php';
session_start();

if ($_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto = $_POST['producto'];
    $personal = $_POST['personal'];
    $cantidad = $_POST['cantidad'];

    // Descontar la cantidad del producto
    try {
        // Iniciar una transacción
        $pdo->beginTransaction();

        // Obtener la cantidad actual del producto
        $sql = "SELECT cantidad FROM productos WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$producto]);
        $currentQuantity = $stmt->fetchColumn();

        // Verificar si hay suficiente stock
        if ($currentQuantity < $cantidad) {
            throw new Exception("No hay suficiente stock para asignar la cantidad solicitada.");
        }

        // Insertar asignación
        $sql = "INSERT INTO asignaciones (producto, personal, cantidad) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$producto, $personal, $cantidad]);

        // Actualizar cantidad en productos
        $sql = "UPDATE productos SET cantidad = cantidad - ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cantidad, $producto]);

        // Verificar el stock después de la actualización
        $sql = "SELECT cantidad FROM productos WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$producto]);
        $stock = $stmt->fetchColumn();

        // Confirmar la transacción
        $pdo->commit();

        // Verificar si el stock es menor de 10
        if ($stock < 10) {
            $warning = "Advertencia: Quedan menos de 10 unidades del producto seleccionado.";
        } else {
            $success = "Producto asignado con éxito.";
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $pdo->rollBack();
        $error = "Error al asignar el producto: " . $e->getMessage();
    }
}

// Obtener productos con cantidad mayor a 0
$productos = $pdo->query("SELECT * FROM productos WHERE cantidad > 0")->fetchAll(PDO::FETCH_ASSOC);
$personal = $pdo->query("SELECT * FROM personal")->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="mb-4">Asignar Producto</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($warning)): ?>
            <div class="alert alert-warning" role="alert">
                <?php echo $warning; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="asignaciones.php">
            <div class="mb-3">
                <label for="producto" class="form-label">Selecciona el Producto</label>
                <select name="producto" id="producto" class="form-select" required>
                    <?php if (empty($productos)): ?>
                        <option value="">No hay productos disponibles</option>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto['id']; ?>"><?php echo $producto['nombre']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="personal" class="form-label">Selecciona el Personal</label>
                <select name="personal" id="personal" class="form-select" required>
                    <?php foreach ($personal as $persona): ?>
                        <option value="<?php echo $persona['id']; ?>"><?php echo $persona['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" placeholder="Cantidad" required min="1">
            </div>
            <button type="submit" class="btn btn-primary">Asignar Producto</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
