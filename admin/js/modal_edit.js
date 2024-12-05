function modalDatos() {

    //$("#alert").modal("hide")

    $("#alert").append(`
    <button class="btn btn-primary" type="button" id="buttonModal" data-bs-toggle="modal" data-bs-target="#staticBackdrop"></button>
    <div class="modal fade" id="staticBackdrop" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg mt-6" role="document">
        <div class="modal-content border-0">
        <div class="position-absolute top-0 end-0 mt-3 me-3 z-1">
            <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
            <div class="bg-light rounded-top-3 py-3 ps-4 pe-6">
                <h4 class="mb-1" id="staticBackdropLabel">Actualizar datos personales</h4>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <label class="form-label" for="username">Usuario</label>
                    <input class="form-control" id="username" type="text" placeholder="Usuario"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="username">Usuario</label>
                    <input class="form-control" id="username" type="text" placeholder="Usuario"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="username">Usuario</label>
                    <input class="form-control" id="username" type="text" placeholder="Usuario"/>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>

    `)

    $("#buttonModal").click()

}

function modalPassword() {
    $("#alert").append(`
    <button class="btn btn-primary" type="button" id="buttonModal" data-bs-toggle="modal" data-bs-target="#staticBackdrop"></button>
    <div class="modal fade" id="staticBackdrop" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog mt-6" role="document">
            <div class="modal-content border-0">
                <div class="position-absolute top-0 end-0 mt-3 me-3 z-1">
                    <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="bg-light rounded-top-3 py-3 ps-4 pe-6">
                        <h4 class="mb-1" id="staticBackdropLabel">Actualizar contraseña</h4>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label" for="password">Nueva contraseña</label>
                            <input class="form-control" id="password" type="password" placeholder="Nueva contraseña"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="repeat_password">Repetir contraseña</label>
                            <input class="form-control" id="repeat_password" type="password" placeholder="Repetir contraseña"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="updatePassword()">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    `)

    $("#buttonModal").click()
}

function updatePassword() {
    var password = $("#password").val()
    var repeat_password = $("#repeat_password").val()

    if (password == "" || repeat_password == "") {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "ABRALDES",
            text: "Campos obligatorios",
            showConfirmButton: true,
            //timer: 2000
        });
    } else if (password != repeat_password) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "ABRALDES",
            text: "Las contraseñas no coinciden",
            showConfirmButton: true,
            //timer: 2000
        });
    } else {
        $.post('../api/v1/abraldes/admin/login', {
            username: $("#username").val(),
            password: $("#password").val()
        }, function (returnedData) {
            console.log(returnedData);
            var returned = JSON.parse(returnedData)
            if (returned["error"] == false) {
                $("#formlogin").append("<input type='hidden' name='id_usuario' value='" + returned["administrador"]["id_administrador"] + "' /> <input type='hidden' name='username' value='" + returned["administrador"]["username"] + "' />");
                $("#formlogin").submit();
            } else {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "ABRALDES",
                    text: returned["msg"],
                    showConfirmButton: true,
                    //timer: 2000
                });
                // SweetAlert("error", );

                // var valid_was = document.getElementById("formlogin2");
                // valid_was.classList.add("needs-validation", "was-validated");

                /*var x = document.getElementById("invalid");  
                var y = document.getElementById("invalidPass"); 
                if (x.style.display !== "none") {
                    x.style.display = "block";
                    y.style.display = "block";
                }
                */ /*else {
                    //x.style.display = "none";
                //}*/
            }
        });
    }
}