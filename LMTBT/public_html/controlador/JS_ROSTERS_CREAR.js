// Controlador de la página para crear rosters.

// ID del equipo al que se le va a crear el roster.
var id;
// Un arreglo con los ID's de las cuentas de los jugadores que formarán parte del nuevo roster.
var miembros = [];

$(document).ready(function() {
    //Desde EQUIPOS_DETALLES se nos manda el ID del equipo.
    id = sessionStorage.getItem("ROSTERS_CREAR");
    if(id !== null) sessionStorage.removeItem("ROSTERS_CREAR");
    
    //Nos sersioramos de que un usuario del tipo adecuado esté logueado.
    $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    //Creamos un modal de Bootstrap.
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    $("#modal-title").html("Cargando información...");
                    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});
                    
                    if(id !== null){
                        //Obtenemos la información del equipo y las categorías disponibles para crearle rosters.
                        $.post( "../controlador/SRV_EQUIPOS.php", {fn : "get", id : id, nb_e : "1", cat_d : "1"}, null, "json")
                        .done(function(infoEquipo) {
                            //Mostramos el nombre del equipo.
                            $("#titulo").append(infoEquipo["nb_e"]);
                            
                            if (Object.keys(infoEquipo["cat_d"]).length > 0) {
                                //Colocamos los rosters disponibles (para crear rosters en este equipo) en un <select>, para que el usuario pueda elegir.
                                var select = document.getElementById("seleccion_categoria");
                                select.innerHTML = "";
                                $.each(infoEquipo["cat_d"], function (index, i) {
                                    var option = document.createElement("option");
                                    option.value = i[0];
                                    option.text = i[1];
                                    select.add(option);
                                });
                                
                                //Cargamos en el iFrame, la página para buscar a los jugadores.
                                var frame = document.getElementById("seleccion_jugador");
                                var onload = setInterval(function() {
                                    var frameDoc = frame.contentDocument || frame.contentWindow.document;
                                    if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                                        clearInterval(onload);
                                        //Indicamos a la página dentro del iFrame ("CUENTAS_BUSQUEDA") que sólo se van a buscar jugadores.
                                        frame.contentWindow.inicializar("JUGADOR", "Agregar", "agregarMiembro");
                                        frame.contentWindow.cambiarCategoria(document.getElementById("seleccion_categoria").value);
                                    }
                                }, 500);
                                frame.src = 'CUENTAS_BUSQUEDA.html';
                                
                                $('#modal').modal('hide');
                                $('#contenido').append(crear_btn_retorno());
                            } else {
                                $("#modal-title").html("Error");
                                $("#modal-body").html("El equipo ya tiene rosters en todas las categorías.");
                                $("#modal-body").append("<br>" + crear_btn_retorno());
                            }
                        })
                        .fail(function(xhr, status, error) {
                            $("#modal-title").html("Error");
                            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor (" + xhr.status + " " + status + ")."));
                            $("#modal-body").append("<br><a href='javascript:recargar();'>Volver a intentar</a>");
                            $("#modal-body").append("<br>" + crear_btn_retorno());
                        });
                    } else {
                        $("#modal-title").html("Error");
                        $("#modal-body").html("Es necesario que primero vaya a <a href='EQUIPOS_VER.html'>esta página</a>, seleccione un equipo y ejecute la opción de crearle un roster.");
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
 * Recarga esta página, en caso de que se de un error al cargar los datos del equipo.
 */
function recargar(){
    sessionStorage.setItem("ROSTERS_CREAR", id);
    location.reload();
}

/**
 * Redirecciona de regreso a la página para ver los detalles del equipo al que el roster pertenece, enviando el ID del mismo.
 */
function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", id);
    document.location.href = "EQUIPOS_DETALLES.html";
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
 * Cambia la categoría seleccionada y limpia la lista de miembros del roster.
 * Se ejecuta en el evento "onchange" del <select> de categorías.
 */
function elegirCategoria(){
    //Dentro del iFrame de búsqueda, cambiamos la categoría de jugadores a buscar.
    var frame = document.getElementById("seleccion_jugador");
    frame.contentWindow.cambiarCategoria(document.getElementById("seleccion_categoria").value);
    frame.contentWindow.buscar();
    
    //Limpiamos la lista de miembros, en la tabla y el arreglo.
    $("#tabla_miembros").find("tr:gt(0)").remove();
    miembros = [];
}

/**
 * Agrega un miembro al nuevo roster.
 * @param {type} id - ID de la cuenta del jugador que se incorpora a la lista.
 */
function agregarMiembro(id){
    //Solo se agrega al nuevo miembro si aún no está en la lista.
    if(miembros.indexOf(id) === -1){
        $("#modal-footer").hide();
        $("#modal-title").html("Agregando jugador...");
        $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
        $('#modal').modal({backdrop: 'static', keyboard: false});
        
        //Hacemos una petición para obtener la información de la cuenta del nuevo miembro.
        $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : id, nb_c : "1", ft : "1"}, null, "json")
            .done(function(infoMiembro) {
                //Mostramos la información del nuevo miembro en la tabla.
                var fila = document.getElementById("tabla_miembros").insertRow(-1);
                
                //Celda de nombre completo
                fila.insertCell(-1).innerHTML = infoMiembro["nb_c"];
                
                //Celda de fotografía
                if(infoMiembro["ft"] === null)
                    fila.insertCell(-1).innerHTML = "<img src=\"../modelo/img/RC_IF_ANONIMO.png\" width='100'/>";
                else
                    fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + infoMiembro["ft"] +"\" width='100'/>";
                
                //Celda de selección de número
                fila.insertCell(-1).innerHTML = "<input type='number' id='nb_" + id + "' min='0' max='99' step='1' value='0' onchange='validarNum(this)'>";
                
                //Celda de eliminación de fila.
                fila.insertCell(-1).innerHTML = "<button class='btn btn-danger' title=\"Descartar\" onclick=\"descartarMiembro(this)\">X</button>";
                
                //Metemos el ID del nuevo miembro al arreglo.
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
 * Redirecciona de regreso a la página para ver los detalles del equipo (al que se le quería crear el roster).
 */
function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", id);
    document.location.href = "EQUIPOS_DETALLES.html";
}

/**
 * Crea el equipo (guardando los datos en el servidor).
 */
function crear(){
    $("#modal-footer").hide();
    $("#modal-title").html("Creando roster...");
    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
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
    
    //Se hace la petición al servidor para crear el roster.
    $.post( "../controlador/SRV_ROSTERS.php", {fn : "crear", id_e : id, id_ct : document.getElementById("seleccion_categoria").value, mb : miembros, nm : numeros})
        .done(function(res) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("Roster creado correctamente<br>" + crear_btn_retorno());
        })
        .fail(function(xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html("Error de servidor. " + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        });
}