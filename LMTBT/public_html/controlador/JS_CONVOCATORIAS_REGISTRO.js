$(document).ready(function(){
    /*
     * Hacemos una petición para obtener la información de la cuenta y asi saber si tiene
     * los permisos necesarios para acceder de lo contrario sera expulsado de la pagina.
     */
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
    //Se carga los datapicker en los inputs correspondientes para poder registrar una fecha
    $(function () {
      $("#fecha_cierre_convocatoria").datepicker();
    });
    $(function () {
      $("#fecha_inicio_torneo").datepicker();
    });
    $(function () {
      $("#fecha_fin_torneo").datepicker();
    });
});
/**
 * Recupera los datos del formulario con la informacion de la convocatoria a registrarse y los
 * envia al servidor para ser almacenados y lanzar una nueva convocatoria.
 */
$(document).on('submit','#formlg',function(event){
    event.preventDefault();
    //obtenemos los datos del formulario por medio del objeto FormData
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
            $('#alertaSucces').append('<center><img src="../modelo/img/RC_IF_CARGANDO.gif" ></center>');
            document.getElementById('btn-submitdos').disabled = true;           
        },
        //si la respuesta es correcta se notifica y vacia los campos del formulario para un nuevo registro.
            success: function(resultado){                             
                if(resultado == "ok"){                    
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
/**
 * Reedirecciona a la pagina de incio
 */
function mandarAinicio(){
    window.location.replace("index.html");
}
/**
 * Realiza una peticion al servidor y envia los datos de la convocatoria creada para poder
 * registrar una noticia y que puede verse en el inicio
 * @param {FormData} formData Objeto que contiene los datos con la informacion obtenida de la convocatoria creada.
 */
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
/**
 * Recupera los datos del formulario y valida que los datos obtenidos no esten vacios y sean correctos
 * @return {boolean} nos retorna true si los datos ontenidos son correctos y no estan vacios de lo contrario no retorna false.
 */
function validarCampos(){
    if(document.getElementById("nombre_torneo").value.trim().length>0 && document.getElementById("fecha_cierre_convocatoria").value.trim().length>0 && 
       document.getElementById("fecha_inicio_torneo").value.trim().length>0 && document.getElementById("fecha_fin_torneo").value.trim().length>0 && 
       document.getElementById("categoria").value.trim().length>0 && document.getElementById("imagen").value.trim().length>0 ){
        return true;
    }else{
        return false;
    }
}