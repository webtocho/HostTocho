// Controlador de la página para editar los datos de una cuenta.

/* ID de la cuenta cuyos datos se van a modificar.
   Se queda nulo si el usuario que abrió la página quiere editar su propia cuenta. */
var id;

$(document).ready(function() {
    //Desde CUENTAS_DETALLES probablemente se nos mande el ID de la cuenta que se quiere editar.
    id = sessionStorage.getItem("CUENTAS_EDICION");
    if(id !== null) sessionStorage.removeItem("CUENTAS_EDICION");
    
    //Creamos un modal de Bootstrap.
    crearModal(false,true,true,true);
    $("#modal-footer").hide();
    $("#modal-title").html("Cargando información...");
    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    //Consultamos si el que está logueado es un administrador.
    $.post( "../controlador/SRV_GET_SESION.php", {tipos : ["ADMINISTRADOR"]}, null, "text")
        .done(function(res) {
            if(parseInt(res) !== 0){
                id = null;
            }
            
            //Hacemos una petición para obtener los datos actuales de la cuenta, que se pondrán en todos los inputs.
            $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : (id === null ? "" : id), pd : "2", all_pd : "1"}, null, "json")
                .done(function(res) {
                    //Usamos los datos actuales de la cuenta para inicializar todos los inputs.
                    
                    inicializar_input_edicion(document.getElementById("apellido_paterno"), res['APELLIDO_PATERNO']);
                    inicializar_input_edicion(document.getElementById("apellido_materno"), res['APELLIDO_MATERNO']);
                    inicializar_input_edicion(document.getElementById("nombre"), res['NOMBRE']);
                    inicializar_input_edicion(document.getElementById("correo"), res['cr']);

                    if(res['tp'] == "JUGADOR"){
                        //Si la cuenta es de un jugador, inicializamos los inputs exclusivos para este tipo de cuenta.
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
                        
                        //Colocamos las opciones para todas las efermedades y marcamos las que padece el usuario.
                        $.each(res['all_en'], function (index, i) {
                            $("#ch_otras_enf").before("<input type='checkbox' name='enfermedad' value='" + i[0] + "'" + (Object.values(res['en']).indexOf(parseInt(i[0])) != -1 ? " checked" : "") + "> " + i[1] + "<br>");
                        });
                        //Colocamos el campo para las otras enfermedades y la llenamos.
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
                        
                        //Colocamos las opciones para todas las alergias y marcamos las que padece el usuario.
                        $.each(res['all_al'], function (index, i) {
                            $("#ch_otras_alg").before("<input type='checkbox' name='alergia' value='" + i[0] + "' " + (Object.values(res['al']).indexOf(parseInt(i[0])) != -1 ? " checked" : "") + "> " + i[1] + "<br>");
                        });
                        //Colocamos el campo para las otras alergias y la llenamos.
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

/**
 * Recarga esta página, en caso de que se de un error al cargar los datos de la cuenta.
 */
function recargar(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_EDICION", id);
    location.reload();
}

/**
 * Cancela la edición. Redirecciona de vuelta, a la página para ver los detalles de la cuenta.
 */
function irAPaginaDeDetalles(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_DETALLES", id);
    document.location.href = "CUENTAS_DETALLES.html";
}

/**
 * Restablece los datos originales en todos los input (deshace los cambios que el usuario haya hecho hasta el momento).
 */
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

/**
 * Esta función se ejecuta cada vez que el usuario modifica un <input> relacionado con la contraseña.
 * Colorea los 2 <input> de la contraseña para saber si el campo de confirmación de contraseña es correcto y si se ha agregado una contraseña.
 */
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

/**
 * Agrega un dato a los parámetros de la petición al servidor que guardará los cambios.
 * Lo agrega sólo si ha sufrido cambios.
 * 
 * @param {FormData} form_data Objeto que almacena los parámetros de la petición.
 * @param {string} input_id El ID de la etiqueta <input> que contiene el dato.
 * @param {string} nombre La clave/nombre del parámetro en la petición.
 * @param {string} descripcion Una cadena describiendo lo que es el campo.
 * @return {Boolean} Si no se presentó ningún error.
 */
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

/**
 * Guarda los cambios en la base de datos.
 */
function editar(){
    //El objeto que almacena los parámetros que se mandarán en la solicitud PHP.
    var parametros = new FormData();
    //Especificamos la función en el servidor.
    parametros.append("fn", "mod");
    
    //Agregamos el parámetro del ID si el usuario que esté abriendo la página va a editar una cuenta ajena.
    parametros.append("id", (id == null ? "" : id));
    
    //Se agregan los nuevos datos básicos a la petición.
    var datos_validos =
       agregar_parametro(parametros, "apellido_paterno", "ap_p", "Apellido paterno") &&
       agregar_parametro(parametros, "apellido_materno", "ap_m", "Apellido materno") &&
       agregar_parametro(parametros, "nombre", "nb", "Nombre(s)") &&
       agregar_parametro(parametros, "correo", "cr", "Correo electrónico");
    if(!datos_validos)
        return;
    
    //Se agregan la nueva contraseña a la petición, si sufrió modificaciones.
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
    
    //Se agregan los nueva foto de perfil a la petición, si se desea cambiar.
    if(document.getElementById("foto").files.length !== 0){
        if(document.getElementById('foto').files[0].size < 10485760)
            parametros.append("ft", document.getElementById('foto').files[0]);
        else{
            alert("La imagen es demasiado grande");
            return;
        }
    }
    
    if(document.getElementById("datos_jugador") !== null){
        //Se agregan los datos exclusivos de los jugadores a la petición, si la cuenta a modificar es de ese tipo.
        var datos_validos =
            agregar_parametro(parametros, "nacimiento", "nc", "Fecha de nacimiento") &&
            agregar_parametro(parametros, "telefono", "tel", "Número de teléfono") &&
            agregar_parametro(parametros, "sangre", "sg", "Tipo de sangre") &&
            agregar_parametro(parametros, "facebook", "fb", "Enlace/link al perfil de Facebook") &&
            agregar_parametro(parametros, "twitter", "tw", "Enlace/link a la página de Twitter") &&
            agregar_parametro(parametros, "instagram", "ig", "Enlace/link a la página de Instagram");
    
        if(!datos_validos)
            return;
        
        //Agregamos enfermedades a la petición.
        var tmp = [];
        $("input:checkbox[name='enfermedad']:checked").each(function (index, i) {
            tmp.push($(i).val());
        });
        parametros.append("en", JSON.stringify(tmp));
        
        //Agregamos alergias a la petición.
        tmp = [];
        $("input:checkbox[name='alergia']:checked").each(function (index, i) {
            tmp.push($(i).val());
        });
        parametros.append("al", JSON.stringify(tmp));
        
        //Agregamos las otras enfermedades a la petición.
        if(document.getElementById("ch_otras_enf").checked){
            if(!agregar_parametro(parametros, "tx_otras_enf", "ot_en", "Otras enfermedades"))
                return;
        } else {
            parametros.append("ot_en", "");
        }
        
        //Agregamos las otras alergias a la petición.
        if(document.getElementById("ch_otras_alg").checked){
            if(!agregar_parametro(parametros, "tx_otras_alg", "ot_al", "Otras alergias"))
                return;
        } else {
            parametros.append("ot_al", "");
        }
    }
    
    //Mostramos el modal mientras se guardan los cambios.
    $("#modal-footer").hide();
    $("#modal-title").html("Aplicando cambios...");
    $("#modal-body").html("<center><img src='../modelo/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    //Se hace la petición para guardar los cambios y el resultado se muestra en pantalla.
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