// Controlador de la página para ver editar el roster.

//ID del equipo al que pertenece el roster.
var id_e;
//ID del roster a editar.
var id_r;
// Un arreglo con los ID's de las cuentas de los jugadores que forman parte del nuevo roster.
var miembros = [];

$(document).ready(function() {
    //Desde ROSTERS_DETALLES se nos manda el ID del equipo y el ID de un roster.
    id_e = sessionStorage.getItem("ROSTERS_EDICION_id_e");
    if(id_e !== null) sessionStorage.removeItem("ROSTERS_EDICION_id_e");
    id_r = sessionStorage.getItem("ROSTERS_EDICION_id_r");
    if(id_r !== null) sessionStorage.removeItem("ROSTERS_EDICION_id_r");
    
    $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    //Creamos un modal de Bootstrap.
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    $("#modal-title").html("Cargando información...");
                    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});
                    
                    if(id_e !== null && id_r !== null){
                        //Cargamos la información del roster.
                        $.post( "../controlador/SRV_ROSTERS.php", {fn : "get", id : id_r}, null, "json")
                            .done(function(infoRoster) {
                                //Cargamos en el iFrame, la página para buscar a los jugadores.
                                var frame = document.getElementById("seleccion_jugador");
                                var onload = setInterval(function() {
                                    var frameDoc = frame.contentDocument || frame.contentWindow.document;
                                    if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                                        clearInterval(onload);
                                        //Indicamos a la página dentro del iFrame ("CUENTAS_BUSQUEDA") que sólo se van a buscar jugadores.
                                        frame.contentWindow.inicializar("JUGADOR", "Agregar", "agregarMiembro");
                                        frame.contentWindow.cambiarCategoria(infoRoster["id_cat"]);
                                    }
                                }, 500);
                                frame.src = 'CUENTAS_BUSQUEDA.html';
                                
                                //Mostramos la información básica del roster en pantalla.
                                $("#categoria").html(infoRoster["cat"]);
                                $("#nombre_equipo").html(infoRoster["eq"]);
                                $("#torneo").html((infoRoster["tor"] != null ? infoRoster["tor"] : "No está participando en ninguno"));
                                
                                //Cargamos la información de los miembros que conforman al roster.
                                $("#modal-title").html("Cargando lista de jugadores...");
                                cargarJugadores(infoRoster["mb"], infoRoster["nm"], 0);
                            })
                            .fail(function(xhr, status, error) {
                                $("#modal-title").html("Error al cargar la información del roster");
                                $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. (" + xhr.status + " " + status + ")"));
                                $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
                                $("#modal-body").append("<br>" + crear_btn_retorno());
                                $("#modal-footer").show();
                            });
                    } else {
                        $("#modal-title").html("Error");
                        $("#modal-body").html("Es necesario que primero vaya a <a href='EQUIPOS_VER.html'>esta página</a>, seleccione un equipo y por último, un roster.");
                        $("#modal-footer").show();
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
 * Carga la información de los jugadores que participan en el roster (antes de hacer las modificaciones).
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
    $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : [lista[index_jg]], nb_c : "1", ft : "1"}, null, "json")
        .done(function(infoJugador) {
            if(infoJugador[0] !== null){
                //Agregamos a la tabla, una fila para un jugador.
                agregarFilaMiembro(lista[index_jg], infoJugador[0], numeros[index_jg]);
                //Agregamos el ID del jugador al arreglo de jugadores.
                miembros.push(lista[index_jg]);
            } else {
                //Si la cuenta de un jugador ya no existe, se muestra una fila indicando este hecho.
                var fila = document.getElementById("tabla_miembros").insertRow(-1);
                fila.insertCell(-1).innerHTML = "<i>&#60Eliminado&#62</i>";
                fila.insertCell(-1).innerHTML = " --- ";
                fila.insertCell(-1).innerHTML = "<input type='number' min='0' max='99' step='1' value='" + numeros[index_jg] + "' onchange='validarNum(this)' disabled>";
                fila.insertCell(-1).innerHTML = "<i>Se descartará automáticamente<br>al guardar.</i>";
            }
            
            //Se carga al siguiente jugador.
            cargarJugadores(lista, numeros, index_jg + 1);
        })
        .fail(function(xhr, status, error) {
            $("#modal-title").html("Error al cargar la lista de jugadores");
            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. (" + xhr.status + " " + status + ")"));
            $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
            $("#modal-body").append("<br>" + crear_btn_retorno());
            $("#modal-footer").show();
        });
}

/**
 * Crea y devuelve el código HTML de un botón dropdown con opciones para regresar a páginas visitadas anteriormente.
 */
function crear_btn_retorno(){
    return crear_dropdown("Regresar a...", ["<a href='javascript:irAPaginaDeDetalles();'>Detalles del roster</a>"]);
}

/**
 * Recarga esta página, en caso de que se de un error al cargar los datos del roster o de sus miembros.
 */
