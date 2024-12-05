<?php

include 'includes/header2.php';

?>
<div class="d-flex justify-content-center align-items-center">
    <div class="row g-3 w-75">
        <div class="col-xl-4 order-xl-1">
            <div class="card">
                <div class="card-header bg-light btn-reveal-trigger d-flex flex-between-center">
                    <h5 class="mb-0">Tienda</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless fs--1 mb-0">
                        <tbody>
                            <tr class="border-bottom">
                                <th class="ps-0 pt-0"><span class="fw-bold">Nombre de Empresa: </span> <span>Comisariato Del Constructor </span>
                                    <div class="text-700 fw-normal fs--1"><span class="fw-bold">Sucursal: </span>Comisariato del Constructor S.A.</div>
                                    <div class="text-700 fw-normal fs--1"><span class="fw-bold">Tienda: </span><?php echo $nomtienda;?></div>
                                </th>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row mt-2 mb-2">
                        <div class="col-lg-12 bg-light p-2">
                            <h5 class="fw-semibold">Seleccionar Método de Pago</h5>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-check">
                                <input class="form-check-input listMetodosPagos" id="flexRadioDefault1" type="radio" name="flexRadioDefault" name="listMetodosPagos" checked="" value="EF" />
                                <label class="form-check-label" for="flexRadioDefault1">Efectivo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input listMetodosPagos" id="flexRadioDefault2" type="radio" name="flexRadioDefault" name="listMetodosPagos" value="TR" />
                                <label class="form-check-label" for="flexRadioDefault2">Transferencia</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input listMetodosPagos" id="flexRadioDefault3" type="radio" name="flexRadioDefault" name="listMetodosPagos" value="NO" />
                                <label class="form-check-label" for="flexRadioDefault3">Crédito</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input listMetodosPagos" id="flexRadioDefault6" type="radio" name="flexRadioDefault" name="listMetodosPagos" value="CH" />
                                <label class="form-check-label" for="flexRadioDefault6">Cheque</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input listMetodosPagos" id="flexRadioDefault7" type="radio" name="flexRadioDefault" name="listMetodosPagos" value="TD" />
                                <label class="form-check-label" for="flexRadioDefault7">Tarjeta de débito</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input listMetodosPagos" id="flexRadioDefault8" type="radio" name="flexRadioDefault" name="listMetodosPagos" value="TC" />
                                <label class="form-check-label" for="flexRadioDefault8">Tarjeta de crédito</label>
                            </div>

                        </div>
                    </div>

                    <div class="d-none">
                        <div class="row">
                            <div class="col-lg-12 bg-light p-2">
                                <h5 class="fw-semibold">Seleccionar Porcentaje de IVA</h5>
                            </div>
                            <div class="col-lg-12 d-flex mt-2">
                                <div class="form-check ms-2 me-2">
                                    <input class="form-check-input listIva" id="flexRadioIva1" type="radio" name="flexRadioIva" name="listIva" value="5" />
                                    <label class="form-check-label" for="flexRadioIva1">5% IVA</label>
                                </div>
                                <div class="form-check ms-2 me-2">
                                    <input class="form-check-input listIva" id="flexRadioIva2" type="radio" name="flexRadioIva" name="listIva" value="15" checked="" />
                                    <label class="form-check-label" for="flexRadioIva2">15% IVA</label>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between text-white" style="background: #e74011; color: #FFF;">
                    <div class="fw-bold">Pago Total</div>
                    <div class="fw-bold" id="montoTotal2"></div>
                </div>
                
                <div class="card-footer d-flex justify-content-end bg-light">
                    <button class="btn btn-lg me-2" type="button" onclick="cancelarProductos()" id="btnCotizarProductos" style="background: #e74011; color: #FFF"><i class="fas fa-times"></i> Cancelar</button>
                    <button class="btn btn-lg" type="button" onclick="cotizarProductos()" id="btnCotizarProductos" style="background: #0f3d53; color: #FFF"><i class="far fa-money-bill-alt"></i> Generar cotización</button>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <div class="row flex-between-center">
                        <div class="row flex-between-center">
                            <div class="col-sm-auto">
                                <h5 class="mb-2 mb-sm-0">Información del Cliente</h5>
                            </div>
                            <div class="col-sm-auto"><a class="btn btn-falcon-default btn-sm" onclick="modalCliente()"><i class="fas fa-plus"></i> Nuevo Cliente </a></div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6 mb-4">
                            <div class="search-box">
                                <div class="col-auto d-none d-lg-block">
                                    <small class="fw-semi-bold">Cliente:</small>
                                </div>
                                <div class="position-relative">
                                    <select class="form-select" id="searchCliente">
                                        <option value="" disabled selected>Seleccione un cliente</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <h6 id="nombre_cliente"><i class="far fa-user"></i> No tiene cliente seleccionado.</h6>
                        </div>
                        <div class="col-md-6 mb-2">
                            <h6 id="ruc"><i class="far fa-address-card"></i> No tiene cliente seleccionado.</h6>
                        </div>
                        <div class="col-md-6 mb-2">
                            <h6 id="correo"><i class="far fa-envelope"></i> No tiene cliente seleccionado.</h6>
                        </div>
                        <div class="col-md-6 mb-2">
                            <h6 id="direccion"><i class="fas fa-map-marker-alt"></i> No tiene cliente seleccionado.</h6>
                        </div>
                        <div class="col-md-6 mb-2">
                            <h6 id="telefono"><i class="fas fa-phone-alt"></i> No tiene cliente seleccionado.</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-1 px-1">
                    <h5 class="mb-0">Lista de Productos</h5>

                </div>
                <div class="card-body p-0">
                    <div class="row gx-card mx-0 text-center bg-200 text-900 fs--1 fw-semi-bold">
                        <div class="col-6 col-md-6 py-2">Nombre del Producto</div>
                        <div class="col-6 col-md-6">
                            <div class="row">
                                <div class="col-md-4 py-2 d-none d-md-block text-center">Cantidad</div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center">Precio Unitario</div>
                                <div class="col-12 col-md-4 text-end py-2">Precio Neto</div>
                            </div>
                        </div>
                    </div>
                    <div id="ListaProductosS">

                    </div>
                    <div class="row fw-bold gx-card mx-0">
                        <div class="col-6 col-md-6 py-2 text-end text-900"></div>
                        <div class="col-6 px-0">
                            <div class="row gx-card mx-0  border-bottom">
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="totalProductos">0 (items)</div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="textoIVA">Sub. (15%)</div>
                                <div class="col-12 col-md-4 text-end py-2" id="subtotal15">$0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="row fw-bold gx-card mx-0">
                        <div class="col-6 col-md-6 py-2 text-end text-900"></div>
                        <div class="col-6 px-0">
                            <div class="row gx-card mx-0  border-bottom">
                                <div class="col-md-4 py-2 d-none d-md-block text-center"></div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="textoIVA">Sub. (5%)</div>
                                <div class="col-12 col-md-4 text-end py-2" id="subtotal5">$0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="row fw-bold gx-card mx-0">
                        <div class="col-6 col-md-6 py-2 text-end text-900"></div>
                        <div class="col-6 px-0">
                            <div class="row gx-card mx-0  border-bottom">
                                <div class="col-md-4 py-2 d-none d-md-block text-center"></div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="textoIVA">Sub. (0%)</div>
                                <div class="col-12 col-md-4 text-end py-2" id="subtotal0">$0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="row fw-bold gx-card mx-0">
                        <div class="col-6 col-md-6 py-2 text-end text-900"></div>
                        <div class="col-6 px-0">
                            <div class="row gx-card mx-0  border-bottom">
                                <div class="col-md-4 py-2 d-none d-md-block text-center"></div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="textoIVA">Sin Imp.</div>
                                <div class="col-12 col-md-4 text-end py-2" id="totalSinImpuestos">$0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="row fw-bold gx-card mx-0">
                        <div class="col-6 col-md-6 py-2 text-end text-900"></div>
                        <div class="col-6 px-0">
                            <div class="row gx-card mx-0  border-bottom">
                                <div class="col-md-4 py-2 d-none d-md-block text-center"></div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="textoIVA">IVA (15%)</div>
                                <div class="col-12 col-md-4 text-end py-2" id="montoIVA15">$0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="row fw-bold gx-card mx-0">
                        <div class="col-6 col-md-6 py-2 text-end text-900"></div>
                        <div class="col-6 px-0">
                            <div class="row gx-card mx-0  border-bottom">
                                <div class="col-md-4 py-2 d-none d-md-block text-center"></div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="textoIVA">IVA (5%)</div>
                                <div class="col-12 col-md-4 text-end py-2" id="montoIVA5">$0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="row fw-bold gx-card mx-0">
                        <div class="col-6 col-md-6 py-2 text-end text-900"></div>
                        <div class="col-6 px-0">
                            <div class="row gx-card mx-0  border-bottom">
                                <div class="col-md-4 py-2 d-none d-md-block text-center"></div>
                                <div class="col-md-4 py-2 d-none d-md-block text-center" id="total">Total</div>
                                <div class="col-12 col-md-4 text-end py-2" id="montoTotal">$0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php

include 'includes/footer2.php';

?>


<script src="js/detalle_cotizar.js"></script>