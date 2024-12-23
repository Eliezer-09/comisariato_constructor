let currentPage = 1; // Página inicial
let itemsPerPage = 20; // Número de productos a mostrar por defecto
let totalPages = 0; // Total de páginas
let cart = [];

let selectedGroups = [];
let selectedSubgroups = [];
let selectedBrands = [];
let selectedLineas = [];

let inputTimeout; // Variable global para manejar el temporizador
let emptyFieldTimeout; // Temporizador para 5 segundos

let perPageFinal = 0
let pageInicial = 0

// var imagenRandom = ["Angulo_01.png", "Angulo_02.png", "Correa_01.png", "Correa_02.png"];
$(document).ready(function () {




    // Cargar carrito de sessionStorage si existe
    if (sessionStorage.getItem('cart')) {
        const savedCart = JSON.parse(sessionStorage.getItem('cart'));
        const cartTimestamp = sessionStorage.getItem('cartTimestamp');
        // Verificar si los datos expiraron (30 minutos)
        const now = new Date().getTime();
        if (now - cartTimestamp < 30 * 60 * 1000) {
            cart = savedCart;
        } else {
            sessionStorage.removeItem('cart'); // Eliminar datos expirados
            sessionStorage.removeItem('cartTimestamp');
            sessionStorage.removeItem('id_cliente');
        }
    }

    // Agregar el evento 'keyup' correctamente
    document.getElementById('searchInput').addEventListener('keyup', () => {
        getProductos(pageInicial, perPageFinal);
    });


    // // Escuchar cambios en el select
    // $("#perPageSelect").on("change", function () {
    //     const perPage = parseInt($(this).val(), 10); // Obtener el valor seleccionado
    //     getProductos(1, perPage); // Llamar a la función con la nueva cantidad de elementos por página
    // });
    // Cargar líneas inicialmente
    cargarLineas();
    cargarMarcas();
    $(document).on('change', 'input[name="linea"]', function () {
        const grupoCodigo = $(this).data('codigo');

        if ($(this).is(':checked')) {
            if (!selectedLineas.includes(grupoCodigo)) {
                selectedLineas.push(grupoCodigo);
            }
        } else {
            selectedLineas = selectedLineas.filter(codigo => codigo !== grupoCodigo);
        }

        cargarGrupos(selectedLineas); // Actualiza la visibilidad de Grupo
        getProductos(pageInicial, perPageFinal);
    });

    $(document).on('change', 'input[name="marcas"]', function () {
        const grupoCodigo = $(this).data('codigo');

        if ($(this).is(':checked')) {
            if (!selectedBrands.includes(grupoCodigo)) {
                selectedBrands.push(grupoCodigo);
            }
        } else {
            selectedBrands = selectedBrands.filter(codigo => codigo !== grupoCodigo);
        }
        getProductos(pageInicial, perPageFinal);
    });

    // Evento para selección de grupos
    $(document).on('change', 'input[name="grupo"]', function () {
        const grupoCodigo = $(this).data('codigo');

        if ($(this).is(':checked')) {
            if (!selectedGroups.includes(grupoCodigo)) {
                selectedGroups.push(grupoCodigo);
            }
        } else {
            selectedGroups = selectedGroups.filter(codigo => codigo !== grupoCodigo);
        }

        cargarSubGrupos(selectedGroups); // Actualiza la visibilidad de SubGrupo
        getProductos(pageInicial, perPageFinal);
    });

    // Evento para selección de subgrupo
    $(document).on('change', 'input[name="subgrupo"]', function () {
        const grupoCodigo = $(this).data('codigo');

        if ($(this).is(':checked')) {
            // Agregar el código del grupo si está seleccionado
            if (!selectedSubgroups.includes(grupoCodigo)) {
                selectedSubgroups.push(grupoCodigo);
            }
        } else {
            // Remover el código del grupo si está desmarcado
            selectedSubgroups = selectedSubgroups.filter(codigo => codigo !== grupoCodigo);
        }
        getProductos(pageInicial, perPageFinal); // Actualizar productos con los nuevos grupos seleccionados
    });

    $(document).on('click', '#resetFilters', function () {
        // Limpiar todos los arreglos
        selectedLineas = [];
        selectedGroups = [];
        selectedSubgroups = [];
        selectedBrands = [];
        // Desmarcar todos los checkboxes
        $('input[type="checkbox"]').prop('checked', false);

        // Ocultar secciones de Grupos y SubGrupos
        $("#ListGrupos").empty().closest('.collapse').collapse('hide');
        $("#ListSubGrupos").empty().closest('.collapse').collapse('hide');

        // Recargar líneas y productos sin filtros
        cargarLineas();
        getProductos(pageInicial, perPageFinal);
    });

    $(document).on('change', 'input[name="inlineRadioOptions"]', function () {
        getProductos(pageInicial, perPageFinal); // Llama a la función para actualizar los productos
    });

    filterList('searchLineas', 'ListLineas'); // Buscar en Líneas
    filterList('searchGrupos', 'ListGrupos'); // Buscar en Grupos
    filterList('searchSubGrupos', 'ListSubGrupos'); // Buscar en SubGrupos
    filterList('searchMarcas', 'ListMarcas'); // Buscar en Marcas


    // // Actualizar productos automáticamente cuando se seleccionan o deseleccionan los checkboxes
    // $('input[type="checkbox"]').change(function () {
    //     updateFilters();
    // });

    // Llamar a estas funciones al cargar la página
    updateCartCount();
    updateCartDropdown();
    // Inicializar la carga de productos con la primera página
    getProductos();

})

