<?php
session_start();

// Limpiar el carrito si hay datos corruptos
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        if (!is_numeric($cantidad) || $cantidad <= 0) {
            unset($_SESSION['carrito'][$id]);
        }
    }
    
    if (empty($_SESSION['carrito'])) {
        unset($_SESSION['carrito']);
    }
}

include 'config/db.php';
include 'includes/header_public.php';

// Obtener productos
$stmt = $pdo->query("SELECT * FROM productos ORDER BY nombre");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <!-- Header con buscador y acceso admin -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">FERRETERIA TOÑITO</h1>
        <div class="d-flex align-items-center">
            <!-- Buscador con autocompletado -->
            <div class="input-group me-3" style="width: 300px;">
                <input type="text" id="buscador" class="form-control" placeholder="Buscar productos...">
                <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            
            <!-- Carrito -->
            <button class="btn btn-outline-primary position-relative me-3" data-bs-toggle="modal" data-bs-target="#modalCarrito">
                <i class="bi bi-cart3"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="contador-carrito">
                    <?php 
                    $total_items = 0;
                    if (isset($_SESSION['carrito'])) {
                        foreach ($_SESSION['carrito'] as $item) {
                            if (is_array($item) && isset($item['cantidad'])) {
                                $total_items += intval($item['cantidad']);
                            }
                        }
                    }
                    echo $total_items;
                    ?>
                </span>
            </button>
            
            <!-- Acceso Administrador -->
            <a href="login.php" class="btn btn-outline-secondary">
                <i class="bi bi-person-lock"></i> Acceso
            </a>
        </div>
    </div>
    
   <!-- Alertas de quiebre de stock -->
   
    
    <!-- Categorías -->
    <div class="mb-4">
        <h3 class="mb-3">Categorías</h3>
        <div class="d-flex flex-wrap gap-2" id="categorias">
            <button class="btn btn-outline-primary categoria-btn active" data-categoria="todos">
                Todos los productos
            </button>
            <button class="btn btn-outline-primary categoria-btn" data-categoria="herramientas">
                Herramientas
            </button>
            <button class="btn btn-outline-primary categoria-btn" data-categoria="electricidad">
                Electricidad
            </button>
            <button class="btn btn-outline-primary categoria-btn" data-categoria="fontaneria">
                Fontanería
            </button>
            <button class="btn btn-outline-primary categoria-btn" data-categoria="pintura">
                Pintura
            </button>
        </div>
    </div>
    
    <!-- Productos -->
    <h3 class="mb-3">Productos Destacados</h3>
    <div class="row" id="productos-container">
        <?php foreach ($productos as $producto): 
            // Determinar categoría
            $categoria = 'general';
            $nombre_lower = strtolower($producto['nombre']);
            
            if (strpos($nombre_lower, 'martillo') !== false || 
                strpos($nombre_lower, 'destornillador') !== false ||
                strpos($nombre_lower, 'herramienta') !== false ||
                strpos($nombre_lower, 'llave') !== false ||
                strpos($nombre_lower, 'alicate') !== false) {
                $categoria = 'herramientas';
            } elseif (strpos($nombre_lower, 'cable') !== false || 
                     strpos($nombre_lower, 'foco') !== false ||
                     strpos($nombre_lower, 'electric') !== false ||
                     strpos($nombre_lower, 'lámpara') !== false ||
                     strpos($nombre_lower, 'interruptor') !== false) {
                $categoria = 'electricidad';
            } elseif (strpos($nombre_lower, 'tuberia') !== false || 
                     strpos($nombre_lower, 'llave') !== false ||
                     strpos($nombre_lower, 'agua') !== false ||
                     strpos($nombre_lower, 'grifo') !== false ||
                     strpos($nombre_lower, 'caño') !== false) {
                $categoria = 'fontaneria';
            } elseif (strpos($nombre_lower, 'pintura') !== false || 
                     strpos($nombre_lower, 'brocha') !== false ||
                     strpos($nombre_lower, 'rodillo') !== false ||
                     strpos($nombre_lower, 'color') !== false ||
                     strpos($nombre_lower, 'laca') !== false) {
                $categoria = 'pintura';
            }
        ?>
            <div class="col-md-4 col-lg-3 mb-4 producto-item" 
                 data-nombre="<?php echo htmlspecialchars(strtolower($producto['nombre'])); ?>"
                 data-categoria="<?php echo $categoria; ?>">
                <div class="card h-100 producto-card">
                    <div class="position-relative">
                        <img src="assets/img/<?php echo $producto['imagen']; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                             style="height: 200px; object-fit: cover;">
                        <?php if ($producto['stock'] <= $producto['stock_minimo']): ?>
                            <span class="position-absolute top-0 end-0 badge bg-danger m-2">¡Quiebre!</span>
                        <?php endif; ?>
                        <?php if ($producto['precio'] < 50): ?>
                            <span class="position-absolute top-0 start-0 badge bg-success m-2">¡Oferta!</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                        <p class="card-text flex-grow-1 text-muted">
                            <?php echo substr(htmlspecialchars($producto['descripcion']), 0, 60) . '...'; ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 mb-0 text-primary">S/<?php echo number_format($producto['precio'], 2, '.', ','); ?></span>
                            <span class="badge bg-<?php 
                                echo $producto['stock'] > 10 ? 'success' : 
                                     ($producto['stock'] > 0 ? 'warning' : 'danger'); 
                            ?>">
                                Stock: <?php echo $producto['stock']; ?>
                            </span>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm ver-detalle" 
                                    data-id="<?php echo $producto['id']; ?>">
                                <i class="bi bi-eye me-1"></i> Ver detalles
                            </button>
                            <button class="btn btn-primary btn-sm agregar-carrito" 
                                    data-id="<?php echo $producto['id']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                    data-precio="<?php echo $producto['precio']; ?>"
                                    data-imagen="<?php echo $producto['imagen']; ?>"
                                    <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <i class="bi bi-cart-plus me-1"></i>
                                <?php echo $producto['stock'] <= 0 ? 'Agotado' : 'Agregar al carrito'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($productos)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-emoji-frown display-1 text-muted"></i>
                <h3 class="mt-3">No hay productos disponibles</h3>
                <p class="text-muted">Pronto tendremos nuevos productos en stock</p>
            </div>
        <?php endif; ?>
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

