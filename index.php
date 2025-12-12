<?php

session_start();

// Limpiar el carrito si hay datos corruptos
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        // Si la cantidad no es un número válido, eliminar el producto del carrito
        if (!is_numeric($cantidad) || $cantidad <= 0) {
            unset($_SESSION['carrito'][$id]);
        }
    }
    
    // Si el carrito queda vacío, eliminarlo
    if (empty($_SESSION['carrito'])) {
        unset($_SESSION['carrito']);
    }
}
include 'config/db.php';
include 'includes/header_public.php';

// Obtener productos con alerta de stock bajo
 $stmt = $pdo->query("SELECT * FROM productos ORDER BY nombre");
 $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <!-- Header con buscador y acceso admin -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Ferretodo</h1>
        <div class="d-flex align-items-center">
            <!-- Buscador con autocompletado -->
            <div class="input-group me-3" style="width: 300px;">
                <input type="text" id="buscador" class="form-control" placeholder="Buscar productos...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            
            <!-- Carrito -->
            <button class="btn btn-outline-primary position-relative me-3" data-bs-toggle="modal" data-bs-target="#modalCarrito">
                <i class="bi bi-cart3"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>
                </span>
            </button>
            
            <!-- Acceso Administrador -->
            <a href="login.php" class="btn btn-outline-secondary">
                <i class="bi bi-person-lock"></i> Acceso Administrador
            </a>
        </div>
    </div>
    
    <!-- Alertas de quiebre de stock -->
    <?php 
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo");
    $quiebre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($quiebre['total'] > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>¡Atención!</strong> Hay <?php echo $quiebre['total']; ?> productos con stock bajo.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Productos -->
    <h3 class="mb-3">Productos Destacados</h3>
    <div class="row" id="productos-container">
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 col-lg-3 mb-4 producto-item" 
                 data-nombre="<?php echo strtolower($producto['nombre']); ?>">
                <div class="card h-100 producto-card">
                    <div class="position-relative">
                        <img src="assets/img/<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo $producto['nombre']; ?>">
                        <?php if ($producto['stock'] <= $producto['stock_minimo']): ?>
                            <span class="position-absolute top-0 end-0 badge bg-danger m-2">¡Quiebre!</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                        <p class="card-text flex-grow-1"><?php echo substr($producto['descripcion'], 0, 60) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">S/<?php echo number_format($producto['precio'], 2); ?></span>
                            <span class="badge bg-secondary">Stock: <?php echo $producto['stock']; ?></span>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-outline-primary btn-sm ver-detalle" 
                                    data-id="<?php echo $producto['id']; ?>">
                                Ver detalles
                            </button>
                            <button class="btn btn-primary btn-sm agregar-carrito" 
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

<!-- Modal para seleccionar tipo de comprobante -->
<div class="modal fade" id="modalTipoComprobante" tabindex="-1" aria-labelledby="modalTipoComprobanteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipoComprobanteLabel">Tipo de Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Seleccione el tipo de comprobante que desea generar:</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary btn-lg" onclick="seleccionarComprobante('boleta')">
                        <i class="bi bi-receipt me-2"></i> Boleta
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-lg" onclick="seleccionarComprobante('factura')">
                        <i class="bi bi-file-earmark-text me-2"></i> Factura
                    </button>
                </div>
                
                <hr>
                
                <div class="alert alert-info">
                    <h6>Depuración:</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="testProcesarCompra()">
                        Probar procesamiento con datos de prueba
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para datos del cliente -->
<div class="modal fade" id="modalDatosCliente" tabindex="-1" aria-labelledby="modalDatosClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDatosClienteLabel">Datos del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formDatosCliente">
                    <input type="hidden" id="tipoComprobante" name="tipo_comprobante">
                    
                    <div class="mb-3">
                        <label for="nombreCliente" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombreCliente" name="nombre_cliente" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipoDocumento" class="form-label">Tipo de Documento</label>
                        <select class="form-select" id="tipoDocumento" name="tipo_documento" required>
                            <option value="">Seleccione...</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="Pasaporte">Pasaporte</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="numeroDocumento" class="form-label">Número de Documento</label>
                        <input type="text" class="form-control" id="numeroDocumento" name="numero_documento" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="emailCliente" class="form-label">Email</label>
                        <input type="email" class="form-control" id="emailCliente" name="email_cliente">
                    </div>
                    
                    <div class="mb-3">
                        <label for="telefonoCliente" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefonoCliente" name="telefono_cliente">
                    </div>
                    
                    <div class="mb-3">
                        <label for="direccionCliente" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccionCliente" name="direccion_cliente" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="procesarCompra()">Confirmar Compra</button>
            </div>
        </div>
    </div>
</div>

<script>
 $(document).ready(function() {
    // Obtener productos para autocompletado
    let productos = [
        <?php foreach ($productos as $producto): ?>
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
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles del producto');
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
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error al cargar los detalles del producto');
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
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error al agregar el producto al carrito');
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
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error al actualizar el carrito');
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
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error al eliminar el producto del carrito');
            }
        });
    });
    
    // Función para seleccionar tipo de comprobante
    function seleccionarComprobante(tipo) {
        console.log('=== SELECCIONAR COMPROBANTE ===');
        console.log('Tipo seleccionado:', tipo);
        
        // Cerrar modal de tipo de comprobante
        $('#modalTipoComprobante').modal('hide');
        console.log('Modal de tipo de comprobante cerrado');
        
        // Establecer el tipo de comprobante en el campo oculto
        $('#tipoComprobante').val(tipo);
        console.log('Tipo de comprobante establecido en campo oculto:', $('#tipoComprobante').val());
        
        // Si es factura, requerir RUC
        if (tipo === 'factura') {
            $('#tipoDocumento').val('RUC');
            console.log('Tipo de documento establecido a RUC');
        } else {
            $('#tipoDocumento').val('DNI');
            console.log('Tipo de documento establecido a DNI');
        }
        
        // Abrir modal de datos del cliente
        $('#modalDatosCliente').modal('show');
        console.log('Modal de datos del cliente abierto');
    }

    // Función para procesar la compra
    function procesarCompra() {
        console.log('=== PROCESAR COMPRA ===');
        
        // Validar formulario
        if (!$('#formDatosCliente')[0].checkValidity()) {
            console.log('Formulario no válido');
            $('#formDatosCliente')[0].reportValidity();
            return;
        }
        
        // Recopilar datos del formulario
        let datosCliente = {
            tipo_comprobante: $('#tipoComprobante').val(),
            nombre_cliente: $('#nombreCliente').val(),
            tipo_documento: $('#tipoDocumento').val(),
            numero_documento: $('#numeroDocumento').val(),
            email_cliente: $('#emailCliente').val(),
            telefono_cliente: $('#telefonoCliente').val(),
            direccion_cliente: $('#direccionCliente').val()
        };
        
        console.log('Datos del cliente a enviar:', datosCliente);
        
        // Mostrar indicador de carga
        let btnConfirmar = $('button[onclick="procesarCompra()"]');
        let textoOriginal = btnConfirmar.html();
        btnConfirmar.html('<span class="spinner-border spinner-border-sm me-2"></span>Procesando...');
        btnConfirmar.prop('disabled', true);
        
        // Enviar datos al servidor
        $.ajax({
            url: 'procesar_compra.php',
            type: 'POST',
            data: datosCliente,
            dataType: 'json',
            success: function(response) {
                console.log('=== RESPUESTA DEL SERVIDOR ===');
                console.log('Respuesta completa:', response);
                
                if (response.success) {
                    console.log('Compra procesada con éxito. ID de venta:', response.id_venta);
                    // Redirigir al comprobante
                    window.location.href = 'comprobante.php?id_venta=' + response.id_venta;
                } else {
                    console.log('Error en la respuesta:', response.message);
                    alert('Error: ' + response.message);
                    // Restaurar botón
                    btnConfirmar.html(textoOriginal);
                    btnConfirmar.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.log('=== ERROR EN LA PETICIÓN ===');
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Respuesta del servidor:', xhr.responseText);
                
                alert('Error al procesar la compra. Por favor, inténtalo de nuevo.');
                // Restaurar botón
                btnConfirmar.html(textoOriginal);
                btnConfirmar.prop('disabled', false);
            }
        });
    }

    // Función para probar el procesamiento con datos de prueba
    function testProcesarCompra() {
        console.log('=== INICIANDO PRUEBA DE PROCESAMIENTO ===');
        
        // Llenar el formulario con datos de prueba
        $('#nombreCliente').val('Cliente de Prueba');
        $('#tipoDocumento').val('DNI');
        $('#numeroDocumento').val('12345678');
        $('#emailCliente').val('test@example.com');
        $('#telefonoCliente').val('987654321');
        $('#direccionCliente').val('Dirección de prueba 123');
        
        console.log('Formulario llenado con datos de prueba');
        
        // Simular selección de boleta
        $('#tipoComprobante').val('boleta');
        
        // Cerrar modal de tipo de comprobante
        $('#modalTipoComprobante').modal('hide');
        
        // Abrir modal de datos del cliente
        $('#modalDatosCliente').modal('show');
        
        console.log('Modales cambiados para prueba');
    }

    // Evento para el botón de procesar compra
    $(document).on('click', '#btnProcesarCompra', function() {
        $('#modalCarrito').modal('hide');
        $('#modalTipoComprobante').modal('show');
    });

    // Actualizar carrito cuando se abre el modal
    $('#modalCarrito').on('show.bs.modal', function () {
        actualizarCarrito();
    });
});
</script>

<?php include 'includes/footer_public.php'; ?>