function filterList(inputId, listId) {
    $(`#${inputId}`).on('input', function () {
        const filter = $(this).val().toLowerCase();
        $(`#${listId} li`).each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(filter));
        });
    });
}

function handleSelectChange(select) {
    const value = select.value;

    // Mostrar el campo de entrada si la opción seleccionada es "Ingresar valor"
    if (value === "custom") {
        document.getElementById('customInputContainer').classList.remove('d-none');
        document.getElementById('customPerPageInput').focus();

        inputContainer.classList.remove('d-none');
        inputField.focus();

        // Inicia el temporizador para ocultar el campo si está vacío después de 5 segundos
        emptyFieldTimeout = setTimeout(() => {
            if (inputField.value.trim() === '') {
                resetToDefault();
            }
        }, 5000);

    } else {
        // Ocultar el campo de entrada y llamar a `getProducto()` con el valor seleccionado
        resetCustomInput();

        getProductos(1, parseInt(value, 10)); // Convierte el valor a número entero y llama a la función
    }
}

function resetToDefault() {
    const select = document.getElementById('perPageSelect');
    const inputContainer = document.getElementById('customInputContainer');
    const inputField = document.getElementById('customPerPageInput');

    // Oculta el campo de entrada
    inputContainer.classList.add('d-none');
    inputField.value = ''; // Limpia el campo de entrada

    // Selecciona la opción predeterminada (20)
    select.value = "20";

    // Llama a `getProducto()` con el valor por defecto
    getProductos(1, 20);
}

function resetCustomInput() {
    clearTimeout(inputTimeout); // Limpia el temporizador previo 
    clearTimeout(emptyFieldTimeout); // Limpia el temporizador de 5 segundos

    const inputContainer = document.getElementById('customInputContainer');
    const inputField = document.getElementById('customPerPageInput');

    // Oculta y limpia el campo de entrada
    inputContainer.classList.add('d-none');
    inputField.value = '';
}

