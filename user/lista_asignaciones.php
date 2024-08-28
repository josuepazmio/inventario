<?php
include '../header.php';
require '../db.php';
session_start();

if ($_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit;
}

// Verificar si el ID de sesión está definido
if (!isset($_SESSION['id'])) {
    die("Error: ID de sesión no está definido.");
}

// Inicializar las variables de fecha
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

// Construir la consulta SQL con el filtro de fechas
$sql = "SELECT asignaciones.id, productos.nombre AS producto, personal.nombre AS personal, asignaciones.cantidad, asignaciones.fecha
        FROM asignaciones
        JOIN productos ON asignaciones.producto = productos.id
        JOIN personal ON asignaciones.personal = personal.id
        WHERE asignaciones.personal = ?";

$params = [$_SESSION['id']];

if ($fecha_inicio && $fecha_fin) {
    $sql .= " AND asignaciones.fecha BETWEEN ? AND ?";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
}

$stmt = $pdo->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . print_r($pdo->errorInfo(), true));
}

$stmt->execute($params);
$asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular el total de insumos por producto
$total_insumos = [];
foreach ($asignaciones as $asignacion) {
    if (!isset($total_insumos[$asignacion['producto']])) {
        $total_insumos[$asignacion['producto']] = 0;
    }
    $total_insumos[$asignacion['producto']] += $asignacion['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Asignaciones</title>
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
        <h1 class="mb-4">Lista de Asignaciones</h1>

        <!-- Formulario de Filtro por Fecha -->
        <form method="POST" action="lista_asignaciones.php" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="fecha_inicio">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="fecha_fin">Fecha de Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
        </form>

        <?php if (empty($asignaciones)): ?>
            <div class="alert alert-info" role="alert">
                No hay asignaciones registradas.
            </div>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                        <th>Personal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asignaciones as $asignacion): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($asignacion['id']); ?></td>
                            <td><?php echo htmlspecialchars($asignacion['producto']); ?></td>
                            <td><?php echo htmlspecialchars($asignacion['cantidad']); ?></td>
                            <td><?php echo htmlspecialchars($asignacion['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($asignacion['personal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Mostrar el total de insumos por producto -->
            <h3 class="mt-4">Total de Insumos por Producto</h3>
            <ul class="list-group">
                <?php foreach ($total_insumos as $producto => $total): ?>
                    <li class="list-group-item">
                        Producto: <?php echo htmlspecialchars($producto); ?> - Total Insumos: <?php echo htmlspecialchars($total); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
