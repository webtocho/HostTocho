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
    
    $("#panel_permisos").hide();
    
    $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(tipoUsuario) {
            tipoUsuario = parseInt(tipoUsuario);
            switch(tipoUsuario){
                case 0:
                case 1:
                    //Creamos un modal de Bootstrap.
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    $("#modal-title").html("Cargando información del roster...");
                    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});

                    if(id_e !== null && id_r !== null){                        
                        //Hacemos una petición al servidor para obtener la información del roster.
                        $.post( "../controlador/SRV_ROSTERS.php", {fn : "get", id : id_r}, null, "json")
                            .done(function(infoRoster) {
                                //Mostramos la información del roster en pantalla.
                                $("#categoria").html(infoRoster["cat"]);
                                $("#nombre_equipo").html(infoRoster["eq"]);
                                $("#torneo").html((infoRoster["tor"] != null ? infoRoster["tor"] : "No está participando en ninguno"));
                                
                                //Vemos si el roster puede ser editado, y se modifica la interfaz de acuerdo a ello.
                                switch(infoRoster["es_ed"]){
                                    case false:
                                        // No puede ser editado, pero como el torneo sigue activo, el administrador puede dar un permiso especial para poder hacerlo.
                                        if(tipoUsuario == 0){
                                            // Si el usuario que abrió la página, es administrador, mostramos los controles para dar permisos.
                                            $("#panel_permisos").show();
                                            $("#panel_permiso_activo").hide();
                                            $('#envoltura_btn_editar').tooltip({title: "De momento, el coach no puede realizar esta acción."});
                                        } else {
                                            $("#btn_editar").prop('disabled', true);
                                            $('#envoltura_btn_editar').tooltip({title: "Necesita solicitar al director de la liga, permiso para realizar esta acción."});
                                        }
                                        break;
                                    case null:
                                        // No puede ser editado, porque el torneo en el que participa ya ha terminado.
                                        $("#btn_editar").prop('disabled', true);
                                        $('#envoltura_btn_editar').tooltip({title: "El torneo en el que este roster participa ha terminado."}); 
                                        break;
                                    case true:
                                        /* Puede ser editado, porque no está inscrito en un torneo o porque no ha pasado el límite para poder editar
                                            (una semana antes del inicio del torneo).*/
                                        $('#envoltura_btn_editar').tooltip({title: (infoRoster["tor"] != null ? "Recuerde que " + (tipoUsuario == 0 ? "el coach no podrá": "no podrá") +
                                                    " editar luego de que se cumpla una semana antes del inicio del torneo." : "Este roster no se ha inscrito en un torneo.")}); 
                                        break;
                                    default:
                                        /* Se puede editar (en condiciones normales no se podría) porque el administrador ha dado un permiso especial
                                           (hasta cierta fecha). En este caso, mandamos la fecha límite. */
                                        //Se obtiene la fecha límite especial para que el coach edite el roster en el formato "día del mes" (por ejemplo: "1 de enero").
                                        var fecha_lim = new Date((infoRoster["es_ed"]).replace(/-/g, '\/'));
                                        fecha_lim = ((new Date( fecha_lim.getTime() + Math.abs(fecha_lim.getTimezoneOffset()*60000) )).toLocaleDateString("es-MX", { month: 'long', day: 'numeric' }));
                                        
                                        if(tipoUsuario == 0){
                                            //Si el usuario que abrió la página, es administrador, mostramos los controles para revocar el permiso.
                                            $("#panel_permisos").show();
                                            $("#panel_permiso_inactivo").hide();
                                            $("#fecha_limite_edicion").html(fecha_lim);
                                        } else {
                                            $('#envoltura_btn_editar').tooltip({title: "Tiene permiso hasta el " + fecha_lim + " para realizar esta acción."});
                                        }
                                }
                                
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
                    fila.insertCell(-1).innerHTML = "<img src=\"../modelo/RC_IF_ANONIMO.png\" width='100'/>";
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
    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
    
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

/**
 * En el caso de que el roster esté inscrito en un torneo activo, no puede ser editado por el coach,
 * a menos de que el director de la liga (administrador) le de un permiso especial.
 * Esta función concede dicho permiso.
 */
function darPermiso(){
    $("#modal-footer").hide();
    $("#modal-title").html("Concediendo permiso...");
    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    $.post( "../controlador/SRV_ROSTERS.php", {fn : "per", id : id_r, tmp : document.getElementById("seleccion_permiso").value})
        .done(function(res) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("El coach podrá editar el equipo durante los días que has seleccionado.");
            $("#modal-footer").show();
            
            // Mostramos los controles para revocar el permiso y la fecha límite del mismo.
            $("#panel_permiso_inactivo").hide();
            $("#panel_permiso_activo").show();
            var fecha_lim = new Date(res.replace(/-/g, '\/'));
            fecha_lim = ((new Date( fecha_lim.getTime() + Math.abs(fecha_lim.getTimezoneOffset()*60000) )).toLocaleDateString("es-MX", { month: 'long', day: 'numeric' }));
            $("#fecha_limite_edicion").html(fecha_lim);
            
            //Actualizamos el tooltip del botón para editar.
            $("#envoltura_btn_editar").attr("data-original-title", "El coach tiene permiso hasta el " + fecha_lim + " para realizar esta acción.");
        })
        .fail(function(xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html("Error de servidor. " + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        });
}

/**
 * Revoca el permiso especial que el director de la liga (administrador) ha concedido a un coach,
 * para que edite el roster, aunque el toneo esté activo (se haya pasado el límite de una semana antes de su inicio).
 */
function revocarPermiso(){
    $("#modal-footer").hide();
    $("#modal-title").html("Revocando permiso...");
    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    $.post( "../controlador/SRV_ROSTERS.php", {fn : "per", id : id_r})
        .done(function(res) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("El coach ya no podrá editar el equipo, a menos que conceda otro permiso.");
            $("#modal-footer").show();
            
            //Mostramos los controles para dar un nuevo permiso.
            $("#panel_permiso_activo").hide();
            $("#panel_permiso_inactivo").show();
            //Actualizamos el tooltip del botón para editar.
            $("#envoltura_btn_editar").attr("data-original-title", "De momento, el coach no puede realizar esta acción.");
        })
        .fail(function(xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html("Error de servidor. " + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        });
}