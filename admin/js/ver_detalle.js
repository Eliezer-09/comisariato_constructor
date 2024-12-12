let currentPage = 1; // Página inicial
let itemsPerPage = 9; // Número de productos a mostrar por defecto
let totalPages = 0; // Total de páginas
let cart = [];
let cartPago = [];
var SubtotalPago = "";
var MontoIVAPago = "";
var totalPago = "";
var metodo_pago = "";
var id_cotizacion = "";
var id_cliente = ""
var nombreCliente = ""
var correoCliente = ""
var telefonoCliente = ""
var direccionCliente = ""
var fechaPago = ""
var numero_orden = $("#numero_orden").val()
var nombre_vendedor = ""
var nombre_tienda = ""
var dire_tienda = ""
let corporativo = 0;
let subtotal15 = 0;
let subtotal5 = 0;
let subtotal0 = 0;
let montoIVA15 = 0;
let montoIVA5 = 0;
let totalSinImpuestos = 0;
let totalConImpuestos = 0;

let totalDescuento = 0;

$(document).ready(function () {


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

        console.log(returnedData)


        if (returnedData.error == false) {
            const cotizacion = returnedData.cotizacion.cotizacion[0];
            listarProductosCotizacion(cotizacion);
            id_cotizacion = cotizacion.codigo_Orden;

            var metodo = "";

            console.log(cotizacion.forma_Pago)
            if (cotizacion.forma_Pago == "TC") {
                metodo = "Tarjeta de Crédito";
            } else if (cotizacion.forma_Pago == "TD") {
                metodo = "Tarjeta de Débito";
            } else if (cotizacion.forma_Pago == "EF") {
                metodo = "Efectivo";
            } else if (cotizacion.forma_Pago == "TR") {
                metodo = "Transferencia";
            } else if (cotizacion.forma_Pago == "NO") {
                metodo = "Crédito";
            } else if (cotizacion.forma_Pago == "VA") {
                metodo = "Varios";
            } else if (cotizacion.forma_Pago == "CH") {
                metodo = "Cheque";
            } else if (cotizacion.forma_Pago == "Desconocido") {
                metodo = "Desconocido";
            }

            const fechaOriginal = cotizacion.fecha;
            const fechaConvertida = fechaOriginal.replace("T", " ").split(".")[0];

            metodo_pago = metodo;
            document.getElementById('metodo_pago').textContent = metodo;
            document.getElementById('fecha_Pago').textContent = `${fechaConvertida}`;
            // document.getElementById('direccion').textContent = `${cotizacion.direccion}`;
            document.getElementById('nombre_Cliente').textContent = `${cotizacion.nombre_Cliente}`;
            // Manejo seguro de correo y teléfono cuando son null
            const correo = cotizacion.correo_Cliente || "Correo no disponible";
            const telefono = cotizacion.numero_Cliente || "Teléfono no disponible";
            const direccion = cotizacion.direccion || "Dirección no disponible";

            document.getElementById("direccion").innerHTML = `${direccion}`;
            document.getElementById('correo_telefono').innerHTML = `<a href="mailto:${correo}">${correo}</a><br><a href="tel:${telefono}">${telefono}</a>`;

            nombreCliente = cotizacion.nombre_Cliente
            correoCliente = correo
            telefonoCliente = telefono
            direccionCliente = direccion
            fechaPago = fechaConvertida
            id_cliente = cotizacion.ruc_Cliente
        	corporativo = cotizacion.corporativo
            nombre_vendedor = cotizacion.nombre_Vendedor
            nombre_tienda = cotizacion.nombre_tienda
        	dire_tienda = cotizacion.direccion_tienda
            // Mostrar el descuento
            document.getElementById('descuento').textContent = `Su descuento es: $${cotizacion.descuento.toFixed(2)}`;
			document.getElementById('vendedor').innerHTML = `${cotizacion.nombre_Vendedor}`;
            totalDescuento = cotizacion.descuento

        } else {
            alert("No se encontraron cotizaciones.");
        }


    }, 'json')
})


