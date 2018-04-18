$(document).ready(function () {
    // $('select').material_select();
    getData();

});

function getData() {
    var id_rol;
    id_rol = localStorage.getItem("id_rol");
    $("#id_rol").val(id_rol);

    $.ajax({
        url: "../controlador/SRV_HORARIOS_EDICION.php",
        data: {accion: "getData", id: id_rol},
        type: "POST",
        datatype: "text",
        success: function (info) {
            info = info.trim();
            if (info == "Fail") {
                alert("No se obtuvieron los datos");
            } else {

                var content = JSON.parse(info);

                jQuery.each(content, function (i, val) {
                    // $("#equipo").val(val.NOMBRE_EQUIPO);
                    $('#equipo1').html("<option value='" + val.NOMBRE_EQUIPO + "' >" + val.NOMBRE_EQUIPO + "</option>");
                    $('#equipo2').html("<option value='" + val.NAME + "' >" + val.NAME + "</option>");
                    $('#fecha').val(val.FECHA);
                    $('#hora').val(val.HORA);
                    $('#campo').val(val.CAMPO);
                });

            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });
}

function updateHorario() {
    var id_rol;
    id_rol = localStorage.getItem("id_rol");
    var fecha = document.getElementById("fecha").value;
    var hora = document.getElementById("hora").value;
    var campo = document.getElementById("campo").value;

    $.ajax({
        url: "../controlador/SRV_HORARIOS_EDICION.php",
        data: {accion: "update", fecha: fecha, hora: hora, campo: campo, id: id_rol},
        type: "POST",
        datatype: "text",
        success: function (info) {

            if (info == 'Fail') {
                alert("No se guardaron cambios");
            } else {

                alert("Datos Guardados");
                window.location.href = "HORARIOS_ASIGNACION.html";

            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });

}