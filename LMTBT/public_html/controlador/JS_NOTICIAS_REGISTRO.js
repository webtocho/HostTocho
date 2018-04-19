$( document ).ready(function() {
      $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR","FOTOGRAFO"]}, null, "text")
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
        url: "../controlador/SRV_NOTICIAS_REGISTRO.php",
        type: "POST",
        data:formData,
        contentType: false,
        processData: false,
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="../modelo/RC_IF_CARGANDO.gif"></center>');
            document.getElementById('enviar').disabled = true;
        },
            success: function(resultado){
                //window.locaton.replace("index.html");
                if(resultado == "ok"){
                    mostrarAlerta("Registro realizado con exito","correcto");
                    document.getElementById('enviar').disabled = false;
                    document.getElementById('titulo').value = "";
                    document.getElementById('descripcion_noticias').value = "";                    
                    document.getElementById('imagen').value = "";                      
                    setTimeout(mandarAinicio, 5000);
                    //window.location.replace("index.html");
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
function mandarAinicio(){
    window.location.replace("index.html");
}
function comprobar_datos(){
    if(document.getElementById("titulo").value.trim().length>0 && document.getElementById("descripcion_noticias").value.trim().length>0 &&
       document.getElementById("imagen").value.trim().length>0 ){
        return true;
    }else{
        return false;
    }
}