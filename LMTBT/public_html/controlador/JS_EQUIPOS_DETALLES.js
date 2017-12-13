var id;

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
                    if(id !== null){
                        $("#modal-title").html("Cargando información...");
                        $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'><center>");
                        $('#modal').modal({backdrop: 'static', keyboard: false});
                        
                        $.post( "../controlador/SRV_EQUIPOS.php", {fn : "get", id : id}, null, "json")
                            .done(function(res) {
                                $("#nombre").html(res["nb_e"]);
                                $("#coach").html(res["nb_c"]);
                                document.getElementById("logotipo").src = "data:image/png;base64," + res["lg"];
                                $('#modal').modal('hide');
                            })
                            .fail(function(xhr, status, error) {
                                $("#modal-title").html("Error");
                                $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. " + xhr.status + " " + status + ")"));
                                $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
                            });
                    } else {
                        $("#modal-title").html("Error");
                        $("#modal-body").html("Es necesario que primero vaya a <a href='EQUIPOS_VER.html'>esta página</a> y seleccione un equipo.");
                        $('#modal').modal({backdrop: 'static', keyboard: false});
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
/*
function irAPaginaDeCrearRoster(){
    sessionStorage.setItem("ROSTERS_CREAR_id", id);
    sessionStorage.setItem("ROSTERS_CREAR_nb", $("#nombre").html());
    document.location.href = "ROSTERS_CREAR.html";
}*/

/*
function irAPaginaDeDetallesDeRoster(id_roster){
    sessionStorage.setItem("ROSTERS_DETALLES_id_e", id);
    sessionStorage.setItem("ROSTERS_DETALLES_nb", $("#nombre").html());
    sessionStorage.setItem("ROSTERS_CREAR_id_r", id_roster);
    return true;
}*/

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