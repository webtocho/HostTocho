/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*$( document ).ready(function() {
    actualizarTabla();
});*/
function cargar_tabla_convocatorias_vencidas(){
    actualizarTabla();
   // var ejecutar = setInterval(function(){actualizarTabla()},5000);
}
//var prro= setInterval(function(){actualizarTabla()},1000);

function actualizarTabla(){
    $.ajax({
        url: "../controlador/SRV_CONSULTA_CONVOCATORIA.php",
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
                $('#contenidoTabla').empty(); //Vaciamos el contenido de la tabla
                $('#contenidoTabla').append(resultado);
            }                   
        },
        error: function(jqXHR, textStatus) {
           //alert("Error de ajax");
        }
    });
}