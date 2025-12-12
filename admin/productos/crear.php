<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../../login.php');
    exit;
}

include '../../config/db.php';
include '../../includes/header.php';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $imagen = $_FILES['imagen']['name'];
    
    // Subir imagen
    if ($imagen) {
        $ruta = '../../assets/img/' . $imagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    }
    
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, imagen) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $precio, $stock, $stock_minimo, $imagen]);
    
    header('Location: index.php');
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-box-seam me-2"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../ventas/index.php">
                            <i class="bi bi-cart-check me-2"></i> Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../usuarios/index.php">
                            <i class="bi bi-people me-2"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../reportes/mas_vendidos.php">
                            <i class="bi bi-graph-up me-2"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../contactos.php">
                            <i class="bi bi-envelope me-2"></i> Contactos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../../logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Agregar Producto</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="index.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="precio" class="form-label">Precio (S/)</label>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>