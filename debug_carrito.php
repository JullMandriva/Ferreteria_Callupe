<?php
session_start();
include '../config/db.php';

echo "<h1>Depuración del Carrito</h1>";

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "<p>El carrito está vacío</p>";
} else {
    echo "<h2>Contenido de la sesión del carrito:</h2>";
    echo "<pre>";
    print_r($_SESSION['carrito']);
    echo "</pre>";
    
    echo "<h2>Detalles de los productos:</h2>";
    $total = 0;
    
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            echo "<h3>Producto: " . $producto['nombre'] . "</h3>";
            echo "<p>Precio original: " . $producto['precio'] . " (tipo: " . gettype($producto['precio']) . ")</p>";
            
            // Convertir a número
            $precio = floatval($producto['precio']);
            echo "<p>Precio convertido: " . $precio . " (tipo: " . gettype($precio) . ")</p>";
            
            // Calcular subtotal
            $subtotal = $precio * $cantidad;
            echo "<p>Subtotal: " . $subtotal . " (tipo: " . gettype($subtotal) . ")</p>";
            echo "<hr>";
            
            $total += $subtotal;
        }
    }
    
    echo "<h2>Total final: " . $total . "</h2>";
}
?>