$(document).ready(function () {
    $('select').material_select();
    getTorneo();
    (document).getElementById("torneo").onchange = getTable;
});

function getTorneo() {
    $('#torneo').html("<option value='Seleccione' disabled selected>Seleccione Torneo</option>");
    $.ajax({
        url: "../controlador/SRV_HORARIOS_ASIGNACION.php",
        data: {accion: "getTorneo"},
        type: "POST",
        datatype: "text",
        success: function (info) {

            info = info.trim()
            if (info == 'Failx') {
                alert("No se encontró ningún torneo.");
            } else {
                $('#torneo').append(info);
                $('select').material_select();
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error al obtener el torneo.");
        }
    });
    $('select').material_select();
}

function getTable() {
    $('#roles').html('<thead><tr><th>Equipo</th><th>VS</th><th>Equipo</th><th>Fecha</th><th>Hora</th><th>Campo</th><th></th> </tr> </thead><tbody id="Rol"></tbody>');
    if (document.getElementById("torneo").value === "Seleccione") {
        alert("Torneo no especificado");

        return;
    }
    var id = document.getElementById("torneo").value;
    $.ajax({
        url: "../controlador/SRV_HORARIOS_ASIGNACION.php",
        data: {accion: "getTable", id: id},
        type: "POST",
        datatype: "text",
        success: function (info) {
            info = info.trim();


            if (info == 'Fail') {
                alert("No se obtuvieron registros");

            } else if (info == 'Failx') {
                alert("No hay datos en la tabla ");

            } else if (info == '!Session') {
                alert("Inicie sesión para continuar");
                window.location.href = "CUENTAS_LOGIN.html";
            } else if (info == "!Type") {
                alert("Usted no tiene permisos");
                window.location.href = "index.html";
            } else {

                $('#Rol').append(info);

            }

        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el servidor");
        }
    });

}

function editTable() {
    var i = 0;
    var id_rol;
    $('a.edit').on('click', function () {
        if (i == 0) {
            id_rol = ($(this).attr("id"));
            console.log(id_rol);
            localStorage.setItem("id_rol", id_rol);
            window.location.href = "HORARIOS_EDICION.html";
            i++;
        }

    });
}

////////////////////////////////////////////  ASIGNAR HORARIOS MEDIANTE MODAL /////////////////////////////////////////////

function OpenModal(id_rol_juego) {
    $('#Modal01').modal({
        dismissible: true, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        inDuration: 300, // Transition in duration
        outDuration: 200, // Transition out duration
        startingTop: '4%', // Starting top style attribute
        endingTop: '10%' // Ending top style attribute
    });
    $('#Modal01').modal('open');
    $("#rol").val(id_rol_juego);
}

function CloseModal() {
    $('#Modal01').modal('close');
}

function GuardarHorarioRolJuego() {
    var id = document.getElementById("rol").value;
    var fecha = document.getElementById("fecha").value;
    var hora = document.getElementById("hora").value;
    var campo = document.getElementById("campo").value;
    $.ajax({
        url: "../controlador/SRV_HORARIOS_EDICION.php",
        data: {
            accion: "guardar_Horario",
            id: id,
            fecha: fecha,
            hora: hora,
            campo: campo
        },
        type: "POST",
        datatype: "text",
        success: function (response) {
            if (response === "ok") {
                alert("Datos guardados correctamete.");
            } else {
                alert("No se pudo guardar los datos. Intente más tarde, por favor.");
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el servidor");
        }
    });

}