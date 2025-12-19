<?php

test('la página principal carga correctamente', function () {
    $response = file_get_contents('http://localhost/Ferreteria_Toñito_Callupe/');

    expect($response)->toBeString();
    expect(strlen($response))->toBeGreaterThan(0);
});
