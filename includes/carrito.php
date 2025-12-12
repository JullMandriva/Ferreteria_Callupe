<?php
session_start();
include '../config/db.php';

// Agregar producto al carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    
    // Verificar stock disponible
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($producto && $producto['stock'] >= $cantidad) {
        // Iniciar transacción
        $pdo->beginTransaction();
        
        try {
            // Reservar stock temporalmente
            $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$cantidad, $id_producto]);
            
            // Agregar al carrito
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }
            
            if (isset($_SESSION['carrito'][$id_producto])) {
                $_SESSION['carrito'][$id_producto] += $cantidad;
            } else {
                $_SESSION['carrito'][$id_producto] = $cantidad;
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error al agregar al carrito']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
    }
    exit;
}

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $id_producto = $_GET['eliminar'];
    
    if (isset($_SESSION['carrito'][$id_producto])) {
        // Devolver stock al inventario
        $cantidad = $_SESSION['carrito'][$id_producto];
        $stmt = $pdo->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$cantidad, $id_producto]);
        
        unset($_SESSION['carrito'][$id_producto]);
    }
    
    header('Location: ../index.php');
    exit;
}
?>