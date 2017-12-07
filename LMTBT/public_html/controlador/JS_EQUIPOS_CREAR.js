var idDelCoach = -1;
var posibles_id = ["-1"];

$( document ).ready(function() {
    //Al momento de cargar la página debemos confirmar que es esté accediento sea un COACH o un ADMIN.
    $.ajax({
        url: "../controlador/SRV_GET_SESION.php",
        type: "POST",
        dataType: 'json',
        async: true,
        processData: false,
        contentType: false,
        beforeSend: function (xhr) {
            //Bloqueamos los controladores.
            document.getElementById("nombre").disabled = true;
            document.getElementById("logotipo").disabled = true;
            document.getElementById("coach").disabled = true;
        },
        success: function (resultado) {
            if(resultado["id"] == null || resultado["tipo"] == null){
                //Si el usuario no ha iniciado sesión, lo redireccionamos.
                window.location.replace("index.php");
            } else if(resultado["id"] != null && resultado["tipo"] != null){
                if(resultado["tipo"].toUpperCase() == "COACH"){
                    $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {
                            //0 = false, 1 = true.
                            tipo : "get_info_cuenta",
                            idCuenta : resultado["id"],
                            nombre: "1",
                            correo: "0",
                            redes: "0",
                            otros: "0",
                            foto: "0",
                        },
                        type: "POST",
                        dataType: 'json',
                        async: true,
                        success: function (datos_de_usuario) {
                            if(datos_de_usuario["error"] == null){
                                document.getElementById("coach").value = datos_de_usuario["APELLIDO_PATERNO"] + " " + datos_de_usuario["APELLIDO_MATERNO"] + " " + datos_de_usuario["NOMBRE"];
                                idDelCoach = parseInt(resultado["id"]);
                            } else {
                                $('#contenido').html("<h2>Parece que su cuenta ya no existe, trate de loguearse de nuevo.</h2>");
                            }
                            
                            //Eliminamos los controlores que el coach usa para buscar un coach.
                            $("#seleccion_coach").remove();
                            $("#busqueda_coach").remove();
                        },
                        error: function (jqXHR, textStatus) {
                            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
                        }
                    });
                } else if (resultado["tipo"].toUpperCase() == "ADMINISTRADOR"){
                    //Eliminamos el control que el coach usa para ver su nombre.
                    $("#coach").remove();
                } else {
                    $('#contenido').html("<h2>Su cuenta no tiene acceso a esta página.</h2>");
                }
            } else {
                console.log("SRV_GET_SESION.php no funciona como debería.");
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        },
        complete: function (jqXHR, textStatus) {
            document.getElementById("nombre").disabled = false;
            document.getElementById("logotipo").disabled = false;
        }
    });
});

function crearEquipo(){
    var nombre;
    
    nombre = $.trim(document.getElementById("nombre").value);
    
    if(nombre.length === 0){
        alert("El nombre es inválido.");
        return false;
    }
    
    if(document.getElementById("logotipo").files.length === 0){
        alert("No ha seleccionado el logotipo.");
        return false;
    }
    
    if(idDelCoach < 0){
        alert("No ha seleccionado un coach.");
        return false;
    }
    
    //El objeto que almacena los parámetros que se mandarán en la solicitud PHP.
    var parametros = new FormData();
    //Especificamos la función en el servidor.
    parametros.append("tipo", "crear_equipo");
    //Agregamos el id del coach.
    parametros.append("id_coach", idDelCoach);
    //Agregamos nombre del equipo.
    parametros.append("nombre", nombre);
    //Agregamos el archivo seleccionado del logotipo del equipo.
    parametros.append("logotipo", document.getElementById('logotipo').files[0]);
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        processData: false,
        contentType: false,
        async: false,
        beforeSend: function (xhr) {
            $("#contenido *").prop( "disabled", true );
        },
        success: function (respuesta) {
            if(respuesta == "ok"){
                //PENDIENTE - IR AL INDEX
                alert("Equipo creado con éxito.");
                document.location.href = "EQUIPOS_VER.html";
            } else {
                alert(respuesta);
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        },
        complete: function (jqXHR, textStatus) {
            $("#contenido *").prop( "disabled", false );
        }
    });
}

function buscarCoaches(){
    var apellido = $.trim(document.getElementById("busqueda_coach").value);
    
    if(apellido.length > 2){
        var opciones = "<option value=\"-1\" selected=\"selected\">Sin resultados</option>";
        
        $.ajax({
            url: "../controlador/SRV_CONSULTAS.php",
            data: {
                tipo : "buscar_coaches",
                criterio : apellido,
            },
            type: "POST",
            dataType: 'json',
            async: true,
            success: function (datos_de_usuario) {                
                if(Object.keys(datos_de_usuario).length > 0){
                    opciones = "";
                    posibles_id = ["-1"];
                    
                    $.each(datos_de_usuario , function( index, i ) {
                        posibles_id.push(parseInt(i[0]));
                        opciones += "<option value=\"" + i[0] + "\">" + i[1] + "</option>";
                    });
                }
            },
            complete: function (jqXHR, textStatus) {
                $("#seleccion_coach").html(opciones);
                elegirCoach();
            }
        });
    }
}

function elegirCoach(){
    var select = document.getElementById("seleccion_coach");
    if(posibles_id.indexOf(parseInt( select.options[select.selectedIndex].value ) !== -1)){
        idDelCoach = parseInt( select.options[select.selectedIndex].value );
    }
}