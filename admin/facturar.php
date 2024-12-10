<?php

include 'includes/header2.php';

$numero_orden = $_GET["q"];

echo "<input class='form-control' type='hidden' id='numero_orden' value='$numero_orden'>"

?>
<!-- ...existing code from actualizar_cotizacion.php... -->
<!-- Replace occurrences of 'Cotizar' with 'Facturar' -->

<!-- Update the button to call facturarProductos() and change the text -->
<div class="card-footer d-flex justify-content-end bg-light">
    <button class="btn btn-lg" type="button" onclick="facturarProductos()" id="btnFacturarProductos" style="background: #0f3d53; color: #FFF"><i class="far fa-money-bill-alt"></i> Facturar</button>
</div>

<!-- ...existing code... -->

<!-- Update the script source at the end -->
<script src="js/facturar.js"></script>
