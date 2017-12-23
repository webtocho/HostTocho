/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).ready(function(){ 
    cargarNoticia();
});

function cargarNoticia(){
    id = sessionStorage.getItem("idNoticia");
    var contenido;
     $.ajax({
            url: "../controlador/SRV_VER_NOTICIA.php",
            data: {tipo: "get",id:id},
            type: "POST",
            datatype: "text",
            success: function (respuesta) {
                 $('#titulo').empty();
                 $('#cuerpo').empty();
                contenido = JSON.parse(respuesta);
                 $('#titulo').append(contenido[0]);
                 $('#cuerpo').append(contenido[1]);
            },
            error: function (jqXHR, textStatus) {
                alert("error obtener");
            }
        });
      
}
