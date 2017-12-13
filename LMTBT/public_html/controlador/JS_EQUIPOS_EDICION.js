//ID del equipo.
var id_e;
//ID del coach original.
var id_c_original;
//ID del coach nuevo (si es que se edita).
var id_c_nuevo;
//Indica si el usuario que abrió la página es coach.
var usr_es_coach;

$(document).ready(function() {
    //Desde EQUIPOS_VER se nos manda el id del equipo a editar.
    id_e = sessionStorage.getItem("EQUIPOS_EDICION");
    if(id_e !== null) sessionStorage.removeItem("EQUIPOS_EDICION");
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                case 1:
                    usr_es_coach = (parseInt(res) === 1);
                    crearModal(false,true,true,true);
                    $("#modal-footer").hide();
                    
                    if(id_e !== null){
                        $("#modal-title").html("Cargando información...");
                        $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'><center>");
                        $('#modal').modal({backdrop: 'static', keyboard: false});
                        
                        $.post( "../controlador/SRV_EQUIPOS.php", {fn : "get", id : id_e, id_c : "1", nb_e : "1", nb_c : "1"}, null, "json")
                            .done(function(res) {                                
                                inicializar_input_edicion(document.getElementById("nombre"), res["nb_e"]);
                                inicializar_input_edicion(document.getElementById("coach"), res["nb_c"]);
                                id_c_original = id_c_nuevo = parseInt(res["id_c"]);
                                
                                //Cargamos un frame para que pueda elegir a un coach.
                                var frame = document.createElement("IFRAME");
                                document.getElementById("campos").appendChild(frame);
                                var onload = setInterval(function() {
                                    var frameDoc = frame.contentDocument || frame.contentWindow.document;
                                    if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                                        clearInterval(onload);
                                        frame.contentWindow.inicializar("COACH", "Elegir", "elegirCoach");
                                    }
                                }, 500);
                                frame.src = 'CUENTAS_BUSQUEDA.html';
                                
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
                        $('#modal').modal({backdrop: 'static', keyboard: false});
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

function elegirCoach(id = null){
    if(id === id_c_nuevo || (usr_es_coach == false && id == null))
        return;
    
    id_c_nuevo = null;
    document.getElementById("coach").value = "Cargando...";
    $("#coach").css("background-color", "#ffbbb2");
    
    $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id : "1", nombre: "1", idCuenta : (id == null ? "" : id)}, null, "json")
        .done(function(res) {
            document.getElementById("coach").value = res["APELLIDO_PATERNO"] + " " + res["APELLIDO_MATERNO"] + " " + res["NOMBRE"];
            id_c_nuevo = parseInt(res["ID_USUARIO"]);
        })
        .fail(function() {
            $("#modal-footer").show();
            $("#modal-title").html("Error");
            $("#modal-body").html("No se pudo cargar la información del nuevo coach.<br>Se restauró al coach original.");
            $('#modal').modal({backdrop: 'static', keyboard: false});
            
            id_c_nuevo = id_c_original;
            document.getElementById("coach").value = document.getElementById("coach").defaultValue;
        })
        .always(function() {
            $("#coach").trigger("input");
        });;
}

function recargar(){
    sessionStorage.setItem("EQUIPOS_EDICION", id_e);
    location.reload();
}

function irAPaginaDeDetalles(){
    sessionStorage.setItem("EQUIPOS_DETALLES", id_e);
    document.location.href = "EQUIPOS_DETALLES.html";
}

function guardarCambios(){
    document.getElementById("nombre").value = $.trim(document.getElementById("nombre").value);
    
    $("#modal-footer").show();
    $("#modal-title").html("Error");
    
    if(document.getElementById("nombre").value.length === 0){
        $("#modal-body").html("El campo de nombre está vacío.");
        $('#modal').modal();
        return;
    }
    
    var se_modifica_el_nombre = (document.getElementById("nombre").value != document.getElementById("nombre").defaultValue);
    var se_modifica_el_logo = (document.getElementById("logotipo").files.length !== 0);
    
    if(!se_modifica_el_nombre && !se_modifica_el_logo && id_c_nuevo == id_c_original){
        $("#modal-body").html("No ha hecho ningún cambio.");
        $('#modal').modal();
        return;
    }
    
    var parametros = new FormData();
    parametros.append("fn", "mod");
    parametros.append("id_e", id_e);
    
    if(se_modifica_el_nombre)
        parametros.append("nb", document.getElementById("nombre").value);
    if(se_modifica_el_logo)
        parametros.append("lg", document.getElementById('logotipo').files[0]);
    if(id_c_nuevo != id_c_original){
        if(confirm("Si continua, " + (usr_es_coach ? "usted" : "el coach anterior") +
                " dejará de tener acceso al equipo.\n¿Está seguro de que desea continuar?"))
            parametros.append("id_c", id_c_nuevo);
        else
            return;
    }
    
    $.ajax({
        url: "../controlador/SRV_EQUIPOS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        processData: false,
        contentType: false,
        beforeSend: function (xhr) {
            $("#modal-footer").hide();
            $("#modal-title").html("Procesando");
            $("#modal-body").html("<center><img src='img/RC_IF_CARGANDO.gif'><center>");
            $('#modal').modal({backdrop: 'static', keyboard: false});
        },
        success: function (respuesta) {
            $("#modal-title").html("Terminado");
            $("#modal-body").html("<center>Equipo modificado correctamente<br><a href='EQUIPOS_VER.html'>Volver a la página de gestión de equipos</a>" +
                (id_c_nuevo === id_c_original ? "<br><a href='javascript:irAPaginaDeDetalles();'>Volver a la página de detalles de este equipo</a>" : "") + "<center>");
        },
        error: function (xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor (" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        }
    });
}