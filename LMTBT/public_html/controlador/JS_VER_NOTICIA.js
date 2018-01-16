/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



 
$(document).ready(function(){
      $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR","COACH","JUGADOR","FOTOGRAFO","CAPTURISTA"]}, null, "text")
        .done(function(res) {
        })
        .fail(function() {
           noRegistrado();
            debug =0;
        });
        
    cargarNoticia();
    cargarComentarios();
});

function noRegistrado(){
     $('#cajaDeComentarios').empty();
    
}
function cargarComentarios(){
    id = sessionStorage.getItem("idNoticia");
     $.ajax({
            url: "../controlador/SRV_VER_NOTICIA.php",
            data: {tipo: "cargarComentarios",id:id},
            type: "POST",
            datatype: "text",
             beforeSend: function (xhr){
                 $('#comentarios').empty();
                  $('#comentarios').append("<img src='./images/cargando_naranja.gif' >");
            },
            success: function (respuesta) {
                 $('#comentarios').empty();
                 $('#comentarios').append(respuesta);
                
                 
            },
            error: function (jqXHR, textStatus) {
                alert("error obtener");
            }
        });
    
    
}

function eliminarComentario(id){
     $.ajax({
            url: "../controlador/SRV_VER_NOTICIA.php",
            data: {tipo: "eliminarComent",id:id},
            type: "POST",
            datatype: "text",
            success: function (respuesta) {
                 $('#comment').val("");
                 cargarComentarios();
            },
            error: function (jqXHR, textStatus) {
                alert("error obtener");
            }
        });
    
}

var caracteresTotal =200;
function caracteres(){
     var texto =  $('#comment').val();
     var total = texto.length;
     
     if(caracteresTotal-total<=0){
          texto = texto.substring(0, 120);
          $('#comment').val(texto);
          $('#caracteres').empty();
          $('#caracteres').append("<p>"+(0)+"</p>");
            
     }else{
          $('#caracteres').empty();
          $('#caracteres').append("<p>"+(caracteresTotal-total)+"</p>");
         
    }
}

function comentar(){
   
    id = sessionStorage.getItem("idNoticia");
    var texto =  $('#comment').val();
    texto = texto.replace(/</g, "&lt;").replace(/>/g, "&gt;");
    if(texto.trim().length>1){
        $.ajax({
            url: "../controlador/SRV_VER_NOTICIA.php",
            data: {tipo: "comentar",id:id,texto:texto},
            type: "POST",
            datatype: "text",
            success: function (respuesta) {
                 $('#comment').val("");
                 cargarComentarios();
                 $('#caracteres').empty();
                 $('#caracteres').append("<p>"+(caracteresTotal)+"</p>");
            },
            error: function (jqXHR, textStatus) {
                alert("error obtener");
            }
        });
    }
    
}
function cargarNoticia(){
    id = sessionStorage.getItem("idNoticia");
    if(id==null){
        expulsar();
    }
    
    var contenido;
     $.ajax({
            url: "../controlador/SRV_VER_NOTICIA.php",
            data: {tipo: "get",id:id},
            type: "POST",
            datatype: "text",
            beforeSend: function (xhr){
                 $('#cuerpo').empty();
                 $('#cuerpo').append("<img src='./images/cargando_naranja.gif' >");
            },
            success: function (respuesta) {
                 $('#titulo').empty();
                 $('#cuerpo').empty();
                 $('#imagenes').empty();
                
                contenido = JSON.parse(respuesta);
                
                 $('#titulo').append(contenido[0]);
                 $('#cuerpo').append(contenido[1]);
                 $('#imagenes').append(contenido[2]);
            },
            error: function (jqXHR, textStatus) {
                alert("error obtener");
            }
        });
      
}