function recargar(){
    sessionStorage.setItem("ROSTERS_EDICION_id_e", id_e);
    sessionStorage.setItem("ROSTERS_EDICION_id_r", id_r);
    location.reload();
}

/**
 * Redirecciona de regreso a la página para ver los detalles del roster, eviandos los ID's del mismo y del equipo al que pertenece.
 */
function irAPaginaDeDetalles(){
    sessionStorage.setItem("ROSTERS_DETALLES_id_e", id_e);
    sessionStorage.setItem("ROSTERS_DETALLES_id_r", id_r);
    document.location.href = "ROSTERS_DETALLES.html";
}

/**
 * Agrega en la tabla de miembros, los datos correspondiente a un jugador.
 * @param {int} id -  El ID de la cuenta miembro a agregar.
 * @param {Object} info - Un objeto con los datos del jugador a agregar.
 * @param {int} num (Opcional) - Mándelo si el jugador está en el roster antes de efectuar los cambios.
 */
function agregarFilaMiembro(id, info, num = 0){
    var fila = document.getElementById("tabla_miembros").insertRow(-1);    
    //Celda de nombre completo
    fila.insertCell(-1).innerHTML = info["nb_c"];
    //Celda de fotografía
    if(info["ft"] === null)
        fila.insertCell(-1).innerHTML = "<img src=\"../modelo/RC_IF_ANONIMO.png\" width='100'/>";
    else
        fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + info["ft"] +"\" width='100'/>";
    //Celda de selección de número
    fila.insertCell(-1).innerHTML = "<input type='number' id='nb_" + id + "' min='0' max='99' step='1' value='" + num + "' onchange='validarNum(this)'>";
    //Celda de eliminación de fila.
    fila.insertCell(-1).innerHTML = "<button class='btn btn-danger' title=\"Descartar\" onclick=\"descartarMiembro(this)\">X</button>";
}

/**
 * Agrega un nuevo miembro a la lista.
 * Esta función se manda a llamar desde el iFrame de búsqueda de jugadores.
 * @param {int} id - ID de la cuenta del jugador que se va a agregar.
 */
function agregarMiembro(id){
    if(miembros.indexOf(id) === -1){
        $("#modal-footer").hide();
        $("#modal-title").html("Agregando jugador...");
        $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
        $('#modal').modal({backdrop: 'static', keyboard: false});
        
        //Hacemos una petición para obtener los datos del nuevo jugador.
        $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : id, nb_c : "1", ft : "1"}, null, "json")
            .done(function(infoJugador) {
                //Agregamos al nuevo miembro en la tabla.
                agregarFilaMiembro(id, infoJugador);
                //Agregamos el ID del nuevo miembro al arreglo.
                miembros.push(id);
                $('#modal').modal('hide');
            })
            .fail(function(xhr, status) {
                $("#modal-title").html("Error");
                $("#modal-body").html("No se pudo agregar al jugador. ");
                $("#modal-body").append((xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")."));
                $("#modal-footer").show();
            });
    }
}

/**
 * Descarta uno de los miembros de la lista actual.
 * @param {<button>} boton El botón para descartar el miembro, ubicado en su file correspondiente.
 */
function descartarMiembro(boton){
    var index = $(boton).closest('td').parent()[0].sectionRowIndex;
    
    miembros.splice(index - 1, 1);
    document.getElementById("tabla_miembros").deleteRow(index);
}

/**
 * Se guardan los cambios.
 */
function guardar(){
    $("#modal-footer").hide();
    $("#modal-title").html("Aplicando cambios...");
    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    //Comprobamos que se tenga la cantidad mínima de miembros.
    if(miembros.length < 5){
        $("#modal-title").html("Error");
        $("#modal-body").html("El roster debe tener 5 miembros como mínimo.");
        $("#modal-footer").show();
        return;
    }
    
    //Cada miembro tiene un número de jugador. Se leen dichos números y se meten a un arreglo.
    var numeros = [];
    miembros.forEach(function (item, index) {
        if(document.getElementById("nb_" + item) != null)
            numeros.push(document.getElementById("nb_" + item).value);
    });
    
    //Comprobamos que no hayan miembros ni números duplicados en los arreglos.
    if(array_tiene_duplicados(numeros)){
        $("#modal-title").html("Error");
        $("#modal-body").html("Hay jugadores con números duplicados.");
        $("#modal-footer").show();
        return;
    }
    if(array_tiene_duplicados(miembros)){
        $("#modal-title").html("Error");
        $("#modal-body").html("Inconsistencia de datos.");
        $("#modal-footer").show();
        return;
    }
    
    //Se hace la petición al servidor para guardar los cambios.
    $.post( "../controlador/SRV_ROSTERS.php", {fn : "mod", id_r : id_r, mb : miembros, nm : numeros})
        .done(function(res) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("Roster modificado correctamente<br><a href='EQUIPOS_VER.html'>Volver a la página de gestión de equipos</a>" +
                                  "<br>" + crear_btn_retorno());
        })
        .fail(function(xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html("Error de servidor. " + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        });
}