function listarProductosCotizacion(detalleProductos) {
    let productosHTML = '';
    subtotal15 = 0;
    subtotal5 = 0;
    subtotal0 = 0;
    montoIVA15 = 0;
    montoIVA5 = 0;
    totalSinImpuestos = 0;
    totalConImpuestos = 0;

    // Iterar sobre los productos en el detalle de la cotización
    detalleProductos.detalle.forEach(function (producto) {
        const precioNeto = producto.cantidad * parseFloat(producto.precio_Unidad);

        cartPago.push({
            nombre_producto: producto.nombre_Producto,
            cantidad: producto.cantidad,
            precio_unitario: producto.precio_Unidad,
            precioNeto: precioNeto
        });

        // Agrupar subtotales en función del porcentaje de IVA
        if (producto.porcentaje_IVA === 15) {
            subtotal15 += precioNeto;
        } else if (producto.porcentaje_IVA === 5) {
            subtotal5 += precioNeto;
        } else if (producto.porcentaje_IVA === 0) {
            subtotal0 += precioNeto;
        }

        // Generar el HTML para cada producto
        productosHTML += `
            <tr>
                <td class="align-middle text-center">
                    <h6 class="mb-0 text-nowrap">${producto.nombre_Producto}</h6>
                </td>
                <td class="align-middle text-center">${producto.cantidad}</td>
                <td class="align-middle text-center">$${parseFloat(producto.precio_Unidad).toFixed(2)}</td>
                <td class="align-middle text-end">$${parseFloat(precioNeto).toFixed(2)}</td>
            </tr>
        `;
    });

    // Calcular IVA y totales
    montoIVA15 = subtotal15 * 0.15;
    montoIVA5 = subtotal5 * 0.05;
    totalSinImpuestos = subtotal15 + subtotal5 + subtotal0;
    totalConImpuestos = totalSinImpuestos + montoIVA15 + montoIVA5;

    // Actualizar los valores en el DOM
    document.getElementById('montoSubtotal15').textContent = `$${parseFloat(subtotal15).toFixed(2)}`;
    document.getElementById('montoSubtotal5').textContent = `$${parseFloat(subtotal5).toFixed(2)}`;
    document.getElementById('montoSubtotal0').textContent = `$${parseFloat(subtotal0).toFixed(2)}`;
    document.getElementById('montoTotalSinImpuestos').textContent = `$${parseFloat(totalSinImpuestos).toFixed(2)}`;
    document.getElementById('montoIVA15').textContent = `$${parseFloat(montoIVA15).toFixed(2)}`;
    document.getElementById('montoIVA5').textContent = `$${parseFloat(montoIVA5).toFixed(2)}`;
    document.getElementById('montoTotal').textContent = `$${parseFloat(totalConImpuestos).toFixed(2)}`;
    document.getElementById('monto').textContent = `$${parseFloat(totalConImpuestos).toFixed(2)}`;
    document.getElementById('montoTotalG').textContent = `$${parseFloat(totalConImpuestos).toFixed(2)}`;


    // Insertar los productos en el tbody de la tabla
    document.getElementById('listProductos').innerHTML = productosHTML;


    // Actualizar variables globales para el pago
    SubtotalPago = totalSinImpuestos;
    MontoIVAPago = montoIVA15 + montoIVA5;
    totalPago = totalConImpuestos;
}


