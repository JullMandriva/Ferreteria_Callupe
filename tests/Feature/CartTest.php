<?php

test('la funcionalidad del carrito carga correctamente', function () {

    $html = file_get_contents(
        'http://localhost/Ferreteria_Toñito_Callupe/includes/carrito.php'
    );

    expect($html)->toBeString();
    expect(strlen($html))->toBeGreaterThan(0);
});
