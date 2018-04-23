
$( document ).ready(function() {   
    getCategorias();     
    (document).getElementById("Categoria").onchange = getTorneo;    
});

function getCategorias(){        
      $.ajax({
        url: "../controlador/SRV_TORNEO_INSCRIPCION.php",
        data: {accion : "getCategorias"},
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {            
        },
        success: function (info) {
            info.trim();
            if(info == '0'){
                $('#alert').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Ha ocurrido un error al recuperar la informacion solicitada. Intentelo de nuevo mas tarde </strong> </div>");
            }
            else if(info == '1'){
                 $('#Categoria').empty();
                 $('#Categoria').append("<option value='none' disabled selected>No se encontraron categorias</option>");
            }
            else{
                $('#Categoria').append(info);
            }
        },
        error: function (jqXHR, textStatus) {
             $('#alert').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde</strong></div>");
        }
    });    
}

function getTorneo(){    
    $('#Torneos').html("<option value='Seleccione' disabled selected>Seleccione Torneo</option>");
    getEquipos();    
    var categoria =  document.getElementById("Categoria").value;           
    $.ajax({
        url: "../controlador/SRV_TORNEO_INSCRIPCION.php",
        data: {accion : "getTorneo",categoria: categoria},
        type: "POST",
        datatype: "text",
        success: function (info) {
            info.trim();
            if(info == '0'){
                 $('#alert').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Ha ocurrido un error al recuperar la informacion solicitada. Intentelo de nuevo mas tarde </strong> </div>");
            }
            else if(info == '1'){
                $('#Torneos').empty();
                 $('#Torneos').append("<option value='none' disabled selected>No se encontraron torneos para esta categoria</option>");
            }
            else{
                     $('#Torneos').append(info);          
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });
}
function getEquipos(){    
    $('#Equipo').html("<option value='Seleccione'>Seleccione Equipo</option>");    
    var categoria =  document.getElementById("Categoria").value;        
    $.ajax({
        url: "../controlador/SRV_TORNEO_INSCRIPCION.php",
        data: {accion : "getEquipos",categoria: categoria},
        type: "POST",
        datatype: "text",
        success: function (info) {
            info.trim();
            
            if(info == '0'){
                 $('#alert').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Ha ocurrido un error al recuperar la informacion solicitada. Intentelo de nuevo mas tarde </strong> </div>");
            }
            else if(info == '1'){
                 $('#Equipo').empty();
                 $('#Equipo').append("<option value='none' disabled selected>No se encontraron equipos</option>");
            }
            else if(info == '2'){
                   $('#body').empty();
                  $('#body').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Acceso denegado: Tipo de usuario no valido</strong> </div>");
                  setTimeout(function(){ window.location.replace("index.html")},1000);
            }
            else if(info == '3'){
                 $('#body').empty();
                  $('#body').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Acceso denegado: Inicie sesion para continuar</strong> </div>");
                  setTimeout(function(){ window.location.replace("CUENTAS_LOGIN.html")},1000);
            }
            else{
                $('#Equipo').append(info);
            }
           
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });           
}
function InscribirEquipo(){
    /*if(document.getElementById("Categoria").value === "Seleccione" || document.getElementById("Torneos").value === "Seleccione" || document.getElementById("Equipo").value === "Seleccione"){
        alert("Faltan Seleccionar Campos");
        
        return;
    }*/
     var id_convocatoria = document.getElementById("Torneos").value;
     var data_categoria = document.getElementById("Categoria").value;
     var data_equipo = document.getElementById("Equipo").value;       
     if(id_convocatoria== "Seleccione" || id_convocatoria== "none" || data_categoria== "Seleccione" || data_categoria== "none" || data_equipo== "Seleccione" || data_equipo== "none"){
         $('#alert').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Error! Introduzca los datos correctos</strong> </div>");
        $('#Torneos').html("<option value='Seleccione' disabled selected>Seleccione Torneo</option>");
        $('#Categoria').html("<option value='Seleccione' disabled selected>Seleccione Categoria</option>");
       $('#Equipo').html("<option value='Seleccione' disabled selected>Seleccione Equipo</option>");
       getCategorias();               
        return;
     }else{                   
        $.ajax({
        url: "../controlador/SRV_TORNEO_INSCRIPCION.php",
        data: {accion : "setTorneo",id_conv: id_convocatoria,id_equi:data_equipo,categoria: data_categoria },
        type: "POST",
        datatype: "text",
        success: function (info) {
             
            if(info == '0'){
               $('#alert').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Ha ocurrido un error al recuperar la informacion solicitada. Intentelo de nuevo mas tarde </strong> </div>");

            }
            else if(info == '1'){
                $('#alert').append("<div class='alert alert-danger alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>No se pudo realizar la inscripcion</strong> </div>");

            }
            else{
                $('#alert').empty();
                $('#alert').append("<div class='alert alert-success alert-dismissable fade in'><a class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Datos guardados correctamente</strong> </div>");
               $('#Torneos').html("<option value='Seleccione' disabled selected>Seleccione Torneo</option>");
                $('#Categoria').html("<option value='Seleccione' disabled selected>Seleccione Categoria</option>");
               $('#Equipo').html("<option value='Seleccione' disabled selected>Seleccione Equipo</option>");
               getEquipos();
                setTimeout(function(){ window.location.href="../vista"},3000);
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
        });                   
     }            
}