function handleCustomInput(input) {
    clearTimeout(inputTimeout); // Limpia el temporizador previo
    clearTimeout(emptyFieldTimeout); // Limpia el temporizador de 5 segundos

    const value = parseInt(input.value, 10); // Convierte el valor ingresado a número entero

    if (value > 0) {
        // Establece un temporizador para llamar a `getProducto()` después de 2 segundos
        inputTimeout = setTimeout(() => {
            getProductos(1, value); // Llama a la función con el valor personalizado
        }, 2000);
    }

    // Reinicia el temporizador de 5 segundos para ocultar el campo si está vacío
    emptyFieldTimeout = setTimeout(() => {
        if (input.value.trim() === '') {
            resetToDefault();
        }
    }, 5000);
}
function cargarLineas() {
    selectedGroups = [];
    selectedSubgroups = [];
    selectedLineas = [];

    // Ocultar inicialmente Grupo y SubGrupo
    $("#groupLink").hide();
    $("#subgroupLink").hide();
    $("#searchGrupos").hide();
    $("#searchSubGrupos").hide();

    $.post("../api/v1/constructor/buscarCategorias", { lineas: "", grupos: "" }, function (returnedData) {
        const returned = JSON.parse(returnedData);
        if (!returned.error) {
            $("#ListLineas").empty();
            $("#ListGrupos").empty();
            $("#ListSubGrupos").empty();
            returned.categorias.forEach(function (data) {
                let resultadoLinea = capitalizeFirstLetter(data.nombre);
                $("#ListLineas").append(`
                    <li>
                        <div class="form-check d-flex">
                            <input class="form-check-input linea" type="checkbox" name="linea" data-codigo="${data.codigo}" />
                            <label class="form-check-label fs--1 flex-1 text-dark">${resultadoLinea}</label>
                        </div>
                    </li>
                `);
            });
        }
    });
}

function stock(codigoProducto) {
    $.get("../api/v1/constructor/stock", { codigo: codigoProducto }, function (data) {
        const stockData = JSON.parse(data);
        let stockHtml = '<table class="table table-striped">';
        stockHtml += '<thead><tr><th>Bodega</th><th>Stock Disponible</th></tr></thead><tbody>';
        stockData.forEach(function (item) {
            stockHtml += `<tr>
                            <td>${item.bodega}</td>
                            <td>${item.suma}</td>
                          </tr>`;
        });
        stockHtml += '</tbody></table>';
        $("#stockContent").html(stockHtml);
    });
}

function cargarMarcas() {
    $.get("../api/v1/constructor/getMarcasApi", {}, function (returnedData) {
        const returned = JSON.parse(returnedData);
        if (returned.error === false) {
            $("#ListMarcas").empty();
            returned.marcas.forEach(function (data) {
                let resultMarca = capitalizeFirstLetter(data.nombre);
                $("#ListMarcas").append(`
                    <li>
                        <div class="form-check d-flex">
                            <input class="form-check-input" type="checkbox" name="marcas" data-codigo="${data.codigo}" />
                            <label class="form-check-label fs--1 flex-1 text-dark" for="marcas-${data.codigo}">
                                ${resultMarca}
                            </label>
                        </div>
                    </li>
                `);
            });
        }
    });
}

function cargarGrupos(lineaCodigo) {
    const lineasString = lineaCodigo.join(",");

    if (lineaCodigo.length > 0) {
        $("#groupLink").show(); // Mostrar Grupo
        $("#searchGrupos").show();
        $.post("../api/v1/constructor/buscarCategorias", { lineas: lineasString, grupos: "" }, function (returnedData) {
            const returned = JSON.parse(returnedData);
            if (!returned.error) {
                $("#ListGrupos").empty();
                $("#ListSubGrupos").empty();
                $("#subgroupLink").hide(); // Ocultar SubGrupo
                returned.categorias.resultados.forEach(function (data) {
                    let resultadoGrupo = capitalizeFirstLetter(data.nombre);
                    $("#ListGrupos").append(`
                        <li>
                            <div class="form-check d-flex">
                                <input class="form-check-input" type="checkbox" name="grupo" data-codigo="${data.codigo}" />
                                <label class="form-check-label fs--1 flex-1 text-dark">${resultadoGrupo}</label>
                            </div>
                        </li>
                    `);
                });
            }
        });
    } else {
        $("#groupLink").hide(); // Ocultar Grupo
        $("#searchGrupos").hide();
        $("#ListGrupos").empty();
        $("#subgroupLink").hide(); // Ocultar SubGrupo
        $("#ListSubGrupos").empty();
        $("#searchSubGrupos").hide();
        selectedGroups = [];
        selectedSubgroups = [];
    }
}

