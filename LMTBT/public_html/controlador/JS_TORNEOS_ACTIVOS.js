/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function cargar_tabla_torneos_activos(){
    var ejecutar = setInterval(function(){actualizar_tabla_torneos_activos()},5000);
}
function actualizar_tabla_torneos_activos(){
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"consultar_torneos_activos"
        },
        type: "POST",
        datatype: "text",
        success: function(resultado) {
            $('#contenido_tabla_torneos').empty(); //Vaciamos el contenido de la tabla
            $('#contenido_tabla_torneos').append(resultado);                         
        },
        error: function(jqXHR, textStatus) {
           //alert("Error de ajax");
        }
    });
}
function abrir_pantalla_para_terminar_torneo(id){
    document.getElementById("botonConfirmacion").onclick = function(){
        terminar_torneo(id);
    };
    $('#ventanaConfirmacion').modal();
}
function terminar_torneo(id){
    $('#ventanaConfirmacion').modal('hide');
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"terminar_torneo",
            id:id
        },
        type: "POST",
        datatype: "text",
        success: function(resultado){
            if(resultado == "ok"){
                alert("El torneo se termino con exito");
            }else{             
                alert(resultado);
            }       
        },
        error: function(jqXHR, textStatus) {
           //alert("Error de ajax");
        }
    });
}


