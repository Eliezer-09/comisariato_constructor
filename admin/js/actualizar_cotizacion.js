let currentPage = 1; // Página inicial
let itemsPerPage = 9; // Número de productos a mostrar por defecto
let totalPages = 0; // Total de páginas
let cart = [];
let cartPago = [];
let SubtotalPago = [];
let MontoIVAPago = [];
let totalPago = [];

let selectedGroups = [];
let selectedSubgroups = [];
let selectedBrands = [];
let selectedLineas = []
var clienteList = []
var numero_orden = $("#numero_orden").val()


// Inicializar valores
let totalProductos = 0;
let montoSubTotal = 0;
let montoIVA = 0;
let montoTotal = 0;

let subtotal15 = 0;
let subtotal5 = 0;
let subtotal0 = 0;
let montoIVA15 = 0;
let montoIVA5 = 0;
let totalSinImpuestos = 0;
let totalConImpuestos = 0;


$(document).ready(function () {


    $('#searchCliente').select2();

    // Inicializar select2
    $('#searchCliente').select2({
        placeholder: 'Seleccione un cliente',
        minimumInputLength: 1,
        ajax: {
            type: 'POST',
            url: '../api/v1/constructor/getClientesApi',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // Validar si el término es un número o contiene letras
                const isNumber = /^\d+$/.test(params.term.trim());
                const upperTerm = params.term ? params.term.toUpperCase() : '';
                return {
                    nombre: upperTerm.trim(),
                    type: isNumber ? 'ruc' : 'nombre', // Enviar el tipo de búsqueda
                    codigovendedor: ""
                };
            },
            processResults: function (data) {
                if (data.clientes && data.clientes.length > 0) {
                    clientesArray = data.clientes;
                    return {
                        results: data.clientes.map(function (cliente) {
                            return {
                                id: cliente.codigo_Cliente,
                                text: cliente.nombre_Cliente + " - " + cliente.ruc
                            };
                        })
                    };
                } else {
                    return { results: [{ id: '', text: 'No se encontraron resultados', disabled: true }] };
                }
            },
            cache: true
        }
    });



    // Manejar el evento de selección
    $('#searchCliente').on('select2:select', function (e) {
        let selectedData = e.params.data;

        if (selectedData && selectedData.id) {
            let clienteSeleccionado = clientesArray.find(cliente => cliente.codigo_Cliente === selectedData.id);

            if (clienteSeleccionado) {
                // Verificar cada campo para mostrar un mensaje si está vacío o es null
                const nombreCliente = clienteSeleccionado.nombre_Cliente || "No tiene nombre";
                const ruc = clienteSeleccionado.ruc || "No tiene RUC";
                const correo = clienteSeleccionado.correo || "No tiene correo";
                const direccion = clienteSeleccionado.direccion || "No tiene dirección";
                const telefono = clienteSeleccionado.telefono || "No tiene teléfono";

                $('#nombre_cliente').html('<i class="far fa-user"></i> ' + nombreCliente);
                $('#ruc').html('<i class="far fa-address-card"></i> ' + ruc);
                $('#correo').html('<i class="far fa-envelope"></i> ' + correo);
                $('#direccion').html('<i class="fas fa-map-marker-alt"></i> ' + direccion);
                $('#telefono').html('<i class="fas fa-phone-alt"></i> ' + telefono);

                // Guardar el ID del cliente en sessionStorage
                sessionStorage.setItem('id_clienteUpdate', clienteSeleccionado.codigo_Cliente);
            } else {
                alert('Cliente no encontrado en el array');
            }
        } else {
            // Limpiar los campos si no se selecciona un cliente válido
            $('#nombre_cliente').html('<i class="far fa-user"></i> No tiene cliente seleccionado.');
            $('#ruc').html('<i class="far fa-address-card"></i> No tiene cliente seleccionado.');
            $('#correo').html('<i class="far fa-envelope"></i> No tiene cliente seleccionado.');
            $('#direccion').html('<i class="fas fa-map-marker-alt"></i> No tiene cliente seleccionado.');
            $('#telefono').html('<i class="fas fa-phone-alt"></i> No tiene cliente seleccionado.');
            sessionStorage.removeItem('id_cliente'); // Remover id_cliente si no hay cliente seleccionado
        }
    });


    $.post("../api/v1/constructor/getCotizacionesApi", {
        codigoCotizacion: numero_orden,
        codigoTienda: $("#codtienda").val(),
        codigoEmp: $("#codemp").val(),
        codigoSuc: $("#codsuc").val(),
        rucCliente: "",
        page: 1,
        pageSize: 20,
        codigovendedor: $("#idAdministrador").val()
    }, function (returnedData) {

        console.log(returnedData);

        if (returnedData.error == false) {
            const cotizacion = returnedData.cotizacion.cotizacion[0];

            // Seleccionar el método de pago basado en cotizacion.forma_Pago
            const metodoPago = cotizacion.forma_Pago;
            if (metodoPago) {
                document.querySelector(`input[name="flexRadioDefault"][value="${metodoPago}"]`).checked = true;
            }
            let product = []
            cotizacion.detalle.forEach(function (data) {
                product.push({
                    Codigo_Producto: data.codigo_Producto, // Asegurarse de que el ID sea un string
                    cantidad: data.cantidad,
                    precio: data.precio_Unidad,
                    imagen: "",
                    nombre: data.nombre_Producto
                })
            })

            // Limpiar sessionStorage de datos antiguos
            sessionStorage.removeItem('cart');
            sessionStorage.removeItem('cartTimestamp');
            sessionStorage.removeItem('id_cliente');

            // Convertir `cotizacion.detalle` a JSON y guardarlo en sessionStorage
            sessionStorage.setItem('cartUpdate', JSON.stringify(product));
            sessionStorage.setItem('cartTimestampUpdate', new Date().getTime());
            sessionStorage.setItem('id_clienteUpdate', cotizacion.ruc_Cliente);

            // Recuperar y verificar los datos del carrito
            const savedCart = JSON.parse(sessionStorage.getItem('cartUpdate'));  // Convertir de JSON a objeto
            const cartTimestamp = sessionStorage.getItem('cartTimestampUpdate');

            console.log(savedCart);
            console.log(cartTimestamp);

            // Comprobar si existe un cliente seleccionado en sessionStorage al cargar la página
            const idClienteGuardado = sessionStorage.getItem("id_clienteUpdate");
            if (idClienteGuardado) {
                $.post('../api/v1/constructor/getClientesApiById', { nombre: idClienteGuardado, codigovendedor: $("#idAdministrador").val() }, function (data) {
                    if (data.error == false) {
                        const cliente = data.clientes;

                        // Añadir la opción del cliente ya seleccionado en select2
                        const clienteOption = new Option(cliente.nombre_Cliente + " - " + cliente.ruc, cliente.codigo_Cliente, true, true);
                        $('#searchCliente').append(clienteOption).trigger('change');

                        // Mostrar la información del cliente
                        $('#nombre_cliente').html('<i class="far fa-user"></i> ' + (cliente.nombre_Cliente || "No tiene nombre"));
                        $('#ruc').html('<i class="far fa-address-card"></i> ' + (cliente.ruc || "No tiene RUC"));
                        $('#correo').html('<i class="far fa-envelope"></i> ' + (cliente.correo || "No tiene correo"));
                        $('#direccion').html('<i class="fas fa-map-marker-alt"></i> ' + (cliente.direccion || "No tiene dirección"));
                        $('#telefono').html('<i class="fas fa-phone-alt"></i> ' + (cliente.telefono || "No tiene teléfono"));
                    }
                }, 'json');
            }

            // Verificar si los datos expiraron (30 minutos)
            const now = new Date().getTime();
            if (now - cartTimestamp < 30 * 60 * 1000) {
                cart = savedCart;
                $("#btnCotizarProductos").css("display", "block");
            }


            // Llamar a estas funciones al cargar la página
            //updateCartCount();
            //updateCartDropdown();

            getProductos();

        } else {
            alert("No se encontraron cotizaciones.");
        }

    }, 'json');



    // Evento para detectar cambios en los radio buttons y actualizar los precios
    document.querySelectorAll('input[name="flexRadioDefault"]').forEach((radioButton) => {
        radioButton.addEventListener('change', function () {
            // Volver a cargar la lista de productos con los nuevos precios según el método de pago
            getProductos();
        });
    });

    // Evento para detectar cambios en los radio buttons y actualizar los precios en las opciones de porcentaje
    document.querySelectorAll('input[name="flexRadioIva"]').forEach((radioButton) => {
        radioButton.addEventListener('change', function () {
            // Volver a cargar la lista de productos con los nuevos precios según el método de pago
            getProductos();
        });
    });


})




