$(function () {
    $('#campo_correo').bind("keypress", function (event) {
        var key = event.which || event.keyCode;
        if (key == 13)
            iniciarSesion();
    })

    $('#campo_password').bind("keypress", function (event) {
        var key = event.which || event.keyCode;
        if (key == 13)
            iniciarSesion();
    })

});

function iniciarSesion() {
    var e_mail, password;
    e_mail = $.trim($('#campo_correo').val());
    password = $.trim($('#campo_password').val());

    if (e_mail.length === 0 && password.length === 0) {
        alert("Datos inválidos.");
        return;
    }

    var patron_e_mail = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (e_mail.length === 0 || !patron_e_mail.test(e_mail)) {
        alert("Email inválido.");
        return;
    }

    if (password.length === 0) {
        alert("Contraseña inválida.");
        return;
    }

    $.ajax({
        type: "POST",
        url: "../controlador/SRV_SESION.php",
        data: {tipo: "login",
            e_mail: e_mail,
            password: password
        },
        dataType: "text",
        async: true,
        // async: false, //Esta operación es síncrona.
        beforeSend: function (xhr) {
            //Bloqueamos los controladores.
            document.getElementById("btn_iniciar_sesion").disabled = true;
            document.getElementById("campo_correo").disabled = true;
            document.getElementById("campo_password").disabled = true;
        },
        success: function (resultado) {
            //El servidor nos contesta con la cadena 'ok' si el inicio de sesión fue exitoso.
            if (resultado == "ok") {
                //Redireccionamos al usuario a la página de login.
                window.location.replace("index.php");
            } else {
                //Se recibió un mensaje de error que se muestra en pantalla.
                alert(resultado);
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        },
        complete: function (jqXHR, textStatus) {
            document.getElementById("btn_iniciar_sesion").disabled = false;
            document.getElementById("campo_correo").disabled = false;
            document.getElementById("campo_password").disabled = false;
        }
    });
}

function abrirPantallaParaIngresarCorreo() {
    $('#tituloVentanaEmergente').empty();
    $('#tituloVentanaEmergente').append("Recuperar cuenta");
    document.getElementById("formulario").onsubmit = function () {
        recuperarPassword();
        return false;
    };
    //Mostramos la ventana emergente
    $('#ventanaEmergente').modal();
}

function recuperarPassword() {
    $('#ventanaEmergente').modal('hide');
    var correo_recuperar = document.getElementById("correo_recuperar").value;
    $.ajax({
        url: "../controlador/SRV_CUENTAS.php",
        data: {
            fn : "recuperar",
            correo_recuperar: correo_recuperar
        },
        type: "POST",
        datatype: "text",
        success: function (resultado) {
            if (resultado == "ok") {
                mostrarAlerta("Te hemos enviado un correo con tu nueva contraseña generada aleatoriamente recuerda cambiarlas", "correcto");
            } else {
                mostrarAlerta(resultado, "fallido");
            }
        },
        error: function (jqXHR, textStatus) {
            mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.", "fallido");
        }
    });
    document.getElementById("correo_recuperar").value = "";
}