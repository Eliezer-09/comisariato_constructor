$(document).ready(function () {
    let currentPage = 1; // Página inicial
    let pageSize = 20; // Tamaño de página fijo en 20
    let totalPages = 1;
    let searchBy = "rucCliente"; // Inicializa con la opción de búsqueda por RUC

    // Cambiar el modo de búsqueda según el radio seleccionado
    $('input[name="busquedaC"]').on('change', function () {
        searchBy = $(this).attr('id') === 'flexRadioDefault1' ? 'nombre' : 'rucCliente';
        currentPage = 1; // Reiniciar a la primera página en cada cambio
        getClientes(currentPage, pageSize); // Recargar resultados de acuerdo con la nueva selección
    });

    // Restricción para solo aceptar dígitos en el input de búsqueda
    $('#searchClientes').on('input', function () {
        currentPage = 1; // Reiniciar a la primera página en cada cambio
        getClientes(currentPage, pageSize);
    });

    // Función para obtener clientes con paginación
    async function getClientes(page, pageSize) {

        // Ocultar el spinner después de cargar los productos
        $("#loadingSpinner").removeClass("d-none");

        // Ocultar el spinner después de cargar los productos
        $("#listCliente").addClass("d-none");

        var searchClientes = $("#searchClientes").val();

        // Determinar los valores de los parámetros según el modo de búsqueda seleccionado
        let nombre = searchBy === 'nombre' ? searchClientes : "";
        let rucCliente = searchBy === 'rucCliente' ? searchClientes : "";

        $.post('../api/v1/constructor/getClientesApiTable', {
            ruc: rucCliente,
            page: page,
            pageSize: pageSize,
            codigo_Vendedor: $("#idAdministrador").val(),
            nombre: nombre
        }, function (returnedDat) {
            var returnedData = JSON.parse(returnedDat);

            if (returnedData["error"] === false) {
                // Limpiamos la tabla antes de agregar nuevos datos
                $('.list').empty();

                if (returnedData["clientes"].clientes.length > 0) {
                    // Agregar cada cliente a la tabla
                    returnedData["clientes"].clientes.forEach(function (client) {
                        var estado = client.estado_Cliente === "1"
                            ? `<small class="badge fw-semi-bold rounded-pill status badge-subtle-danger"> Inactivo</small>`
                            : `<small class="badge fw-semi-bold rounded-pill status badge-subtle-success"> Activo</small>`;

                        $('.list').append(`
                            <tr>
                                <td class="text-center">${client.nombre_Cliente}</td>
                                <td class="text-center">${client.ruc}</td>
                                <td class="text-center">${client.direccion}</td>
                                <td class="text-center">${client.telefono}</td>
                                <td class="text-center">${client.correo || 'N/A'}</td>
                                <td class="text-center">${estado}</td>
                                <td class="text-center">
                                    <a class="btn btn-md text-white fw-bold" type="button" style="background-color: #0f3d53" onclick="modalClienteUpdate('${client.nombre_Cliente}', '${client.ruc}', '${client.telefono}', '${client.direccion}', '${client.correo}')"><i class="far fa-edit"></i></a>
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
                let totalCount = parseInt(returnedData.clientes["header"]["X-Total-Count"], 10) || 0;
                totalPages = Math.ceil(totalCount / pageSize); // Número total de páginas basado en el pageSize fijo de 20
                currentPage = page;

                // Actualización de la información de paginación
                $('#infoClientes').text(`Total de Clientes: ${returnedData["clientes"].clientes.length}/${totalCount}. Página ${currentPage} de ${totalPages}`);
                renderPagination();
            }
            // Ocultar spinner y mostrar tabla después de cargar datos
            $("#loadingSpinner").addClass("d-none");
            $("#listCliente").removeClass("d-none");
        });
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

});




function modalCliente() {
    $("#alert").text("")

    $("#alert").append(`    
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary d-none" id="btnCliente" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
        Modal de Registrar Clientes
        </button>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Registrar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <div class="mb-3">
                            <label class="form-label" for="nombre_clienteCreate">Nombre del Cliente</label>
                            <input class="form-control" id="nombre_clienteCreate" type="text" placeholder="Nombre del Cliente" oninput="validarNombreCliente(this)" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="rucCreate">Cédula/RUC</label>
                            <input class="form-control" id="rucCreate" type="text" placeholder="123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="telefonoCreate">Teléfono</label>
                            <input class="form-control" id="telefonoCreate" type="text" placeholder="teléfono" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <div class="mb-3">
                            <label class="form-label" for="correoCreate">Correo electrónico</label>
                            <input class="form-control" id="correoCreate" type="email" placeholder="name@example.com" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <div class="mb-3">
                            <label class="form-label" for="direccionCreate">Dirección</label>
                            <input class="form-control" id="direccionCreate" type="text" placeholder="Dirección" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCliente()">Guardar Cliente</button>
            </div>
            </div>
        </div>
        </div>

    `)

    $("#btnCliente").click()
}

function modalClienteUpdate(nombre, ruc, telefono, direccion, correo) {
    $("#alert").text("")



    $("#alert").append(`    
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary d-none" id="btnCliente" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
        Modal de Registrar Clientes
        </button>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Registrar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                <input class="form-control" id="id_Cliente" type="hidden" placeholder="Nombre del Cliente" value="${ruc}" />
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <div class="mb-3">
                            <label class="form-label" for="nombre_clienteCreate">Nombre del Cliente</label>
                            <input class="form-control" id="nombre_clienteUpdate" type="text" placeholder="Nombre del Cliente" oninput="validarNombreCliente(this)" value="${nombre}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="rucCreate">Cédula/RUC</label>
                            <input class="form-control" id="rucUpdate" type="text" placeholder="123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="${ruc}" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="telefonoCreate">Teléfono</label>
                            <input class="form-control" id="telefonoUpdate" type="text" placeholder="teléfono" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="${telefono}" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <div class="mb-3">
                            <label class="form-label" for="correoCreate">Correo electrónico</label>
                            <input class="form-control" id="correoUpdate" type="email" placeholder="name@example.com" value="${correo}" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-xl-12">
                        <div class="mb-3">
                            <label class="form-label" for="direccionCreate">Dirección</label>
                            <input class="form-control" id="direccionUpdate" type="text" placeholder="Dirección" value="${direccion}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarCliente()">Actualizar Cliente</button>
            </div>
            </div>
        </div>
        </div>

    `)

    $("#btnCliente").click()
}

function guardarCliente() {
    var nombre_cliente = $("#nombre_clienteCreate").val();
    var ruc = $("#rucCreate").val();
    var telefono = $("#telefonoCreate").val();
    var correo = $("#correoCreate").val();
    var direccion = $("#direccionCreate").val();


    // Validación del teléfono (10 dígitos y solo números)
    var telefonoValido = /^[0-9]{10}$/.test(telefono);

    // Validación de correo
    var correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);

    // Validar campos vacíos
    if (nombre_cliente == "" || ruc == "" || telefono == "" || correo == "" || direccion == "") {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Campos obligatorios para la creación del Cliente!",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        })
        return;
    } else if (!telefonoValido) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Por favor ingrese un número de teléfono válido de 10 dígitos.",
            icon: "error",
            confirmButtonText: "Ok!"
        });
        return;
    } else if (!correoValido) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Por favor ingrese un correo válido.",
            icon: "error",
            confirmButtonText: "Ok!"
        });
        return;
    } else {

        $.post("../api/v1/constructor/getClienteApiCreate", {
            nombre_cliente: nombre_cliente,
            ruc: ruc,
            correo: correo,
            telefono: telefono,
            direccion: direccion,
            codigo_Empresa: $("#codemp").val(),
            codigo_Sucursal: $("#codsuc").val(),
            codigo_Vendedor: $("#codtienda").val()
        }, function (createCliente) {

            if (createCliente.error == false) {
                Swal.fire({
                    title: "Comisariato del Constructor",
                    text: createCliente.msg,
                    icon: "success",
                    confirmButtonText: "Ok!"
                });
            } else {
                Swal.fire({
                    title: "Comisariato del Constructor",
                    text: createCliente.msg,
                    icon: "error",
                    confirmButtonText: "Ok!"
                });
            }


        }, 'json');
    }

    // Si todas las validaciones pasan, continuar con el guardado
    // Agregar lógica para guardar el cliente
}


function actualizarCliente() {
    var nombre_cliente = $("#nombre_clienteUpdate").val();
    var ruc = $("#rucUpdate").val();
    var telefono = $("#telefonoUpdate").val();
    var correo = $("#correoUpdate").val();
    var direccion = $("#direccionUpdate").val();

    // Validación del teléfono (10 dígitos y solo números)
    var telefonoValido = /^[0-9]{10}$/.test(telefono);

    // Validar campos vacíos
    if (nombre_cliente == "" || ruc == "" || telefono == "" || correo == "" || direccion == "") {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Campos obligatorios para la actualización del Cliente!",
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        });
        return;
    }
    // Validación del número de teléfono
    if (!telefonoValido) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Por favor ingrese un número de teléfono válido de 10 dígitos.",
            icon: "error",
            confirmButtonText: "Ok!"
        });
        return;
    }

    if (!correoValido) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Por favor ingrese un correo válido.", 
            icon: "error",
            confirmButtonText: "Ok!"
        });
        return;
    }

    // Enviar los datos al backend
    $.post("../api/v1/constructor/getClienteApiUpdate", {
        nombre_cliente: nombre_cliente,
        ruc: ruc,
        correo: correo,
        telefono: telefono,
        direccion: direccion,
    }, function (createCliente) {
        if (createCliente.error == false) {
            Swal.fire({
                title: "Comisariato del Constructor",
                text: createCliente.msg,
                icon: "success",
                confirmButtonText: "Ok!"
            }).then(() => {
                // Redirección al ver_clientes.php después de la actualización exitosa
                window.location.href = "ver_clientes.php";
            });
        } else {
            Swal.fire({
                title: "Comisariato del Constructor",
                text: createCliente.msg,
                icon: "error",
                confirmButtonText: "Ok!"
            });
        }
    }, 'json');
}



// Función para validar que solo se ingrese letras en el nombre y que la primera letra sea mayúscula
function validarNombreCliente(input) {
    input.value = input.value.toUpperCase().replace(/[^A-Z\s]/g, '');

}


function validarRUC(ruc) {
    if (!/^\d{13}$/.test(ruc)) {
        return false; // El RUC debe tener 13 dígitos numéricos.
    }

    const provincia = parseInt(ruc.substring(0, 2));
    const tercerDigito = parseInt(ruc[2]);
    const digitosTipo = ruc.substring(0, 10).split('').map(Number);
    const coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];

    // Validar que los dos primeros dígitos representen una provincia
    if (provincia < 1 || provincia > 24) {
        return false;
    }

    // Validar el tercer dígito según el tipo de contribuyente
    if (tercerDigito >= 0 && tercerDigito <= 5) {
        // Cédula o RUC de persona natural
        return validarCedulaRUC(digitosTipo, coeficientes);
    } else if (tercerDigito === 6) {
        // Instituciones públicas
        return validarInstitucionPublica(digitosTipo);
    } else if (tercerDigito === 9) {
        // Sociedades privadas o extranjeras
        return validarSociedadPrivada(digitosTipo);
    }

    return false;
}

function validarCedulaRUC(digitos, coeficientes) {
    let suma = 0;
    for (let i = 0; i < coeficientes.length; i++) {
        let valor = digitos[i] * coeficientes[i];
        if (valor >= 10) valor -= 9;
        suma += valor;
    }

    let verificador = 10 - (suma % 10);
    if (verificador === 10) verificador = 0;
    return verificador === digitos[9];
}

function validarInstitucionPublica(digitos) {
    const coeficientes = [3, 2, 7, 6, 5, 4, 3, 2];
    let suma = 0;
    for (let i = 0; i < coeficientes.length; i++) {
        suma += digitos[i] * coeficientes[i];
    }

    let verificador = 11 - (suma % 11);
    if (verificador === 11) verificador = 0;
    return verificador === digitos[8];
}

function validarSociedadPrivada(digitos) {
    const coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
    let suma = 0;
    for (let i = 0; i < coeficientes.length; i++) {
        suma += digitos[i] * coeficientes[i];
    }

    let verificador = 11 - (suma % 11);
    if (verificador === 11) verificador = 0;
    return verificador === digitos[9];
}