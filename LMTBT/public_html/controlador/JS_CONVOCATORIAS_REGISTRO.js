$(document).ready(function(){
   $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR"]}, null, "text")
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
        url: "../controlador/SRV_CONVOCATORIAS_REGISTRO.php",
        type: "POST",
        data:formData,    
        contentType: false,
        processData: false,
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="../modelo/RC_IF_CARGANDO.gif" alt="Flowers in Chania"></center>');
            document.getElementById('btn-submitdos').disabled = true;           
        },
            success: function(resultado){              
                //window.locaton.replace("index.php");
                if(resultado == "ok"){
                    //alert("Registro realizado con exito");
                    //window.location.replace("index.php");
                    registraNoticia(formData);
                    mostrarAlerta("Registro realizado con exito","correcto");
                    document.getElementById('btn-submitdos').disabled = false;
                    document.getElementById('nombre_torneo').value = "";
                    document.getElementById('fecha_cierre_convocatoria').value = "";
                    document.getElementById('fecha_inicio_torneo').value = "";
                    document.getElementById('fecha_fin_torneo').value = "";
                    document.getElementById('categoria').value = "";                    
                    document.getElementById('imagen').value = "";                    
                    setTimeout(mandarAinicio, 5000); 
                    
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

function mandarAinicio(){
    window.location.replace("index.php");
}

function registraNoticia(formData){
    formData.delete('nombre');
    formData.delete('fecha_cierre');
    formData.delete('fecha_inicio');
    formData.delete('fecha_fin');
    formData.delete('fecha_fin');
    formData.delete('categoria');
    var titulo = document.getElementById('nombre_torneo').value;
    var descripcion = "Nuevo torneo de la liga municipal tocho bandera ya puedes inscribirte";
    formData.append('titulo_noticia',titulo);
    formData.append('descripcion',descripcion);
    $.ajax({
        url: "../controlador/SRV_NOTICIAS_REGISTRO.php",       
        data:formData,
        type: "POST",
        contentType: false,
        processData: false,
            beforeSend: function (xhr){                
            },
            success: function (respuesta){                 
            },
            error: function (jqXHR, textStatus) {                                
            }
    });    
}

function validarCampos(){
    if(document.getElementById("nombre_torneo").value.trim().length>0 && document.getElementById("fecha_cierre_convocatoria").value.trim().length>0 && 
       document.getElementById("fecha_inicio_torneo").value.trim().length>0 && document.getElementById("fecha_fin_torneo").value.trim().length>0 && 
       document.getElementById("categoria").value.trim().length>0 && document.getElementById("imagen").value.trim().length>0 ){
        return true;
    }else{
        return false;
    }
}