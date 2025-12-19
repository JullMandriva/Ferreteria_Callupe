<?php

test('la página del carrito carga correctamente', function () {

    $html = file_get_contents('http://localhost/Ferreteria_Toñito_Callupe/carrito.php');

    expect($html)->toBeString();
    expect($html)->toContain('El carrito está vacío');
});
test('el carrito muestra productos correctamente', function () {

    // Simular una sesión con productos en el carrito
    $_SESSION['carrito'] = [
        1 => 2, // Producto con ID 1, cantidad 2
        3 => 1  // Producto con ID 3, cantidad 1
    ];

    $html = file_get_contents('http://localhost/Ferreteria_Toñito_Callupe/carrito.php');

    expect($html)->toBeString();
    expect($html)->toContain('Producto:'); // Verificar que se muestran productos
    expect($html)->toContain('Cantidad: 2'); // Verificar cantidad del primer producto
    expect($html)->toContain('Cantidad: 1'); // Verificar cantidad del segundo producto
});