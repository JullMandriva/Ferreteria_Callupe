<?php
session_start();
include 'includes/header_public.php';
?>

<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Nuestros Servicios</h1>
        <div class="d-flex align-items-center">
            <a href="index.php" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
            </a>
            <?php include 'includes/carrito_boton.php'; ?>
        </div>
    </div>
    
    <!-- Introducción -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h2 class="display-6 mb-3">Más que una ferretería</h2>
            <p class="lead text-muted">
                En Ferretodo no solo vendemos productos, ofrecemos soluciones completas 
                para tus proyectos de construcción, reparación y mantenimiento.
            </p>
        </div>
    </div>
    
    <!-- Servicios -->
    <div class="row g-4">
        <!-- Servicio 1 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-tools display-4 text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">Asesoría Técnica</h4>
                    <p class="card-text text-muted">
                        Nuestros expertos te ayudan a elegir los productos adecuados 
                        para cada proyecto. ¡Consulta sin compromiso!
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Asesoría personalizada</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Planos y presupuestos</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Soluciones a medida</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center">
                    <a href="contactanos.php" class="btn btn-outline-primary">
                        <i class="bi bi-chat-left-text me-1"></i> Solicitar asesoría
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Servicio 2 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-truck display-4 text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">Entrega a Domicilio</h4>
                    <p class="card-text text-muted">
                        Llevamos tus productos hasta la puerta de tu casa u obra. 
                        Servicio disponible en toda la ciudad.
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Entrega en 24-48 horas</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Seguimiento en línea</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Sin costo por compras mayores a S/200</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEntrega">
                        <i class="bi bi-info-circle me-1"></i> Más información
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Servicio 3 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-wrench display-4 text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">Instalación Profesional</h4>
                    <p class="card-text text-muted">
                        ¿Necesitas ayuda con la instalación? Contamos con técnicos 
                        certificados para diversos trabajos.
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Fontanería y electricidad</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Cerrajería</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Instalación de herrajes</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center">
                    <a href="contactanos.php" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-check me-1"></i> Agendar instalación
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Servicio 4 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-arrow-repeat display-4 text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">Garantía Extendida</h4>
                    <p class="card-text text-muted">
                        Todos nuestros productos cuentan con garantía. Ofrecemos 
                        servicio post-venta y soporte técnico.
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Garantía de 12 meses</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Cambios sin costo</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Reparaciones en taller</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalGarantia">
                        <i class="bi bi-shield-check me-1"></i> Ver términos
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Servicio 5 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-building display-4 text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">Ventas Corporativas</h4>
                    <p class="card-text text-muted">
                        Precios especiales para constructoras, talleres y empresas. 
                        Programas de abastecimiento continuo.
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Descuentos por volumen</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Facturación electrónica</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Crédito comercial</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center">
                    <a href="contactanos.php" class="btn btn-outline-primary">
                        <i class="bi bi-building me-1"></i> Solicitar cotización
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Servicio 6 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <i class="bi bi-phone display-4 text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">Soporte 24/7</h4>
                    <p class="card-text text-muted">
                        ¿Emergencia? Contáctanos en cualquier momento. 
                        Tenemos personal disponible para urgencias.
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Atención telefónica permanente</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> WhatsApp empresarial</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Visitas de emergencia</li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center">
                    <a href="tel:+51987654321" class="btn btn-outline-primary">
                        <i class="bi bi-telephone me-1"></i> Llamar ahora: 987 654 321
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call to Action -->
    <div class="card bg-primary text-white mt-5">
        <div class="card-body text-center p-5">
            <h2 class="card-title mb-3">¿Necesitas un servicio personalizado?</h2>
            <p class="card-text lead mb-4">
                Cuéntanos sobre tu proyecto y te ayudaremos a encontrar la mejor solución.
            </p>
            <a href="contactanos.php" class="btn btn-light btn-lg">
                <i class="bi bi-chat-dots me-2"></i> Contáctanos ahora
            </a>
        </div>
    </div>
</div>

<!-- Modal Entrega -->
<div class="modal fade" id="modalEntrega">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Entrega a Domicilio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Zonas de cobertura:</h6>
                <ul>
                    <li>Centro de Lima</li>
                    <li>Miraflores</li>
                    <li>San Isidro</li>
                    <li>La Molina</li>
                    <li>Surco</li>
                </ul>
                <h6 class="mt-3">Tarifas:</h6>
                <ul>
                    <li>Compras menores a S/200: S/15</li>
                    <li>Compras de S/200 a S/500: S/10</li>
                    <li>Compras mayores a S/500: Gratis</li>
                </ul>
                <p class="text-muted mt-3"><small>*Horario de entrega: Lunes a Sábado de 9am a 6pm</small></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Garantía -->
<div class="modal fade" id="modalGarantia">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Términos de Garantía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>¿Qué cubre nuestra garantía?</h6>
                <ul>
                    <li>Defectos de fabricación</li>
                    <li>Fallas en materiales</li>
                    <li>Problemas de funcionamiento</li>
                </ul>
                <h6 class="mt-3">¿Qué no cubre?</h6>
                <ul>
                    <li>Daños por mal uso</li>
                    <li>Instalación incorrecta</li>
                    <li>Desgaste normal por uso</li>
                </ul>
                <h6 class="mt-3">Procedimiento:</h6>
                <ol>
                    <li>Presentar boleta o factura original</li>
                    <li>El producto debe estar en su empaque original</li>
                    <li>No deben faltar piezas o accesorios</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>

<?php include 'includes/footer_public.php'; ?>