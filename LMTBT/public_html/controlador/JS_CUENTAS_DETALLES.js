var id_usuario;

//Recibe una cadena y la devuelve de tal forma que tenga la primera letra mayúscula, y el resto sean minúsculas.
function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

$( document ).ready(function() {
    $("#datos_jugador").hide();
    //Si un admin abrió esta página, es posible que quiera ver los datos de una cuenta que no sea la suya.
    id_usuario = sessionStorage.getItem("id_usuario");
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {
            tipo : "get_info_cuenta",
            idCuenta : id_usuario
        },
        type: "POST",
        dataType: 'json',
        async: false,
        success: function (datos_de_usuario) {
            //El código que se va a mostrar en los campos en los que todavía no hay información.
            var indefinido = "<i style='color : red;'>Sin especificar</i>";
            
            if(datos_de_usuario["error"] == null){
                $("#nombre").html(datos_de_usuario["APELLIDO_PATERNO"] + " " + datos_de_usuario["APELLIDO_MATERNO"] + " " + datos_de_usuario["NOMBRE"]);
                $("#correo").html(datos_de_usuario["CORREO"]);
                $("#sexo").html((datos_de_usuario["SEXO"] != null ? capitalize(datos_de_usuario["SEXO"]) : indefinido));
                $("#tipo").html((datos_de_usuario["TIPO_USUARIO"] != null ? capitalize(datos_de_usuario["TIPO_USUARIO"]) : indefinido));
                
                if(datos_de_usuario["TIPO_USUARIO"] == "JUGADOR"){
                    if(datos_de_usuario["FOTO_PERFIL"] != null)
                        document.getElementById("foto").src = "data:image/png;base64," + datos_de_usuario["FOTO_PERFIL"];
                    else
                        document.getElementById("foto").src = "img/RC_IF_ANONIMO.png";
                    
                    if(datos_de_usuario["FECHA_NACIMIENTO"] != null)
                        $("#nacimiento").html((new Date(datos_de_usuario["FECHA_NACIMIENTO"].replace(/-/g, '\/'))).toLocaleDateString("es-ES", { year: 'numeric', month: 'long', day: 'numeric' }));
                    else
                        $("#nacimiento").html(indefinido);
                    
                    $("#telefono").html((datos_de_usuario["TELEFONO"] != null ? datos_de_usuario["TELEFONO"] : indefinido));
                    $("#sangre").html((datos_de_usuario["TIPO_SANGRE"] != null ? datos_de_usuario["TIPO_SANGRE"] : indefinido));
                    if(datos_de_usuario["FACEBOOK"] != null || datos_de_usuario["INSTAGRAM"] != null || datos_de_usuario["TWITTER"] != null){
                        $("#lista_redes").html("");
                        if(datos_de_usuario["FACEBOOK"] != null)
                            $("#lista_redes").append("<li><a href='" + datos_de_usuario["FACEBOOK"] + "' target='_blank'>Facebook</a></li>");
                        if(datos_de_usuario["TWITTER"] != null)
                            $("#lista_redes").append("<li><a href='" + datos_de_usuario["TWITTER"] + "' target='_blank'>Twitter</a></li>");
                        if(datos_de_usuario["INSTAGRAM"] != null)
                            $("#lista_redes").append("<li><a href='" + datos_de_usuario["INSTAGRAM"] + "' target='_blank'>Instagram</a></li>");
                    }
                    $("#datos_jugador").show();
                } else {
                    $("#datos_jugador").remove();
                }
            } else {
                alert(datos_de_usuario["error"]);
                //PENDIENTE: Redireccionar al index
                document.location.href = "index.php";
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        }
    });
});

function irAPaginaDeEdicion(){
    if(id_usuario != null)
        sessionStorage.setItem("id_usuario", id_usuario);
    document.location.href = "CUENTAS_EDICION.html";
}