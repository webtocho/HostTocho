/**
 * Realiza el llamado a la funcion actualizar_tabla_torneos_activos();
 */
function cargar_tabla_torneos_activos(){
    actualizar_tabla_torneos_activos();
}
/**
 * Realiza una peticion al servidor y recupera todos los torneos activos para
 * visualizarlos en una tabla en el inicio y poder realizar acciones
 */
function actualizar_tabla_torneos_activos(){
    $.ajax({
        url: "../controlador/SRV_TORNEOS_ACTIVOS.php",
        data:{
            tipo:"consultar_torneos_activos"
        },
        type: "POST",
        datatype: "text",
        success: function(resultado) {
            if(resultado == "error"){
                $('#torneos_activos').empty();
                $('#torneos_activos').append("<div class='item'><a>No tienes permisos para ver esto</a></div>");
            }else{
                $('#contenido_tabla_torneos').empty(); //Vaciamos el contenido de la tabla
                $('#contenido_tabla_torneos').append(resultado);                         
            }
        },
        error: function(jqXHR, textStatus) { 
        }
    });
}
/**
 * Despliega un ventana modal o pantalla emergente para poder dar por terminado un torneo
 * @param {string} id es el identificador del torneo que se ha seleccionado
 */
function abrir_pantalla_para_terminar_torneo(id){
    document.getElementById("botonConfirmacion").onclick = function(){
        terminar_torneo(id);
    };
    $('#ventanaConfirmacion').modal();
}
/**
 * Realiza una peticion al servidor para poder dar por concluido un torneo
 * @param {string} id es el identificador de la convocatoria, para saber cuál se ha seleccionado.
 */
function terminar_torneo(id){
    $('#ventanaConfirmacion').modal('hide');
    $('#alertaSucces').modal();
    setTimeout(quitarVentana, 5000);
    //Se realiza la peticion al servidor
    $.ajax({
        url: "../controlador/SRV_TORNEOS_ACTIVOS.php",
        data:{
            tipo:"terminar_torneo",
            id:id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="../modelo/img/RC_IF_CARGANDO.gif" ></center>');
        },
        success: function(resultado){
            if(resultado == "ok"){                
                mostrarAlerta("El torneo se termino con éxito","correcto");
            }else{     
                mostrarAlerta(resultado,"fallido");                
            }       
        },
        error: function(jqXHR, textStatus) {           
        }
    });    
}
/**
 * Quita la ventana de alerta que aparece en el inicio en donde se muestra la respuesta dada por el servidor
 */
function quitarVentana(){
    $('#alertaSucces').modal('hide');
}