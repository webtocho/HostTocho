/*
 * se obtiene la sesion iniciada, en el caso de no tener una sesion activa, no se podra
 * agregar comentarios, solo verlos
 */
$(document).ready(function(){
      $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR","COACH","JUGADOR","FOTOGRAFO","CAPTURISTA"]}, null, "text")
        .done(function(res) {
        })
        .fail(function() {
           noRegistrado();
            debug =0;
        });
        
    cargarNoticia();
    cargarComentarios();
});


/*
 * 
 * si no hay una sesion iniciada no activara la caja de comentarios
 */
function noRegistrado(){
     $('#cajaDeComentarios').empty();
}
/*
 * se cargan todos los comentarios de la noticia seleccionada y se agregan a los div correspondientes
 */
function cargarComentarios(){
    id = sessionStorage.getItem("idNoticia");
     $.ajax({
            url: "../controlador/SRV_NOTICIAS_VER.php",
            data: {tipo: "cargarComentarios",id:id},
            type: "POST",
            datatype: "text",
             beforeSend: function (xhr){
                 $('#comentarios').empty();
                  $('#comentarios').append("<img src='../modelo/RC_IF_CARGANDO.gif' >");
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
 /*
  * si la cuenta que entro a la pagina es de tipo administrador, tendra la opcion de eliminar los comentarios
  *este metodo recibe el id del comentario a eliminar
 **/
 
function eliminarComentario(id){
     $.ajax({
            url: "../controlador/SRV_NOTICIAS_VER.php",
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

/*
 * la variable caracteresTotal nos dice el maximo de caracteres que se permiten en un comentario
 */
var caracteresTotal =200;
/*
 * esta fancion valida que el comentario no se pase del numero de caracteres definido
 * al llegar al maximo de caracteres, ya no se deja agregar uno mas
 */
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
/*
 * este metodo valida cualquier tipo de inyeccion a la base de datos y que el comentario contenga texto
 * una ves se valide todo, se manda a insertar con el id de la noticia
 */
function comentar(){
   
    id = sessionStorage.getItem("idNoticia");
    var texto =  $('#comment').val();
    texto = texto.replace(/</g, "&lt;").replace(/>/g, "&gt;");
    if(texto.trim().length>1){
        $.ajax({
            url: "../controlador/SRV_NOTICIAS_VER.php",
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
/*
 * esta funcion carga la noticia y su contenido multimedia en la pagina
 */
function cargarNoticia(){
    id = sessionStorage.getItem("idNoticia");
    if(id==null){
        expulsar();
    }
    
    var contenido;
     $.ajax({
            url: "../controlador/SRV_NOTICIAS_VER.php",
            data: {tipo: "get",id:id},
            type: "POST",
            datatype: "text",
            beforeSend: function (xhr){
                 $('#cuerpo').empty();
                 $('#cuerpo').append("<img src='../modelo/RC_IF_CARGANDO.gif' >");
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
