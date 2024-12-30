<?php

include 'includes/header2.php';

$orden = $_GET["q"];

echo "<input type='hidden' class='form-control' id='numero_orden' value='$orden'>";

?>
<!-- Estilos CSS para la pantalla de carga -->
<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Fondo opaco */
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .loading-content {
        text-align: center;
        color: white;
    }

    .spinner {
        border: 8px solid rgba(255, 255, 255, 0.3);
        /* Fondo del spinner */
        border-top: 8px solid white;
        /* Color del spinner */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<!-- Overlay de carga -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <div class="spinner"></div>
        <p>El PDF se está generando, espere un momento...</p>
    </div>
</div>

<div class="d-flex justify-content-center align-items-center mt-3">
    <div class="row w-50">
        <div class="card mb-3 mt-3">
            <div class="card-body">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md">
                        <h5 class="mb-2 mb-md-0">Orden #<?php echo $orden; ?></h5>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm me-1 mb-2 mb-sm-0 text-white fw-bold" style="background: #e84e0f" type="button" onclick="generarPDF()"><i class="far fa-file-pdf"></i> Descargar (.pdf)</button>
                        <!-- <button class="btn btn-falcon-default btn-sm me-1 mb-2 mb-sm-0" type="button" onclick="printDiv('invoiceDiv')">Print</button> -->
                        <a class="btn btn-falcon-primary btn-sm mb-2 mb-sm-0" type="button" href="cotizador.php"><i class="far fa-arrow-alt-circle-left"></i> Generar nueva cotización</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3" id="invoiceDiv">
            <div class="card-body">
                <div class="row align-items-center text-center mb-3">
                    <div class="col-sm-6 text-sm-start"><img src="../img/LOGO_CONSTRUCTOR.png" alt="Logo_Comisariato_Constructor" width="260" height="80"></div>
                    <div class="col text-sm-end mt-3 mt-sm-0">
                        <h2 class="mb-3">Comisariato del Constructor</h2>
                        <h5>Comisariato del Constructor S.A.</h5>
                        <p class="fs--1 mb-0">Tienda <?php echo $nomtienda;?></p>
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-500">Facturar a</h6>
                        <h5 id="nombre_Cliente"></h5>
                        <p class="fs--1 mb-0" id="direccion"></p>
                        <p class="fs--1" id="correo_telefono"></p>
                    </div>
                    <div class="col-sm-auto ms-auto">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless fs--1">
                                <tbody>
                                    <tr>
                                        <th class="text-sm-end">Nombre del Vendedor:</th>
                                        <td id="vendedor"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-sm-end">Número de Orden:</th>
                                        <td><?php echo $orden; ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-sm-end">Fecha de Cotización:</th>
                                        <td id="fecha_Pago"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-sm-end">Método de pago:</th>
                                        <td id="metodo_pago"></td>
                                    </tr>
                                    <tr class="alert alert-success fw-bold">
                                        <th class="text-sm-end">Monto Total:</th>
                                        <td id="montoTotalG"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="table-responsive scrollbar mt-4 fs--1">
                    <table class="table table-striped border-bottom">
                        <thead data-bs-theme="light">
                            <tr class="text-white dark__bg-1000" style="background: #e84e0f">
                                <th class="border-0 text-center fw-bold">Nombre del Producto</th>
                                <th class="border-0 text-center fw-bold">Cantidad</th>
                                <th class="border-0 text-center fw-bold">Precio Unitario</th>
                                <th class="border-0 text-end fw-bold">Precio Neto</th>
                            </tr>
                        </thead>
                        <tbody id="listProductos">

                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="alert alert-primary py-0">
                            <h5 class="text-900 fw-bold" id="descuento">Su descuento es: $0.00</h5>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <table class="table table-sm table-borderless fs--1 text-end">
                                <tbody>
                                    <tr>
                                        <th class="text-900">Subtotal 15%:</th>
                                        <td class="fw-semi-bold text-end" id="montoSubtotal15">$0.00</td>
                                    </tr>
                                    <tr>
                                        <th class="text-900">Subtotal 5%:</th>
                                        <td class="fw-semi-bold text-end" id="montoSubtotal5">$0.00</td>
                                    </tr>
                                    <tr>
                                        <th class="text-900">Subtotal 0%:</th>
                                        <td class="fw-semi-bold text-end" id="montoSubtotal0">$0.00</td>
                                    </tr>
                                    <tr>
                                        <th class="text-900">Total Sin Impuestos:</th>
                                        <td class="fw-semi-bold text-end" id="montoTotalSinImpuestos">$0.00</td>
                                    </tr>
                                    <tr>
                                        <th class="text-900">IVA 15%:</th>
                                        <td class="fw-semi-bold text-end" id="montoIVA15">$0.00</td>
                                    </tr>
                                    <tr>
                                        <th class="text-900">IVA 5%:</th>
                                        <td class="fw-semi-bold text-end" id="montoIVA5">$0.00</td>
                                    </tr>
                                    <tr class="border-top">
                                        <th class="text-900">Total:</th>
                                        <td class="fw-semi-bold text-end" id="montoTotal">$0.00</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php

include 'includes/footer2.php';

?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>


<script src="js/ver_detalle.js"></script>