<?php
session_start();
include 'config/db.php';

// Habilitar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Verificar carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Obtener usuario "Sistema" para ventas web
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = 'sistema@ferreteria.com' OR rol = 'admin' LIMIT 1");
        $stmt->execute();
        $usuario_sistema = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $id_usuario = $usuario_sistema['id'] ?? 1; // Usar id 1 como fallback
        
        // Datos del cliente
        $tipo_comprobante = $_POST['tipo_comprobante'] ?? '';
        $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
        $tipo_documento = $_POST['tipo_documento'] ?? '';
        $numero_documento = trim($_POST['numero_documento'] ?? '');
        $email_cliente = trim($_POST['email_cliente'] ?? '');
        $telefono_cliente = trim($_POST['telefono_cliente'] ?? '');
        $direccion_cliente = trim($_POST['direccion_cliente'] ?? '');
        
        // Validaciones
        if (empty($nombre_cliente)) {
            throw new Exception('Ingrese su nombre completo');
        }
        
        if (empty($tipo_documento)) {
            throw new Exception('Seleccione tipo de documento');
        }
        
        if (empty($numero_documento)) {
            throw new Exception('Ingrese número de documento');
        }
        
        if (empty($telefono_cliente)) {
            throw new Exception('Ingrese su teléfono');
        }
        
        // Validar formato de documento
        if ($tipo_documento === 'DNI' && !preg_match('/^\d{8}$/', $numero_documento)) {
            throw new Exception('El DNI debe tener 8 dígitos');
        }
        
        if ($tipo_documento === 'RUC' && !preg_match('/^\d{11}$/', $numero_documento)) {
            throw new Exception('El RUC debe tener 11 dígitos');
        }
        
        if ($tipo_comprobante === 'factura' && $tipo_documento !== 'RUC') {
            throw new Exception('Para factura se requiere RUC');
        }
        
        // Validar email si se proporciona
        if (!empty($email_cliente) && !filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Calcular total y verificar stock
        $total = 0;
        $detalles = [];
        
        foreach ($_SESSION['carrito'] as $id_producto => $item) {
            if (!is_array($item) || !isset($item['precio'], $item['cantidad'])) {
                continue;
            }
            
            $precio = floatval($item['precio']);
            $cantidad = intval($item['cantidad']);
            
            if ($cantidad < 1) {
                throw new Exception('Cantidad inválida para producto ID: ' . $id_producto);
            }
            
            // Verificar stock en tiempo real
            $stmt = $pdo->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                throw new Exception('Producto no encontrado: ID ' . $id_producto);
            }
            
            if ($producto['stock'] < $cantidad) {
                throw new Exception('Stock insuficiente para: ' . $producto['nombre'] . 
                                  '. Disponible: ' . $producto['stock']);
            }
            
            $subtotal = $precio * $cantidad;
            $total += $subtotal;
            
            $detalles[] = [
                'id_producto' => $id_producto,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
                'nombre' => $producto['nombre']
            ];
        }
        
        if ($total <= 0) {
            throw new Exception('Error en el cálculo del total');
        }
        
        // Generar número de comprobante
        $prefijo = $tipo_comprobante === 'boleta' ? 'B' : 'F';
        $fecha = date('Ymd');
        $secuencia = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $numero_comprobante = $prefijo . '-' . $fecha . '-' . $secuencia;
        
        // Insertar venta (usar id_usuario del sistema)
        $sql_venta = "INSERT INTO ventas (
            id_usuario, total, tipo_documento, numero_documento,
            nombre_cliente, tipo_documento_cliente, numero_documento_cliente,
            email_cliente, telefono_cliente, direccion_cliente, fecha
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql_venta);
        $result = $stmt->execute([
            $id_usuario,  // <-- Aquí está la corrección
            $total,
            $tipo_comprobante,
            $numero_comprobante,
            $nombre_cliente,
            $tipo_documento,
            $numero_documento,
            $email_cliente,
            $telefono_cliente,
            $direccion_cliente
        ]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception('Error al insertar venta: ' . ($errorInfo[2] ?? 'Error desconocido'));
        }
        
        $id_venta = $pdo->lastInsertId();
        
        if (!$id_venta) {
            throw new Exception('No se pudo obtener el ID de la venta');
        }
        
        // Insertar detalles y actualizar stock
        foreach ($detalles as $detalle) {
            // Insertar detalle
            $sql_detalle = "INSERT INTO detalle_ventas (
                id_venta, id_producto, cantidad, precio_unitario, subtotal
            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql_detalle);
            $result = $stmt->execute([
                $id_venta,
                $detalle['id_producto'],
                $detalle['cantidad'],
                $detalle['precio_unitario'],
                $detalle['subtotal']
            ]);
            
            if (!$result) {
                throw new Exception('Error al insertar detalle de venta');
            }
            
            // Actualizar stock
            $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt = $pdo->prepare($sql_update_stock);
            $result = $stmt->execute([$detalle['cantidad'], $detalle['id_producto']]);
            
            if (!$result) {
                throw new Exception('Error al actualizar stock');
            }
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Limpiar carrito
        unset($_SESSION['carrito']);
        
        // Responder éxito
        echo json_encode([
            'success' => true,
            'message' => 'Compra procesada exitosamente',
            'id_venta' => $id_venta,
            'numero_comprobante' => $numero_comprobante,
            'total' => number_format($total, 2)
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log del error
        error_log('Error en procesar_compra: ' . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>