// Controlador de la página para editar los datos de un equipo.

//ID del equipo a editar.
var idEquipo;
//ID del coach original (quien dirige al equipo antes de la edición).
var idCoachViejo;
//ID del coach nuevo (si es que se edita).
var idCoachNuevo;
//Indica si el usuario que abrió la página es coach.
var usrEsCoach;

$(document).ready(function() {
    //Desde EQUIPOS_VER se nos manda el id del equipo a editar.
    idEquipo = sessionStorage.getItem("EQUIPOS_EDICION");
    if(idEquipo !== null) sessionStorage.removeItem("EQUIPOS_EDICION");
    
    $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    usrEsCoach = (parseInt(res) === 1);
                    
                    //Se crea un modal de Bootstrap.
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    $("#modal-title").html("Cargando información...");
                    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
                    $('#modal').modal({backdrop: 'static', keyboard: false});
                    
                    if(idEquipo !== null){
                        //Se hace una petición para obtener los datos del equipo actuales.
                        $.post( "../controlador/SRV_EQUIPOS.php", {fn : "get", id : idEquipo, id_c : "1", nb_e : "1", nb_c : "1"}, null, "json")
                            .done(function(infoEquipo) {
                                //Llenamos los campos con los datos actuales del equipo.
                                inicializar_input_edicion(document.getElementById("nombre"), infoEquipo["nb_e"]);
                                inicializar_input_edicion(document.getElementById("coach"), infoEquipo["nb_c"]);
                                idCoachViejo = idCoachNuevo = parseInt(infoEquipo["id_c"]);
                                
                                //Creamos un iFrame para que pueda elegir a un coach para transferirle la propiedad del equipo.
                                var frame = document.createElement("IFRAME");
                                document.getElementById("campos").appendChild(frame);
                                var onload = setInterval(function() {
                                    var frameDoc = frame.contentDocument || frame.contentWindow.document;
                                    if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                                        clearInterval(onload);
                                        //Indicamos a la página dentro del iFrame ("CUENTAS_BUSQUEDA") que sólo se van a poder buscar coaches.
                                        frame.contentWindow.inicializar("COACH", "Elegir", "elegirCoach");
                                    }
                                }, 500);
                                frame.src = 'CUENTAS_BUSQUEDA.html';
                                $(frame).css("width","100%");
                                $(frame).css("height", "400px");
                                
                                $('#modal').modal('hide');
                            })
                            .fail(function(xhr, status, error) {
                                $("#modal-title").html("Error");
                                $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor (" + xhr.status + " " + status + ")."));
                                $("#modal-body").append("<br><a href='javascript:recargar();'>Volver a intentar</a>");
                                $("#modal-body").append("<br><a href='javascript:irAPaginaDeDetalles();'>Regresar a la página de detalles de este equipo</a>");
                                $("#modal-body").append("<br><a href='EQUIPOS_VER.html'>Regresar a la página de selección de equipo</a>");
                            });
                    } else {
                        $("#modal-title").html("Error");
                        $("#modal-body").html("Es necesario que primero vaya a <a href='EQUIPOS_VER.html'>esta página</a> y seleccione un equipo.");
                    }
                    break;
                default:
                    $('#contenido').html("<div class='alert alert-danger'>\n\
                                  <strong>Error:</strong> No tiene permiso de acceder a esta página. Será redireccionado en unos segundos.\n\
                                  </div>");
                    setTimeout(function(){ expulsar(); }, 4000);
                    return;
            }
        })
        .fail(function() {
            expulsar();
        });
});

/**
 * Selecciona otro coach para que dirija al equipo.
 * @param {int} id - El ID del nuevo dirigente del equipo. Si no se manda, el nuevo dirigente será el coach que tenga su sesión iniciada.
 */
