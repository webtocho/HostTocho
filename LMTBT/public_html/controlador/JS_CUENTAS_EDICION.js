var id_usuario;

//Recibe una cadena y la devuelve de tal forma que tenga la primera letra mayúscula, y el resto sean minúsculas.
function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

function aplicar_cambio(input){
    if(input.type == "text"){
        input.value = $.trim(input.value);
        input.value = input.value.replace(/ +(?= )/g,'');
    }
    if(input.required){
        if(input.defaultValue == input.value)
            $("#" + input.id).css("background-color", "white");
        else if(input.value == "")
            $("#" + input.id).css("background-color", "#ff0000");
        else
            $("#" + input.id).css("background-color", "#b3d9ff");
    } else {
        if(input.defaultValue == input.value)
            $("#" + input.id).css("background-color", "#ff9999");
        else
            $("#" + input.id).css("background-color", "#80ffaa");
    }
}

function aplicar_cambio_contrasena(){
    var input_contrasena, input_confirmacion;
    input_contrasena = document.getElementById("contrasena");
    input_confirmacion = document.getElementById("contrasena_confirmacion");
    
    if(input_contrasena.value == "")
        $("#contrasena").css("background-color", "white");
    else
        $("#contrasena").css("background-color", "#b3d9ff");
    
    if(input_confirmacion.value == "" && input_contrasena.value == "")
        $("#contrasena_confirmacion").css("background-color", "white");
    else if (input_contrasena.value == input_confirmacion.value)
        $("#contrasena_confirmacion").css("background-color", "#00ff00");
    else
        $("#contrasena_confirmacion").css("background-color", "#ff0000");
}

function asignar_valor_inicial(id_input, dato){
    if(dato != null){
        document.getElementById(id_input).defaultValue = dato;
        document.getElementById(id_input).required = true;
    } else {
        $("#" + id_input).css("background-color", "#ff9999");
    }
}

function reiniciarDatos(){
    document.getElementById("formulario").reset();
    
    aplicar_cambio(document.getElementById("apellido_paterno"));
    aplicar_cambio(document.getElementById("apellido_materno"));
    aplicar_cambio(document.getElementById("nombre"));
    aplicar_cambio(document.getElementById("correo"));
    aplicar_cambio_contrasena();
    if(document.getElementById("datos_jugador") !== null){
        aplicar_cambio(document.getElementById("nacimiento"));
        aplicar_cambio(document.getElementById("telefono"));
        aplicar_cambio(document.getElementById("sangre"));
        aplicar_cambio(document.getElementById("facebook"));
        aplicar_cambio(document.getElementById("twitter"));
        aplicar_cambio(document.getElementById("instagram"));
    }
}

