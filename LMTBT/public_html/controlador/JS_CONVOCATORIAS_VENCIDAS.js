/**
 * Realiza el llamado a la funcion actualizarTabla()
 */
function cargar_tabla_convocatorias_vencidas(){
    actualizarTabla();  
}
/**
 * Realiza una peticion al servidor para obtener una lista con las convocatorias lanzadas
 * para poder mostrarselas al administrador en el inicio y que el pueda realizar acciones.
 */
function actualizarTabla(){
    $.ajax({
        url: "../controlador/SRV_CONVOCATORIAS.php",
        data:{
            tipo:"consultar"
        },
        type: "POST",
        datatype: "text",        
        success: function(resultado) {            
            if(resultado == "error"){
                $('#convocatorias_lanzadas').empty();    
                $('#convocatorias_lanzadas').append("<div class='item'><a>No tienes permisos para ver esto</a></div>");
            }else{
                $('#contenidoTabla').empty();
                $('#contenidoTabla').append(resultado);
            }                   
        },
        error: function(jqXHR, textStatus) {           
        }
    });
}