function elegirCoach(id = null){
    if(id === idCoachNuevo || (usrEsCoach == false && id == null))
        return;
    
    idCoachNuevo = null;
    document.getElementById("coach").value = "Cargando...";
    $("#coach").css("background-color", "#ffbbb2");
    
    //Hacemos una petición para obtener la información del nuevo coach.
    $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : (id == null ? "" : id), id : "1", nb_c: "1"}, null, "json")
        .done(function(infoCoachNuevo) {
            //Cambiamos de coach.
            document.getElementById("coach").value = infoCoachNuevo["nb_c"];
            idCoachNuevo = parseInt(infoCoachNuevo["id"]);
        })
        .fail(function() {
            //Se muestra un mensaje de error.
            $("#modal-footer").show();
            $("#modal-title").html("Error");
            $("#modal-body").html("No se pudo cargar la información del nuevo coach.<br>Se restauró al coach original.");
            $('#modal').modal({backdrop: 'static', keyboard: false});
            
            //Restablecemos al coach original.
            idCoachNuevo = idCoachViejo;
            document.getElementById("coach").value = document.getElementById("coach").defaultValue;
        })
        .always(function() {
            $("#coach").trigger("input");
        });;
}

/**
 * Recarga la página, en caso de que se de un error al cargar los datos del equipo.
 */
function recargar(){
    sessionStorage.setItem("EQUIPOS_EDICION", idEquipo);
    location.reload();
}

/**
 * Redirecciona de regreso a la página para ver los detalles del equipo, enviando el ID del mismo.
 */
function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", idEquipo);
    document.location.href = "EQUIPOS_DETALLES.html";
}

/**
 * Guarda los cambios que el usuario haya hecho.
 */
function guardarCambios(){
    document.getElementById("nombre").value = $.trim(document.getElementById("nombre").value);
    
    $("#modal-footer").hide();
    $("#modal-title").html("Guardando cambios...");
    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    //Comprobamos que el nombre del equipo sea válido.
    if(document.getElementById("nombre").value.length === 0){
        $("#modal-title").html("Error");
        $("#modal-body").html("El campo de nombre está vacío.");
        $("#modal-footer").show();
        return;
    }
    
    //Creamos variables booleanas que indican qué cambios se van a modificar.
    var seModificaElNombre = (document.getElementById("nombre").value != document.getElementById("nombre").defaultValue);
    var seModificaElLogo = (document.getElementById("logotipo").files.length !== 0);
    
    //Comprobamos si se va a realizar al menos un cambio.
    if(!seModificaElNombre && !seModificaElLogo && idCoachNuevo == idCoachViejo){
        $("#modal-title").html("Error");
        $("#modal-body").html("No ha hecho ningún cambio.");
        $("#modal-footer").show();
        return;
    }
    
    //Creamos un objeto para los parámetros y le metemos los datos necesarios (entre ellos, los nuevos valores).
    var parametros = new FormData();
    parametros.append("fn", "mod");
    parametros.append("id_e", idEquipo);
    
    if(seModificaElNombre)
        parametros.append("nb", document.getElementById("nombre").value);
    if(seModificaElLogo)
        parametros.append("lg", document.getElementById('logotipo').files[0]);
    if(idCoachNuevo != idCoachViejo){
        if(confirm("Si continua, " + (usrEsCoach ? "usted" : "el coach anterior") +
                " dejará de tener acceso al equipo.\n¿Está seguro de que desea continuar?"))
            parametros.append("id_c", idCoachNuevo);
        else
            return;
    }
    
    //Se hace una petición al servidor para guardar los datos.
    $.ajax({
        url: "../controlador/SRV_EQUIPOS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        processData: false,
        contentType: false,
        success: function (respuesta) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("<center>Equipo modificado correctamente<br><a href='EQUIPOS_VER.html'>Volver a la página de gestión de equipos</a>" +
                (idCoachNuevo === idCoachViejo ? "<br><a href='javascript:irAPaginaDeDetalles();'>Volver a la página de detalles de este equipo</a>" : "") + "<center>");
        },
        error: function (xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor (" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        }
    });
}