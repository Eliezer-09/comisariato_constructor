$(document).ready(function () {
    let currentPage = 1; // Página inicial
    let pageSize = 20; // Tamaño de página fijo en 20
    let totalPages = 1;
    let searchBy = "codigoCotizacion"; // Inicializa con la opción de búsqueda por RUC

    // Cambiar el modo de búsqueda según el radio seleccionado
    $('input[name="busquedaC"]').on('change', function () {
        // Determina el tipo de búsqueda según el radio seleccionado
        if ($(this).attr('id') === 'flexRadioDefault1') {
            searchBy = 'codigoCotizacion';
        } else if ($(this).attr('id') === 'flexRadioDefault2') {
            searchBy = 'rucCliente';
        } else if ($(this).attr('id') === 'flexRadioDefault3') {
            searchBy = 'nombreCliente';
        }

        currentPage = 1; // Reiniciar a la primera página en cada cambio
        getClientes(currentPage, pageSize); // Recargar resultados de acuerdo con la nueva selección
    });

    // Eventos para Fecha Inicio y Fecha Fin
    $("#fechaInicio, #fechaFin").on('change', function () {
        validarFechasYCargarClientes();
    });

    // Restricción para solo aceptar dígitos en el input de búsqueda
    $('#searchCotizacion').on('input', function () {
        currentPage = 1; // Reiniciar a la primera página en cada cambio
        getClientes(currentPage, pageSize);
    });

    // Función para obtener clientes con paginación
    async function getClientes(page, pageSize) {

        // Ocultar el spinner después de cargar los productos
        $("#loadingSpinner").removeClass("d-none");

        // Ocultar el spinner después de cargar los productos
        $("#listarCotizacion").addClass("d-none");


        var searchCotizacion = $("#searchCotizacion").val();
        var fechaInicio = $("#fechaInicio").val();
        var fechaFin = $("#fechaFin").val();
        // Determinar los valores de los parámetros según el modo de búsqueda seleccionado
        let codigoCotizacion = searchBy === 'codigoCotizacion' ? searchCotizacion : "";
        let rucCliente = searchBy === 'rucCliente' ? searchCotizacion : "";
        let nombreCliente = searchBy === 'nombreCliente' ? searchCotizacion : "";


        $.post('../api/v1/constructor/getCotizacionesApi', {
            codigoCotizacion: codigoCotizacion,
            codigoTienda: $("#codtienda").val(),
            codigoEmp: $("#codemp").val(),
            codigoSuc: $("#codsuc").val(),
            rucCliente: rucCliente,
            nombreCliente: nombreCliente,
            page: page,
            codigovendedor: $("#idAdministrador").val(),
            pageSize: pageSize,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin
        }, function (returnedDat) {
            var returnedData = JSON.parse(returnedDat);

            if (returnedData["error"] === false) {
                // Limpiamos la tabla antes de agregar nuevos datos
                $('.list').empty();

                if (returnedData.error == false && returnedData.cotizacion && returnedData["cotizacion"].cotizacion.length > 0) {
                    // Agregar cada cliente a la tabla
                    returnedData["cotizacion"].cotizacion.forEach(function (cotizacion) {
                        let subtotal = 0;
                        let monto_iva = 0;
                        let total = 0;
                        cotizacion.detalle.forEach(function (data) {
                            subtotal += data.precio_Unidad * data.cantidad;
                        });
                        monto_iva = subtotal * 0.15;
                        total = subtotal + monto_iva;
                        const fechaConvertida = cotizacion.fecha.replace("T", " ").split(".")[0];

                        var metodo = ""
                        if (cotizacion.forma_Pago == "EF") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-success">Efectivo<span class="ms-1 fas fa-dollar-sign" data-fa-transform="shrink-2"></span></span>`
                        } else if (cotizacion.forma_Pago == "TR") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-warning">Transferencia Bancaria<span class="ms-1 fas fa-money-bill-transfer" data-fa-transform="shrink-2"></span></span>`
                        } else if (cotizacion.forma_Pago == "TC") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-primary">Tarjeta de Crédito<span class="ms-1 fas fa-credit-card" data-fa-transform="shrink-2"></span></span>`
                        } else if (cotizacion.forma_Pago == "TD") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-secondary">Tarjeta de Débito<span class="ms-1 fas fa-credit-card" data-fa-transform="shrink-2"></span></span>`
                        } else if (cotizacion.forma_Pago == "NO") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-success">Credito<span class="ms-1 fas fa-file-contract" data-fa-transform="shrink-2"></span></span>`
                        } else if (cotizacion.forma_Pago == "VA") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-secondary">Varios<span class="ms-1 fas fa-check-double" data-fa-transform="shrink-2"></span></span>`
                        } else if (cotizacion.forma_Pago == "CH") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-info">Cheque<span class="ms-1 fas fa-money-check-pen" data-fa-transform="shrink-2"></span></span>`
                        } else if (cotizacion.forma_Pago == "Desconocido") {
                            metodo = `<span class="badge badge rounded-pill d-block badge-subtle-warning">No Asignado<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>`
                        }

                        var rowClass = "";
                        let acciones = "";
                        if (!cotizacion.factura) {
                            rowClass = "";
                            // Mostrar todas las acciones
                            acciones = `
                                <a class="btn btn-md text-white fw-bold" style="background-color: #0f3d53" href="actualizar_cotizacion.php?q=${cotizacion.codigo_Orden}"><i class="far fa-edit"></i></a>
                                <a class="btn btn-md text-white fw-bold ms-2" style="background-color: #e84e0f" href="ver_detalle.php?q=${cotizacion.codigo_Orden}"><i class="far fa-eye"></i></a>
                                <a class="btn btn-md text-white fw-bold ms-2 solicitar-descuento" style="background-color: #28a745" data-codigo-orden="${cotizacion.codigo_Orden}">
                                    <i class="fas fa-percentage"></i>
                                </a>
                            `;
                        } else {
                            rowClass = "facturada-row";
                            // Bloquear todas las acciones excepto ver detalle
                            acciones = `
                                <a class="btn btn-md text-white fw-bold ms-2" style="background-color: #e84e0f" href="ver_detalle.php?q=${cotizacion.codigo_Orden}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a class="btn btn-md text-white fw-bold ms-2 block-facturada" style="background-color: #0f3d53" data-factura="${cotizacion.factura}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <a class="btn btn-md text-white fw-bold ms-2 block-facturada" style="background-color: #28a745" data-factura="${cotizacion.factura}">
                                    <i class="fas fa-percentage"></i>
                                </a>
                            `;
                        }
                        $('#listarCotizacion').append(`
                            <tr class="${rowClass}">
                                <td class="text-center">${cotizacion.nombre_Cliente}</td>
                                <td class="text-center">${cotizacion.codigo_Orden}</td>
                                <td class="text-center">${cotizacion.detalle.length}</td>
                                <td class="text-center">$${subtotal.toFixed(2)}</td>
                                <td class="text-center">$${monto_iva.toFixed(2)}</td>
                                <td class="text-center">$${total.toFixed(2)}</td>
                                <td class="text-center">${metodo}</td>
                                <td class="text-center">${fechaConvertida}</td>
                                <td class="text-center d-flex justify-content-center align-items-center">
                                    ${acciones}
                                </td>                                                       
                            </tr>
                        `);
                    });
                } else {
                    $('.list').append(`
                        <tr>
                            <td colspan="10" class="text-center">No existen datos para mostrar</td>
                        </tr>
                    `);
                }

                // Configuración de la paginación
                let totalCount = parseInt(returnedData.cotizacion["header"]["X-Total-Count"], 10) || 0;
                totalPages = Math.ceil(totalCount / pageSize); // Número total de páginas basado en el pageSize fijo de 20
                currentPage = page;

                // Actualización de la información de paginación
                $('#infoClientes').text(`Total de Cotizaciones: ${returnedData["cotizacion"].cotizacion.length}/${totalCount}. Página ${currentPage} de ${totalPages}`);
                renderPagination();
            }

            // Ocultar spinner y mostrar tabla después de cargar datos
            $("#loadingSpinner").addClass("d-none");
            $("#listarCotizacion").removeClass("d-none");
        });
    }

    // Función para validar fechas y cargar clientes
    function validarFechasYCargarClientes() {
        const fechaInicio = new Date($("#fechaInicio").val());
        const fechaFin = new Date($("#fechaFin").val());

        if (fechaInicio > fechaFin) {
            toastr.warning("La Fecha Inicio no puede ser mayor que la Fecha Fin.");
            $("#fechaInicio").val($("#fechaFin").val()); // Ajustar Fecha Inicio para que no exceda Fecha Fin
        }

        getClientes(1, pageSize); // Recargar los clientes con las nuevas fechas
    }


    function renderPagination() {
        $('.pagination').empty();

        if (currentPage > 1) {
            $('.pagination').append(`<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a></li>`);
        }

        // Rango dinámico de páginas
        let startPage = Math.max(1, currentPage - 5);
        let endPage = Math.min(totalPages, currentPage + 5);

        // Mostrar las primeras páginas y "..." si hay más de 20 páginas
        if (startPage > 1) {
            $('.pagination').append(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
            if (startPage > 2) {
                $('.pagination').append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }

        // Generar las páginas visibles dentro del rango
        for (let i = startPage; i <= endPage; i++) {
            $('.pagination').append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }

        // Mostrar las últimas páginas y "..." si hay más de 20 páginas
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                $('.pagination').append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
            $('.pagination').append(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`);
        }

        if (currentPage < totalPages) {
            $('.pagination').append(`<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a></li>`);
        }
    }

    // Evento de cambio de página
    $('.pagination').on('click', 'a', function (e) {
        e.preventDefault();
        let selectedPage = parseInt($(this).data('page'));

        // Mantener el pageSize fijo en 20 y cambiar solo el page
        getClientes(selectedPage, pageSize);
    });

    // Llama a la función inicial para cargar los datos
    getClientes(currentPage, pageSize);

    
    const $table = $('#cabeceraTable'); // Selecciona la tabla
    const $thead = $table.find('thead'); // Selecciona el thead original
    const $clonedThead = $thead.clone(); // Clona el thead
    const $tableContainer = $table.closest('.table-responsive'); // Contenedor de la tabla

    // Agregar el encabezado clonado al contenedor
    $clonedThead.addClass('sticky-header').css({
        position: 'fixed',
        top: 0,
        zIndex: 1000,
        display: 'none', // Oculto inicialmente
        backgroundColor: 'white'
    });
    $tableContainer.prepend($clonedThead);

    // Sincronizar anchos de las columnas
    function syncWidths() {
        $thead.find('th').each(function (index) {
            const width = $(this).outerWidth();
            $clonedThead.find('th').eq(index).css('width', width);
        });
    }
    syncWidths();

    // Mostrar/ocultar encabezado fijo al hacer scroll
    $tableContainer.on('scroll', function () {
        const scrollTop = $(this).scrollTop();
        if (scrollTop > $thead.offset().top) {
            $clonedThead.show();
        } else {
            $clonedThead.hide();
        }
    });

    // Actualizar anchos al redimensionar ventana
    $(window).on('resize', syncWidths);

    // Evento para el botón 'Descuento'
    $('#listarCotizacion').on('click', '.solicitar-descuento', function() {
        const codigoOrden = $(this).data('codigo-orden');
        const codigoTienda = $("#codtienda").val(); // Obtener el código de tienda
        const nombreCuenta = $("#idAdministrador").val(); // Obtener el nombre del vendedor

        // Llamar a la función definida en descuento.js
        solicitarDescuento(codigoOrden, codigoTienda, nombreCuenta);
    });

    $('#listarCotizacion').on('click', '.block-facturada', function(e) {
        e.preventDefault();
        const factura = $(this).data('factura');
        Swal.fire({
            icon: 'warning',
            title: 'Cotización Facturada',
            text: 'Esta cotización ya fue facturada. Factura: ' + factura,
            confirmButtonText: 'Aceptar'
        });
    });

});
