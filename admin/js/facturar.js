// ...existing code from actualizar_cotizacion.js...

// Replace function names from 'cotizar' to 'facturar' where appropriate

function facturarProductos() {
    const metodoPago = document.querySelector('input[name="flexRadioDefault"]:checked').value;
    const idClienteGuardado = sessionStorage.getItem('id_clienteUpdate'); // Obtener id_cliente del sessionStorage

    if (!idClienteGuardado) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "No existen clientes seleccionados!",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        });
    } else if (cartPago.length === 0) {
        Swal.fire({
            title: "Comisariato del Constructor",
            text: "No existen productos seleccionados!",
            icon: "warning",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!"
        });
    } else {
        // Filtrar solo los campos necesarios del cartPago
        const detalle = cartPago.map(item => ({
            codigo_Producto: item.Codigo_Producto,
            cantidad: item.cantidad
        }));

        // TODO: Cambiar a la API de facturación cuando esté disponible
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
                    text: "Factura generada con éxito!",
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

                // Limpiar sessionStorage
                sessionStorage.removeItem('cartUpdate');
                sessionStorage.removeItem('cartTimestampUpdate');
                sessionStorage.removeItem('id_clienteUpdate');
                sessionStorage.removeItem('cart');
                sessionStorage.removeItem('cartTimestamp');
                sessionStorage.removeItem('id_cliente');
            }
        });
    }
}

// ...existing code...
