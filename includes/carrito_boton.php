<?php
// includes/carrito_boton.php
?>
<!-- Carrito -->
<button class="btn btn-outline-primary position-relative" data-bs-toggle="modal" data-bs-target="#modalCarrito">
    <i class="bi bi-cart3"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        <?php 
        $total_items = 0;
        if (isset($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item) {
                $total_items += $item['cantidad'];
            }
        }
        echo $total_items;
        ?>
    </span>
</button>