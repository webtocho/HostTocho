$(document).ready(function () {
	mostrar_categorias_al_registrar_convocatoria();
});

// recuperamos la lista de categorias disponibles para el registro de convocatorias, y la añadimos al select correspondiente
function mostrar_categorias_al_registrar_convocatoria() {
    $.ajax({
        url: "../controlador/SRV_CATEGORIAS_MOSTRAR_FORMULARIO.php",
        data: {"tipo": "mostrar_categorias_al_registrar_convocatoria"},
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {},
        success: function (respuesta) {
            // si ocurre un error se notifica al usuario
            if (respuesta != "Ha ocurrido un error al recuperar la informacion solicitada. Intente mas tarde porfavor.") {
                $('#categoria').html(respuesta);
            } else {
                alert("Ha ocurrido un error al recuperar la informacion solicitada. Intente mas tarde porfavor.");
            }
        },
        error: function (jqXHR, textStatus) {}
    });
}