function getProductos() {
    // Inicializar valores
    totalProductos = 0;
    montoSubTotal = 0;
    montoIVA = 0;
    montoTotal = 0;

    subtotal15 = 0;
    subtotal5 = 0;
    subtotal0 = 0;
    montoIVA15 = 0;
    montoIVA5 = 0;
    totalSinImpuestos = 0;
    totalConImpuestos = 0;


    // Reiniciar cartPago para evitar duplicados
    cartPago = [];

    // Obtener el método de pago y el IVA seleccionado
    const metodoPago = document.querySelector('input[name="flexRadioDefault"]:checked').value;
    const productosEnCarrito = JSON.parse(sessionStorage.getItem('cartUpdate')) || [];

    const defaultImageUrl = '../imagen_productos/Imagen_no_disponible.png'; // Ruta de la imagen alternativa

    // Generar HTML inicial para los productos del carrito si aún no está generado
    let listaHTML = productosEnCarrito.map((producto, index) => `
        <div class="row gx-card mx-0 align-items-center border-bottom border-200" id="producto-${index}">
            <div class="col-6 py-3">
                <div class="d-flex align-items-center">
                    <a href="#"><img class="img-fluid rounded-1 me-3 d-none d-md-block" src="../imagen_productos/${producto.Codigo_Producto}.png" onerror="this.onerror=null; this.src='${defaultImageUrl}';"  alt="" width="60"></a>
                    <div class="flex-1">
                        <h5 class="fs-0"><a class="text-900" href="#">${producto.nombre}</a></h5>
                        <p style="font-size: 12px">Código: ${producto.Codigo_Producto}</p>
                        <div class="fs--2 fs-md--1"><a class="" style="color: #e74011;" href="#!" onclick="eliminarProducto(${index})">Remove</a></div>
                    </div>
                </div>
            </div>
            <div class="col-6 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4 d-flex justify-content-end justify-content-md-center order-1 order-md-0">
                        <div>
                            <div class="input-group input-group-sm flex-nowrap" data-quantity="data-quantity">
                                <button class="btn btn-sm border-300 px-2 shadow-none" onclick="updateQuantity(${index}, 'minus')" style="background: #e74011; color: #FFF;">-</button>
                                <input class="form-control text-center px-2 input-spin-none" type="number" min="1" value="${producto.cantidad}" style="width: 50px" id="cantidad-${index}" onchange="updateQuantity(${index}, 'set', this.value)">
                                <button class="btn btn-sm border-300 px-2 shadow-none" onclick="updateQuantity(${index}, 'plus')" style="background: #e74011; color: #FFF;">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center ps-0 order-0 order-md-1 mb-2 mb-md-0 text-600" id="precio2-${index}">Cargando...</div>
                    <div class="col-md-4 text-end ps-0 order-0 order-md-1 mb-2 mb-md-0 text-600" id="precio-${index}">Cargando...</div>
                </div>
            </div>
        </div>
    `).join('');

    // Insertar la lista inicial en el contenedor solo una vez
    document.getElementById('ListaProductosS').innerHTML = listaHTML;

    // Crear una lista de promesas para manejar las solicitudes de precios
    const promises = productosEnCarrito.map((producto, index) => {
        return new Promise((resolve) => {
            $.post("../api/v1/constructor/getPrecioApi", {
                codigo_Tienda: $("#codtienda").val(),
                codigo_Producto: producto.Codigo_Producto
            }, function (returnedData) {
                let precioProducto = 0;

                if (!returnedData.error) {
                    // Selección de precio basado en el método de pago
                    if (metodoPago === 'EF' || metodoPago === 'TR' || metodoPago === 'CH' || metodoPago === 'VA' || metodoPago === 'NO' || metodoPago === 'Desconocido') {
                        precioProducto = returnedData.precio.precio_Sin_IVA;
                    } else if (metodoPago === 'TD') {
                        precioProducto = returnedData.precio.tD_Sin_IVA;
                    } else if (metodoPago === 'TC') {
                        precioProducto = returnedData.precio.tC_Sin_IVA;
                    }

                    // Agrupar subtotales en función del porcentaje de IVA
                    if (returnedData.precio.iva === 15) {
                        subtotal15 += precioProducto * producto.cantidad;
                    } else if (returnedData.precio.iva === 5) {
                        subtotal5 += precioProducto * producto.cantidad;
                    } else if (returnedData.precio.iva === 0) {
                        subtotal0 += precioProducto * producto.cantidad;
                    }

                    const subtotalProducto = precioProducto * producto.cantidad;
                    montoSubTotal += subtotalProducto;
                    totalProductos += producto.cantidad;

                    // Calcular IVA y totales
                    montoIVA15 = subtotal15 * 0.15;
                    montoIVA5 = subtotal5 * 0.05;
                    totalSinImpuestos = subtotal15 + subtotal5 + subtotal0;
                    totalConImpuestos = totalSinImpuestos + montoIVA15 + montoIVA5;

                    document.getElementById('subtotal15').textContent = `$${subtotal15.toFixed(2)}`;
                    document.getElementById('subtotal5').textContent = `$${subtotal5.toFixed(2)}`;
                    document.getElementById('subtotal0').textContent = `$${subtotal0.toFixed(2)}`;
                    document.getElementById('totalSinImpuestos').textContent = `$${totalSinImpuestos.toFixed(2)}`;
                    document.getElementById('montoIVA15').textContent = `$${montoIVA15.toFixed(2)}`;
                    document.getElementById('montoIVA5').textContent = `$${montoIVA5.toFixed(2)}`;
                    document.getElementById('montoTotal').textContent = `$${totalConImpuestos.toFixed(2)}`;


                    // Añadir el producto al carrito para pago sin duplicados
                    cartPago.push({
                        Codigo_Producto: producto.Codigo_Producto,
                        cantidad: producto.cantidad,
                        nombre_producto: producto.nombre,
                        imagen: producto.imagen,
                        precio_unitario: precioProducto,
                        precio_neto: subtotalProducto.toFixed(2)
                    });

                    // Solo actualizar precios si han cambiado
                    if (document.getElementById(`precio2-${index}`).textContent !== `$${precioProducto.toFixed(2)}`) {
                        document.getElementById(`precio2-${index}`).textContent = `$${precioProducto.toFixed(2)}`;
                    }
                    if (document.getElementById(`precio-${index}`).textContent !== `$${subtotalProducto.toFixed(2)}`) {
                        document.getElementById(`precio-${index}`).textContent = `$${subtotalProducto.toFixed(2)}`;
                    }
                }
                resolve(); // Resolver la promesa para este producto
            }, 'json');
        });
    });

    // // Ejecutar todas las promesas y actualizar los totales al final
    // Promise.all(promises).then(() => {
    //     // Calcular y mostrar los totales
    //     montoIVA = montoSubTotal * valorIVA;
    //     montoTotal = montoSubTotal + montoIVA;

    //     document.getElementById('totalProductos').textContent = `${totalProductos} (items)`;
    //     document.getElementById('montoSubTotal').textContent = `$${montoSubTotal.toFixed(2)}`;
    //     document.getElementById('montoIVA').textContent = `$${montoIVA.toFixed(2)}`;
    //     document.getElementById('montoTotal').textContent = `$${montoTotal.toFixed(2)}`;
    //     document.getElementById('montoTotal2').textContent = `$${montoTotal.toFixed(2)}`;
    //     document.getElementById('textoIVA').textContent = `IVA (${(valorIVA * 100).toFixed()}%)`;

    // });
}




