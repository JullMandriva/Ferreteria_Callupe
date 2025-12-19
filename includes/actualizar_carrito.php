<?php
session_start();

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    $total = 0;
    $total_items = 0;
    ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="40%">Producto</th>
                    <th width="15%">Precio Unit.</th>
                    <th width="20%">Cantidad</th>
                    <th width="15%">Subtotal</th>
                    <th width="10%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($_SESSION['carrito'] as $id => $item): 
                    if (!is_array($item) || !isset($item['precio']) || !isset($item['cantidad'])) {
                        continue;
                    }
                    
                    $precio = floatval($item['precio']);
                    $cantidad = intval($item['cantidad']);
                    $subtotal = $precio * $cantidad;
                    $total += $subtotal;
                    $total_items += $cantidad;
                    
                    $precio_formateado = number_format($precio, 2, '.', '');
                    $subtotal_formateado = number_format($subtotal, 2, '.', '');
                ?>
                <tr data-id="<?php echo $id; ?>">
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="../assets/img/<?php echo htmlspecialchars($item['imagen'] ?? 'default.jpg'); ?>" 
                                 width="50" height="50" class="rounded me-3 object-fit-cover">
                            <div>
                                <strong><?php echo htmlspecialchars($item['nombre'] ?? 'Producto'); ?></strong><br>
                                <small class="text-muted">Código: <?php echo $id; ?></small>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <span class="h6 mb-0">S/<?php echo $precio_formateado; ?></span>
                    </td>
                    <td class="align-middle">
                        <div class="input-group input-group-sm" style="width: 120px;">
                            <button class="btn btn-outline-secondary actualizar-cantidad" 
                                    type="button" 
                                    data-id="<?php echo $id; ?>"
                                    data-action="decrement">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="text" class="form-control text-center cantidad-producto" 
                                   value="<?php echo $cantidad; ?>"
                                   data-id="<?php echo $id; ?>"
                                   data-precio="<?php echo $precio; ?>"
                                   readonly>
                            <button class="btn btn-outline-secondary actualizar-cantidad" 
                                    type="button"
                                    data-id="<?php echo $id; ?>"
                                    data-action="increment">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="align-middle">
                        <span class="h6 text-primary subtotal-item">S/<?php echo $subtotal_formateado; ?></span>
                    </td>
                    <td class="align-middle">
                        <button class="btn btn-sm btn-danger eliminar-carrito" 
                                data-id="<?php echo $id; ?>"
                                title="Eliminar producto">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <td colspan="3" class="text-end">
                        <h5 class="mb-0">Total (<?php echo $total_items; ?> productos):</h5>
                    </td>
                    <td>
                        <h4 class="mb-0 text-success">S/<span id="total-carrito"><?php echo number_format($total, 2, '.', ''); ?></span></h4>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning vaciar-carrito">
                            <i class="bi bi-cart-x"></i> Vaciar
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Botones de acción -->
        <div class="d-flex justify-content-between mt-4">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                <i class="bi bi-arrow-left me-2"></i> Seguir comprando
            </button>
            <div>
                <button class="btn btn-warning me-2 vaciar-carrito">
                    <i class="bi bi-cart-x me-2"></i> Vaciar carrito
                </button>
                <button class="btn btn-success btn-lg" id="btnProcesarCompra">
                    <i class="bi bi-credit-card me-2"></i> Proceder al pago
                </button>
            </div>
        </div>
    </div>
    
    <script>
    // Función para formatear números
    function formatearNumero(num) {
        return parseFloat(num).toFixed(2);
    }
    
    // Actualizar cantidad
    $(document).on('click', '.actualizar-cantidad', function() {
        const id = $(this).data('id');
        const action = $(this).data('action');
        const input = $(this).siblings('.cantidad-producto');
        const precio = parseFloat(input.data('precio'));
        let cantidadActual = parseInt(input.val());
        
        let nuevaCantidad = action === 'increment' ? cantidadActual + 1 : cantidadActual - 1;
        
        if (nuevaCantidad < 1) {
            if (confirm('¿Desea eliminar este producto del carrito?')) {
                eliminarProducto(id);
            }
            return;
        }
        
        // Actualizar en servidor
        $.ajax({
            url: '../includes/carrito.php',
            type: 'POST',
            data: {
                actualizar_cantidad: 1,
                id_producto: id,
                cantidad: nuevaCantidad
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Actualizar input
                    input.val(nuevaCantidad);
                    
                    // Calcular nuevo subtotal
                    const nuevoSubtotal = precio * nuevaCantidad;
                    input.closest('tr').find('.subtotal-item').text('S/' + formatearNumero(nuevoSubtotal));
                    
                    // Actualizar total general
                    $('#total-carrito').text(formatearNumero(response.total_carrito));
                    
                    // Actualizar contador
                    actualizarContadorCarrito();
                    
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al actualizar la cantidad', 'error');
            }
        });
    });
    
    // Eliminar producto
    function eliminarProducto(id) {
        $.ajax({
            url: '../includes/carrito.php',
            type: 'GET',
            data: {eliminar: id},
            success: function() {
                // Recargar carrito
                actualizarCarrito();
                actualizarContadorCarrito();
            },
            error: function() {
                Swal.fire('Error', 'Error al eliminar el producto', 'error');
            }
        });
    }
    
    $(document).on('click', '.eliminar-carrito', function() {
        if (!confirm('¿Está seguro de eliminar este producto del carrito?')) return;
        
        const id = $(this).data('id');
        eliminarProducto(id);
    });
    
    // Vaciar carrito
    $(document).on('click', '.vaciar-carrito', function() {
        if (!confirm('¿Está seguro de vaciar todo el carrito?')) return;
        
        $.ajax({
            url: '../includes/carrito.php',
            type: 'GET',
            data: {vaciar: 1},
            success: function() {
                // Recargar carrito
                actualizarCarrito();
                actualizarContadorCarrito();
                
                // Cerrar modal si está vacío
                setTimeout(function() {
                    if ($('#contador-carrito').text() === '0') {
                        $('#modalCarrito').modal('hide');
                    }
                }, 500);
            },
            error: function() {
                Swal.fire('Error', 'Error al vaciar el carrito', 'error');
            }
        });
    });
    
    // Actualizar contador
    function actualizarContadorCarrito() {
        $.ajax({
            url: '../includes/get_contador_carrito.php',
            type: 'GET',
            success: function(response) {
                $('#contador-carrito').text(response);
            }
        });
    }
    </script>
    
    <?php
} else {
    ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
        <h4>Tu carrito está vacío</h4>
        <p class="text-muted mb-4">Agrega productos para comenzar tu compra</p>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
            <i class="bi bi-arrow-left me-2"></i> Seguir comprando
        </button>
    </div>
    <?php
}
?>