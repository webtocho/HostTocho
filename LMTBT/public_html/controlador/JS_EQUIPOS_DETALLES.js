var id; //ID de equipo

$(document).ready(function() {
    //Desde EQUIPOS_VER se nos manda el id del equipo a consultar.
    id = sessionStorage.getItem("EQUIPOS_DETALLES");
    if(id !== null) sessionStorage.removeItem("EQUIPOS_DETALLES");
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    crearModal(false,true,false,false);
                    $("#modal-title").html("Cargando información...");
                    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});

                    if(id !== null){
                        $.post( "../controlador/SRV_EQUIPOS.php", {fn : "get", id : id, id_c : "1", nb_e : "1", nb_c : "1", lg : "1",  r_act : "1"}, null, "json")
                            .done(function(res) {
                                $("#nombre").html(res["nb_e"]);
                                $("#coach").html(res["nb_c"]);
                                document.getElementById("logotipo").src = "data:image/png;base64," + res["lg"];
                                
                                if (Object.keys(res["r_act"]).length > 0) {
                                    $("#lista_rosters").html("");
                                    $.each(res["r_act"], function (index, i) {
                                            $("#lista_rosters").append("<li><a href='javascript:irAPaginaDeDetallesDeRoster(" + i[0] + ")'>" + i[1] + "</a></li>");
                                    });
                                }
                                
                                $('#modal').modal('hide');
                            })
                            .fail(function(xhr, status, error) {
                                $("#modal-title").html("Error");
                                $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. (" + xhr.status + " " + status + ")"));
                                $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
                                $("#modal-body").append("<br><a href='EQUIPOS_VER.html'>Volver a la página de gestión de equipos</a>");
                            });
                    } else {
                        $("#modal-title").html("Error");
                        $("#modal-body").html("Es necesario que primero vaya a <a href='EQUIPOS_VER.html'>esta página</a> y seleccione un equipo.");
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
    sessionStorage.setItem("EQUIPOS_DETALLES", parseInt(id));
    location.reload();
}

function irAPaginaDeEdicion(){
    //Este método permite guardar una variable de sesión del lado del cliente.
    sessionStorage.setItem("EQUIPOS_EDICION", id);
    document.location.href = "EQUIPOS_EDICION.html";
}

function irAPaginaDeCrearRoster(){
    sessionStorage.setItem("ROSTERS_CREAR", id);
    document.location.href = "ROSTERS_CREAR.html";
}

function irAPaginaDeDetallesDeRoster(id_roster){
    sessionStorage.setItem("ROSTERS_DETALLES_id_e", id);
    sessionStorage.setItem("ROSTERS_DETALLES_id_r", id_roster);
    document.location.href = "ROSTERS_DETALLES.html";
}

// Para eliminar un equipo, puede se que el equipo este participando en un torneo actualmente, de ser asi no se podra eliminar, de lo contrario si.
function EliminarEquipo() {
    if (confirm('¿Está seguro de que desea eliminar este equipo?')) {
        $.ajax({
            url: "../controlador/SRV_EQUIPOS_ELIMINACION.php",
            data: {tipo: "eliminar_equipo",
                id_equipo: id
            },
            type: "POST",
            datatype: "text",
            beforeSend: function (xhr) {},
            success: function (respuesta) {
                if (respuesta == "ok") {
                    alert("El equipo fue eliminado con éxito.");
                    document.location.href = "EQUIPOS_VER.html";
                } else if (respuesta == "no") {
                    alert("El equipo no puede ser eliminado ya que participa en un torneo actualmente o no se encuentran registros de este equipo para eliminar.");
                    document.location.href = "EQUIPOS_VER.html";
                } else {
                    alert(respuesta);
                    document.location.href = "index.php";
                }
            },
            error: function (jqXHR, textStatus) {}
        });
    } else {
        document.location.href = "EQUIPOS_VER.html";
    }
}