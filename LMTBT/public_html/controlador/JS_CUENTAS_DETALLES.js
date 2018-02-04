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
        .done(function(usr) {
            if(parseInt(usr) !== 0){
                id = null;
            }
            
            $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id_c : (id === null ? "" : id), pd : "1"}, null, "json")
                .done(function(res) {
                    var indefinido = "<i style='color : red;'>Sin especificar</i>";
                    
                    $("#nombre").html(res['nb_c']);
                    $("#correo").html(res['cr']);
                    $("#sexo").html((res["sx"] == "M" ? "Masculino" : (res["sx"] == 'F' ? "Femenino" : indefinido)));
                    $("#tipo").html(res['tp']);
                    
                    if(res['ft'] !== null)
                        document.getElementById("foto").src = "data:image/png;base64," + res['ft'];
                    else
                        document.getElementById("foto").src = "img/RC_IF_ANONIMO.png";
                    
                    if(parseInt(usr) === 0){
                        if(id != null)
                            $("#btn_editar").after("<button class=\"btn btn-danger\" onclick=\"eliminarCuenta()\">Eliminar</button>");
                        
                        if(res['ft'] !== null)
                            $("#btn_editar").after("<button class=\"btn btn-warning\" id=\"btn_borrar_ft\" onclick=\"borrarFoto()\">Borrar foto de perfil</button>");
                    }
                    
                    if(res['tp'] == "JUGADOR"){                        
                        if(res['nc'] != null){
                            var nc = new Date((res['nc']).replace(/-/g, '\/'));
                            $("#nacimiento").html((new Date( nc.getTime() + Math.abs(nc.getTimezoneOffset()*60000) )).toLocaleDateString("es-MX", { year: 'numeric', month: 'long', day: 'numeric' }));
                        } else
                            $("#nacimiento").html(indefinido);

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
                        
                        if(Object.keys(res["en"]).length > 0){
                            $("#lista_enfermedades").html("");
                            $.each(res["en"], function (index, i) {
                                $("#lista_enfermedades").append("<li>" + i + "</li>");
                            });
                        }
                        
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

function recargar(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_DETALLES", id);
    location.reload();
}

function irAPaginaDeEdicion(){
    if(id !== null)
        sessionStorage.setItem("CUENTAS_EDICION", id);
    document.location.href = "CUENTAS_EDICION.html";
}

function borrarFoto(){
    $("#modal-footer").hide();
    $("#modal-title").html("Confirmación");
    $("#modal-body").html("¿Está seguro de que desea borrar la foto de perfil de este usuario?<br><br>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-danger\" onclick=\"hacerCambios('Eliminando foto de perfil...', true)\">Si</button>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">No</button>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
}

function eliminarCuenta(){
    $("#modal-footer").hide();
    $("#modal-title").html("Confirmación");
    $("#modal-body").html("¿Está seguro de que desea eliminar esta cuenta? Sus datos no se podrán recuperar.<br><br>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-danger\" onclick=\"hacerCambios('Eliminando cuenta...', false)\">Si</button>");
    $("#modal-body").append("<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">No</button>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
}

function hacerCambios(titulo, del_cuenta_o_foto){
    $("#modal-footer").hide();
    $("#modal-title").html(titulo);
    $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'></center>");
    $('#modal').modal({backdrop: 'static', keyboard: false});
    
    $.post( "../controlador/SRV_CUENTAS.php", {fn : "borrar" + (del_cuenta_o_foto === true ? "_ft" : ""), id : id})
        .done(function(res) {
            if(del_cuenta_o_foto){
                document.getElementById("foto").src = "img/RC_IF_ANONIMO.png";
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