<!-- Modal para carrito de compras -->
<div class="modal fade" id="modalCarrito" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cart3 me-2"></i> Mi Carrito de Compras
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="carrito-content">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir comprando</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar tipo de comprobante -->
<div class="modal fade" id="modalTipoComprobante" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tipo de Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Seleccione el tipo de comprobante que desea generar:</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary btn-lg py-3" onclick="seleccionarComprobante('boleta')">
                        <i class="bi bi-receipt me-2"></i> Boleta
                        <small class="d-block mt-1">(Para persona natural con DNI)</small>
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-lg py-3" onclick="seleccionarComprobante('factura')">
                        <i class="bi bi-file-earmark-text me-2"></i> Factura
                        <small class="d-block mt-1">(Para empresas con RUC)</small>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para datos del cliente -->
<div class="modal fade" id="modalDatosCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formDatosCliente" novalidate>
                    <input type="hidden" id="tipoComprobante" name="tipo_comprobante">
                    
                    <div class="mb-3">
                        <label for="nombreCliente" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombreCliente" name="nombre_cliente" required>
                        <div class="invalid-feedback">Por favor ingrese su nombre completo</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipoDocumento" class="form-label">Tipo de Documento *</label>
                        <select class="form-select" id="tipoDocumento" name="tipo_documento" required>
                            <option value="">Seleccione...</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">Carnet de Extranjería</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un tipo de documento</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="numeroDocumento" class="form-label">Número de Documento *</label>
                        <input type="text" class="form-control" id="numeroDocumento" name="numero_documento" required>
                        <div class="invalid-feedback">Por favor ingrese su número de documento</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="emailCliente" class="form-label">Email</label>
                        <input type="email" class="form-control" id="emailCliente" name="email_cliente">
                        <div class="invalid-feedback">Por favor ingrese un email válido</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="telefonoCliente" class="form-label">Teléfono/Celular *</label>
                        <input type="tel" class="form-control" id="telefonoCliente" name="telefono_cliente" required>
                        <div class="invalid-feedback">Por favor ingrese su teléfono</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="direccionCliente" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccionCliente" name="direccion_cliente" rows="2" placeholder="Dirección para entrega (opcional)"></textarea>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="aceptoTerminos" required>
                        <label class="form-check-label" for="aceptoTerminos">
                            Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#modalTerminos">términos y condiciones</a> *
                        </label>
                        <div class="invalid-feedback">Debe aceptar los términos y condiciones</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarCompra" onclick="procesarCompra()">
                    <i class="bi bi-check-circle me-1"></i> Confirmar Compra
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Términos y Condiciones -->
<div class="modal fade" id="modalTerminos">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Términos y Condiciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Política de Precios</h6>
                <p>Todos los precios están expresados en Soles (S/) e incluyen IGV.</p>
                
                <h6 class="mt-3">2. Métodos de Pago</h6>
                <p>Aceptamos pago en efectivo, transferencia bancaria y tarjetas de crédito/débito.</p>
                
                <h6 class="mt-3">3. Entrega</h6>
                <p>El tiempo de entrega depende de la disponibilidad del producto y la ubicación.</p>
                
                <h6 class="mt-3">4. Garantía</h6>
                <p>Todos los productos tienen garantía de 12 meses contra defectos de fabricación.</p>
                
                <h6 class="mt-3">5. Devoluciones</h6>
                <p>Aceptamos devoluciones dentro de los 7 días posteriores a la compra, con el producto en su empaque original.</p>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 para mejores alertas -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Productos para autocompletado
    let productos = [
        <?php foreach ($productos as $producto): ?>
            {
                id: <?php echo $producto['id']; ?>,
                label: "<?php echo addslashes($producto['nombre']); ?> - S/<?php echo number_format($producto['precio'], 2); ?>",
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
            verDetallesProducto(ui.item.id);
            return false;
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append(`<div class="d-flex align-items-center p-2">
                        <img src="assets/img/${item.imagen}" width="40" height="40" class="rounded me-2">
                        <div>
                            <strong>${item.nombre}</strong><br>
                            <small class="text-muted">S/ ${parseFloat(item.precio).toFixed(2)}</small>
                        </div>
                    </div>`)
            .appendTo(ul);
    };
    
    // Botón de búsqueda
    $("#btnBuscar").click(function() {
        let valor = $("#buscador").val().toLowerCase();
        if (valor.trim() !== '') {
            buscarProductos(valor);
        }
    });
    
    // Filtro por búsqueda en tiempo real
    $("#buscador").on("keyup", function() {
        let valor = $(this).val().toLowerCase();
        $(".producto-item").each(function() {
            let nombre = $(this).data("nombre");
            $(this).toggle(nombre.indexOf(valor) > -1);
        });
    });
    
    // Filtro por categorías
    $(".categoria-btn").click(function() {
        $(".categoria-btn").removeClass("active");
        $(this).addClass("active");
        
        let categoria = $(this).data("categoria");
        
        if (categoria === "todos") {
            $(".producto-item").show();
        } else {
            $(".producto-item").each(function() {
                let itemCategoria = $(this).data("categoria");
                $(this).toggle(itemCategoria === categoria);
            });
        }
    });
    
    // Ver detalles del producto
    $(document).on('click', '.ver-detalle', function() {
        let id_producto = $(this).data('id');
        verDetallesProducto(id_producto);
    });
    
    // Agregar al carrito
    $(document).on('click', '.agregar-carrito', function() {
        let id_producto = $(this).data('id');
        let btn = $(this);
        
        agregarAlCarrito(id_producto, btn);
    });
    
    // Función para ver detalles del producto
    function verDetallesProducto(id_producto) {
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
                Swal.fire('Error', 'No se pudieron cargar los detalles del producto', 'error');
            }
        });
    }
    
    // Función para agregar al carrito
    function agregarAlCarrito(id_producto, btn) {
        $.ajax({
            url: 'includes/carrito.php',
            type: 'POST',
            dataType: 'json',
            data: {id_producto: id_producto, cantidad: 1},
            success: function(response) {
                if (response.success) {
                    // Actualizar contador del carrito
                    $('#contador-carrito').text(response.total_items);
                    
                    // Cambiar estado del botón
                    btn.html('<i class="bi bi-check-circle me-1"></i> Agregado');
                    btn.removeClass('btn-primary').addClass('btn-success');
                    btn.prop('disabled', true);
                    
                    setTimeout(function() {
                        btn.html('<i class="bi bi-cart-plus me-1"></i> Agregar al carrito');
                        btn.removeClass('btn-success').addClass('btn-primary');
                        btn.prop('disabled', false);
                    }, 1500);
                    
                    // Mostrar notificación
                    Swal.fire({
                        icon: 'success',
                        title: '¡Producto agregado!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al agregar el producto al carrito', 'error');
            }
        });
    }
    
    // Actualizar carrito cuando se abre el modal
    $('#modalCarrito').on('show.bs.modal', function() {
        actualizarCarrito();
    });
});

