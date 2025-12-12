<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../../login.php');
    exit;
}

include '../../config/db.php';

 $id = $_GET['id'];

// Eliminar producto
 $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
 $stmt->execute([$id]);

header('Location: index.php');
exit;
?>