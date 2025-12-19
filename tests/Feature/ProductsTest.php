<?php

test('la página principal muestra productos', function () {

    $html = file_get_contents('http://localhost/Ferreteria_Toñito_Callupe/');

    expect($html)->toContain('S/');
    expect(strlen($html))->toBeGreaterThan(500);
});