// Función para actualizar los filtros seleccionados
function updateFilters() {
    selectedGroups = [];
    selectedSubgroups = [];
    selectedBrands = [];


    // Obtener grupos seleccionados
    $('#ListGrupos input:checked').each(function () {
        selectedGroups.push($(this).attr('id').replace('category-', ''));
    });

    // Obtener subgrupos seleccionados
    $('#ListSubGrupos input:checked').each(function () {
        selectedSubgroups.push($(this).attr('id').replace('subcategory-', ''));
    });

    // Obtener marcas seleccionadas
    $('#ListMarcas input:checked').each(function () {
        selectedBrands.push($(this).attr('id').replace('marca-', ''));
    });

    // Actualizar la lista de productos con los nuevos filtros
    getProductos();
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

    // Recorremos los productos del carrito
    cart.forEach((product, index) => {
        const totalProducto = product.cantidad * product.precio_Unidad;
        cartItemsContainer.innerHTML += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <a>${product.nombre_Producto}</a>
                    <div class="input-group w-100" data-quantity="data-quantity">
                        <button class="btn btn-sm btn-outline-secondary border-300 px-2 shadow-none" data-type="minus" onclick="updateQuantity2(${index}, 'minus')">-</button>
                        <input class="form-control text-center px-2 input-spin-none" type="number" min="1" value="${product.cantidad}" id="product-quantity-${index}" style="width: 50px">
                        <button class="btn btn-sm btn-outline-secondary border-300 px-2 shadow-none" data-type="plus" onclick="updateQuantity2(${index}, 'plus')">+</button>
                    </div>
                </div>
                <span class="badge badge-subtle-primary rounded-pill" id="total-product-${index}">$${totalProducto.toFixed(2)}</span>
            </li>
        `;
    });
    updateCartCount();

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

    // Calcular el nuevo total
    const newTotal = product.cantidad * product.precio;

    // Actualizar el total visual del producto
    document.getElementById(`total-product-${index}`).innerText = `$${newTotal.toFixed(2)}`;

    // Actualizar el carrito en sessionStorage
    sessionStorage.setItem('cartUpdate', JSON.stringify(cart));

    // Mantener el dropdown activo (evitar que se cierre)
    $('.dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });

    getProductos()
    updateCartCount()
}

// Función para incrementar o disminuir la cantidad
function updateQuantity(index, action, value) {
    // Obtener los productos del carrito desde el sessionStorage
    let productosEnCarrito = JSON.parse(sessionStorage.getItem('cartUpdate')) || [];
    let producto = productosEnCarrito[index];
    let cantidadActual = producto.cantidad;

    if (action === 'plus') {
        cantidadActual++;
    } else if (action === 'minus' && cantidadActual > 1) {
        cantidadActual--;
    } else if (action === 'set') {
        cantidadActual = parseInt(value);
        if (isNaN(cantidadActual) || cantidadActual < 1) cantidadActual = 1;
    }

    productosEnCarrito[index].cantidad = cantidadActual;

    // Actualizar el carrito en sessionStorage
    sessionStorage.setItem('cartUpdate', JSON.stringify(productosEnCarrito));

    // Actualizar la cantidad en la interfaz
    document.getElementById(`cantidad-${index}`).value = cantidadActual;
    document.getElementById(`precio-${index}`).textContent = `$${(producto.precio * cantidadActual).toFixed(2)}`;

    // Actualizar el total del carrito
    getProductos();
    // Llamar a estas funciones al cargar la página
    //updateCartCount();
    //updateCartDropdown();
}


// Función para eliminar un producto del carrito
function eliminarProducto(index) {
    // Obtener los productos del carrito desde el sessionStorage
    let productosEnCarrito = JSON.parse(sessionStorage.getItem('cartUpdate')) || [];

    // Eliminar el producto del array
    productosEnCarrito.splice(index, 1);

    // Actualizar el carrito en sessionStorage
    sessionStorage.setItem('cartUpdate', JSON.stringify(productosEnCarrito));

    // Actualizar la lista de productos
    getProductos();
    // Llamar a estas funciones al cargar la página
    updateCartCount();
    updateCartDropdown();
}


function cotizarProductos() {
    const metodoPago = document.querySelector('input[name="flexRadioDefault"]:checked').value;
    const idClienteGuardado = sessionStorage.getItem('id_clienteUpdate'); // Obtener id_cliente del sessionStorage

    if (!idClienteGuardado) {  // Validación si no existe idClienteGuardado o está vacío
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "No existen clientes seleccionados!",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        });
    } else if (cartPago.length === 0) {  // Validación si el carrito está vacío
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "No existen productos seleccionados!",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        });
    } else {

        console.log(cartPago)
        // Filtrar solo los campos "Codigo_Producto" y "cantidad" de cada objeto en cartPago
        const detalle = cartPago.map(item => ({
            codigo_Producto: item.Codigo_Producto,
            cantidad: item.cantidad
        }));

        $.post("../api/v1/constructor/actualizar_cotizacionApi/", {
            codigo_Cliente: idClienteGuardado,
            detalle: detalle,
            forma_Pago: metodoPago,
            codemp: $("#codemp").val(),
            codsuc: $("#codsuc").val(),
            codigo_Tienda: $("#codtienda").val(),
            codigo_Vendedor: $("#idAdministrador").val(),
            numero_orden: numero_orden
        }, function (returnedData) {
            returned = JSON.parse(returnedData);
            if (returned.error == false) {
                Swal.fire({
                    title: "Comisariato del Constructor",
                    text: "Cotización actualizada con éxito!",
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ver detalle"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "ver_detalle.php?q=" + returned.numero_orden;
                    }
                });

                sessionStorage.removeItem('cartUpdate'); // Eliminar datos expirados
                sessionStorage.removeItem('cartTimestampUpdate');
                sessionStorage.removeItem('id_clienteUpdate');

                sessionStorage.removeItem('cart'); // Eliminar datos expirados
                sessionStorage.removeItem('cartTimestamp');
                sessionStorage.removeItem('id_cliente');
            }
        });
    }
}


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
                            <input class="form-control" id="nombre_clienteCreate" type="text" placeholder="Nombre del Cliente" oninput="validarNombreCliente()" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="rucCreate">RUC</label>
                            <input class="form-control" id="rucCreate" type="text" placeholder="1234567890001" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
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

function guardarCliente() {
    var nombre_cliente = $("#nombre_clienteCreate").val();
    var ruc = $("#rucCreate").val();
    var telefono = $("#telefonoCreate").val();
    var correo = $("#correoCreate").val();
    var direccion = $("#direccionCreate").val();


    // Validación del RUC (13 dígitos y solo números)
    var rucValido = /^[0-9]{13}$/.test(ruc);

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
    } else if (!rucValido) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Por favor ingrese un RUC de 13 dígitos.",
            icon: "error",
            confirmButtonText: "Ok!"
        });
        return;
    } else if (!validarRUC(ruc)) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "Por favor ingrese un RUC válido.",
            icon: "error",
            confirmButtonText: "Ok!"
        });
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


// Función para validar que solo se ingrese letras en el nombre y que la primera letra sea mayúscula
function validarNombreCliente() {
    var nombre_cliente = $("#nombre_clienteCreate");
    var texto = nombre_cliente.val();

    // Convertir solo la primera letra en mayúscula y el resto en minúscula
    texto = texto.toUpperCase().replace(/[^A-ZÑ\s]/g, '');
    nombre_cliente.val(texto);
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

function modalListProductos() {

    var search = $("#searchInput").val()

    if (search == "") {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "No existe código en búsqueda!",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        });

        return;
    }


    $("#alert").text("")

    $("#alert").append(`    
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary d-none" id="btnModal" data-bs-toggle="modal" data-bs-target="#staticBackdropProducto">
        Launch static backdrop modal
        </button>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdropProducto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Lista de Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 g-3" id="ListProductos">
 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!--button type="button" class="btn btn-primary">Understood</button-->
            </div>
            </div>
        </div>
        </div>
        
    `)

    $.post("../api/v1/constructor/getProductoApiBuscar", {
        nombre: search,
        codigo_Empresa: $("#codemp").val(),
        codigo_Sucursal: $("#codsuc").val(),
        codigo_Tienda: $("#codtienda").val(),
        codigoGrupo: selectedGroups,
        codigoSubgrupo: selectedSubgroups,
        codigoMarca: selectedBrands,
        codigoLinea: selectedLineas,
        opcionBusqueda: "option2",
        page: 1,  // Enviar el inicio del rango como `page`
        pageSize: 20  // Enviar el final del rango como `pageSize`
    }, function (returnedData) {
        const returned = JSON.parse(returnedData);

        if (returned.error == false) {
            const productos = returned.productos;
            const totalProductos = returned.productos.header["X-Total-Count"];
            const totalPages = returned.productos.header["X-Total-Pages"];

            // Limpiar la lista de productos antes de agregar nuevos
            $("#ListProductos").empty();


            if (productos.productos.length != 0) {
                // Mostrar productos filtrados


                productos.productos.forEach(function (data) {


                    var codigo = ""
                    codigo = String(data.codigo_Producto)

                    let imageUrl = `../imagen_productos/${data.codigo_Producto}.png`;

                    const ancho = data.ancho !== null ? `${data.ancho} cm` : '0 cm';
                    const espesor = data.espesor !== null ? `${data.espesor} cm` : '0 cm';

                    // Quitar " y ' del nombre del producto
                    let nombreLimpio = data.nombre.replace(/["']/g, '');


                    $("#ListProductos").append(`
                    <div class="col-md-4 col-xxl-3">
                        <div class="card h-100 overflow-hidden">
                            <div class="card-body p-0 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="hoverbox text-center">
                                        <a class="text-decoration-none">
                                            <img class="object-fit-cover img-expand" style="height: 250px; width: 250px" src="${imageUrl}" alt="" />
                                        </a>
                                    </div>
                                    <div class="p-1">
                                        <h5 class="fs-0 mb-2"><a class="text-dark" href="#">${data.nombre}</a></h5>
                                        <h6 class="text-dark mt-1 mb-1"><a><span class="fw-bold">Código: </span></a>${data.codigo_Producto}</h6>
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
                                            <button class="btn btn-sm border-300 px-2 shadow-none" data-type="minus" onclick="updateQuantity3(this, 'minus')" style="background: #e74011; color: #FFF; font-weight: bold">-</button>
                                            <input class="form-control text-center px-2 input-spin-none" type="number" min="1" value="1" aria-label="Amount (to the nearest dollar)" style="width: 50px">
                                            <button class="btn btn-sm border-300 px-2 shadow-none" data-type="plus" onclick="updateQuantity3(this, 'plus')" style="background: #e74011; color: #FFF; font-weight: bold">+</button>
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

            } else {
                $("#ListProductos").append(`
                    <div class="col-md-12 col-xxl-12 d-flex justify-content-center align-items-center card p-3">
                        No existen productos en la búsqueda.
                    </div>   
                `);
            }
        }
    });

    $("#btnModal").click()
}

