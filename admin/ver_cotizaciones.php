<?php
require 'includes/header2.php';
?>

<style>
    .table-responsive {
        overflow-y: auto;
        /* Permite el scroll vertical */
        max-height: 700px;
        /* Define la altura máxima del contenedor */
    }

    thead th {
        position: sticky;
        /* Fija los elementos al hacer scroll */
        top: 2;
        /* Define la distancia desde la parte superior */
        z-index: 2;
        /* Asegura que se superponga a otros elementos */
        background: #000;
        /* Fondo para que no se traslape con el contenido */
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        /* Opcional: añade un efecto de sombra */
    }

    .sticky-header th {
        border: 1px solid #dee2e6;
        /* Opcional: bordes */
        background: #000;
        /* Fondo blanco para claridad */
    }

    #loadingSpinner {
        height: 100px;
        width: 100%;
        text-align: center;
        margin: 20px 0;
    }
</style>

<div class="content">

    <div class="d-flex justify-content-center align-items-center">
        <div class="content p-2" style="width: 85%">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <a><button class="btn btn-outline-primary rounded-pill me-1 mb-1"
                                    type='button' style="margin: 1rem;" href="cotizador.php">Crear cotización</button></a>
                        </div>
                    </div>
                </div>

                <div class="p-2" data-list='{"valueNames":["name","email","age"],"page":5,"pagination":true}'>
                    <div class="row justify-content-between g-0">
                        <div class="mb-3 col-sm-5 col-lg-6">
                            <div class="d-flex">
                                <div class="mb-3 w-50">
                                    <label class="form-label" for="fechaInicio">Fecha Inicio</label>
                                    <input class="form-control" id="fechaInicio" type="date" max="<?php echo date("Y-m-d"); ?>" />
                                </div>
                                <div class="mb-3 w-50 ms-2">
                                    <label class="form-label" for="fechaFin">Fecha Fin</label>
                                    <input class="form-control" id="fechaFin" type="date" max="<?php echo date("Y-m-d"); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-auto col-sm-5 mb-3">
                            <form>
                                <div class="input-group">
                                    <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Search..." aria-label="search" id="searchCotizacion" />
                                    <div class="input-group-text bg-transparent"><span class="fa fa-search fs--1 text-600"></span></div>
                                </div>
                            </form>
                            <div class="d-flex">
                                <div class="form-check">
                                    <input class="form-check-input" id="flexRadioDefault1" type="radio" name="busquedaC" checked="" />
                                    <label class="form-check-label" for="flexRadioDefault1">Código de cotización</label>
                                </div>
                                <div class="form-check ms-2">
                                    <input class="form-check-input" id="flexRadioDefault2" type="radio" name="busquedaC" />
                                    <label class="form-check-label" for="flexRadioDefault2">RUC de Cliente</label>
                                </div>
                                <div class="form-check ms-2">
                                    <input class="form-check-input" id="flexRadioDefault3" type="radio" name="busquedaC" />
                                    <label class="form-check-label" for="flexRadioDefault3">nombre del Cliente</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive scrollbar">
                        <table class="table table-bordered table-striped" id="cabeceraTable">
                            <thead class="text-900" id="listBusquedaA">
                                <tr>
                                    <th class="text-center bg-200" data-sort="name">Cliente</th>
                                    <th class="text-center bg-200" data-sort="ruc">Número de Orden</th>
                                    <th class="text-center bg-200" data-sort="email">Total de Productos</th>
                                    <th class="text-center bg-200" data-sort="email">Subtotal</th>
                                    <th class="text-center bg-200" data-sort="email">Monto IVA</th>
                                    <th class="text-center bg-200" data-sort="email">Total</th>
                                    <th class="text-center bg-200" data-sort="email">Método de Pago</th>
                                    <th class="text-center bg-200" data-sort="email">Fecha de Creación</th>
                                    <th class="text-center bg-200" data-sort="age">Acciones</th>
                                </tr>
                            </thead>
                            <div id="loadingSpinner" class="d-flex justify-content-center align-items-center" style="height: 100px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <tbody class="list" id="listarCotizacion">
                            </tbody>
                        </table>
                        <!-- TOTAL DE CLIENTES Y PAGINACIONES -->
                        <p id="infoClientes" class="ms-2 me-2" style="font-size: 12px">Total de Cotizaciones: 0/0. Página 1/1</p>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                            <ul class="pagination mb-0"></ul>
                            <button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next"><span class="fas fa-chevron-right"> </span></button>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>
</div>
<?php
require 'includes/footer2.php';
?>
<script src="js/descuento.js"></script>
<script src="js/ver_cotizaciones.js"></script>