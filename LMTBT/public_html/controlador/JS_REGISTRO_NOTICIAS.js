/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function ejecutar_recuperar_noticias(){
    var ejecutar = setInterval(function(){recuperar_noticias()},5000);    
}
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
        },
            success: function(resultado){
                //window.locaton.replace("index.php");
                if(resultado == "ok"){                                      
                    mostrarAlerta("Registro realizado con exito","correcto");
                    //window.location.replace("index.php");
                }else{                    
                    mostrarAlerta(resultado,"fallido");
                }
            },
            error: function(jqXHR, textStatus) {
                //alert("No se ejecuto");
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
function recuperar_noticias(){
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {"tipo": "recuperar_nocicias"},
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