// Función para actualizar el contenido del carrito
function actualizarCarrito() {
    $.ajax({
        url: 'includes/actualizar_carrito.php',
        type: 'GET',
        success: function(response) {
            $('#carrito-content').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            $('#carrito-content').html('<div class="text-center py-5 text-danger">Error al cargar el carrito</div>');
        }
    });
}

// Función para actualizar contador del carrito
function actualizarContadorCarrito() {
    $.ajax({
        url: 'includes/get_contador_carrito.php',
        type: 'GET',
        success: function(response) {
            $('#contador-carrito').text(response);
        }
    });
}

// Función para seleccionar tipo de comprobante
function seleccionarComprobante(tipo) {
    // Cerrar modal actual
    $('#modalTipoComprobante').modal('hide');
    
    // Configurar tipo de documento según selección
    if (tipo === 'factura') {
        $('#tipoDocumento').val('RUC').prop('disabled', true);
    } else {
        $('#tipoDocumento').val('DNI').prop('disabled', false);
    }
    
    // Guardar tipo de comprobante
    $('#tipoComprobante').val(tipo);
    
    // Limpiar formulario (excepto tipo de documento)
    $('#nombreCliente').val('');
    $('#numeroDocumento').val('');
    $('#emailCliente').val('');
    $('#telefonoCliente').val('');
    $('#direccionCliente').val('');
    $('#aceptoTerminos').prop('checked', false);
    
    // Remover clases de validación
    $('#formDatosCliente').removeClass('was-validated');
    $('.form-control').removeClass('is-invalid');
    
    // Abrir modal de datos del cliente
    setTimeout(function() {
        $('#modalDatosCliente').modal('show');
    }, 300);
}

