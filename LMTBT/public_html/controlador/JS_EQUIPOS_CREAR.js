// Controlador de la página para crear equipos.

/* Indica el ID del coach que va a dirigir el equipo. El único escenario donde puede ser nulo
   consiste en que el administrador abra la página (iniciará siendo nulo hasta que elija un coach).*/
var idDelCoach = null;
// Boleano que indica si el usuario que abrió la página es un coach.
var usrEsCoach = null;

$(document).ready(function() {
    //Comprobamos si el usuario logueado es administrador o coach.
    $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(tipoDeUsr) {
            //"tipoDeUsr" (tipo de usuario) indica si el usuario logueado es de alguno de los tipos necesarios.
            switch(parseInt(tipoDeUsr)){
                case 0: //Es administrador.
                    usrEsCoach = false;
                    
                    /* Creamos un iFrame que contiene "CUENTAS_BUSQUEDA", para que el administrador pueda buscar y
                       elegir el coach que va a dirigir el equipo. */
                    var frame = document.createElement("IFRAME");
                    document.getElementById("campos").appendChild(frame);
                    var onload = setInterval(function() {
                        var frameDoc = frame.contentDocument || frame.contentWindow.document;
                        if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                            clearInterval(onload);
                            //Indicamos a "CUENTAS_BUSQUEDA" que sólo va a buscar cuentas de tipo coach.
                            frame.contentWindow.inicializar("COACH", "Elegir", "elegirCoach");
                        }
                    }, 500);
                    frame.src = 'CUENTAS_BUSQUEDA.html';
                    
                    $(frame).css("width","100%");
                    $(frame).css("height", "400px");
                    break;
                case 1: //Es coach.
                    usrEsCoach = true;
                    //Indicamos que el coach que está logueado va a dirigir el nuevo equipo.
                    elegirCoach();
                    break;
                default: //Inició sesión pero no tiene permiso.
                    $('#contenido').html("<div class='alert alert-danger'>\n\
                                  <strong>Error:</strong> No tiene permiso de acceder a esta página. Será redireccionado en unos segundos.\n\
                                  </div>");
                    setTimeout(function(){ expulsar(); }, 4000);
                    break;
            }
        })
        .fail(function() {
            //Hubo error
            expulsar();
        });
    
    crearModal(false, true, true, true);
    $('[data-toggle="tooltip"]').tooltip();
    $("#btn-submitdos").after(crear_dropdown("Regresar a...", ["<a href='EQUIPOS_VER.html'>Gestión de equipos</a>"]));
});

/**
 * Selecciona el coach que va a dirigir al nuevo equipo.
 * @param {int} id (Opcional) - El ID de la cuenta del coach. Si no lo manda, se elegirá al coach logueado actualmente. 
 */
function elegirCoach(id = null){
    if(idDelCoach !== null && id === idDelCoach)
        return;
    
    //Dejamos nula esta variable antes de hacer el cambio, por si sucede algún error o esta función se ejecuta varias veces seguidas.
    idDelCoach = null;
    
    //Mostramos en pantalla el hecho de que la información del coach se está cargando.
    document.getElementById("coach").value = "Cargando...";
    $("#modal-footer").hide();
    $("#modal-title").html("Cargando coach...");
    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    //Hacemos una petición para obtener el ID (en caso de que no se mande ningún parámetro) y el nombre del nuevo coach.
    $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : (id == null ? "" : id), id : "1", nb_c: "1"}, null, "json")
        .done(function(infoCoach) {
            //Se selecciona el coach usando su información.
            document.getElementById("coach").value = infoCoach["nb_c"];
            idDelCoach = parseInt(infoCoach["id"]);
            $('#modal').modal('hide');
        })
        .fail(function() {
            //Se muestra en pantalla que hubo un error al consultar los datos del coach.
            document.getElementById("coach").value = (usrEsCoach ? "<Error>" : "<Seleccione un coach>");
            
            $("#modal-title").html("Error");
            $("#modal-body").html("No se pudo cargar " + (usrEsCoach ? "su información" : "la información del coach") +  ".");
            $("#modal-body").append("<br>" + (usrEsCoach ? "<a href='javascript:location.reload();'>Recargue la página.</a>" : "Intente seleccionarlo de nuevo."));
            if(usrEsCoach)
                $("#modal-footer").hide();
            else
                $("#modal-footer").show();
        });
}

/**
 * Guarda el nuevo equipo.
 */
function crearEquipo(){
    //Eliminamos espacios innecesarios que puedan estar al inicio o el fin del campo del nombre.
    document.getElementById("nombre").value = $.trim(document.getElementById("nombre").value);
    
    $("#modal-footer").hide();
    $("#modal-title").html("Creando equipo...");
    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    //Comprobamos que el nombre del equipo sea válido.
    if(document.getElementById("nombre").value.length === 0){
        $("#modal-title").html("Error");
        $("#modal-body").html("El nombre es inválido.");
        $("#modal-footer").show();
        return;
    }
    
    //Comprobamos si eligió una imagen de logotipo.
    if(document.getElementById("logotipo").files.length === 0){
        $("#modal-title").html("Error");
        $("#modal-body").html("No ha seleccionado el logotipo.");
        $("#modal-footer").show();
        return;
    }
    
    //Comprobamos que ya se haya seleccionado qué coach va a dirigir el equipo.
    if(idDelCoach === null){
        $("#modal-title").html("Error");
        $("#modal-body").html("No ha seleccionado un coach.");
        $("#modal-footer").show();
        return;
    }
    
    //El objeto que almacena los parámetros que se mandarán en la solicitud PHP.
    var parametros = new FormData();
    //Especificamos la función en el servidor.
    parametros.append("fn", "crear");
    //Agregamos el ID del coach que va dirigir el equipo.
    parametros.append("id", idDelCoach);
    //Agregamos nombre del equipo.
    parametros.append("nb", document.getElementById("nombre").value);
    //Agregamos el archivo seleccionado del logotipo del equipo.
    parametros.append("lg", document.getElementById('logotipo').files[0]);
    
    //Mandamos la orden de crear el equipo al servidor.
    $.ajax({
        url: "../controlador/SRV_EQUIPOS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        processData: false,
        contentType: false,
        success: function (respuesta) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("<center>Equipo creado correctamente<br><a href='EQUIPOS_VER.html'>Volver a la página de gestión de equipos</a></center>");
        },
        error: function (xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor (" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        }
    });
}