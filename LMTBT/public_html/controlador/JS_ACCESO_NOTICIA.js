/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){   
   //accesoNoticia();
});
function accesoNoticia(){
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {"tipo": "acceso_crear_noticias"},
        type: "POST",
        datatype: "text",
        success: function (respuesta) {
            if (respuesta != "ok"){
                window.location.replace("index.php");
            }
        },
        error: function (jqXHR, textStatus) {
        }
        });
}

