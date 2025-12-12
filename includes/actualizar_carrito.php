<?php
session_start();
include '../config/db.php';

// Función para formatear número correctamente
function formatearNumero($numero) {
    // Asegurarse de que sea un número, limpiando cualquier carácter no numérico
    $numero = preg_replace('/[^0-9.]/', '', $numero);
    $numero = floatval($numero);
    // Formatear con 2 decimales
    return number_format($numero, 2, '.', ',');
}

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])):
    $total = 0;
    ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio (BD)</th>
                <th>Precio (Convertido)</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($_SESSION['carrito'] as $id => $cantidad): 
                $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($producto) {
                    // Depuración: mostrar el precio original
                    echo "<!-- Precio original: " . $producto['precio'] . " -->";
                    
                    // Asegurarse de que el precio sea un número válido
                    $precio = floatval($producto['precio']);
                    echo "<!-- Precio convertido: " . $precio . " -->";
                    
                    // Calcular subtotal
                    $subtotal = $precio * $cantidad;
                    echo "<!-- Subtotal calculado: " . $subtotal . " -->";
                    
                    // Sumar al total (asegurarse de que sea un número)
                    $total = floatval($total) + floatval($subtotal);
                    echo "<!-- Total acumulado: " . $total . " -->";
            ?>
            <tr>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['precio']; ?></td>
                <td>S/<?php echo formatearNumero($precio); ?></td>
                <td><?php echo $cantidad; ?></td>
                <td>S/<?php echo formatearNumero($subtotal); ?></td>
                <td>
                    <button class="btn btn-sm btn-danger eliminar-carrito" data-id="<?php echo $id; ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php } endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total:</th>
                <th>S/<?php echo formatearNumero($total); ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <!-- Total sin formatear para depuración: <?php echo $total; ?> -->
<?php else: ?>
    <div class="text-center py-4">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <p class="mt-3">Tu carrito está vacío</p>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Seguir comprando</button>
    </div>
<?php endif; ?>