// Función para incrementar o disminuir la cantidad
function updateQuantity3(button, action) {
    const inputField = button.closest('.input-group').querySelector('input');
    let currentValue = parseInt(inputField.value);
    if (action === 'plus') {
        currentValue++;
    } else if (action === 'minus' && currentValue > 1) {
        currentValue--;
    }
    inputField.value = currentValue;
}

function addToCart(idProducto, cantidad, precio, nombre, imagen) {

    // Convertir idProducto a string para la comparación
    const idProductoString = String(idProducto);
    console.log(idProductoString);

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
        sessionStorage.setItem('cartUpdate', JSON.stringify(cart));
        sessionStorage.setItem('cartTimestampUpdate', new Date().getTime());

        // Actualizar el carrito en la interfaz
        //updateCartCount();
        //updateCartDropdown();

        // Mostrar mensaje de éxito
        toastr.success("Producto agregado al carrito");
        getProductos()
        $("#staticBackdropProducto").modal("hide")
    }

    console.log(cart);
}

function updatePagination(currentPage, totalPagesTotal) {
    let paginationHtml = '';
    let totalPages = 0

    if (currentPage > 1) {
        paginationHtml += `<button class="btn btn-falcon-default btn-sm me-2" onclick="getProductos(${currentPage - 1}, itemsPerPage)" data-bs-toggle="tooltip" data-bs-placement="top" title="Prev"><span class="fas fa-chevron-left"></span></button>`;
    }


    totalPages = (totalPagesTotal / 20)

    if (totalPages <= 10) {
        // Mostrar todas las páginas si el total es 10 o menos
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<a class="btn btn-sm ${i === currentPage ? 'btn-falcon-default text-primary' : 'btn-falcon-default'} me-2" onclick="getProductos(${i}, itemsPerPage)">${i}</a>`;
        }
    } else {
        // Mostrar los primeros 3, último, y puntos suspensivos si es necesario
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (currentPage > 4) {
            paginationHtml += `<a class="btn btn-sm btn-falcon-default me-2" onclick="getProductos(1, itemsPerPage)">1</a>`;
            if (currentPage > 5) {
                paginationHtml += `<span class="btn btn-sm me-2 disabled">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<a class="btn btn-sm ${i === currentPage ? 'btn-falcon-default text-primary' : 'btn-falcon-default'} me-2" onclick="getProductos(${i}, itemsPerPage)">${i}</a>`;
        }

        if (currentPage < totalPages - 3) {
            if (currentPage < totalPages - 4) {
                paginationHtml += `<span class="btn btn-sm me-2 disabled">...</span>`;
            }
            paginationHtml += `<a class="btn btn-sm btn-falcon-default me-2" onclick="getProductos(${totalPages}, itemsPerPage)">${totalPages}</a>`;
        }
    }

    if (currentPage < totalPages) {
        paginationHtml += `<button class="btn btn-falcon-default btn-sm" onclick="getProductos(${currentPage + 1}, itemsPerPage)" data-bs-toggle="tooltip" data-bs-placement="top" title="Next"><span class="fas fa-chevron-right"> </span></button>`;
    }

    $('#pagination').html(paginationHtml);
}