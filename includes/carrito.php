<?php
session_start();
include '../config/db.php';

// Función para calcular total del carrito
function calcularTotalCarrito() {
    $total_carrito = 0;
    $total_items = 0;
    
    if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            if (is_array($item) && isset($item['precio'], $item['cantidad'])) {
                $precio = floatval($item['precio']);
                $cantidad = intval($item['cantidad']);
                $total_carrito += $precio * $cantidad;
                $total_items += $cantidad;
            }
        }
    }
    
    return [
        'total' => $total_carrito,
        'items' => $total_items
    ];
}

// Agregar producto al carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_producto'])) {
    $id_producto = intval($_POST['id_producto']);
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
    
    if ($cantidad < 1) {
        echo json_encode(['success' => false, 'message' => 'Cantidad inválida']);
        exit;
    }
    
    // Verificar producto en base de datos
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND stock > 0");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        echo json_encode(['success' => false, 'message' => 'Producto no disponible']);
        exit;
    }
    
    // Verificar stock
    if ($producto['stock'] < $cantidad) {
        echo json_encode(['success' => false, 'message' => 'Stock insuficiente. Disponible: ' . $producto['stock']]);
        exit;
    }
    
    // Inicializar carrito si no existe
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    // Preparar datos del producto
    $producto_carrito = [
        'id' => $id_producto,
        'nombre' => $producto['nombre'],
        'descripcion' => $producto['descripcion'],
        'precio' => floatval($producto['precio']),
        'imagen' => $producto['imagen'],
        'stock' => $producto['stock']
    ];
    
    // Agregar o actualizar en carrito
    if (isset($_SESSION['carrito'][$id_producto])) {
        // Actualizar cantidad
        $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
    } else {
        // Agregar nuevo producto
        $producto_carrito['cantidad'] = $cantidad;
        $_SESSION['carrito'][$id_producto] = $producto_carrito;
    }
    
    // Calcular totales
    $totales = calcularTotalCarrito();
    
    echo json_encode([
        'success' => true,
        'message' => 'Producto agregado al carrito',
        'total_items' => $totales['items'],
        'total_carrito' => number_format($totales['total'], 2, '.', '')
    ]);
    exit;
}

// Actualizar cantidad en el carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_cantidad'])) {
    $id_producto = intval($_POST['id_producto']);
    $nueva_cantidad = intval($_POST['cantidad']);
    
    // Verificar que el producto esté en el carrito
    if (!isset($_SESSION['carrito'][$id_producto])) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
        exit;
    }
    
    if ($nueva_cantidad < 1) {
        echo json_encode(['success' => false, 'message' => 'La cantidad debe ser al menos 1']);
        exit;
    }
    
    // Verificar stock disponible
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto || $producto['stock'] < $nueva_cantidad) {
        echo json_encode(['success' => false, 'message' => 'Stock insuficiente. Disponible: ' . ($producto['stock'] ?? 0)]);
        exit;
    }
    
    // Actualizar cantidad
    $_SESSION['carrito'][$id_producto]['cantidad'] = $nueva_cantidad;
    
    // Calcular nuevos totales
    $totales = calcularTotalCarrito();
    
    echo json_encode([
        'success' => true,
        'total_carrito' => number_format($totales['total'], 2, '.', ''),
        'total_items' => $totales['items']
    ]);
    exit;
}

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $id_producto = intval($_GET['eliminar']);
    
    if (isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]);
    }
    
    // Si el carrito queda vacío, eliminarlo
    if (empty($_SESSION['carrito'])) {
        unset($_SESSION['carrito']);
    }
    
    // Redirigir a la página anterior
    $referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header('Location: ' . $referer);
    exit;
}

// Vaciar carrito
if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);
    
    $referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header('Location: ' . $referer);
    exit;
}

// Si se accede directamente, redirigir
header('Location: ../index.php');
exit;
?>