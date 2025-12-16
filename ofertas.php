<?php
session_start();
include 'config/db.php';
include 'includes/header_public.php';

// Obtener productos en oferta (podemos definir que oferta son productos con precio < 100)
$stmt = $pdo->query("SELECT * FROM productos WHERE precio < 100 ORDER BY precio ASC");
$ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <!-- Header con buscador -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Ofertas Especiales</h1>
        <div class="d-flex align-items-center">
            <a href="index.php" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
            </a>
            <?php include 'includes/carrito_boton.php'; ?>
        </div>
    </div>
    
    <!-- Banner de ofertas -->
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-tag-fill display-6 me-3"></i>
            <div>
                <h4 class="alert-heading mb-1">¡Ofertas Relámpago!</h4>
                <p class="mb-0">Precios especiales por tiempo limitado. ¡Aprovecha!</p>
            </div>
        </div>
    </div>
    
    <?php if (!empty($ofertas)): ?>
        <div class="row">
            <?php foreach ($ofertas as $producto): 
                $descuento = round((150 - $producto['precio']) / 150 * 100); // Ejemplo de cálculo
            ?>
                <div class="col-md-3 col-lg-3 mb-4">
                    <div class="card h-100 border-warning producto-card shadow-sm">
                        <div class="position-relative">
                            <!-- Badge de oferta -->
                            <span class="position-absolute top-0 start-0 badge bg-danger m-2">
                                -<?php echo $descuento; ?>%
                            </span>
                            
                            <!-- Badge de quiebre stock -->
                            <?php if ($producto['stock'] <= $producto['stock_minimo']): ?>
                                <span class="position-absolute top-0 end-0 badge bg-danger m-2">¡Quiebre!</span>
                            <?php endif; ?>
                            
                            <img src="assets/img/<?php echo $producto['imagen']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo $producto['nombre']; ?>"
                                 style="height: 180px; object-fit: cover;">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                            <p class="card-text flex-grow-1">
                                <?php echo substr($producto['descripcion'], 0, 50) . '...'; ?>
                            </p>
                            
                            <!-- Precios -->
                            <div class="mb-2">
                                <span class="text-muted text-decoration-line-through me-2">
                                    S/<?php echo number_format($producto['precio'] * 1.5, 2); ?>
                                </span>
                                <span class="h4 text-danger mb-0">
                                    S/<?php echo number_format($producto['precio'], 2); ?>
                                </span>
                            </div>
                            
                            <!-- Stock y ahorro -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-box"></i> <?php echo $producto['stock']; ?>
                                </span>
                                <span class="badge bg-success">
                                    <i class="bi bi-coin"></i> Ahorras S/<?php echo number_format($producto['precio'] * 0.5, 2); ?>
                                </span>
                            </div>
                            
                            <!-- Botones -->
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-warning btn-sm ver-detalle" 
                                        data-id="<?php echo $producto['id']; ?>">
                                    <i class="bi bi-eye me-1"></i> Ver detalles
                                </button>
                                <button class="btn btn-danger btn-sm agregar-carrito" 
                                        data-id="<?php echo $producto['id']; ?>"
                                        <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                                    <i class="bi bi-cart-plus me-1"></i>
                                    <?php echo $producto['stock'] <= 0 ? 'Agotado' : '¡Aprovechar oferta!'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-emoji-frown display-1 text-muted"></i>
            <h3 class="mt-3">No hay ofertas disponibles</h3>
            <p class="text-muted">Vuelve más tarde para ver nuestras promociones</p>
            <a href="index.php" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i> Ver catálogo completo
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Oferta especial del día -->
    <div class="card bg-dark text-white mt-5">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="card-title">¡Oferta del Día!</h3>
                    <p class="card-text">Martillo profesional con 40% de descuento solo por hoy.</p>
                    <p class="card-text"><small>Válido hasta las 11:59 PM</small></p>
                    <a href="#" class="btn btn-warning">
                        <i class="bi bi-lightning-charge me-1"></i> Comprar ahora
                    </a>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-lightning display-1 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles del producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-producto-content">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Ver detalles del producto
    $('.ver-detalle').click(function() {
        let id_producto = $(this).data('id');
        
        $.ajax({
            url: 'includes/modal_producto.php',
            type: 'POST',
            data: {id_producto: id_producto},
            success: function(response) {
                $('#modal-producto-content').html(response);
                $('#modalProducto').modal('show');
            }
        });
    });
    
    // Agregar al carrito
    $('.agregar-carrito').click(function() {
        let id_producto = $(this).data('id');
        let btn = $(this);
        
        $.ajax({
            url: 'includes/carrito.php',
            type: 'POST',
            data: {id_producto: id_producto, cantidad: 1},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Actualizar contador
                    $('.badge.bg-danger').text(response.total_items);
                    
                    // Cambiar botón
                    btn.html('<i class="bi bi-check-circle me-1"></i> Agregado');
                    btn.removeClass('btn-danger').addClass('btn-success');
                    
                    setTimeout(function() {
                        btn.html('<i class="bi bi-cart-plus me-1"></i> ¡Aprovechar oferta!');
                        btn.removeClass('btn-success').addClass('btn-danger');
                    }, 2000);
                } else {
                    alert(response.message);
                }
            }
        });
    });
});
</script>

<?php include 'includes/footer_public.php'; ?>