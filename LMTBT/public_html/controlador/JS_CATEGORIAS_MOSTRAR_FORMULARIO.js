/*
 * Una vez que se cargue el código html en el formulario para crear un nuevo torneo o una nueva covocatoria,
 * se cargan los elementos de datepicker para facilitar el registro.
 */
$(function () {
    $("#fecha_cierre_convocatoria").datepicker();
    $("#fecha_inicio_torneo").datepicker();
    $("#fecha_fin_torneo").datepicker();
});

$(document).ready(function () {
    mostrar_categorias_al_registrar_convocatoria();
});

// Recuperamos la lista de categorias disponibles para el registro de convocatorias, y la añadimos al select correspondiente.
function mostrar_categorias_al_registrar_convocatoria() {
    $.ajax({
        url: "../controlador/SRV_CATEGORIAS_MOSTRAR_FORMULARIO.php",
        data: {"tipo": "mostrar_categorias_al_registrar_convocatoria"},
        type: "POST",
        datatype: "text",
        success: function (respuesta) {
            // si ocurre un error se notifica al usuario
            if (respuesta != "Ha ocurrido un error al recuperar la información solicitada. Intente más tarde, por favor.") {
                $('#categoria').html(respuesta);
            } else {
                alert("Ha ocurrido un error al recuperar la información solicitada. Intente más tarde, por favor.");
            }
        }
    });
}