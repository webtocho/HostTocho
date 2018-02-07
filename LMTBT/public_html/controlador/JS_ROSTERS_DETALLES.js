//ID del equipo
var id_e;
//ID del roster
var id_r;

$(document).ready(function() {
    id_e = sessionStorage.getItem("ROSTERS_DETALLES_id_e");
    if(id_e !== null) sessionStorage.removeItem("ROSTERS_DETALLES_id_e");
    id_r = sessionStorage.getItem("ROSTERS_DETALLES_id_r");
    if(id_r !== null) sessionStorage.removeItem("ROSTERS_DETALLES_id_r");
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    $("#modal-title").html("Cargando información del roster...");
                    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});

                    if(id_e !== null && id_r !== null){                        
                        $.post( "../controlador/SRV_ROSTERS.php", {fn : "get", id : id_r}, null, "json")
                            .done(function(res) {
                                $("#categoria").html(res["cat"]);
                                $("#nombre_equipo").html(res["eq"]);
                                $("#torneo").html((res["tor"] != null ? res["tor"] : "No está participando en ninguno"));
                                if(!res["es_ed"])
                                    $("#btn_editar").remove();
                                
                                $("#modal-title").html("Cargando lista de jugadores...");
                                $.post( "../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : res["mb"], nb_c : "1", sx : "1", ed : "1", ft : "1"}, null, "json")
                                    .done(function(res_j) {
                                        var fila, celda;
                                        $.each(res_j, function (index, i) {
                                            fila = document.getElementById("miembros").insertRow(-1);
                                            
                                            if(i !== null){
                                                //Celda de nombre
                                                fila.insertCell(-1).innerHTML = i["nb_c"];
                                                //Celda de género
                                                fila.insertCell(-1).innerHTML = (i["sx"] == "M" ? "Masculino" : (i["sx"] == 'F' ? "Femenino" : "<No definido>"));
                                                //Celda de edad
                                                fila.insertCell(-1).innerHTML = (i["ed"] != null ? i["ed"] : "<No definido>");
                                                //Celda de foto
                                                if(i["ft"] === null)
                                                    fila.insertCell(-1).innerHTML = "<img src=\"img/RC_IF_ANONIMO.png\" width='100'/>";
                                                else
                                                    fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + i["ft"] +"\" width='100'/>";
                                            } else {
                                                fila.insertCell(-1).innerHTML = "<i>&#60Eliminado&#62</i>";
                                                fila.insertCell(-1).innerHTML = fila.insertCell(-1).innerHTML = fila.insertCell(-1).innerHTML = "---";
                                            }
                                            
                                            fila.insertCell(-1).innerHTML = res["nm"][index];
                                        });

                                        $('#modal').modal('hide');
                                        $('#contenido').append(crear_btn_retorno());
                                    })
                                    .fail(function(xhr, status, error) {
                                        $("#modal-title").html("Error al cargar la lista de jugadores");
                                        $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. (" + xhr.status + " " + status + ")"));
                                        $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
                                        $("#modal-body").append("<br>" + crear_btn_retorno());
                                    });
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

function recargar(){
    sessionStorage.setItem("ROSTERS_DETALLES_id_e", id_e);
    sessionStorage.setItem("ROSTERS_DETALLES_id_r", id_r);
    location.reload();
}

function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", id_e);
    document.location.href = "EQUIPOS_DETALLES.html";
}

function irAPaginaDeEdicion(){
    sessionStorage.setItem("ROSTERS_EDICION_id_e", id_e);
    sessionStorage.setItem("ROSTERS_EDICION_id_r", id_r);
    document.location.href = "ROSTERS_EDICION.html";
}

function crear_btn_retorno(){
    return crear_dropdown("Regresar a...", [
        "<a href='javascript:irAPaginaDeDetalles();'>Detalles del equipo</a>",
        "<a href='EQUIPOS_VER.html'>Gestión de equipos</a>"]);
}

function confirmar_eliminacion(){
    $("#modal-footer").hide();
    $("#modal-title").html("Confirmación");
    $("#modal-body").html("¿Está seguro de que desea eliminar el roster? Los datos no se podrán recuperar.<br><br>");
    $("#modal-body").append("<button type='button' class='btn btn-danger' onclick='eliminar()'>Si</button>");
    $("#modal-body").append("<button type='button' class='btn btn-primary' data-dismiss='modal'>No</button>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
}

function eliminar(){
    $("#modal-title").html("Eliminando roster...");
    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
    
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