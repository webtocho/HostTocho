$(document).ready(function() {
    CargarEquipos();
});
function CargarEquipos(){
    $.ajax({
        url: "../controlador/SRV_PUBLICO_EQUIPOS_CARGAR.php",
        success: function (res) {
            $('#Equipos').empty();
            $('#Equipos').append(res);
            $('#Roster').hide();
            $('#Datos').empty();
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}
function CargarRoster(id){
    $.ajax({
        data: {id_equipo:id},
        url: "../controlador/SRV_PUBLICO_ROSTER_CARGAR.php",
        type: "POST",
        datatype: "text",
        success: function (res) {
            $('#Roster').empty();
            $('#Roster').append(res);
            $('#Roster').show();
            $('#Datos').empty();
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}
function CargarDatos(id){
    $.ajax({
        data: {id_roster:id},
        url: "../controlador/SRV_PUBLICO_DATOS_CARGAR.php",
        type: "POST",
        datatype: "text",
        success: function (res) {
            $('#Datos').empty();
            $('#Datos').append(res);
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}

