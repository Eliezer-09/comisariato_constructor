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
<div class="d-flex justify-content-center align-items-center">
    <div class="content p-2" style="width: 85%">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-4">
                        <a><button class="btn btn-outline-primary rounded-pill me-1 mb-1"
                                type='button' style="margin: 1rem;" onclick="modalCliente()">Agregar Cliente</button></a>
                    </div>
                </div>
            </div>

            <div id="tableExample3" class="p-2" data-list='{"valueNames":["name","email","age"],"page":5,"pagination":true}'>
                <div class="row justify-content-end g-0">
                    <div class="col-auto col-sm-5 mb-3">
                        <form>
                            <div class="input-group">
                                <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Search..." aria-label="search" id="searchClientes" />
                                <div class="input-group-text bg-transparent"><span class="fa fa-search fs--1 text-600"></span></div>
                            </div>
                        </form>
                        <div class="d-flex">
                            <div class="form-check">
                                <input class="form-check-input" id="flexRadioDefault1" type="radio" name="busquedaC" />
                                <label class="form-check-label" for="flexRadioDefault1">Buscar por Nombre</label>
                            </div>
                            <div class="form-check ms-2">
                                <input class="form-check-input" id="flexRadioDefault2" type="radio" name="busquedaC" checked="" />
                                <label class="form-check-label" for="flexRadioDefault2">Buscar por RUC</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="text-center bg-200" data-sort="name">Nombre del Cliente</th>
                                <th class="text-center bg-200" data-sort="ruc">RUC</th>
                                <th class="text-center bg-200" data-sort="email">Dirección</th>
                                <th class="text-center bg-200" data-sort="email">Teléfono</th>
                                <th class="text-center bg-200" data-sort="email">Correo electrónico</th>
                                <th class="text-center bg-200" data-sort="age">Estado</th>
                                <th class="text-center bg-200" data-sort="age">Acciones</th>
                            </tr>
                        </thead>
                        <div id="loadingSpinner" class="d-flex justify-content-center align-items-center" style="height: 100px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <tbody id="listCliente" class="list">
                        </tbody>
                    </table>
                </div>
                <!-- TOTAL DE CLIENTES Y PAGINACIONES -->
                <p id="infoClientes" class="ms-2 me-2" style="font-size: 12px">Total de Clientes: 0/0. Página 1/1</p>
                <div class="d-flex justify-content-center mt-3">
                    <button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                    <ul class="pagination mb-0"></ul>
                    <button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next"><span class="fas fa-chevron-right"> </span></button>
                </div>
            </div>


        </div>
    </div>

</div>
<?php
require 'includes/footer2.php';
?>


<script src="js/ver_clientes.js"></script>