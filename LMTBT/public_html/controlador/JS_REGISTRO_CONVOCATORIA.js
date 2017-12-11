/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*$(document).ready(function(){
   accesoConvocatoria();
});
function accesoConvocatoria(){
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {"tipo": "acceso_convocatoria"},
        type: "POST",
        datatype: "text",
        success: function (respuesta) {
            if (respuesta === "no"){
                window.location.replace("index.php");
            }
        },
        error: function (jqXHR, textStatus) {
        }
        });
}*/
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
            success: function(resultado){              
                //window.locaton.replace("index.php");
                if(resultado == "ok"){
                    alert("Registro realizado con exito");
                    window.location.replace("index.php");
                }else{
                    //alert(resultado);
                }
            },
            error: function(jqXHR, textStatus) {               
                //alert("No se ejecuto");
            }
     });
    }else{
        alert("Debes llenar todos los campos del formulario");
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