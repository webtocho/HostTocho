var id; //ID de equipo
var miembros = [];

$(document).ready(function() {
    id = sessionStorage.getItem("ROSTERS_CREAR");
    if(id !== null) sessionStorage.removeItem("ROSTERS_CREAR");
    
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
                    
                    if(id !== null){
                        $.post( "../controlador/SRV_EQUIPOS.php", {fn : "get", id : id, nb_e : "1", cat_d : "1"}, null, "json")
                        .done(function(res) {                                
                            $("#titulo").append(res["nb_e"]);
                            
                            if (Object.keys(res["cat_d"]).length > 0) {
                                var select = document.getElementById("seleccion_categoria");
                                select.innerHTML = "";
                                
                                $.each(res["cat_d"], function (index, i) {
                                    var option = document.createElement("option");
                                    option.value = i[0];
                                    option.text = i[1];
                                    select.add(option);
                                });
                                
                                var frame = document.getElementById("seleccion_jugador");
                                var onload = setInterval(function() {
                                    var frameDoc = frame.contentDocument || frame.contentWindow.document;
                                    if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                                        clearInterval(onload);
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

function recargar(){
    sessionStorage.setItem("ROSTERS_CREAR", id);
    location.reload();
}

function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", id);
    document.location.href = "EQUIPOS_DETALLES.html";
}

function crear_btn_retorno(){
    return crear_dropdown("Regresar a...", [
        "<a href='javascript:irAPaginaDeDetalles();'>Detalles del equipo</a>",
        "<a href='EQUIPOS_VER.html'>Gestión de equipos</a>"]);
}

function elegirCategoria(){
    var frame = document.getElementById("seleccion_jugador");
    frame.contentWindow.cambiarCategoria(document.getElementById("seleccion_categoria").value);
    frame.contentWindow.buscar();
    
    $("#tabla_miembros").find("tr:gt(0)").remove();
    miembros = [];
}

function agregarMiembro(id){
    if(miembros.indexOf(id) === -1){
        $("#modal-footer").hide();
        $("#modal-title").html("Agregando jugador...");
        $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
        $('#modal').modal({backdrop: 'static', keyboard: false});
        
        $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : id, nb_c : "1", ft : "1"}, null, "json")
            .done(function(res) {
                var fila = document.getElementById("tabla_miembros").insertRow(-1);
                
                //Celda de nombre completo
                fila.insertCell(-1).innerHTML = res["nb_c"];
                
                //Celda de fotografía
                if(res["ft"] === null)
                    fila.insertCell(-1).innerHTML = "<img src=\"img/RC_IF_ANONIMO.png\" width='100'/>";
                else
                    fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + res["ft"] +"\" width='100'/>";
                
                //Celda de selección de número
                fila.insertCell(-1).innerHTML = "<input type='number' id='nb_" + id + "' min='0' max='99' step='1' value='0' onchange='validarNum(this)'>";
                
                //Celda de eliminación de fila.
                fila.insertCell(-1).innerHTML = "<button class='btn btn-danger' title=\"Descartar\" onclick=\"descartarMiembro(this)\">X</button>";
                
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

function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", id);
    document.location.href = "EQUIPOS_DETALLES.html";
}

function crear(){
    $("#modal-footer").hide();
    $("#modal-title").html("Creando roster...");
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