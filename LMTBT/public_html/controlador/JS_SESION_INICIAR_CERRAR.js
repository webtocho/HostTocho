$(document).ready(function () {
    iniciar_cerrar_session();
});

/**
 * Cuando el usuario inicia SESSION cambia la funcion del botón de ingresar por cerrar sesion y viceversa
 */
function iniciar_cerrar_session() {
    $.ajax({
        url: "../controlador/SRV_SESION.php",
        data: {"tipo": "iniciar_cerrar_session"},
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {
        },
        success: function (respuesta) {
            if (respuesta === "ok") {
                $('#iniciar_cerrar_session').empty();
                $('#iniciar_cerrar_session').append("<a onclick='cerrarSesion()'> Cerrar sesion </a>");
            } else {
                $('#iniciar_cerrar_session').empty();
                $('#iniciar_cerrar_session').append("<a href='CUENTAS_LOGIN.html'> Ingresar </a>");
            }
        },
        error: function (jqXHR, textStatus) {
        }
    });
}