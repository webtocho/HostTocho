/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){
   $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR"]}, null, "text")
        .done(function(res){
            switch(parseInt(res)){
                case 0:
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
$(document).on('submit','#formlg',function(event){
    event.preventDefault();
    var formData = new FormData($('#formlg')[0]);    
    if(validarCampos() === true){
     $.ajax({
        url: "../controlador/SRV_REGISTRO_CONVOCATORIA.php",
        type: "POST",
        data:formData,    
        contentType: false,
        processData: false,
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="images/cargando_naranja.gif" alt="Flowers in Chania"></center>');
            document.getElementById('btn-submitdos').disabled = true;           
        },
            success: function(resultado){              
                //window.locaton.replace("index.php");
                if(resultado == "ok"){
                    //alert("Registro realizado con exito");
                    //window.location.replace("index.php");
                    mostrarAlerta("Registro realizado con exito","correcto");
                    document.getElementById('btn-submitdos').disabled = false;
                }else{                    
                    mostrarAlerta(resultado,"fallido");
                    document.getElementById('btn-submitdos').disabled = false;
                }
            },
            error: function(jqXHR, textStatus) {
                mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.","fallido");
                document.getElementById('btn-submitdos').disabled = false;
            }
     });
    }else{
        mostrarAlerta("Debes llenar todos los campos","fallido");
    }
});
function validarCampos(){
    if(document.getElementById("nombre_torneo").value.trim().length>0 && document.getElementById("fecha_cierre_convocatoria").value.trim().length>0 && 
       document.getElementById("fecha_inicio_torneo").value.trim().length>0 && document.getElementById("fecha_fin_torneo").value.trim().length>0 && 
       document.getElementById("categoria").value.trim().length>0 && document.getElementById("imagen").value.trim().length>0 ){
        return true;
    }else{
        return false;
    }
}