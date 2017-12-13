var idDelCoach = null;

$(document).ready(function() {
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                    //Es admin
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
                    break;
                case 1:
                    //Es coach
                    elegirCoach();
                    break;
                default:
                    //Inició sesión pero no tiene permiso
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
});

function elegirCoach(id = null){
    if(id === idDelCoach)
        return;
    
    idDelCoach = null;
    document.getElementById("coach").value = "Cargando...";
    
    $.post("../controlador/SRV_CUENTAS.php", {fn : "get_info", id : "1", nombre: "1", idCuenta : (id == null ? "" : id)}, null, "json")
        .done(function(res) {
            document.getElementById("coach").value = res["APELLIDO_PATERNO"] + " " + res["APELLIDO_MATERNO"] + " " + res["NOMBRE"];
            idDelCoach = parseInt(res["ID_USUARIO"]);
        })
        .fail(function() {
            document.getElementById("coach").value = "<Seleccione un coach>";
        });
}

function crearEquipo(){
    document.getElementById("nombre").value = $.trim(document.getElementById("nombre").value);
    
    $("#modal-footer").show();
    $("#modal-title").html("Error");
    
    if(document.getElementById("nombre").value.length === 0){
        $("#modal-body").html("El nombre es inválido.");
        $('#modal').modal();
        return;
    }
    
    if(document.getElementById("logotipo").files.length === 0){
        $("#modal-body").html("No ha seleccionado el logotipo.");
        $('#modal').modal();
        return;
    }
    
    if(idDelCoach === null){
        $("#modal-body").html("No ha seleccionado un coach.");
        $('#modal').modal();
        return;
    }
    
    //El objeto que almacena los parámetros que se mandarán en la solicitud PHP.
    var parametros = new FormData();
    //Especificamos la función en el servidor.
    parametros.append("fn", "crear");
    //Agregamos el id del coach.
    parametros.append("id", idDelCoach);
    //Agregamos nombre del equipo.
    parametros.append("nb", document.getElementById("nombre").value);
    //Agregamos el archivo seleccionado del logotipo del equipo.
    parametros.append("lg", document.getElementById('logotipo').files[0]);
    
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
            $("#modal-body").html("<center>Equipo creado correctamente<br><a href='EQUIPOS_VER.html'>Volver a la página de gestión de equipos</a><center>");
        },
        error: function (xhr, status) {
            $("#modal-title").html("Error");
            $("#modal-body").html((xhr.status == 500 ? xhr.responseText : "Error de servidor (" + xhr.status + " " + status + ")."));
            $("#modal-footer").show();
        }
    });
}