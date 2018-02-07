/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function ejecutar_recuperar_noticias(){
    recuperar_baner();
    recuperar_noticias(0);
    
}
function recuperar_baner(){
       $.ajax({
        url: "../controlador/SRV_RECUPERAR_BANNER.php",       
         data: {},
        type: "POST",
        datatype: "text",
            beforeSend: function (xhr){
                
               
                $('#titulo1').empty();
                $('#titulo2').empty();
                $('#titulo3').empty(); 
                $('.banner').css('background-image','url(./images/baner.gif)');
            },
            success: function (respuesta){ 
                contenido = JSON.parse(respuesta);
                $('#titulo1').append(contenido[0]);
                $('#titulo2').append(contenido[1]);  
                $('#titulo3').append(contenido[2]);
                $('.banner').css('background-image','url('+contenido[3]+')' ); 
            },
            error: function (jqXHR, textStatus) {                
            }
    });
    
   
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