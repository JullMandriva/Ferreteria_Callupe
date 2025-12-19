<?php
session_start();
include 'config/db.php';
include 'includes/header_public.php';

// Obtener productos en oferta
$stmt = $pdo->query("SELECT * FROM productos WHERE precio < 50 ORDER BY precio ASC");
$productos_oferta = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos con descuento (simulado)
$stmt = $pdo->query("SELECT * FROM productos WHERE stock > 10 ORDER BY RAND() LIMIT 6");
$productos_descuento = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Ofertas Especiales</h1>
            <p class="lead">Aprovecha nuestras increíbles ofertas y descuentos exclusivos. ¡Las mejores herramientas al mejor precio!</p>
        </div>
    </div>
    
    <!-- Banner de ofertas -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-fire fs-1 me-3"></i>
                    <div>
                        <h4 class="alert-heading">¡Gran Oferta Semanal!</h4>
                        <p class="mb-0">20% de descuento en herramientas eléctricas. Válido hasta el fin de mes.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    
    <!-- Productos en oferta -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Productos en Oferta</h2>
            <p class="text-muted">Precios especiales en productos seleccionados</p>
        </div>
        
        <?php foreach ($productos_oferta as $producto): ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card h-100 oferta-card">
                    <div class="position-relative">
                        <img src="assets/img/<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo $producto['nombre']; ?>">
                        <span class="position-absolute top-0 start-0 badge bg-danger m-2">OFERTA</span>
                        <span class="position-absolute top-0 end-0 badge bg-success m-2">
                            <?php echo rand(10, 30); ?>% OFF
                        </span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                        <p class="card-text flex-grow-1"><?php echo substr($producto['descripcion'], 0, 60) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted text-decoration-line-through">S/<?php echo number_format($producto['precio'] * 1.3, 2); ?></span>
                            <span class="h5 mb-0 text-danger">S/<?php echo number_format($producto['precio'], 2); ?></span>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm ver-detalle" 
                                    data-id="<?php echo $producto['id']; ?>"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalProducto">
                                Ver detalles
                            </button>
                            <button class="btn btn-danger btn-sm agregar-carrito" 
                                    data-id="<?php echo $producto['id']; ?>"
                                    <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <?php echo $producto['stock'] <= 0 ? 'Agotado' : 'Agregar al carrito'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Productos con descuento -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Descuentos Especiales</h2>
            <p class="text-muted">Productos seleccionados con descuentos por tiempo limitado</p>
        </div>
        
        <?php foreach ($productos_descuento as $producto): ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="assets/img/<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo $producto['nombre']; ?>">
                        <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                            <?php echo rand(5, 15); ?>% DTO
                        </span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                        <p class="card-text flex-grow-1"><?php echo substr($producto['descripcion'], 0, 60) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted text-decoration-line-through">S/<?php echo number_format($producto['precio'] * 1.2, 2); ?></span>
                            <span class="h5 mb-0 text-warning">S/<?php echo number_format($producto['precio'], 2); ?></span>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm ver-detalle" 
                                    data-id="<?php echo $producto['id']; ?>"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalProducto">
                                Ver detalles
                            </button>
                            <button class="btn btn-warning btn-sm agregar-carrito" 
                                    data-id="<?php echo $producto['id']; ?>"
                                    <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <?php echo $producto['stock'] <= 0 ? 'Agotado' : 'Agregar al carrito'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Banner de promoción -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="card-title">¡No te pierdas nuestras ofertas!</h3>
                    <p class="card-text">Suscríbete a nuestro newsletter y recibe un 10% de descuento en tu próxima compra.</p>
                    <div class="d-flex justify-content-center">
                        <div class="input-group" style="max-width: 400px;">
                            <input type="email" class="form-control" placeholder="Tu email">
                            <button class="btn btn-light" type="button">Suscribirse</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles del producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductoLabel">Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-producto-content">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para carrito de compras -->
<div class="modal fade" id="modalCarrito" tabindex="-1" aria-labelledby="modalCarritoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCarritoLabel">
                    <i class="bi bi-cart3 me-2"></i> Mi Carrito de Compras
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="carrito-content">
                    <!-- El contenido se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir comprando</button>
                <button type="button" class="btn btn-primary" id="btnProcesarCompra" style="display: none;">
                    <i class="bi bi-credit-card me-2"></i> Procesar compra
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Copia el mismo JavaScript de tu index.php aquí
$(document).ready(function() {
    // Obtener productos para autocompletado
    let productos = [
        <?php 
        $todos_productos = array_merge($productos_oferta, $productos_descuento);
        foreach ($todos_productos as $producto): ?>
            {
                id: <?php echo $producto['id']; ?>,
                nombre: "<?php echo addslashes($producto['nombre']); ?>",
                imagen: "<?php echo $producto['imagen']; ?>",
                precio: <?php echo $producto['precio']; ?>
            },
        <?php endforeach; ?>
    ];
    
    // Configurar autocompletado
    $("#buscador").autocomplete({
        source: productos,
        minLength: 2,
        select: function(event, ui) {
            // Abrir modal del producto seleccionado
            $.ajax({
                url: 'includes/modal_producto.php',
                type: 'POST',
                data: {id_producto: ui.item.id},
                success: function(response) {
                    $('#modal-producto-content').html(response);
                    $('#modalProducto').modal('show');
                }
            });
            return false;
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append(`<div class="d-flex align-items-center">
                        <img src="assets/img/${item.imagen}" width="40" class="me-2">
                        <div>
                            <strong>${item.nombre}</strong><br>
                            <small class="text-muted">S/ ${item.precio.toFixed(2)}</small>
                        </div>
                    </div>`)
            .appendTo(ul);
    };
    
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
            dataType: 'json',
            data: {id_producto: id_producto, cantidad: 1},
            success: function(response) {
                if (response.success) {
                    // Actualizar contador del carrito
                    let count = parseInt($('.badge').text()) + 1;
                    $('.badge').text(count);
                    
                    // Cambiar estado del botón
                    btn.html('<i class="bi bi-check-circle"></i> Agregado');
                    btn.removeClass('btn-primary').addClass('btn-success');
                    
                    setTimeout(function() {
                        btn.html('Agregar al carrito');
                        btn.removeClass('btn-success').addClass('btn-primary');
                    }, 2000);
                    
                    // Actualizar el carrito sin recargar la página
                    actualizarCarrito();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Función para actualizar el carrito dinámicamente
    function actualizarCarrito() {
        $.ajax({
            url: 'includes/actualizar_carrito.php',
            type: 'GET',
            success: function(response) {
                $('#carrito-content').html(response);
                
                // Mostrar u ocultar botón de procesar compra
                let tieneProductos = $('#carrito-content table').length > 0;
                $('#btnProcesarCompra').toggle(tieneProductos);
            }
        });
    }

    // Eliminar del carrito
    $(document).on('click', '.eliminar-carrito', function() {
        let id_producto = $(this).data('id');
        
        $.ajax({
            url: 'includes/carrito.php',
            type: 'GET',
            data: {eliminar: id_producto},
            success: function() {
                // Actualizar el carrito dinámicamente
                actualizarCarrito();
                
                // Actualizar contador
                let count = parseInt($('.badge').text()) - 1;
                $('.badge').text(count >= 0 ? count : 0);
            }
        });
    });

    // Actualizar carrito cuando se abre el modal
    $('#modalCarrito').on('show.bs.modal', function () {
        actualizarCarrito();
    });
});
</script>

<?php include 'includes/footer_public.php'; ?>