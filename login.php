<?php
session_start();
include 'config/db.php';

// Si ya está logueado, redirigir según su rol
if (isset($_SESSION['usuario'])) {
    // Redirigir según rol
    switch ($_SESSION['usuario']['rol']) {
        case 'admin':
            header('Location: admin/index.php');
            break;
        case 'cajero':
            // Si no existe la página de cajero, redirigir al admin
            if (file_exists('admin/ventas/index.php')) {
                header('Location: admin/ventas/index.php');
            } else {
                header('Location: admin/index.php');
            }
            break;
        case 'almacenero':
            // Si no existe la página de almacenero, redirigir al admin
            if (file_exists('admin/inventario/index.php')) {
                header('Location: admin/inventario/index.php');
            } else {
                header('Location: admin/index.php');
            }
            break;
        default:
            header('Location: index.php');
    }
    exit;
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // QUITAR la verificación de 'activo = 1' por ahora hasta que agregues el campo
    // $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        // Verificar si la contraseña está encriptada
        if (password_verify($password, $usuario['password'])) {
            // Login exitoso
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'rol' => $usuario['rol']
            ];
            
            // Redirigir según rol
            switch ($usuario['rol']) {
                case 'admin':
                    header('Location: admin/index.php');
                    break;
                case 'cajero':
                    // Verificar si existe la página de cajero
                    if (file_exists('admin/ventas/index.php')) {
                        header('Location: admin/ventas/index.php');
                    } else {
                        header('Location: admin/index.php');
                    }
                    break;
                case 'almacenero':
                    // Verificar si existe la página de almacenero
                    if (file_exists('admin/inventario/index.php')) {
                        header('Location: admin/inventario/index.php');
                    } else {
                        header('Location: admin/index.php');
                    }
                    break;
                default:
                    // Si no tiene rol definido, ir al admin
                    header('Location: admin/index.php');
            }
            exit;
        } else {
            // También verificar si la contraseña está en texto plano (para migración)
            if ($usuario['password'] === $password) {
                // Reencriptar contraseña
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $usuario['id']]);
                
                $_SESSION['usuario'] = [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'email' => $usuario['email'],
                    'rol' => $usuario['rol']
                ];
                
                header('Location: admin/index.php');
                exit;
            } else {
                $error = "Credenciales incorrectas";
            }
        }
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrador , Cajero y Almacenero - Ferreteria</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 15px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #0d6efd;
            color: white;
            text-align: center;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header py-3">
                <h4 class="mb-0"><i class="bi bi-shield-lock me-2"></i> Sistema Ferretería</h4>
                <p class="mb-0 small">Acceso para administradores y empleados</p>
            </div>
            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Volver al sitio público
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3 text-muted">
            <small>Ferretería TOÑITO &copy; <?php echo date('Y'); ?></small>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>