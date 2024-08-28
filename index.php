<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
include 'header.php';

// Si el usuario ya ha iniciado sesión, redirigirlo al panel correspondiente
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else if ($_SESSION['role'] == 'user') {
        header('Location: user/user_menu.php');
    }
    exit;
}

// Si no ha iniciado sesión, mostrar un mensaje de bienvenida y opciones de inicio de sesión o registro
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <h1 class="my-4">Bienvenido al Sistema de Inventario</h1>
        <p>Por favor, inicia sesión.</p>
        
        <div class="btn-group" role="group">
            <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
            <a href="register.php" class="btn btn-secondary">Registrarse</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
