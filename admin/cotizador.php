<?php

include 'includes/header2.php';

?>

<style>
    .modal-body {
    max-height: 500px; /* Limitar la altura del contenido */
    overflow-y: auto; /* Scroll solo dentro del modal */
    }
    .table-striped {
        border-collapse: collapse; /* Elimina espacios entre bordes */
        width: 100%; /* Asegura que ocupe el ancho completo */
    }

    .table-striped th, .table-striped td {
        text-align: left; /* Alinear contenido a la izquierda */
        padding: 12px 15px;
    }

    .table-striped th {
        background-color: #e84e0f; /* Color del encabezado */
        color: #fff; /* Color del texto en el encabezado */
        font-weight: bold;
    }

    .table-striped tr:nth-child(even) {
        background-color: #f2f2f2; /* Color alternativo para las filas pares */
    }

    .table-striped tr:hover {
        background-color: #d1ecf1; /* Color de hover para las filas */
    }
    .table-striped thead th {
        /*position: sticky; /* Hace que el encabezado sea fijo */
        top: -2; /* Lo fija en la parte superior */
        background-color: #e84e0f; /* Asegura que el color de fondo permanezca visible */
        color: #fff; /* Color del texto */
        z-index: 1; /* Evita que se superponga al contenido */
        text-align: left;
        padding: 12px 15px;
    }
    .table-striped tbody tr:nth-child(even) {
        background-color: #f9f9f9; /* Alterna el color de las filas */
    }

    .table-striped tbody tr:hover {
        background-color: #d1ecf1; /* Color al pasar el ratón */
    }                                                                                                       

    .spinner-border {
        margin: 20px auto; /* Centrar el spinner */
        width: 3rem;
        height: 3rem;
    }

    .hoverbox {
        transition: transform 0.3s ease;
        display: inline-block;
    }

    .hoverbox:hover {
        transform: scale(1.2);
        /* Escala el contenedor entero */
    }

    .img-expand {
        width: 100%;
        height: auto;
        transition: transform 0.3s ease;
    }

    #loadingSpinner {
        height: 100px;
        width: 100%;
        text-align: center;
        margin: 20px 0;
    }
</style>
<div class="d-flex justify-content-center align-items-center">
    <div class="row g-3" style="width: 80%">
        <div class="col-xxl-2 col-xl-3">
            <aside class="scrollbar-overlay font-sans-serif p-4 p-xl-3 ps-xl-0 offcanvas offcanvas-start offcanvas-filter-sidebar" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
                <div class="d-flex flex-between-center">
                    <div class="d-flex gap-2 flex-xl-grow-1 align-items-center justify-content-xl-between">
                        <h5 class="mb-0 text-dark fw-bold d-flex align-items-center" id="filterOffcanvasLabel"><span class="fas fa-stream fs--1 me-1"></span><span>Filtrar</span></h5>
                        <button id="resetFilters" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <button class="btn-close text-reset d-xl-none shadow-none" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <ul class="list-unstyled">
                    <li class="border-bottom"><a class="nav-link collapse-indicator-plus text-dark fw-bold py-3" data-bs-toggle="collapse" href="#rating-collapse" aria-controls="rating-collapse" aria-expanded="true">Líneas</a>
                        <div class="collapse show" id="rating-collapse">
                            <input type="text" class="form-control mb-2" id="searchLineas" placeholder="Buscar línea..." />
                            <ul class="list-unstyled" id="ListLineas">


                            </ul>
                        </div>
                    </li>
                    <li class="border-bottom">
                        <a class="nav-link collapse-indicator-plus text-dark fw-bold py-3" data-bs-toggle="collapse" href="#category-collapse" aria-controls="category-collapse" aria-expanded="false" style="display: none;" id="groupLink">Grupos</a>
                        <div class="collapse" id="category-collapse">
                            <input type="text" class="form-control mb-2" id="searchGrupos" placeholder="Buscar grupo..." />
                            <ul class="list-unstyled" id="ListGrupos"></ul>
                        </div>
                    </li>
                    <li class="border-bottom">
                        <a class="nav-link collapse-indicator-plus text-dark fw-bold py-3" data-bs-toggle="collapse" href="#subject-collapse" aria-controls="subject-collapse" aria-expanded="false" style="display: none;" id="subgroupLink">SubGrupos</a>
                        <div class="collapse" id="subject-collapse">
                            <input type="text" class="form-control mb-2" id="searchSubGrupos" placeholder="Buscar subgrupo..." />
                            <ul class="list-unstyled" id="ListSubGrupos"></ul>
                        </div>
                    </li>
                    <li class="border-bottom"><a class="nav-link collapse-indicator-plus text-dark fw-bold py-3" data-bs-toggle="collapse" href="#marca-collapse" aria-controls="marca-collapse" aria-expanded="false">Marcas</a>
                        <div class="collapse" id="marca-collapse">
                            <input type="text" class="form-control mb-2" id="searchMarcas" placeholder="Buscar marca..." />
                            <ul class="list-unstyled" id="ListMarcas">


                            </ul>
                        </div>
                    </li>
                </ul>
            </aside>
        </div>
        <div class="col-xxl-10 col-xl-9">
            <div class="card mb-3">
                <div class="card-header d-flex flex-between-center bg-light py-2 px-3">
                    <h5 class="">Todos los Productos</h5>

                </div>
                <div class="card-body pt-0 pt-md-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto d-xl-none">
                            <button class="btn btn-sm p-0 btn-link position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas"><span class="fas fa-stream fs-0 text-700"></span></button>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="col-auto d-none d-lg-block"><small class="fw-semi-bold">Búsqueda de producto:</small></div>
                            <div class="position-relative">
                                <input class="form-control form-control search-input lh-1 rounded-2 ps-4" type="text" placeholder="Buscar por nombre o código" id="searchInput" oninput="this.value = this.value.toUpperCase()"/>
                                <div class="position-absolute top-50 start-0 translate-middle-y ms-2"><span class="fas fa-search text-400 fs--1"></span></div>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="inlineRadio1" type="radio" name="inlineRadioOptions" value="option1" checked="" />
                                <label class="form-check-label" for="inlineRadio1">Nombre del Producto</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="inlineRadio2" type="radio" name="inlineRadioOptions" value="option2" />
                                <label class="form-check-label" for="inlineRadio2">Código del Producto</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="loadingSpinner" class="d-flex justify-content-center align-items-center" style="height: 100px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="row mb-3 g-3" id="ListProductos">

            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 flex-center justify-content-md-between">
                        <div class="col-auto">
                            <div class="row gx-2">
                                <div class="col-auto"><small>Show:</small></div>
                                <div class="col-auto">
                                    <select class="form-select form-select-sm" aria-label="Show products" id="perPageSelect" onchange="handleSelectChange(this)">
                                        <option selected="selected" value="20">20</option>
                                        <option value="40">40</option>
                                        <option value="60">60</option>
                                        <option value="80">80</option>
                                        <option value="100">100</option>
                                        <option value="custom">Ingresar valor</option>

                                    </select>
                                </div>
                                <div class="col-auto d-none" id="customInputContainer">
                                    <input
                                        type="number"
                                        class="form-control form-control-sm"
                                        id="customPerPageInput"
                                        placeholder="Ingrese valor"
                                        oninput="handleCustomInput(this)"
                                        min="1" />
                                </div>
                            </div>
                        </div>
                        <div class="col-auto" id="pagination"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


<?php

include 'includes/footer2.php';

?>


<script src="js/cotizador.js"></script>