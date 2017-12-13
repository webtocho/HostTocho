/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function ejecutar_recuperar_noticias(){
    var ejecutar = setInterval(function(){recuperar_noticias()},5000);
}
function recuperar_noticias(){
    $.ajax({
        url: "../controlador/SRV_RECUPERAR_NOTICIAS.php",       
        type: "POST",
        datatype: "text",
            beforeSend: function (xhr){
            },
            success: function (respuesta){                
                    $('#slider3').empty();
                    $('#slider3').append(respuesta);
            },
            error: function (jqXHR, textStatus) {
                //alert("Erro al ejecutar");
            }
    });
}