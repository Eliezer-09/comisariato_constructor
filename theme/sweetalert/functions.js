function SweetAlert(tipo, msg, url) {
    if (url === undefined) url = 'listar.php';
    if (tipo == "success") {
        swal({
            title: "Studio Fútbol",
            text: msg,
            type: "success"
        }, function () { });
    } else if (tipo == "warning") {
        swal({
            title: "Studio Fútbol",
            text: msg,
            type: "warning"
        }, function () { });
    } else if (tipo == "error") {
        swal({
            title: "Studio Fútbol",
            text: msg,
            type: "error"
        }, function () { });
    } else if (tipo == "url_success") {
        swal({
            title: "Studio Fútbol",
            text: msg,
            type: "success",
            confirmButtonColor: "#3d3d3d",
            confirmButtonText: "Ok",
            closeOnConfirm: false
        }, function () {
            window.location.href = url;
        });
    } else if (tipo == "url_error") {
        swal({
            title: "Studio Fútbol",
            text: msg,
            type: "error",
            confirmButtonColor: "#3d3d3d",
            confirmButtonText: "Ok",
            closeOnConfirm: false
        }, function () {
            window.location.href = url;
        });
    }
}