// Función para procesar la compra
function procesarCompra() {
    // Validar formulario
    const form = $('#formDatosCliente')[0];
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // Validar términos y condiciones
    if (!$('#aceptoTerminos').is(':checked')) {
        Swal.fire('Error', 'Debe aceptar los términos y condiciones', 'error');
        return;
    }
    
    // Validaciones específicas
    const tipoComprobante = $('#tipoComprobante').val();
    const tipoDocumento = $('#tipoDocumento').val();
    const numeroDocumento = $('#numeroDocumento').val().trim();
    
    if (tipoComprobante === 'factura' && tipoDocumento !== 'RUC') {
        Swal.fire('Error', 'Para factura se requiere RUC como tipo de documento', 'error');
        return;
    }
    
    if (tipoDocumento === 'DNI' && numeroDocumento.length !== 8) {
        Swal.fire('Error', 'El DNI debe tener 8 dígitos', 'error');
        return;
    }
    
    if (tipoDocumento === 'RUC' && numeroDocumento.length !== 11) {
        Swal.fire('Error', 'El RUC debe tener 11 dígitos', 'error');
        return;
    }
    
    // Deshabilitar botón y mostrar loading
    const btnConfirmar = $('#btnConfirmarCompra');
    const textoOriginal = btnConfirmar.html();
    btnConfirmar.html('<span class="spinner-border spinner-border-sm me-2"></span>Procesando...');
    btnConfirmar.prop('disabled', true);
    
    // Preparar datos
    const datosCliente = {
        tipo_comprobante: tipoComprobante,
        nombre_cliente: $('#nombreCliente').val().trim(),
        tipo_documento: tipoDocumento,
        numero_documento: numeroDocumento,
        email_cliente: $('#emailCliente').val().trim(),
        telefono_cliente: $('#telefonoCliente').val().trim(),
        direccion_cliente: $('#direccionCliente').val().trim()
    };
    
    // Enviar datos al servidor
    $.ajax({
        url: 'procesar_compra.php',
        type: 'POST',
        data: datosCliente,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Mostrar éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Compra exitosa!',
                    html: `Su comprobante <strong>${response.numero_comprobante}</strong> ha sido generado.<br>
                          Total: <strong>S/${response.total}</strong>`,
                    showConfirmButton: true,
                    confirmButtonText: 'Ver Comprobante'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'comprobante.php?id_venta=' + response.id_venta;
                    }
                });
                
                // Cerrar modales
                $('#modalDatosCliente').modal('hide');
                $('#modalCarrito').modal('hide');
                
            } else {
                // Restaurar botón
                btnConfirmar.html(textoOriginal);
                btnConfirmar.prop('disabled', false);
                
                // Mostrar error
                Swal.fire('Error', response.message || 'Error al procesar la compra', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error, xhr.responseText);
            
            // Restaurar botón
            btnConfirmar.html(textoOriginal);
            btnConfirmar.prop('disabled', false);
            
            // Mostrar error
            Swal.fire('Error', 'Error de conexión. Por favor intente nuevamente.', 'error');
        }
    });
}

// Evento para abrir modal de tipo de comprobante
$(document).on('click', '#btnProcesarCompra', function(e) {
    e.preventDefault();
    
    // Verificar que haya productos en el carrito
    if ($('#contador-carrito').text() === '0') {
        Swal.fire('Carrito vacío', 'Agregue productos al carrito antes de proceder al pago', 'warning');
        return;
    }
    
    $('#modalCarrito').modal('hide');
    
    // Pequeño delay para que se cierre el modal
    setTimeout(function() {
        $('#modalTipoComprobante').modal('show');
    }, 300);
});
</script>

<?php include 'includes/footer_public.php'; ?>