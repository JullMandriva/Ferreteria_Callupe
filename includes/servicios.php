<?php
session_start();
include 'config/db.php';
include 'includes/header_public.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Nuestros Servicios</h1>
            <p class="lead">En Ferretodo no solo vendemos productos, ofrecemos servicios profesionales para satisfacer todas tus necesidades.</p>
        </div>
    </div>
    
    <!-- Servicios principales -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 service-card">
                <div class="card-body text-center">
                    <i class="bi bi-tools display-1 text-primary mb-3"></i>
                    <h3 class="card-title">Reparación de Herramientas</h3>
                    <p class="card-text">Reparamos todo tipo de herramientas eléctricas y manuales. Contamos con técnicos especializados y repuestos originales.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Diagnóstico gratuito</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Reparación garantizada</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Servicio a domicilio</li>
                    </ul>
                    <button class="btn btn-primary mt-3">Solicitar Servicio</button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 service-card">
                <div class="card-body text-center">
                    <i class="bi bi-house-door display-1 text-primary mb-3"></i>
                    <h3 class="card-title">Instalaciones Eléctricas</h3>
                    <p class="card-text">Realizamos instalaciones eléctricas residenciales e industriales con la máxima seguridad y garantía de calidad.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Certificación oficial</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Materiales de primera</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Asesoría profesional</li>
                    </ul>
                    <button class="btn btn-primary mt-3">Cotizar Ahora</button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 service-card">
                <div class="card-body text-center">
                    <i class="bi bi-people display-1 text-primary mb-3"></i>
                    <h3 class="card-title">Asesoría Técnica</h3>
                    <p class="card-text">Nuestros expertos te ayudarán a elegir las herramientas adecuadas para tus proyectos y te enseñarán a usarlas correctamente.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Atención personalizada</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Capacitación práctica</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Soporte continuo</li>
                    </ul>
                    <button class="btn btn-primary mt-3">Agendar Cita</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Proceso de servicio -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4 text-center">¿Cómo funcionan nuestros servicios?</h2>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-3 text-center mb-4">
            <div class="service-step">
                <div class="step-number">1</div>
                <i class="bi bi-telephone-fill text-primary"></i>
                <h4>Contacto</h4>
                <p>Llámanos o escríbenos para consultar sobre tu necesidad</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center mb-4">
            <div class="service-step">
                <div class="step-number">2</div>
                <i class="bi bi-clipboard-check text-primary"></i>
                <h4>Evaluación</h4>
                <p>Analizamos tu requerimiento y te presentamos opciones</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center mb-4">
            <div class="service-step">
                <div class="step-number">3</div>
                <i class="bi bi-wrench-adjustable text-primary"></i>
                <h4>Ejecución</h4>
                <p>Realizamos el servicio con profesionalismo y calidad</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center mb-4">
            <div class="service-step">
                <div class="step-number">4</div>
                <i class="bi bi-hand-thumbs-up text-primary"></i>
                <h4>Garantía</h4>
                <p>Ofrecemos garantía en todos nuestros servicios</p>
            </div>
        </div>
    </div>
    
    <!-- Testimonios -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4 text-center">Lo que dicen nuestros clientes</h2>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <i class="bi bi-chat-quote text-primary fs-1 me-3"></i>
                        <div>
                            <h5 class="card-title">Excelente servicio</h5>
                            <p class="card-text">"El servicio de instalación eléctrica fue rápido y profesional. El técnico fue muy amable y explicó todo el proceso."</p>
                            <small class="text-muted">- Carlos M.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <i class="bi bi-chat-quote text-primary fs-1 me-3"></i>
                        <div>
                            <h5 class="card-title">Reparación perfecta</h5>
                            <p class="card-text">"Llevé mi taladro para reparación y lo devolvieron como nuevo. El precio fue muy razonable."</p>
                            <small class="text-muted">- Ana G.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <i class="bi bi-chat-quote text-primary fs-1 me-3"></i>
                        <div>
                            <h5 class="card-title">Asesoría invaluable</h5>
                            <p class="card-text">"Me ayudaron a elegir las herramientas adecuadas para mi proyecto de construcción. ¡Muy recomendados!"</p>
                            <small class="text-muted">- Luis R.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CTA final -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="card-title">¿Necesitas alguno de nuestros servicios?</h3>
                    <p class="card-text">No dudes en contactarnos. Estamos aquí para ayudarte con cualquier proyecto o necesidad.</p>
                    <div class="d-flex justify-content-center mt-4">
                        <a href="contactanos.php" class="btn btn-light btn-lg me-3">
                            <i class="bi bi-envelope me-2"></i> Contáctanos
                        </a>
                        <a href="tel:+51987654321" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-telephone me-2"></i> Llamar ahora
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.service-card {
    transition: transform 0.3s ease;
    border: 1px solid #e0e0e0;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.service-step {
    padding: 20px;
    border-radius: 10px;
    background-color: #f8f9fa;
    margin-bottom: 20px;
}

.step-number {
    width: 40px;
    height: 40px;
    background-color: #0d6efd;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    margin: 0 auto 15px;
}
</style>

<?php include 'includes/footer_public.php'; ?>