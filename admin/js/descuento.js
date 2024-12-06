function solicitarDescuento(numeroProforma, codigoTienda, nombreCuenta) {
    Swal.fire({
        title: 'Comisariato del Constructor',
        text: '¿Desea solicitar un descuento para esta proforma?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, solicitar',
        cancelButtonText: 'No, cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar solicitud POST al endpoint existente con manejo de errores
            $.post('../api/v1/constructor/solicitarDescuento', {
                numeroProforma: numeroProforma,
                codigoTienda: codigoTienda,
                nombreCuenta: nombreCuenta
            }, function(data) {
                console.log(data);
                returned = JSON.parse(data);
                
                if (returned.error == false) {
                    Swal.fire({
                        title: 'Solicitud enviada',
                        text: 'La solicitud de descuento ha sido enviada exitosamente.',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Hubo un problema al enviar la solicitud.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        }
    });
}


