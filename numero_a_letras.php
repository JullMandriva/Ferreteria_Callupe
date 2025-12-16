<?php
function num2letras($num, $moneda = 'SOLES', $centimos = 'CENTIMOS') {
    $num = number_format($num, 2, '.', '');
    $partes = explode('.', $num);
    $entero = $partes[0];
    $decimal = isset($partes[1]) ? $partes[1] : '00';
    
    $entero_letras = convertir_numero($entero);
    $decimal_letras = convertir_numero($decimal);
    
    return strtoupper($entero_letras . ' ' . $moneda . ' CON ' . $decimal_letras . ' ' . $centimos);
}

function convertir_numero($numero) {
    $unidades = array('', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE');
    $decenas = array('', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA');
    $especiales = array(
        11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE', 15 => 'QUINCE',
        16 => 'DIECISEIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO', 19 => 'DIECINUEVE',
        21 => 'VEINTIUNO', 22 => 'VEINTIDOS', 23 => 'VEINTITRES', 24 => 'VEINTICUATRO',
        25 => 'VEINTICINCO', 26 => 'VEINTISEIS', 27 => 'VEINTISIETE', 28 => 'VEINTIOCHO',
        29 => 'VEINTINUEVE'
    );
    
    if ($numero == 0) return 'CERO';
    
    $texto = '';
    
    if ($numero >= 1000) {
        $miles = floor($numero / 1000);
        if ($miles == 1) {
            $texto .= 'MIL ';
        } else {
            $texto .= convertir_numero($miles) . ' MIL ';
        }
        $numero %= 1000;
    }
    
    if ($numero >= 100) {
        $cientos = floor($numero / 100);
        if ($cientos == 1) {
            if ($numero == 100) {
                $texto .= 'CIEN ';
            } else {
                $texto .= 'CIENTO ';
            }
        } elseif ($cientos == 5) {
            $texto .= 'QUINIENTOS ';
        } elseif ($cientos == 7) {
            $texto .= 'SETECIENTOS ';
        } elseif ($cientos == 9) {
            $texto .= 'NOVECIENTOS ';
        } else {
            $texto .= $unidades[$cientos] . 'CIENTOS ';
        }
        $numero %= 100;
    }
    
    if ($numero >= 10) {
        if (isset($especiales[$numero])) {
            $texto .= $especiales[$numero] . ' ';
            $numero = 0;
        } else {
            $decena = floor($numero / 10);
            $texto .= $decenas[$decena];
            $numero %= 10;
            if ($numero > 0) {
                $texto .= ' Y ';
            }
        }
    }
    
    if ($numero > 0) {
        $texto .= $unidades[$numero] . ' ';
    }
    
    return trim($texto);
}
?>