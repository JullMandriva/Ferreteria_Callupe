<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../../login.php');
    exit;
}

include '../../config/db.php';
include '../../includes/header.php';

// Obtener todos los usuarios
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nombre");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];
    
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $password, $rol]);
    
    header('Location: index.php');
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Usuarios</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                        <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $usuario['rol'] == 'admin' ? 'danger' : 
                                           ($usuario['rol'] == 'cajero' ? 'primary' : 'success'); 
                                ?>">
                                    <?php echo ucfirst($usuario['rol']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                            <td>
                                <?php if ($usuario['id'] != $_SESSION['usuario']['id']): ?>
                                <a href="editar.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="eliminar.php?id=<?php echo $usuario['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="crear" value="1">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="rol" required>
                            <option value="admin">Administrador</option>
                            <option value="cajero">Cajero</option>
                            <option value="almacenero">Almacenero</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>