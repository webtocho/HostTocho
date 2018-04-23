$(document).ready(function () {
    mostrar_convocatorias_activas();
});
$('[data-toggle="tooltip"]').tooltip();
/**
 * Este método muestra la lista de las convocatorias inactivas, es decir, que ya se han cerrado y se espera la ejecucion del torneo (encuentros de equipos(roles de juego))
 */
function mostrar_convocatorias_activas() {
    $.ajax({
        url: "../controlador/SRV_ROLES_JUEGO.php",
        data: {"tipo": "lista_convocatorias_inactivas"},
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {},
        success: function (respuesta) {
            if (respuesta != "no") {
                $('#lista_convocatorias_inactivas').empty();
                $('#lista_convocatorias_inactivas').html(respuesta);
            } else if (respuesta === "no") {
                document.location.href = "index.html";
            }
        },
        error: function (jqXHR, textStatus) {
        }
    });
}

/**
 * Al seleccionar una convocatoria, mostramos la lista de roles de juego asociados a ella  
 * @param {type} id_convocatoria - ID del torneo o convocatoria cuyos roles de juego se quieren ver.
 */
function lista_convocatorias_inactivas() {
    // Obtener la ID de la convocatoria o torneo del elemento select
    var id_convocatoria = document.getElementById("lista_convocatorias_inactivas").value;
    // si no se ha seleccionado un torneo no se ejecuta la funcion 
    if (id_convocatoria != -1) {
        $.ajax({
            url: "../controlador/SRV_ROLES_JUEGO.php",
            data: {tipo: "roles_juegos_convocatoria_seleccionada",
                id_convocatoria: id_convocatoria
            },
            type: "POST",
            datatype: "text",
            beforeSend: function (xhr) {},
            success: function (respuesta) {
                // si ocurre un error se notifica al usuario, de lo contrario se muestran los roles de juego recuperada de la consulta.
                if (respuesta === "Ha ocurrido un error al recuperar la informacion. Intentelo mas tarde.") {
                    alert(respuesta);
                    document.location.href = "index.html";
                } else if (respuesta != "no") {
                    $('#roles_juegos_convocatoria_seleccionada').html(respuesta);
                } else {
                    document.location.href = "index.html";
                }
            },
            error: function (jqXHR, textStatus) {}
        });
    }
}