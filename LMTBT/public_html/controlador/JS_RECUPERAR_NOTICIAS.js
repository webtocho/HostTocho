/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function ejecutar_recuperar_noticias(){
    recuperar_noticias(0);
    //var ejecutar = setInterval(function(){recuperar_noticias()},5000);
}
function recuperar_noticias(linea){
    var fila= parseInt(linea) * 5;
    $.ajax({
        url: "../controlador/SRV_RECUPERAR_NOTICIAS.php",       
         data: {fila: fila},
        type: "POST",
        datatype: "text",
            beforeSend: function (xhr){
                 $('#apartadoNoticia').empty();
                  $('#apartadoNoticia').append("<center><img src='./images/cargando_naranja.gif' ></center>");
            },
            success: function (respuesta){                
                   // $('#slider3').empty();
                   // $('#slider3').append(respuesta);
                   
                    $('#apartadoNoticia').empty();
                    $('#apartadoNoticia').append(respuesta);
                   
            },
            error: function (jqXHR, textStatus) {                
            }
    });
}