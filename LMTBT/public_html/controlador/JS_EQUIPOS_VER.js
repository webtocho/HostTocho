// Controlador de la página para buscar equipos.

$(document).ready(function() {
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    //Se realiza una primera búsqueda automáticamente.
                    buscar();
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
 * Efectúa una búsqueda de equipos, de acuerdo a los filtros (del nombre) que el usuario haya puesto.
 */
function buscar(){
    //Eliminamos los resultados de la búsqueda anterior.
    var div_equipos = document.getElementById("equipos");
    while (div_equipos.childNodes.length > 2) {
        div_equipos.removeChild(div_equipos.lastChild);
    }
    
    /* Esta variable guarda el código HTML del espacio que muestra los resultados; ya que dicho espacio
       se va a reemplazar temporalmente con un GIF con la animación de "Cargando..." */
    var espacioResultados = $("#equipos").html();
    
    //Desabilitamos todos los botones mientras se hace la petición al servidor.
    $("#filtros :input").prop("disabled", true);
    $("#equipos").html("<img src='img/RC_IF_CARGANDO.gif'>");
    
    //Primero obtenemos cuántos resultados en total arroja la búsqueda.
    $.post( "../controlador/SRV_EQUIPOS.php", {fn : "num", sr: document.getElementById("sr").value}, null, "text")
        .done(function(cantidadTotalDeResultados) {
            //Preparamos la paginación (con el plugin pagination.js), para que el usuario vea los resultados de la búsqueda por página.
            //Cada vez que el usuario cambie de página, se hace una petición al servidor.
            $('#paginas').pagination({
                dataSource: '../controlador/SRV_EQUIPOS.php',
                locator: 'items',
                totalNumber: parseInt(cantidadTotalDeResultados),
                pageSize: document.getElementById("rp").value,
                ajax: {
                    type: "POST",
                    data: {fn: 'bus', sr: document.getElementById("sr").value},
                    beforeSend: function() {
                        $("#filtros :input").prop("disabled", true);
                        $("#equipos").html("<img src='img/RC_IF_CARGANDO.gif'>");
                    }
                },
                formatAjaxError: function (jqXHR, textStatus, errorThrown) {
                    $("#equipos").html(espacioResultados);
                    $("#equipos").append("<div style='display: inline-block;'>Intente de nuevo. " +
                            (jqXHR.status == 500 ? jqXHR.responseText : "(" + jqXHR.status + " " + textStatus + ")") +  "</div>");
                    $("#filtros :input").prop("disabled", false);
                    return;
                },
                callback: function(data, pagination) {
                    //Mostramos los resultados de una página.
                    $("#equipos").html(espacioResultados);
                    $.each(data , function( index, i ) {
                        // En la posición 0 viene el ID; en la 1, el nombre; y en la 2, el logotipo. 
                        $("#equipos").append("<div class='equipo'>" 
                                    + "<img class='img-equipo' src=\"data:image/png;base64," + i[2] +"\"/>"
                                    + "<p class='Titulo-E'>" + i[1]+ "</p>" + "<button class='btn-vde' onclick=\"irAVerDetalles(" + i[0] + ")\">Ver detalles</button>"
                                    + "</div>");
                    });

                    $("#filtros :input").prop("disabled", false);
                }
            });
        })
        .fail(function(xhr, status, error) {
            $("#equipos").html(espacioResultados);
            $("#equipos").append("<div style='display: inline-block;'>Intente de nuevo. " +
                    (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")") +  "</div>");
            return;
        })
        .always(function() {
            $("#filtros :input").prop("disabled", false);
        });;
}

/**
 * Redirecciona a la página para ver los detalles de un equipo.
 * Esta funcion se manda a llamar desde el iFrame.
 * @param {int} id - El ID del equipo cuyos detalles se van a ver.
 */
function irAVerDetalles(id){
    //Este método permite guardar una variable de sesión del lado del cliente.
    sessionStorage.setItem("EQUIPOS_DETALLES", parseInt(id));
    document.location.href = "EQUIPOS_DETALLES.html";
}