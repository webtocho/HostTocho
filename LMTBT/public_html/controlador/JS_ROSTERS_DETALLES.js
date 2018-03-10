// Controlador de la página para ver los detalles de un roster.

//ID del equipo al que pertenece el roster.
var id_e;
//ID del roster.
var id_r;

$(document).ready(function() {
    //Desde EQUIPOS_DETALLES se nos manda el ID del equipo y el ID de un roster.
    id_e = sessionStorage.getItem("ROSTERS_DETALLES_id_e");
    if(id_e !== null) sessionStorage.removeItem("ROSTERS_DETALLES_id_e");
    id_r = sessionStorage.getItem("ROSTERS_DETALLES_id_r");
    if(id_r !== null) sessionStorage.removeItem("ROSTERS_DETALLES_id_r");
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    //Creamos un modal de Bootstrap.
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    $("#modal-title").html("Cargando información del roster...");
                    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});

                    if(id_e !== null && id_r !== null){                        
                        //Hacemos una petición al servidor para obtener la información del roster.
                        $.post( "../controlador/SRV_ROSTERS.php", {fn : "get", id : id_r}, null, "json")
                            .done(function(infoRoster) {
                                //Mostramos la información del roster en pantalla.
                                $("#categoria").html(infoRoster["cat"]);
                                $("#nombre_equipo").html(infoRoster["eq"]);
                                $("#torneo").html((infoRoster["tor"] != null ? infoRoster["tor"] : "No está participando en ninguno"));
                                if(!infoRoster["es_ed"])
                                    $("#btn_editar").remove();
                                
                                $("#modal-title").html("Cargando lista de jugadores...");
                                cargarJugadores(infoRoster["mb"], infoRoster["nm"], 0);
                            })
                            .fail(function(xhr, status, error) {
                                $("#modal-title").html("Error al cargar la información del roster");
                                $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. (" + xhr.status + " " + status + ")"));
                                $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
                                $("#modal-body").append("<br>" + crear_btn_retorno());
                            });
                    } else {
                        $("#modal-title").html("Error");
                        $("#modal-body").html("Es necesario que primero vaya a <a href='EQUIPOS_VER.html'>esta página</a>, seleccione un equipo y por último, un roster.");
                    }
                    break;
                default:
                    $('#contenido').html("<div class='alert alert-danger'>\n\
                                  <strong>Error:</strong> No tiene permiso de acceder a esta página. Será redireccionado en unos segundos.\n\
                                  </div>");
                    setTimeout(function(){ expulsar(); }, 4000);
                    return;
            }
        })
        .fail(function() {
            expulsar();
        });
});

/**
 * Carga la información de los jugadores que participan en el roster.
 * @param {Array} lista Un arreglo unidimensional de los ID's de los jugadores que participantes.
 * @param {Array} numeros Un arreglo unidimensional con los números de los números de jugador (debe ser del mismo tamaño que el otro parámetro).
 * @param {Number} index_jg El índice del jugador 
 */
function cargarJugadores(lista, numeros, index_jg){
    //Esta función es recursiva, cada ejecución pertenece a un jugador. Esta es la condición de salida.
    if(index_jg === lista.length){
        $('#modal').modal('hide');
        $('#contenido').append(crear_btn_retorno());
        return;
    }
    
    //Se hace una petición para obtener la información de un jugador.
    $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : [lista[index_jg]], nb_c : "1", sx : "1", ed : "1", ft : "1"}, null, "json")
        .done(function(infoJugador) {
            //Mostramos la info del jugador en una tabla.
            var fila = document.getElementById("miembros").insertRow(-1);
            
            if(infoJugador[0] !== null){
                //Celda de nombre
                fila.insertCell(-1).innerHTML = infoJugador[0]["nb_c"];
                //Celda de género
                fila.insertCell(-1).innerHTML = (infoJugador[0]["sx"] == "M" ? "Masculino" : (infoJugador[0]["sx"] == 'F' ? "Femenino" : "<No definido>"));
                //Celda de edad
                fila.insertCell(-1).innerHTML = (infoJugador[0]["ed"] != null ? infoJugador[0]["ed"] : "<No definido>");
                //Celda de foto
                if(infoJugador[0]["ft"] === null)
                    fila.insertCell(-1).innerHTML = "<img src=\"img/RC_IF_ANONIMO.png\" width='100'/>";
                else
                    fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + infoJugador[0]["ft"] +"\" width='100'/>";
            } else {
                fila.insertCell(-1).innerHTML = "<i>&#60Eliminado&#62</i>";
                fila.insertCell(-1).innerHTML = fila.insertCell(-1).innerHTML = fila.insertCell(-1).innerHTML = "---";
            }

            fila.insertCell(-1).innerHTML = numeros[index_jg];
            
            //Se carga al siguiente jugador.
            cargarJugadores(lista, numeros, index_jg + 1);
        })
        .fail(function(xhr, status, error) {
            $("#modal-title").html("Error al cargar la lista de jugadores");
            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. (" + xhr.status + " " + status + ")"));
            $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
            $("#modal-body").append("<br>" + crear_btn_retorno());
        });
}

/**
 * Recarga esta página, en caso de que se de un error al cargar los datos del roster o de sus miembros.
 */
function recargar(){
    sessionStorage.setItem("ROSTERS_DETALLES_id_e", id_e);
    sessionStorage.setItem("ROSTERS_DETALLES_id_r", id_r);
    location.reload();
}

/**
 * Redirecciona de regreso a la página para ver los detalles del equipo al que el roster pertenece, enviando el ID del mismo.
 */
function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", id_e);
    document.location.href = "EQUIPOS_DETALLES.html";
}

/**
 * Redirecciona a la página para editar el roster, enviando los ID's del roster y el equipo.
 */
function irAPaginaDeEdicion(){
    sessionStorage.setItem("ROSTERS_EDICION_id_e", id_e);
    sessionStorage.setItem("ROSTERS_EDICION_id_r", id_r);
    document.location.href = "ROSTERS_EDICION.html";
}

/**
 * Crea y devuelve el código HTML de un botón dropdown con opciones para regresar a páginas visitadas anteriormente.
 */
function crear_btn_retorno(){
    return crear_dropdown("Regresar a...", [
        "<a href='javascript:irAPaginaDeDetalles();'>Detalles del equipo</a>",
        "<a href='EQUIPOS_VER.html'>Gestión de equipos</a>"]);
}

/**
 * Muestra un mensaje de confirmación, si el usuario no decide cancelar la operación, el roster es eliminado.
 */
function confirmar_eliminacion(){
    $("#modal-footer").hide();
    $("#modal-title").html("Confirmación");
    $("#modal-body").html("¿Está seguro de que desea eliminar el roster? Los datos no se podrán recuperar.<br><br>");
    $("#modal-body").append("<button type='button' class='btn btn-danger' onclick='eliminar()'>Si</button>");
    $("#modal-body").append("<button type='button' class='btn btn-primary' data-dismiss='modal'>No</button>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
}

/**
 * Elimina el roster después de haber aceptado el mensaje de confirmación.
 */
function eliminar(){
    $("#modal-title").html("Eliminando roster...");
    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
    
    //Hace la petición al servidor para efectuar la eliminación.
    $.post( "../controlador/SRV_ROSTERS.php", {fn : "eli", id : id_r})
        .done(function(res) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("Roster eliminado<br>" + crear_btn_retorno());
        })
        .fail(function(xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html("Error de servidor. " + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        });
}