<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos/index.php">
                            <i class="bi bi-box-seam me-2"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ventas/index.php">
                            <i class="bi bi-cart-check me-2"></i> Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios/index.php">
                            <i class="bi bi-people me-2"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes/mas_vendidos.php">
                            <i class="bi bi-graph-up me-2"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contactos.php">
                            <i class="bi bi-envelope me-2"></i> Contactos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Contactos</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Contactos</h5>
                    <p class="card-text">Esta sección está en desarrollo. Pronto podrás gestionar los contactos de clientes y proveedores.</p>
                    
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        Funcionalidad en construcción. Por favor, vuelve más tarde.
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>