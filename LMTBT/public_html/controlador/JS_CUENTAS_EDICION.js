var id; //ID de cuenta

$(document).ready(function() {
    //Desde EQUIPOS_VER se nos manda el id del equipo a consultar.
    id = sessionStorage.getItem("CUENTAS_DETALLES");
    if(id !== null) sessionStorage.removeItem("CUENTAS_DETALLES");
    
    crearModal(false,true,true,true);
    $("#modal-footer").hide();
    $("#modal-title").html("Cargando información...");
    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos : ["ADMINISTRADOR"]}, null, "text")
        .done(function(res) {
            if(parseInt(res) !== 0){
                id = null;
            }
            
            $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : (id === null ? "" : id), pd : "2", all_pd : "1"}, null, "json")
                .done(function(res) {
                    inicializar_input_edicion(document.getElementById("apellido_paterno"), res['APELLIDO_PATERNO']);
                    inicializar_input_edicion(document.getElementById("apellido_materno"), res['APELLIDO_MATERNO']);
                    inicializar_input_edicion(document.getElementById("nombre"), res['NOMBRE']);
                    inicializar_input_edicion(document.getElementById("correo"), res['cr']);

                    if(res['tp'] == "JUGADOR"){
                        inicializar_input_edicion(document.getElementById("nacimiento"), res['nc']);
                        inicializar_input_edicion(document.getElementById("telefono"), res['TELEFONO']);
                        inicializar_input_edicion(document.getElementById("sangre"), res['TIPO_SANGRE']);
                        if(res["TIPO_SANGRE"] != null){
                            $("#sangre option[value=\"" + res["TIPO_SANGRE"] + "\"]").prependTo("#sangre");
                            $("#sangre option[value=\"\"]").remove();
                        } else {
                            document.getElementById("sangre").defaultValue = "";
                        }
                        inicializar_input_edicion(document.getElementById("facebook"), res["FACEBOOK"]);
                        inicializar_input_edicion(document.getElementById("twitter"), res["TWITTER"]);
                        inicializar_input_edicion(document.getElementById("instagram"), res["INSTAGRAM"]);
                        
                        $.each(res['all_en'], function (index, i) {
                            $("#ch_otras_enf").before("<input type='checkbox' name='enfermedad' value='" + i[0] + "'" + (Object.values(res['en']).indexOf(parseInt(i[0])) != -1 ? " checked" : "") + "> " + i[1] + "<br>");
                        });
                        document.getElementById("ch_otras_enf").checked = document.getElementById("ch_otras_enf").defaultChecked = (res['en']['otros'] !== null);
                        inicializar_input_edicion(document.getElementById("tx_otras_enf"), res['en']['otros']);
                        if(!document.getElementById("ch_otras_enf").checked)
                            $("#tx_otras_enf").hide();
                        $('#ch_otras_enf').change(function () {
                            var $this = $(this);
                            if ($this.is(':checked')) {
                                $('#tx_otras_enf').show();
                            } else {
                                $('#tx_otras_enf').hide();
                            }
                        });
                        
                        $.each(res['all_al'], function (index, i) {
                            $("#ch_otras_alg").before("<input type='checkbox' name='alergia' value='" + i[0] + "' " + (Object.values(res['al']).indexOf(parseInt(i[0])) != -1 ? " checked" : "") + "> " + i[1] + "<br>");
                        });
                        document.getElementById("ch_otras_alg").checked = document.getElementById("ch_otras_alg").defaultChecked = (res['al']['otros'] !== null);
                        inicializar_input_edicion(document.getElementById("tx_otras_alg"), res['al']['otros']);
                        if(!document.getElementById("ch_otras_alg").checked)
                            $("#tx_otras_alg").hide();
                        $('#ch_otras_alg').change(function () {
                            var $this = $(this);
                            if ($this.is(':checked')) {
                                $('#tx_otras_alg').show();
                            } else {
                                $('#tx_otras_alg').hide();
                            }
                        });
                        
                        $("#datos_jugador").show();
                    } else {
                        $("#datos_jugador").remove();
                    }
                    
                    $('#modal').modal('hide');
                })
                .fail(function(xhr, status, error) {
                    $("#modal-title").html("Error");
                    $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor. (" + xhr.status + " " + status + ")"));
                    $("#modal-body").append("<br><a href='javascript:recargar();'>Reintentar</a>");
                });
        })
        .fail(function() {
            expulsar();
        });
});

function recargar(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_EDICION", id);
    location.reload();
}