$( document ).ready(function() {
    $("#datos_jugador").hide();
    //Si un admin abrió esta página, es posible que quiera ver los datos de una cuenta que no sea la suya.
    id_usuario = sessionStorage.getItem("id_usuario");
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {
            tipo : "get_info_cuenta",
            idCuenta : id_usuario,
            nombre : "1",
            tipo_usuario : "1",
            correo : "1",
            redes : "1",
            otros : "1",
            foto : "0"
        },
        type: "POST",
        dataType: 'json',
        async: false,
        success: function (datos_de_usuario) {
            if(datos_de_usuario["error"] == null){
                asignar_valor_inicial("apellido_paterno", datos_de_usuario["APELLIDO_PATERNO"]);
                asignar_valor_inicial("apellido_materno", datos_de_usuario["APELLIDO_MATERNO"]);
                asignar_valor_inicial("nombre", datos_de_usuario["NOMBRE"]);
                asignar_valor_inicial("correo", datos_de_usuario["CORREO"]);
                
                if(datos_de_usuario["TIPO_USUARIO"] == "JUGADOR"){
                    asignar_valor_inicial("nacimiento", datos_de_usuario["FECHA_NACIMIENTO"]);
                    asignar_valor_inicial("telefono", datos_de_usuario["TELEFONO"]);
                    asignar_valor_inicial("sangre", datos_de_usuario["TIPO_SANGRE"]);
                    
                    if(datos_de_usuario["TIPO_SANGRE"] != null){
                        $("#sangre option[value=\"" + datos_de_usuario["TIPO_SANGRE"] + "\"]").prependTo("#sangre");
                        $("#sangre option[value=\"\"]").remove();
                    } else {
                        document.getElementById("sangre").defaultValue = "";
                    }
                    
                    asignar_valor_inicial("facebook", datos_de_usuario["FACEBOOK"]);
                    asignar_valor_inicial("twitter", datos_de_usuario["TWITTER"]);
                    asignar_valor_inicial("instagram", datos_de_usuario["INSTAGRAM"]);
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

function agregar_parametro(form_data, input_id, nombre, descripcion){
    var input = document.getElementById(input_id);
    if(input.required){
        if(input.value == ""){
            if(confirm("Usted ha borrado el dato <" + descripcion + ">, que ya había sido establecido con anterioridad.\n"
                       + "Los datos establecidos sólo pueden ser modificados. Si continúa, el dato viejo se mantendrá.")){
                input.value = input.defaultValue;
                aplicar_cambio(input);
                form_data.append(nombre, input.value);
            } else {
                return false;
            }
        } else if (input.value != input.defaultValue){
            form_data.append(nombre, input.value);
        }
    } else {
        if(input.value != "")
            form_data.append(nombre, input.value);
    }
    return true;
}

function editarUsuario(){
    //El objeto que almacena los parámetros que se mandarán en la solicitud PHP.
    var parametros = new FormData();
    //Especificamos la función en el servidor.
    parametros.append("tipo", "editar_cuenta");
    
    parametros.append("id_cuenta", (id_usuario == null ? "" : id_usuario));
    
    var datos_validos =
       agregar_parametro(parametros, "apellido_paterno", "APELLIDO_PATERNO", "Apellido paterno") &&
       agregar_parametro(parametros, "apellido_materno", "APELLIDO_MATERNO", "Apellido materno") &&
       agregar_parametro(parametros, "nombre", "NOMBRE", "Nombre(s)") &&
       agregar_parametro(parametros, "correo", "CORREO", "Correo electrónico");
    
    if(!datos_validos)
        return;
    
    if(document.getElementById("contrasena").value != ""){
        if(document.getElementById("contrasena").value == document.getElementById("contrasena_confirmacion").value)
            parametros.append("PASSWORD", document.getElementById("contrasena").value);
        else{
            alert("Las contraseñas no coinciden.");
            return;
        }
    } else {
        document.getElementById("contrasena_confirmacion").value = "";
        aplicar_cambio_contrasena();
    }
    
    if(document.getElementById("datos_jugador") !== null){
        var datos_validos =
            agregar_parametro(parametros, "nacimiento", "FECHA_NACIMIENTO", "Fecha de nacimiento") &&
            agregar_parametro(parametros, "telefono", "TELEFONO", "Número de teléfono") &&
            agregar_parametro(parametros, "sangre", "TIPO_SANGRE", "Tipo de sangre") &&
            agregar_parametro(parametros, "facebook", "FACEBOOK", "Enlace/link al perfil de Facebook") &&
            agregar_parametro(parametros, "twitter", "TWITTER", "Enlace/link a la página de Twitter") &&
            agregar_parametro(parametros, "instagram", "INSTAGRAM", "Enlace/link a la página de Instagram");
    
        if(!datos_validos)
            return;
        
        if(document.getElementById("foto").files.length !== 0){
            if(document.getElementById('foto').files[0].size < 10485760)
                parametros.append("FOTO_PERFIL", document.getElementById('foto').files[0]);
            else{
                alert("La imagen es demasiado grande");
                return;
            }
        }
    }
    
    /*
    var cantidad_parametros = 0;
    for (var entry of parametros.entries()){
        cantidad_parametros++;
    }
    if(cantidad_parametros < 2){
        alert("No ha hecho ningún cambio.");
        return;
    }*/
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        processData: false,
        contentType: false,
        async: false,
        success: function (respuesta) {
            if(respuesta == "ok"){
                //PENDIENTE
                alert("Cambios aplicados con éxito.");
                document.location.href = "CUENTAS_DETALLES.html";
            } else {
                alert(respuesta);
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        }
    });
}