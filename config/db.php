<?php
 $host = 'localhost';
 $dbname = 'monteroa_ferreteria';
 $user = 'monteroa_ferreteria';
 $pass = 'Martin1528!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Configuraci贸n adicional de seguridad
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En producci贸n, no mostrar detalles del error
    die("Error de conexi贸n a la base de datos. Por favor contacte al administrador.");
}
?>
