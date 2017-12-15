/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$( document ).ready(function() {
      $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR","FOTOGRAFO"]}, null, "text")
        .done(function(res){
            switch(parseInt(res)){
                case 0:
                break;
                case 1:
                break;
                default:
                expulsar();
                return;
            }
        })
        .fail(function() {
            expulsar();
        });
});
$(document).on('submit','#form_noticias',function(event){
    event.preventDefault();
    var formData = new FormData($('#form_noticias')[0]);
    if(comprobar_datos() == true){
    $.ajax({
        url: "../controlador/SRV_REGISTRO_NOTICIAS.php",
        type: "POST",
        data:formData,
        contentType: false,
        processData: false,
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="images/cargando_naranja.gif" alt="Flowers in Chania"></center>');
            document.getElementById('enviar').disabled = true;
        },
            success: function(resultado){
                //window.locaton.replace("index.php");
                if(resultado == "ok"){                                      
                    mostrarAlerta("Registro realizado con exito","correcto");
                    document.getElementById('enviar').disabled = false;
                    //window.location.replace("index.php");
                }else{                    
                    mostrarAlerta(resultado,"fallido");
                    document.getElementById('enviar').disabled = false;
                }
            },
            error: function(jqXHR, textStatus) {
               mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.","fallido");
               document.getElementById('enviar').disabled = false;
            }
    });
    }else{       
        mostrarAlerta("Debes de llenar todos los campos","fallido");
    }
});
function comprobar_datos(){
    if(document.getElementById("titulo").value.trim().length>0 && document.getElementById("descripcion_noticias").value.trim().length>0 &&
       document.getElementById("imagen").value.trim().length>0 ){
        return true;
    }else{
        return false;
    }
}