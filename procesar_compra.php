<?php
session_start();
include 'config/db.php';

// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Función para registrar en el log del servidor
function logDebug($message) {
    error_log("[DEBUG] " . $message);
}

// Cabecera para respuesta JSON
header('Content-Type: application/json');

logDebug("Iniciando procesar_compra.php");
logDebug("Método de solicitud: " . $_SERVER['REQUEST_METHOD']);

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    logDebug("Carrito vacío");
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Registrar datos recibidos
        logDebug("Datos POST recibidos: " . print_r($_POST, true));
        
        // Recibir datos del cliente
        $tipo_comprobante = $_POST['tipo_comprobante'] ?? '';
        $nombre_cliente = $_POST['nombre_cliente'] ?? '';
        $tipo_documento = $_POST['tipo_documento'] ?? '';
        $numero_documento = $_POST['numero_documento'] ?? '';
        $email_cliente = $_POST['email_cliente'] ?? '';
        $telefono_cliente = $_POST['telefono_cliente'] ?? '';
        $direccion_cliente = $_POST['direccion_cliente'] ?? '';
        
        logDebug("Tipo comprobante: $tipo_comprobante");
        logDebug("Nombre cliente: $nombre_cliente");
        
        // Validar datos mínimos
        if (empty($tipo_comprobante) || empty($nombre_cliente) || empty($tipo_documento) || empty($numero_documento)) {
            logDebug("Faltan datos obligatorios");
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
            exit;
        }
        
        // Iniciar transacción
        $pdo->beginTransaction();
        logDebug("Transacción iniciada");
        
        // Calcular total y preparar detalles
        $total = 0;
        $detalles = [];
        
        logDebug("Procesando carrito: " . print_r($_SESSION['carrito'], true));
        
        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto) {
                // Asegurarse de que el precio sea un número válido
                $precio = floatval($producto['precio']);
                $subtotal = $precio * $cantidad;
                $total += $subtotal;
                
                $detalles[] = [
                    'id_producto' => $id_producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal
                ];
                
                logDebug("Producto: {$producto['nombre']}, Precio: $precio, Cantidad: $cantidad, Subtotal: $subtotal");
            }
        }
        
        logDebug("Total calculado: $total");
        
        // Generar número de comprobante
        $prefijo = $tipo_comprobante === 'boleta' ? 'B' : 'F';
        $numero_comprobante = $prefijo . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        logDebug("Número de comprobante generado: $numero_comprobante");
        
        // Insertar venta
        $sql = "INSERT INTO ventas (id_usuario, total, tipo_documento, numero_documento, nombre_cliente, tipo_documento_cliente, numero_documento_cliente, email_cliente, telefono_cliente, direccion_cliente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([1, $total, $tipo_comprobante, $numero_comprobante, $nombre_cliente, $tipo_documento, $numero_documento, $email_cliente, $telefono_cliente, $direccion_cliente]);
        
        if (!$result) {
            logDebug("Error al insertar venta: " . print_r($stmt->errorInfo(), true));
            throw new Exception("Error al insertar venta");
        }
        
        $id_venta = $pdo->lastInsertId();
        logDebug("Venta insertada con ID: $id_venta");
        
        // Insertar detalles de venta
        foreach ($detalles as $detalle) {
            $sql = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $id_venta,
                $detalle['id_producto'],
                $detalle['cantidad'],
                $detalle['precio_unitario'],
                $detalle['subtotal']
            ]);
            
            if (!$result) {
                logDebug("Error al insertar detalle: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error al insertar detalle");
            }
        }
        
        logDebug("Todos los detalles insertados correctamente");
        
        // Confirmar transacción
        $pdo->commit();
        logDebug("Transacción confirmada");
        
        // Limpiar carrito
        unset($_SESSION['carrito']);
        logDebug("Carrito limpiado");
        
        // Devolver éxito
        echo json_encode(['success' => true, 'id_venta' => $id_venta]);
        
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
            logDebug("Transacción revertida debido a error: " . $e->getMessage());
        }
        
        echo json_encode(['success' => false, 'message' => 'Error al procesar la compra: ' . $e->getMessage()]);
    }
    exit;
} else {
    logDebug("Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
?>