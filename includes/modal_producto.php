<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_producto = $_POST['id_producto'];
    
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($producto) {
        // Asegurarse de que el precio sea un número válido
        $precio = (float) $producto['precio'];
        ?>
        <div class="row">
            <div class="col-md-5">
                <img src="../assets/img/<?php echo $producto['imagen']; ?>" class="img-fluid rounded" alt="<?php echo $producto['nombre']; ?>">
            </div>
            <div class="col-md-7">
                <h3><?php echo $producto['nombre']; ?></h3>
                
                <div class="mb-3">
                    <span class="h3 text-primary">S/<?php echo number_format($precio, 2, '.', ','); ?></span>
                    <span class="badge bg-<?php echo $producto['stock'] > 0 ? 'success' : 'danger'; ?> ms-2">
                        <?php echo $producto['stock'] > 0 ? 'Disponible' : 'Agotado'; ?>
                    </span>
                </div>
                
                <div class="mb-3">
                    <h5>Descripción:</h5>
                    <p><?php echo nl2br($producto['descripcion']); ?></p>
                </div>
                
                <div class="mb-3">
                    <h5>Características:</h5>
                    <ul>
                        <li>Stock disponible: <?php echo $producto['stock']; ?> unidades</li>
                        <li>Stock mínimo: <?php echo $producto['stock_minimo']; ?> unidades</li>
                        <li>Garantía: 12 meses</li>
                    </ul>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg agregar-carrito-modal" 
                            data-id="<?php echo $producto['id']; ?>"
                            <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <i class="bi bi-cart-plus me-2"></i>
                        <?php echo $producto['stock'] <= 0 ? 'Producto agotado' : 'Agregar al carrito'; ?>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
        
        <script>
        // Agregar al carrito desde el modal
        $('.agregar-carrito-modal').click(function() {
            let id_producto = $(this).data('id');
            let btn = $(this);
            
            $.ajax({
                url: '../includes/carrito.php',
                type: 'POST',
                dataType: 'json',
                data: {id_producto: id_producto, cantidad: 1},
                success: function(response) {
                    if (response.success) {
                        // Actualizar contador del carrito
                        let count = parseInt($('.badge').text()) + 1;
                        $('.badge').text(count);
                        
                        // Cambiar estado del botón
                        btn.html('<i class="bi bi-check-circle me-2"></i> Agregado al carrito');
                        btn.removeClass('btn-primary').addClass('btn-success');
                        
                        // Actualizar el carrito sin recargar la página
                        actualizarCarrito();
                        
                        setTimeout(function() {
                            $('#modalProducto').modal('hide');
                        }, 1500);
                    } else {
                        alert(response.message || 'Error al agregar el producto al carrito');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error al agregar el producto al carrito');
                }
            });
        });
        </script>
        <?php
    }
}
?>