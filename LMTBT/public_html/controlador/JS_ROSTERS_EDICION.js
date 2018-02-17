//ID del equipo
var id_e;
//ID del roster
var id_r;
//Miembros del roster
var miembros = [];

$(document).ready(function() {
    id_e = sessionStorage.getItem("ROSTERS_EDICION_id_e");
    if(id_e !== null) sessionStorage.removeItem("ROSTERS_EDICION_id_e");
    id_r = sessionStorage.getItem("ROSTERS_EDICION_id_r");
    if(id_r !== null) sessionStorage.removeItem("ROSTERS_EDICION_id_r");
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    $("#modal-title").html("Cargando información...");
                    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});
                    
                    if(id_e !== null && id_r !== null){
                        $.post( "../controlador/SRV_ROSTERS.php", {fn : "get", id : id_r}, null, "json")
                            .done(function(res) {
                                var frame = document.getElementById("seleccion_jugador");
                                var onload = setInterval(function() {
                                    var frameDoc = frame.contentDocument || frame.contentWindow.document;
                                    if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                                        clearInterval(onload);
                                        frame.contentWindow.inicializar("JUGADOR", "Agregar", "agregarMiembro");
                                        frame.contentWindow.cambiarCategoria(res["id_cat"]);
                                    }
                                }, 500);
                                frame.src = 'CUENTAS_BUSQUEDA.html';
                                
                                $("#categoria").html(res["cat"]);
                                $("#nombre_equipo").html(res["eq"]);
                                $("#torneo").html((res["tor"] != null ? res["tor"] : "No está participando en ninguno"));
                                
                                $("#modal-title").html("Cargando lista de jugadores...");
                                cargarJugadores(res["mb"], res["nm"], 0);
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
 * Carga la información de los jugadores que participan en el roster.
 * @param {Array} lista Un arreglo unidimensional de los ID's de los jugadores que participantes.
 * @param {Array} numeros Un arreglo unidimensional con los números de los números de jugador (debe ser del mismo tamaño que el otro parámetro).
 * @param {Number} index_jg El índice del jugador 
 */
function cargarJugadores(lista, numeros, index_jg){
    if(index_jg === lista.length){
        $('#modal').modal('hide');
        $('#contenido').append(crear_btn_retorno());
        return;
    }
    
    $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : [lista[index_jg]], nb_c : "1", ft : "1"}, null, "json")
        .done(function(res) {
            if(res[0] !== null){
                agregarFilaMiembro(lista[index_jg], res[0], numeros[index_jg]);
                miembros.push(lista[index_jg]);
            } else {
                var fila = document.getElementById("tabla_miembros").insertRow(-1);
                fila.insertCell(-1).innerHTML = "<i>&#60Eliminado&#62</i>";
                fila.insertCell(-1).innerHTML = " --- ";
                fila.insertCell(-1).innerHTML = "<input type='number' min='0' max='99' step='1' value='" + numeros[index_jg] + "' onchange='validarNum(this)' disabled>";
                fila.insertCell(-1).innerHTML = "<i>Se descartará automáticamente<br>al guardar.</i>";
            }
            
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

function crear_btn_retorno(){
    return crear_dropdown("Regresar a...", ["<a href='javascript:irAPaginaDeDetalles();'>Detalles del roster</a>"]);
}

function recargar(){
    sessionStorage.setItem("ROSTERS_EDICION_id_e", id_e);
    sessionStorage.setItem("ROSTERS_EDICION_id_r", id_r);
    location.reload();
}

function irAPaginaDeDetalles(){
    sessionStorage.setItem("ROSTERS_DETALLES_id_e", id_e);
    sessionStorage.setItem("ROSTERS_DETALLES_id_r", id_r);
    document.location.href = "ROSTERS_DETALLES.html";
}

function agregarFilaMiembro(id, info, num = 0){
    var fila = document.getElementById("tabla_miembros").insertRow(-1);    
    //Celda de nombre completo
    fila.insertCell(-1).innerHTML = info["nb_c"];
    //Celda de fotografía
    if(info["ft"] === null)
        fila.insertCell(-1).innerHTML = "<img src=\"img/RC_IF_ANONIMO.png\" width='100'/>";
    else
        fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + info["ft"] +"\" width='100'/>";
    //Celda de selección de número
    fila.insertCell(-1).innerHTML = "<input type='number' id='nb_" + id + "' min='0' max='99' step='1' value='" + num + "' onchange='validarNum(this)'>";
    //Celda de eliminación de fila.
    fila.insertCell(-1).innerHTML = "<button class='btn btn-danger' title=\"Descartar\" onclick=\"descartarMiembro(this)\">X</button>";
}

function agregarMiembro(id){
    if(miembros.indexOf(id) === -1){
        $("#modal-footer").hide();
        $("#modal-title").html("Agregando jugador...");
        $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
        $('#modal').modal({backdrop: 'static', keyboard: false});
        
        $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : id, nb_c : "1", ft : "1"}, null, "json")
            .done(function(res) {
                agregarFilaMiembro(id, res);
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

function descartarMiembro(boton){
    var index = $(boton).closest('td').parent()[0].sectionRowIndex;
    
    miembros.splice(index - 1, 1);
    document.getElementById("tabla_miembros").deleteRow(index);
}

function guardar(){
    $("#modal-footer").hide();
    $("#modal-title").html("Aplicando cambios...");
    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    if(miembros.length < 5){
        $("#modal-title").html("Error");
        $("#modal-body").html("El roster debe tener 5 miembros como mínimo.");
        $("#modal-footer").show();
        return;
    }
    
    var numeros = [];
    miembros.forEach(function (item, index) {
        if(document.getElementById("nb_" + item) != null)
            numeros.push(document.getElementById("nb_" + item).value);
    });
    
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