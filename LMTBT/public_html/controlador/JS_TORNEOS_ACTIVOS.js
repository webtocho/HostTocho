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
        url: "../controlador/SRV_CONSULTA_TORNEOS_ACTIVOS.php",
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
    $('#alertaSucces').modal();
    setTimeout(quitarVentana, 5000);
    $.ajax({
        url: "../controlador/SRV_CONSULTA_TORNEOS_ACTIVOS.php",
        data:{
            tipo:"terminar_torneo",
            id:id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="images/cargando_naranja.gif" alt="Flowers in Chania"></center>');
        },
        success: function(resultado){
            if(resultado == "ok"){
                //alert("El torneo se termino con exito");
                mostrarAlerta("El torneo se termino con exito","correcto");
            }else{     
                mostrarAlerta(resultado,"fallido");
                //alert(resultado);
            }       
        },
        error: function(jqXHR, textStatus) {           
        }
    });    
}
function quitarVentana(){
    $('#alertaSucces').modal('hide');
}


