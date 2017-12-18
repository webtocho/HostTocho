$(document).ready(function() {
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
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

function buscar(){
    var div_equipos = document.getElementById("equipos");
    while (div_equipos.childNodes.length > 2) {
        div_equipos.removeChild(div_equipos.lastChild);
    }
    
    var tmp = $("#equipos").html();
    $("#paginas").html();
    
    $("#filtros :input").prop("disabled", true);
    $("#equipos").html("<img src='img/RC_IF_CARGANDO.gif'>");
    
    $.post( "../controlador/SRV_EQUIPOS.php", {fn : "num"}, null, "text")
        .done(function(res) {
            $('#paginas').pagination({
                dataSource: '../controlador/SRV_EQUIPOS.php',
                locator: 'items',
                totalNumber: parseInt(res),
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
                    $("#equipos").html(tmp);
                    $("#equipos").append("<div style='display: inline-block;'>Intente de nuevo. " +
                            (jqXHR.status == 500 ? jqXHR.responseText : "(" + jqXHR.status + " " + textStatus + ")") +  "</div>");
                    $("#filtros :input").prop("disabled", false);
                    return;
                },
                callback: function(data, pagination) {
                    $("#equipos").html(tmp);
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
            $("#equipos").html(tmp);
            $("#equipos").append("<div style='display: inline-block;'>Intente de nuevo. " +
                    (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")") +  "</div>");
            return;
        })
        .always(function() {
            $("#filtros :input").prop("disabled", false);
        });;
}

function irAVerDetalles(id){
    //Este método permite guardar una variable de sesión del lado del cliente.
    sessionStorage.setItem("EQUIPOS_DETALLES", parseInt(id));
    document.location.href = "EQUIPOS_DETALLES.html";
}