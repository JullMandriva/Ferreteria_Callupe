<?php

test('la página de login carga correctamente', function () {

    $html = file_get_contents('http://localhost/Ferreteria_Toñito_Callupe/login.php');

    expect($html)->toBeString();
    expect($html)->toContain('login');
});