function cargarSubGrupos(grupos) {
    const gruposString = grupos.join(",");

    if (grupos.length > 0) {
        $("#subgroupLink").show(); // Mostrar SubGrupo
        $("#searchSubGrupos").show();
        $.post("../api/v1/constructor/buscarCategorias", { lineas: selectedLineas[0], grupos: gruposString }, function (returnedData) {
            const returned = JSON.parse(returnedData);
            if (!returned.error) {
                $("#ListSubGrupos").empty();
                returned.categorias.resultados.forEach(function (data) {
                    let resultadoSubGrupo = capitalizeFirstLetter(data.nombre);
                    $("#ListSubGrupos").append(`
                        <li>
                            <div class="form-check d-flex">
                                <input class="form-check-input" type="checkbox" name="subgrupo" data-codigo="${data.codigo}" />
                                <label class="form-check-label fs--1 flex-1 text-dark">${resultadoSubGrupo}</label>
                            </div>
                        </li>
                    `);
                });
            }
        });
    } else {
        $("#subgroupLink").hide(); // Ocultar SubGrupo
        $("#ListSubGrupos").empty();
        $("#searchSubGrupos").hide();

        selectedSubgroups = [];
    }
}

/// Modificar la función para agregar productos y actualizar el carrito
function addToCart(idProducto, cantidad, precio, nombre, imagen) {

    // Convertir idProducto a string para la comparación
    const idProductoString = String(idProducto);

    const product = {
        Codigo_Producto: idProductoString, // Asegurarse de que el ID sea un string
        cantidad: cantidad,
        precio: precio,
        imagen: imagen,
        nombre: nombre
    };

    // Verificar si el producto ya está en el carrito
    const existingProductIndex = cart.findIndex(item => item.Codigo_Producto === idProductoString);
    if (existingProductIndex >= 0) {
        // Si el producto ya está en el carrito, mostrar advertencia en lugar de aumentar cantidad
        toastr.warning("Este producto ya está en el carrito");
    } else {
        // Agregar nuevo producto al carrito
        cart.push(product);

        // Guardar en sessionStorage
        sessionStorage.setItem('cart', JSON.stringify(cart));
        sessionStorage.setItem('cartTimestamp', new Date().getTime());

        // Actualizar el carrito en la interfaz
        updateCartCount();
        updateCartDropdown();

        // Mostrar mensaje de éxito
        toastr.success("Producto agregado al carrito");
    }
}


function capitalizeFirstLetter(texto) {
    return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
}

// Función para obtener una imagen aleatoria
function getRandomImage() {
    return imagenRandom[Math.floor(Math.random() * imagenRandom.length)];
}


