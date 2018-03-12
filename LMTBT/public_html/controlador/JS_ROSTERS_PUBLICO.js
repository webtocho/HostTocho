$(document).ready(function() {
    CargarEquipos();
});
function CargarEquipos(){
    //Funcion para cargar los equipos (para mostrar al publico)
    $.ajax({
        url: "../controlador/SRV_PUBLICO_EQUIPOS_CARGAR.php",
        beforeSend: function (xhr){
                 $('#Datos').empty();
                 $('#Datos').append("<img src='./images/cargando_naranja.gif' >");
        },
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
    //Funcion para cargar los rosters de un equipo
    $.ajax({
        data: {id_equipo:id},
        url: "../controlador/SRV_PUBLICO_ROSTER_CARGAR.php",
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr){
                 $('#Datos').empty();
                 $('#Datos').append("<img src='./images/cargando_naranja.gif' >");
        },
        success: function (res) {
            html = JSON.parse(res);
            $('#Roster').empty();
            $('#logo').empty();
            $('#logo').append(html[0]);
            $('#Roster').append(html[1]);
            $('#Roster').show();
            $('#Datos').empty();
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}
function CargarDatos(id){
    //Funcion para cargar los datos de un roster
    $.ajax({
        data: {id_roster:id},
        url: "../controlador/SRV_PUBLICO_DATOS_CARGAR.php",
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr){
                 $('#Datos').empty();
                 $('#Datos').append("<img src='./images/cargando_naranja.gif' >");
        },
        success: function (res) {
            $('#Datos').empty();
            $('#Datos').append(res);
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}

