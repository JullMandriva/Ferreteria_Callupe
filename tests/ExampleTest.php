<?php

test('la página principal carga correctamente', function () {
    $response = file_get_contents('http://localhost:8000');

    expect($response)->toBeString();
    expect(strlen($response))->toBeGreaterThan(0);
});
