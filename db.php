<?php
$host = 'localhost'; // O la dirección del servidor de base de datos proporcionada por tu hosting
$db   = 'inventario'; // Verifica el nombre exacto de la base de datos
$user = 'root'; // Verifica el nombre de usuario exacto
$pass = 'root'; // Verifica la contraseña exacta
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage(); // Muestra el error de conexión para depuración
    exit;
}
?>
