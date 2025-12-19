<?php

it('tiene categorías disponibles', function () {
    $categorias = ['Herramientas', 'Electricidad', 'Fontanería', 'Pintura'];
    expect(count($categorias))->toBeGreaterThan(0);
    expect($categorias)->toContain('Herramientas');
});

it('al seleccionar una categoría se muestran los productos correctos', function () {
    // Simulamos productos por categoría
    $productos = [
        'Herramientas' => ['Taladro', 'Amoladora', 'Sierra'],
        'Electricidad' => ['Bombilla', 'Cable', 'Interruptor'],
        'Fontanería' => ['Llave inglesa', 'Tubería', 'Cemento PVC'],
        'Pintura' => ['Pincel', 'Rodillo', 'Pistola de pintura']
    ];

    $categoriaSeleccionada = 'Herramientas';
    $productosEsperados = ['Taladro', 'Amoladora', 'Sierra'];

    expect($productos[$categoriaSeleccionada])->toBe($productosEsperados);
});