function irAPaginaDeDetalles(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_DETALLES", id);
    document.location.href = "CUENTAS_DETALLES.html";
}

function reiniciarDatos(){
    document.getElementById("formulario").reset();
    
    $(document.getElementById("apellido_paterno")).trigger("input");
    $(document.getElementById("apellido_materno")).trigger("input");
    $(document.getElementById("nombre")).trigger("input");
    $(document.getElementById("correo")).trigger("input");
    aplicar_cambio_contrasena();
    
    if(document.getElementById("datos_jugador") !== null){
        $(document.getElementById("nacimiento")).trigger("change");
        $(document.getElementById("telefono")).trigger("input");
        $(document.getElementById("ch_otras_enf")).trigger("change");
        $(document.getElementById("ch_otras_alg")).trigger("change");
        $(document.getElementById("tx_otras_enf")).trigger("input");
        $(document.getElementById("tx_otras_alg")).trigger("input");
        $(document.getElementById("sangre")).trigger("change");
        $(document.getElementById("facebook")).trigger("input");
        $(document.getElementById("twitter")).trigger("input");
        $(document.getElementById("instagram")).trigger("input");
    }
}

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

function editar(){
    //El objeto que almacena los parámetros que se mandarán en la solicitud PHP.
    var parametros = new FormData();
    //Especificamos la función en el servidor.
    parametros.append("fn", "mod");
    
    parametros.append("id", (id == null ? "" : id));
    
    var datos_validos =
       agregar_parametro(parametros, "apellido_paterno", "ap_p", "Apellido paterno") &&
       agregar_parametro(parametros, "apellido_materno", "ap_m", "Apellido materno") &&
       agregar_parametro(parametros, "nombre", "nb", "Nombre(s)") &&
       agregar_parametro(parametros, "correo", "cr", "Correo electrónico");
    
    if(!datos_validos)
        return;
    
    if(document.getElementById("contrasena").value != ""){
        if(document.getElementById("contrasena").value == document.getElementById("contrasena_confirmacion").value)
            parametros.append("ps", document.getElementById("contrasena").value);
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
            agregar_parametro(parametros, "nacimiento", "nc", "Fecha de nacimiento") &&
            agregar_parametro(parametros, "telefono", "tel", "Número de teléfono") &&
            agregar_parametro(parametros, "sangre", "sg", "Tipo de sangre") &&
            agregar_parametro(parametros, "facebook", "fb", "Enlace/link al perfil de Facebook") &&
            agregar_parametro(parametros, "twitter", "tw", "Enlace/link a la página de Twitter") &&
            agregar_parametro(parametros, "instagram", "ig", "Enlace/link a la página de Instagram");
    
        if(!datos_validos)
            return;
        
        if(document.getElementById("foto").files.length !== 0){
            if(document.getElementById('foto').files[0].size < 10485760)
                parametros.append("ft", document.getElementById('foto').files[0]);
            else{
                alert("La imagen es demasiado grande");
                return;
            }
        }
        
        //Agregamos enfermedades.
        var tmp = [];
        $("input:checkbox[name='enfermedad']:checked").each(function (index, i) {
            tmp.push($(i).val());
        });
        parametros.append("en", JSON.stringify(tmp));
        
        //Agregamos alergias.
        tmp = [];
        $("input:checkbox[name='alergia']:checked").each(function (index, i) {
            tmp.push($(i).val());
        });
        parametros.append("al", JSON.stringify(tmp));
        
        if(document.getElementById("ch_otras_enf").checked){
            if(!agregar_parametro(parametros, "tx_otras_enf", "ot_en", "Otras enfermedades"))
                return;
        } else {
            parametros.append("ot_en", "");
        }
        if(document.getElementById("ch_otras_alg").checked){
            if(!agregar_parametro(parametros, "tx_otras_alg", "ot_al", "Otras alergias"))
                return;
        } else {
            parametros.append("ot_al", "");
        }
    }
    
    $("#modal-footer").hide();
    $("#modal-title").html("Aplicando cambios...");
    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    $.ajax({
        url: "../controlador/SRV_CUENTAS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        processData: false,
        contentType: false,
        async: false,
        success: function (respuesta) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("<center>Cuenta modificada correctamente.<br><a href='javascript:irAPaginaDeDetalles();'>Volver a la página de detalles.</a></center>");
            $("#modal-footer").show();
        },
        error: function (xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor (" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        }
    });
}

/*var id_usuario;

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
}*/