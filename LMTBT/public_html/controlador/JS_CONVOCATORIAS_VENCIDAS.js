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
    var ejecutar = setInterval(function(){actualizarTabla()},5000);
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
function abrirPantallaParaEditarConsulta(id){
    $('#tituloVentanaEmergente').empty();
    $('#tituloVentanaEmergente').append("Modificar fecha de cierre");
    var fila = (document).getElementById(id).childNodes;   
    document.getElementById("formulario").onsubmit = function(){
        editarFecha(id);
        return false;
    };
    //Mostramos la ventana emergente
    $('#ventanaEmergente').modal();
}
function editarFecha(id){
    $('#ventanaEmergente').modal('hide');
    var nueva_fecha = document.getElementById("nueva_fecha").value;
    $.ajax({
        url: "../controlador/SRV_CONSULTA_CONVOCATORIA.php",
        data:{
            tipo:"modificar",
            nueva_fecha:nueva_fecha,
            id:id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="images/cargando_naranja.gif" alt="Flowers in Chania"></center>');
        },
        success: function(resultado) {
            if(resultado == "ok"){               
                //alert("Cambio realizado con exito");
                mostrarAlerta("Cambio realizado con exito","correcto");                
            }else{             
                //alert(resultado);
                mostrarAlerta(resultado,"fallido"); 
            }       
        },
        error: function(jqXHR, textStatus) {
           //alert("Error de ajax");
           mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.","fallido");
        }
    });
}