function cerrarSesion(){
    $.ajax({
        url: "../controlador/SRV_CONSULTA_LOGIN_LOGOUT.php",
        data: {tipo : "logout"},
        type: "POST",
        dataType: 'text',
        // async: false, //Esta operación es síncrona.
        success: function (resultado) {
            console.log(resultado);
            //El servidor nos contesta con la cadena 'ok' si el cierre de sesión fue exitoso.
            if(resultado == "ok"){
                //Redireccionamos al usuario a la página de login.
                window.location.replace("index.php");
            }
            else{
                //Se recibió un mensaje de error que se muestra en pantalla.
                alert(resultado);
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        }
    });
}