function generarPDF() {
    document.getElementById('loading-overlay').style.display = 'flex';

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ compress: true });

    const logoURL = 'http://192.168.1.153/img/5.png';
    const img = new Image();
    img.src = logoURL;
	
    img.onload = function () {
        // Reducir la calidad de la imagen para optimización
   	 function addHeader(doc) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = img.width / 2;
        canvas.height = img.height / 2;
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        const compressedImg = canvas.toDataURL('image/PNG', 0.5);

        // Agregar logo como marca de agua (translúcido)
        doc.addImage(compressedImg, 'PNG', 50, 80, 100, 50, null, 'SLOW');
        doc.setTextColor(150); // Color gris para el texto de la marca de agua
        doc.setFontSize(14);

        // Títulos principales
        doc.setFontSize(12);
        doc.setTextColor(0);
        doc.text('COMISARIATO DEL CONSTRUCTOR S.A.', 105, 15, { align: 'center' });
        doc.setFontSize(10);
        doc.text('RUC: 0992708328001', 105, 20, { align: 'center' });
        doc.text(`Sucursal: ${nombre_tienda}`, 105, 25, { align: 'center' });
        doc.text(`${dire_tienda}`, 105, 30, { align: 'center' });
 	  }

        // Información principal en formato de dos columnas
        doc.setFontSize(10);
        doc.setFont('Helvetica', 'bold');
        doc.text('CLIENTE: ', 15, 45);
        doc.setFont('Helvetica', 'normal');
        doc.text(`${nombreCliente}`, 37, 45);

        doc.setFont('Helvetica', 'bold');
        doc.text('DIRECCIÓN: ', 15, 55);
        doc.setFont('Helvetica', 'normal');
        doc.text(`${direccionCliente}`, 37, 55);

        doc.setFont('Helvetica', 'bold');
        doc.text('VENDEDOR:', 15, 50);
        doc.setFont('Helvetica', 'normal');
        doc.text(`${nombre_vendedor}`, 37, 50);

        doc.setFont('Helvetica', 'bold');
        doc.text('FECHA:', 135, 50);
        doc.setFont('Helvetica ', 'normal');
        doc.text(`${fechaPago}`, 160, 50,);       

        doc.setFont('Helvetica', 'bold');
        doc.text('CL. / RUC:', 135, 45);
        doc.setFont('Helvetica', 'bold');
        doc.text(`${id_cliente}`, 160, 45);
    
    	doc.setFont('Helvetica', 'bold');
        doc.text('TELEFONO:', 135, 55);
        doc.setFont('Helvetica', 'normal');
        doc.text(`${telefonoCliente}`, 160, 55);
    
    	doc.setFontSize(14);
        doc.setFont('Helvetica', 'bold');
        doc.text('PROFORMA #', 75, 37, { align: 'left' });
        doc.setFont('Helvetica', 'bold');
		doc.text(`${numero_orden}`, 110, 37, { align: 'left' });
        // Tabla de productos
        let body = [];
        cartPago.forEach(producto => {
            const precioNeto = producto.cantidad * producto.precio_unitario;
            body.push([
                producto.nombre_producto,
                producto.cantidad,
                `$${parseFloat(producto.precio_unitario).toFixed(2)}`,
                `$${parseFloat(precioNeto).toFixed(2)}`
            ]);
        });

        doc.autoTable({
        	startY: 70,
        	head: [['Nombre del Producto', 'Cantidad', 'Precio Unitario', 'Precio Neto']],
        	body: body,
        	theme: 'grid',
        	styles: { fontSize: 8 },
        	headStyles: { 
            fillColor: [227, 67, 28],
            halign: 'center'
       		 }, // Naranja
        	alternateRowStyles: { fillColor: [240, 240, 240] },
        	columnStyles: {
            	0: { halign: 'left' },  // Nombre del Producto
            	1: { halign: 'center' }, // Cantidad
            	2: { halign: 'right' }, // Precio Unitario
            	3: { halign: 'right' }  // Precio Neto
        },
        didDrawPage: function (data) {
            addHeader(doc);
        },
        didDrawCell: function (data) {
            if (data.row.index === data.table.body.length - 1) {
                const startY = doc.lastAutoTable.finalY + 5;
                addFooter(doc, startY);
            }
        }
    	});
    	// Totales
		const startY = doc.lastAutoTable.finalY + 5;
		const netPriceX = 195; // Ajusta esta coordenada según la posición de "Precio Neto"
		doc.setFontSize(10);
		doc.text(`Subtotal 15%: $${parseFloat(subtotal15).toFixed(2)}`, netPriceX, startY, null, null, 'right');
		doc.text(`Subtotal 5%: $${parseFloat(subtotal5).toFixed(2)}`, netPriceX, startY + 5, null, null, 'right');
		doc.text(`Subtotal 0%: $${parseFloat(subtotal0).toFixed(2)}`, netPriceX, startY + 10, null, null, 'right');
		doc.text(`Total Sin Impuestos: $${parseFloat(totalSinImpuestos).toFixed(2)}`, netPriceX, startY + 15, null, null, 'right');
		doc.text(`IVA 15%: $${parseFloat(montoIVA15).toFixed(2)}`, netPriceX, startY + 20, null, null, 'right');
		doc.text(`IVA 5%: $${parseFloat(montoIVA5).toFixed(2)}`, netPriceX, startY + 25, null, null, 'right');
		doc.setFontSize(12);
		doc.text(`Total: $${parseFloat(totalConImpuestos).toFixed(2)}`, netPriceX, startY + 35, null, null, 'right');

	// Descuento
		doc.setFontSize(16);
		doc.setFont('Helvetica', 'bold');
		doc.text(`Descuento: $${parseFloat(totalDescuento).toFixed(2)}`, 15, startY + 45);

        // Leyendas
        if (corporativo !== 4) {
            doc.setFontSize(10);
            doc.setFont('Helvetica', 'bold');
            doc.text('Construimos la mejor oferta para ti,', 15, startY + 5);
            doc.text('presenta otra proforma y mejoramos nuestros precios.', 15, startY + 10);
        } else {
            doc.setFontSize(10);
            doc.setFont('Helvetica', 'bold');
            doc.text('Construimos la mejor oferta para ti.', 15, startY + 5);
        }
        
         // Texto a imprimir y posición
		
    	function addFooter(doc, startY) {
        	doc.setFont('Helvetica', 'normal');
        	doc.text('Precios y stock están sujetos a cambios sin previo aviso.', 15, doc.internal.pageSize.height - 20);
        }
        doc.save(`Proforma_${nombreCliente}_${fechaPago}.pdf`);
        document.getElementById('loading-overlay').style.display = 'none';
    };
}
XMLDocument


function printDiv(divId) {
    const divToPrint = document.getElementById(divId).innerHTML;
    const newWindow = window.open('', '', 'height=800,width=1200');

    newWindow.document.write('<html><head><title>Imprimir Factura</title>');
    newWindow.document.write('<link rel="stylesheet" href="estilos-imprimir.css" type="text/css" media="print"/>');
    newWindow.document.write('</head><body>');
    newWindow.document.write(divToPrint);
    newWindow.document.write('</body></html>');

    newWindow.document.close();
    newWindow.focus();
    newWindow.print();
    newWindow.close();
}

