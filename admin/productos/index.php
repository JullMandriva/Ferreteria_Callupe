<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../../login.php');
    exit;
}

include '../../config/db.php';
include '../../includes/header.php';

// Obtener todos los productos
 $stmt = $pdo->query("SELECT * FROM productos ORDER BY nombre");
 $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h1 class="h2">Gestión de Productos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="crear.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Producto
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Stock Mínimo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo $producto['id']; ?></td>
                                <td>
                                    <?php if ($producto['imagen']): ?>
                                        <img src="../../assets/img/<?php echo $producto['imagen']; ?>" width="50" height="50" class="img-thumbnail">
                                    <?php else: ?>
                                        <span class="text-muted">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td><?php echo substr($producto['descripcion'], 0, 50) . '...'; ?></td>
                                <td>S/<?php echo number_format($producto['precio'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $producto['stock'] <= $producto['stock_minimo'] ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo $producto['stock']; ?>
                                    </span>
                                </td>
                                <td><?php echo $producto['stock_minimo']; ?></td>
                                <td>
                                    <a href="editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="eliminar.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>