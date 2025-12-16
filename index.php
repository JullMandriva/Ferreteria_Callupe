<?php
// Configurar encabezados de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Enrutamiento simple
 $request_uri = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// Si la peticiиоn es para la API, redirigir al API Gateway
if (strpos($request_uri, 'api/') === 0) {
    // Extraer el endpoint de la API
    $endpoint = substr($request_uri, 4);
    $_GET['url'] = $endpoint;
    include_once 'backend/api-gateway/index.php';
    exit;
}

// Para todas las demивs peticiones, mostrar el frontend
include_once 'frontend/index.html';
?>