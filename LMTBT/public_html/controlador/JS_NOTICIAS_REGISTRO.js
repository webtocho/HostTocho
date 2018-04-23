$( document ).ready(function() {
      /*
       * Hacemos una petición para obtener la información de la cuenta y asi saber si tiene
       * los permisos necesarios para acceder de lo contrario sera expulsado de la pagina.
       */
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
/**
 * Recupera los datos del formulario con la información de la noticia a registrarse y los
 * envia al servidor para ser almacenados y asi lanzar una nueva noticia en el inicio.
 */
$(document).on('submit','#form_noticias',function(event){
    //Previene que la pagina se recargue para evitar errores
    event.preventDefault();
    //Recupera los datos del formulario
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
            $('#alertaSucces').append('<center><img src="../modelo/img/RC_IF_CARGANDO.gif"></center>');
            document.getElementById('enviar').disabled = true;
        },
        //Si el resultado es correcto se notifica y limpia el formulario para un nuevo registro
            success: function(resultado){                
                if(resultado == "ok"){
                    mostrarAlerta("Registro realizado con éxito","correcto");
                    document.getElementById('enviar').disabled = false;
                    document.getElementById('titulo').value = "";
                    document.getElementById('descripcion_noticias').value = "";                    
                    document.getElementById('imagen').value = "";                      
                    setTimeout(mandarAinicio, 5000);                   
                }else{                    
                    mostrarAlerta(resultado,"fallido");
                    document.getElementById('enviar').disabled = false;
                }
            },
            error: function(jqXHR, textStatus) {
               mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Inténtelo de nuevo más tarde.","fallido");
               document.getElementById('enviar').disabled = false;
            }
    });
    }else{       
        mostrarAlerta("Debes de llenar todos los campos","fallido");
    }
});
//Reedirecciona a la pagina de inicio
function mandarAinicio(){
    window.location.replace("index.html");
}
/**
 * Recupera los datos del formulario de noticias y evalua que estos no esten vacios
 * @returns {boolean} regresa true si los datos no estan vacios de lo contrario nos regresa false
 */
function comprobar_datos(){
    if(document.getElementById("titulo").value.trim().length>0 && document.getElementById("descripcion_noticias").value.trim().length>0 &&
       document.getElementById("imagen").value.trim().length>0 ){
        return true;
    }else{
        return false;
    }
}