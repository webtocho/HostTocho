// Controlador de la página para ver los detalles de un equipo.

// ID del equipo cuyos detalles se consultan.
var id;

$(document).ready(function() {
    //Desde EQUIPOS_VER se nos manda el id del equipo a consultar.
    id = sessionStorage.getItem("EQUIPOS_DETALLES");
    if(id !== null) sessionStorage.removeItem("EQUIPOS_DETALLES");
    
    $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    crearModal(false,true,false,false);
                    $("#modal-title").html("Cargando información...");
                    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});
                    
                    if(id !== null){
                        //Se hace la petición al servidor para obtener la información del equipo.
                        $.post( "../controlador/SRV_EQUIPOS.php", {fn : "get", id : id, id_c : "1", nb_e : "1", nb_c : "1", lg : "1",  r_act : "1"}, null, "json")
                            .done(function(infoEquipo) {
                                $("#nombre").html(infoEquipo["nb_e"]);
                                $("#coach").html(infoEquipo["nb_c"]);
                                document.getElementById("logotipo").src = "data:image/png;base64," + infoEquipo["lg"];
                                
                                if (Object.keys(infoEquipo["r_act"]).length > 0) {
                                    $("#lista_rosters").html("");
                                    $.each(infoEquipo["r_act"], function (index, i) {
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

/**
 * Recarga la página, en caso de que se de un error al cargar los datos del equipo.
 */
function recargar(){
    sessionStorage.setItem("EQUIPOS_DETALLES", parseInt(id));
    location.reload();
}

/**
 * Redirecciona a la página para editar el equipo, enviándole su ID.
 */
function irAPaginaDeEdicion(){
    //Este método permite guardar una variable de sesión del lado del cliente.
    sessionStorage.setItem("EQUIPOS_EDICION", id);
    document.location.href = "EQUIPOS_EDICION.html";
}

/**
 * Redirecciona a la página para crearle un roster al equipo, enviándole su ID.
 */
function irAPaginaDeCrearRoster(){
    sessionStorage.setItem("ROSTERS_CREAR", id);
    document.location.href = "ROSTERS_CREAR.html";
}

/**
 * Redirecciona a la página para ver la información de un roster del equipo, enviándo los ID's del equipo y del roster.
 * @param {type} id_roster - ID del roster cuyos detalles se quieren ver.
 */
function irAPaginaDeDetallesDeRoster(id_roster){
    sessionStorage.setItem("ROSTERS_DETALLES_id_e", id);
    sessionStorage.setItem("ROSTERS_DETALLES_id_r", id_roster);
    document.location.href = "ROSTERS_DETALLES.html";
}

// Al eliminar un equipo, puede ser que este esté participando en un torneo actualmente, de ser asi no se podra eliminar, de lo contrario si.
function EliminarEquipo() {
	// al seleccionar el boton de eliminar equipo, se mostrara una ventana de confirmación para el usuario
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
				// se notifica al usuario si el equipo pudo ser eliminado o no y se redirecciona a la página  correspondiente
				if (respuesta == "ok") {
					alert("El equipo fue eliminado con éxito.");
					document.location.href = "EQUIPOS_VER.html";
				} else if (respuesta == "no") {
					alert("El equipo no puede ser eliminado ya que participa en un torneo actualmente");
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