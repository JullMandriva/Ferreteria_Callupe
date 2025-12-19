<?php
session_start();

$total_items = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_items += intval($item['cantidad']);
    }
}

echo $total_items;
?>