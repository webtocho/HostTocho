
function RegistrarCoach(){
    var correo = document.getElementById("Correo").value;
    var password = document.getElementById("Password").value;
    var nombre = document.getElementById("nombre").value;
    var apellido_paterno = document.getElementById("ApellidoPaterno").value;
    var apellido_materno = document.getElementById("ApellidoMaterno").value;

    if(correo.trim().length>0 && password.trim().length>0 && nombre.trim().length>0 && apellido_paterno.trim().length>0 && apellido_materno.trim().length>0){

        $.ajax({
            url: "../controlador/SRV_CONSULTAS.php",
            data: {tipo:"crear_coach",
            correo: correo,
            password:password,
            tipo_usuario:"COACH",
            estado:"DESCONECTADO",
            nombre:nombre,
            apellido_paterno:apellido_paterno,
            apellido_materno:apellido_materno,
            /* No necesitan mandar esto. ATTE Javier
            fecha_nacimiento:"",
            sexo:"",
            tipo_sangre:"",
            telefono:"",
            foto_perfil:"",
            facebook:"",
            instagram:"",
            twiter:"" */
            },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr) {

        },
        success: function(respuesta) {
            console.log(respuesta);
            if(respuesta == "ok") {
                alert("Registro realizado con exito.");
                window.location.replace("index.php");
            }else{
                alert(respuesta);
            }
        },
        error: function(jqXHR, textStatus) {

        }
    });


    }else{
        var mensaje = "Por favor Complete lo siguiente:";
        if(correo.trim().length>0) mensaje+="\nCorreo electronico";
        if(password.trim().length>0) mensaje+="\nContraseña";
        if(nombre.trim().length>0) mensaje+="\nNombre";
        if(apellido_paterno.trim().length>0) mensaje+="\nApellido Paterno";
        if(apellido_materno.trim().length>0) mensaje+="\nApellido Materno";

        alert(mensaje);
    }

}