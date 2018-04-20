// Controlador de la página para ver los detalles de una cuenta.

/* ID de la cuenta cuyos detalles se consultan.
   Se queda nulo si el usuario que abrió la página quiere ver los datos de su propia cuenta. */
var id;

$(document).ready(function() {/* ID de la cuenta cuyos detalles se consultan.
   Se queda nulo si el usuario que abrió la página quiere ver los datos de su propia cuenta. */
    //Desde CUENTAS_GESTION, es probable que se nos manda el ID de la cuenta a consultar.
    id = sessionStorage.getItem("CUENTAS_DETALLES");
    if(id !== null) sessionStorage.removeItem("CUENTAS_DETALLES");
    
    //Creamos un modal de Bootstrap.
    crearModal(false,true,true,true);
    $("#modal-footer").hide();
    $("#modal-title").html("Cargando información...");
    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    //Consultamos si el que está logueado es un administrador.
    $.post( "../controlador/SRV_SESION_GET.php", {tipos : ["ADMINISTRADOR"]}, null, "text")
        .done(function(usr) {
            if(parseInt(usr) !== 0){
                id = null;
            }
            
            //Hacemos una petición para obtener la información de la cuenta.
            $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : (id === null ? "" : id), pd : "1"}, null, "json")
                .done(function(res) {
                    //Una vez que obtenemos la respuesta (en la variables "res"), mostramos la información de la cuenta en pantalla.
                    var indefinido = "<i style='color : red;'>Sin especificar</i>";
                    
                    $("#nombre").html(res['nb_c']);
                    $("#correo").html(res['cr']);
                    $("#sexo").html((res["sx"] == "M" ? "Masculino" : (res["sx"] == 'F' ? "Femenino" : indefinido)));
                    $("#tipo").html(res['tp']);
                    
                    if(res['ft'] !== null)
                        document.getElementById("foto").src = "data:image/png;base64," + res['ft'];
                    else
                        document.getElementById("foto").src = "../modelo/img/RC_IF_ANONIMO.png";
                    
                    if(parseInt(usr) === 0){
                        //Si un administrador abrió la página, mostramos sus opciones exclusivas. 
                        if(id != null)
                            $("#btn_editar").after("<button class=\"btn btn-danger\" onclick=\"eliminarCuenta()\">Eliminar</button>");
                        
                        if(res['ft'] !== null)
                            $("#btn_editar").after("<button class=\"btn btn-warning\" id=\"btn_borrar_ft\" onclick=\"borrarFoto()\">Borrar foto de perfil</button>");
                    }
                    
                    if(res['tp'] == "JUGADOR"){
                        //Si la cuenta a mostrar es de un jugador, cargamos sus datos exclusivos (como fecha de nacimiento).
                        
                        //Se carga la fecha de nacimiento.
                        if(res['nc'] != null){
                            var nc = new Date((res['nc']).replace(/-/g, '\/'));
                            $("#nacimiento").html((new Date( nc.getTime() + Math.abs(nc.getTimezoneOffset()*60000) )).toLocaleDateString("es-MX", { year: 'numeric', month: 'long', day: 'numeric' }));
                        } else
                            $("#nacimiento").html(indefinido);
                        
                        //Se cargan teléfono, sangre y redes sociales.
                        $("#telefono").html((res["TELEFONO"] != null ? res["TELEFONO"] : indefinido));
                        $("#sangre").html((res["TIPO_SANGRE"] != null ? res["TIPO_SANGRE"] : indefinido));
                        if(res["FACEBOOK"] != null || res["INSTAGRAM"] != null || res["TWITTER"] != null){
                            $("#lista_redes").html("");
                            if(res["FACEBOOK"] != null)
                                $("#lista_redes").append("<li><a href='" + res["FACEBOOK"] + "' target='_blank'>Facebook</a></li>");
                            if(res["TWITTER"] != null)
                                $("#lista_redes").append("<li><a href='" + res["TWITTER"] + "' target='_blank'>Twitter</a></li>");
                            if(res["INSTAGRAM"] != null)
                                $("#lista_redes").append("<li><a href='" + res["INSTAGRAM"] + "' target='_blank'>Instagram</a></li>");
                        }
                        
                        //Se carga la lista de enfermedades que el usuario padece.
                        if(Object.keys(res["en"]).length > 0){
                            $("#lista_enfermedades").html("");
                            $.each(res["en"], function (index, i) {
                                $("#lista_enfermedades").append("<li>" + i + "</li>");
                            });
                        }
                        
                        //Se carga la lista de alergias que el usuario padece.
                        if(Object.keys(res["al"]).length > 0){
                            $("#lista_alergias").html("");
                            $.each(res["al"], function (index, i) {
                                $("#lista_alergias").append("<li>" + i + "</li>");
                            });
                        }
                        
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
 * Recarga la página, en caso de que se de un error al cargar los datos de la cuenta.
 */
function recargar(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_DETALLES", id);
    location.reload();
}

/**
 * Redirecciona a la página para editar la cuenta, enviándole el ID de la cuenta que se muestra actualmente.
 */
function irAPaginaDeEdicion(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_EDICION", id);
    document.location.href = "CUENTAS_EDICION.html";
}

/**
 * Permite al administrador, borrar la foto de perfil de la cuenta.
 * Muestra un mensaje de confirmación y en caso de que usuario decida continuar, se ejecuta la función "hacerCambios".
 */
function borrarFoto(){
    $("#modal-footer").hide();
    $("#modal-title").html("Confirmación");
    $("#modal-body").html("¿Está seguro de que desea borrar la foto de perfil de este usuario?<br><br>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-danger\" onclick=\"hacerCambios(true)\">Si</button>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">No</button>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
}

/**
 * Permite al administrador, borrar la cuenta que se esté mostrando actualmente.
 * Funciona de forma similar a la función "borrarFoto".
 */
function eliminarCuenta(){
    $("#modal-footer").hide();
    $("#modal-title").html("Confirmación");
    $("#modal-body").html("¿Está seguro de que desea eliminar esta cuenta? Sus datos no se podrán recuperar.<br><br>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-danger\" onclick=\"hacerCambios(false)\">Si</button>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">No</button>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
}

/**
 * Efectúa uno de los siguientes cambios en la cuenta:
 * - Borrarla en su totalidad.
 * - Borrar su foto de perfil.
 * Es llamada por las funciones "borrarFoto" y "eliminarCuenta".
 * 
 * @param {type} del_cuenta_o_foto - Indica qué cambio se va a hacer. Si es true se borra la foto, si es false, se borra la cuenta.
 * @return {undefined}
 */
function hacerCambios(del_cuenta_o_foto){
    $("#modal-footer").hide();
    $("#modal-title").html((del_cuenta_o_foto ? "Eliminando foto de perfil..." : "Eliminando cuenta..."));
    $("#modal-body").html("<center><img src='../modelo/img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    $.post( "../controlador/SRV_CUENTAS.php", {fn : "borrar" + (del_cuenta_o_foto === true ? "_ft" : ""), id : id})
        .done(function(res) {
            if(del_cuenta_o_foto){
                document.getElementById("foto").src = "../modelo/img/RC_IF_ANONIMO.png";
                $("#btn_borrar_ft").remove();
                $('#modal').modal('hide');
            } else {
                $("#modal-title").html("Terminado");
                $("#modal-body").html("Cuenta eliminada correctamente.<br><a href='CUENTAS_GESTION.html'>Volver a la página de gestión de cuentas.</a>");
            }
        })
        .fail(function(xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html("Error de servidor. " + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        });
}