function getProductos(page = 1, perPage = 20) {
    const searchTerm = $("#searchInput").val();
    const searchType = $('input[name="inlineRadioOptions"]:checked').val();
    const nombreB = searchType === "option1" || searchType === "option2" ? searchTerm : "";

    const gruposString = selectedGroups.join(",");
    const subgruposString = selectedSubgroups.join(",");
    const lineasString = selectedLineas.join(",");
    const marcasString = selectedBrands.join(",");

    pageInicial = page;
    perPageFinal = perPage;

    console.log(pageInicial);
    console.log(perPageFinal);

    $("#loadingSpinner").removeClass("d-none");
    $("#ListProductos").addClass("d-none");

    $.post("../api/v1/constructor/getProductoApiBuscar", {
        nombre: nombreB,
        codigo_Empresa: $("#codemp").val(),
        codigo_Sucursal: $("#codsuc").val(),
        codigo_Tienda: $("#codtienda").val(),
        codigoGrupo: gruposString,
        codigoSubgrupo: subgruposString,
        codigoMarca: marcasString,
        codigoLinea: lineasString,
        opcionBusqueda: searchType,
        page: page,
        pageSize: perPage
    }, function (returnedData) {
        const returned = JSON.parse(returnedData);

        if (returned.error === false) {
            const productos = returned.productos;
            const totalProductos = (returned.productos.header["X-Total-Count"] / returned.productos.header["X-Page-Size"]);

            console.log(returned.productos.header["X-Total-Count"]);
            console.log(returned.productos.header["X-Page-Size"]);

            console.log(totalProductos);

            $("#ListProductos").empty();

            if (productos.productos && productos.productos.mssg != "No se encontraron productos con los criterios proporcionados.") {
                productos.productos.forEach(function (data) {
                    const codigo = String(data.codigo_Producto);
                    const codigoAlterno = data.codigo_Alterno ? String(data.codigo_Alterno) : null;
                    const imageUrl = `../imagen_productos/${data.codigo_Producto}.png`;
                    const defaultImageUrl = '../imagen_productos/Artboard.webp';
                    const nombreLimpio = data.nombre.replace(/["']/g, '');

                    $("#ListProductos").append(`
                    <div class="col-md-4 col-xxl-3">
                        <div class="card h-100 overflow-hidden">
                            <div class="card-body p-0 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="hoverbox text-center">
                                        <a class="text-decoration-none text-center">
                                            <img class="object-fit-cover img-expand" style="height: 250px; width: 250px" src="${imageUrl}" onerror="this.onerror=null; this.src='${defaultImageUrl}';"  alt="" />
                                        </a>
                                    </div>
                                    <div class="p-1">
                                        <h5 class="fs-0 mb-2"><a class="text-dark fw-bold" href="#">${data.nombre}</a></h5>
                                        <p class="text-dark mt-1 mb-1"><a><span class="fw-bold"></a>${data.desc_ampl}</p>
                                        <h6 class="text-dark mt-1 mb-1"><a><span class="fw-bold">Código: </span></a>${data.codigo_Producto} ${data.codigo_Alterno ? `<a><span class="fw-bold"></span></a>(${data.codigo_Alterno})</h6>` : ''}
                                        <h6 class="text-dark mt-1 mb-1"><a><span class="fw-bold">Disponible: </span></a>${data.stockActual} <a href="#" onclick="stock('${codigo}')" data-bs-toggle="modal" data-bs-target="#stockModal"><span class="fas fa-search"></span></a></h6>                                
                                    </div>
                                </div>
                                <div class="row g-0 align-items-end">
                                    <div class="col ps-3">
                                        <h3 class="d-flex align-items-center">
                                            <span style="color: #0f3d53" class="fe-bold">$${parseFloat(data.precio).toFixed(2)}</span>
                                            <p class="ms-2 mt-3 fs--1 text-700 d-flex justify-content-center align-items-center">Precios incluyen IVA</p>
                                        </h3>
                                    </div>
                                    
                                </div>
                                <div class="row d-flex justify-content-center align-items-center">
                                    <div class="col-lg-12 m-2 d-flex justify-content-center align-items-center">
                                        <div class="input-group w-50" data-quantity="data-quantity">
                                            <button class="btn btn-sm border-300 px-2 shadow-none" data-type="minus" onclick="updateQuantity(this, 'minus')" style="background: #e74011; color: #FFF; font-weight: bold">-</button>
                                            <input class="form-control text-center px-2 input-spin-none" type="number" min="1" value="1" aria-label="Amount (to the nearest dollar)" style="width: 50px">
                                            <button class="btn btn-sm border-300 px-2 shadow-none" data-type="plus" onclick="updateQuantity(this, 'plus')" style="background: #e74011; color: #FFF; font-weight: bold">+</button>
                                        </div>
                                        <a class="btn ms-3 me-3" onclick="addToCart('${codigo}', parseInt(this.previousElementSibling.querySelector('input').value), ${data.precio}, '${nombreLimpio}', '${imageUrl}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Add to Cart" style="background: #e74011; color: #FFF; font-weight: bold">
                                            <span class="fas fa-cart-plus" data-fa-transform="down-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   
                `);
                });
                
                // Modal HTML
                $("body").append(`
                    <div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="stockModalLabel">Stock Disponible</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="stockContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                        </div>
                    </div>
                    </div>
                    `);

                updatePagination(page, totalProductos, perPage);

                $("#loadingSpinner").css("display", "none");
            } else {
                $("#ListProductos").append(`
                    <div class="col-md-12 col-xxl-12 d-flex justify-content-center align-items-center card p-3">
                        No existen productos en la búsqueda.
                    </div>   
                `);
            }
        }
        $("#ListProductos").removeClass("d-none");
        $("#loadingSpinner").addClass("d-none");
    });
}



// Función para actualizar el número de productos en el ícono del carrito
function updateCartCount() {
    const cartCount = cart.reduce((sum, product) => sum + product.cantidad, 0);
    document.getElementById('cartCount').innerText = cartCount;
}

// Función para mostrar los productos del carrito en el dropdown
function updateCartDropdown() {
    const cartItemsContainer = document.getElementById('cartItems');
    cartItemsContainer.innerHTML = '';

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="text-center">Tu carrito está vacío</p>';
        return;
    }


    let totalCantidad = 0;
    let subtotal = 0;

    // Recorremos los productos del carrito
    cart.forEach((product, index) => {
        const totalProducto = product.cantidad * product.precio;
        totalCantidad += product.cantidad;
        subtotal += totalProducto;
        const defaultImageUrl = '../imagen_productos/Artboard.webp'; // Ruta de la imagen alternativa

        cartItemsContainer.innerHTML += `
            <li class="list-group-item position-relative">
                <!-- Botón de eliminar producto -->
                <a class="pos ition-absolute end-0 me-3" href="#" onclick="eliminarProducto(${index}, event)" style="color: #0f3d53">X</a>
                <div class="d-flex justify-content-between align-items-center">
                    <img src="${product.imagen}" width="48" height="48" class="text-center d-flex justify-content-center align-items-center" 
                     onerror="this.onerror=null; this.src='${defaultImageUrl}'; <script>console.log('Imagen no encontrada - ${product.codigo}');</script>"  
                     alt="">
                    <div class="w-100 ms-3">
                        <a>${product.nombre}</a>
                        <div class="input-group my-2">
                            <button class="btn btn-sm border-300 px-2 shadow-none" data-type="minus" onclick="updateQuantity2(${index}, 'minus')" style="background: #e74011; color: #FFF">-</button>
                            <input class="form-control text-center px-2 input-spin-none" type="number" min="1" value="${product.cantidad}" id="product-quantity-${index}" style="width: 50px">
                            <button class="btn btn-sm border-300 px-2 shadow-none" data-type="plus" onclick="updateQuantity2(${index}, 'plus')" style="background: #e74011; color: #FFF">+</button>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-subtle-primary rounded-pill" id="total-product-${index}">$${totalProducto.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            </li>
        `;
    });

    // Agregamos la sección de total de productos y subtotal antes del botón "Ir a cotizar"
    cartItemsContainer.innerHTML += `
        <li class="list-group-item d-flex justify-content-between bg-light">
            <strong>Total de productos:</strong>
            <span id="totalCantidadCarrito">${totalCantidad}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between bg-light">
            <strong>Subtotal:</strong>
            <span id="subtotalCarrito">$${subtotal.toFixed(2)}</span>
        </li>
`;

}

// Función para eliminar un producto del carrito
function eliminarProducto(index, event) {
    // Prevenir que el dropdown se cierre
    event.preventDefault();
    event.stopPropagation();

    // Obtener los productos del carrito desde el sessionStorage
    let productosEnCarrito = JSON.parse(sessionStorage.getItem('cart')) || [];

    // Eliminar el producto del array
    productosEnCarrito.splice(index, 1);

    // Actualizar el carrito en sessionStorage
    sessionStorage.setItem('cart', JSON.stringify(productosEnCarrito));

    // Actualizar la variable cart con los nuevos datos del sessionStorage
    cart = productosEnCarrito;

    // Llamar a estas funciones para actualizar el DOM
    updateCartCount();
    updateCartDropdown();
}



// Función para actualizar la cantidad de un producto en el carrito y su total
function updateQuantity2(index, action) {
    let product = cart[index];
    let currentQuantity = parseInt(document.getElementById(`product-quantity-${index}`).value);

    if (action === 'plus') {
        currentQuantity++;
    } else if (action === 'minus' && currentQuantity > 1) {
        currentQuantity--;
    }

    // Actualizar la cantidad del producto en el carrito
    product.cantidad = currentQuantity;

    // Actualizar la cantidad en el input
    document.getElementById(`product-quantity-${index}`).value = currentQuantity;

    // Calcular el nuevo total para este producto
    const newTotal = product.cantidad * product.precio;

    // Actualizar el total visual del producto
    document.getElementById(`total-product-${index}`).innerText = `$${newTotal.toFixed(2)}`;

    // Actualizar el carrito en sessionStorage
    sessionStorage.setItem('cart', JSON.stringify(cart));

    // Recalcular el total de productos y el subtotal
    let totalCantidad = 0;
    let subtotal = 0;

    cart.forEach((prod) => {
        totalCantidad += prod.cantidad;
        subtotal += prod.cantidad * prod.precio;
    });

    // Actualizar el total de productos y subtotal visualmente
    $("#totalCantidadCarrito").text(totalCantidad)
    $("#subtotalCarrito").text("$ " + subtotal.toFixed(2))

    // Actualizar el número de productos en el ícono del carrito
    updateCartCount();

    // Mantener el dropdown activo (evitar que se cierre)
    $('.dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });
}


// Función para incrementar o disminuir la cantidad
function updateQuantity(button, action) {
    const inputField = button.closest('.input-group').querySelector('input');
    let currentValue = parseInt(inputField.value);
    if (action === 'plus') {
        currentValue++;
    } else if (action === 'minus' && currentValue > 1) {
        currentValue--;
    }
    inputField.value = currentValue;
}



/*Para implementar la paginación en tu función getProductos usando el valor de X-Total-Pages y paginando cada 20 productos, haremos los siguientes ajustes en el código:
Extraer y usar X-Total-Pages: Al recibir los datos desde el backend, el X-Total-Pages servirá como el límite de páginas.
Enviar los parámetros page y pageSize en cada solicitud de paginación.
Actualizar la visualización de los productos según el rango solicitado (1-20, 21-40, etc.).
Aquí tienes el código ajustado:

javascript
Copiar código*/
// Función para actualizar la paginación
// Función para actualizar la paginación con puntos suspensivos
function updatePagination(currentPage, totalProductos, perPage) {
    let paginationHtml = '';
    const totalPages = Math.ceil(totalProductos); // Calcular total de páginas basado en 20 items por página

    if (currentPage > 1) {
        paginationHtml += `<button class="btn btn-falcon-default btn-sm me-2" onclick="getProductos(${currentPage - 1}, ${perPage})" data-bs-toggle="tooltip" data-bs-placement="top" title="Prev"><span class="fas fa-chevron-left"></span></button>`;
    }

    if (totalPages <= 10) {
        // Mostrar todas las páginas si el total es 10 o menos
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<a class="btn btn-sm ${i === currentPage ? 'btn-falcon-default text-primary' : 'btn-falcon-default'} me-2" onclick="getProductos(${i}, ${perPage})">${i}</a>`;
        }
    } else {
        // Mostrar los primeros 3, último, y puntos suspensivos si es necesario
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (currentPage > 4) {
            paginationHtml += `<a class="btn btn-sm btn-falcon-default me-2" onclick="getProductos(1, ${perPage})">1</a>`;
            if (currentPage > 5) {
                paginationHtml += `<span class="btn btn-sm me-2 disabled">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<a class="btn btn-sm ${i === currentPage ? 'btn-falcon-default text-primary' : 'btn-falcon-default'} me-2" onclick="getProductos(${i}, ${perPage})">${i}</a>`;
        }

        if (currentPage < totalPages - 3) {
            if (currentPage < totalPages - 4) {
                paginationHtml += `<span class="btn btn-sm me-2 disabled">...</span>`;
            }
            paginationHtml += `<a class="btn btn-sm btn-falcon-default me-2" onclick="getProductos(${totalPages}, ${perPage})">${totalPages}</a>`;
        }
    }

    if (currentPage < totalPages) {
        paginationHtml += `<button class="btn btn-falcon-default btn-sm" onclick="getProductos(${currentPage + 1}, ${perPage})" data-bs-toggle="tooltip" data-bs-placement="top" title="Next"><span class="fas fa-chevron-right"> </span></button>`;
    }

    $('#pagination').html(paginationHtml);
}



function irDetalleCotizar() {
    var cliente = $("#listClientes").val()


    if (cart.length == 0) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "El carrito de cotizar está vacío.",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        })
        // SweetAlert("warning", "Tu carrito de compra está vacío")
    } else if (cliente == "") {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Seleccione un cliente",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        })
    } else {
        // sessionStorage.setItem('id_cliente', cliente); // Guardar en sessionStorage
        window.location.href = "detalle_cotizar.php"
    }
}


function imageExists(url) {
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status != 404;
}
