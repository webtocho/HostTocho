
$( document ).ready(function() {
   
     (document).getElementById("Categoria").onchange = getTorneo;
     getEquipos();
});



function getTorneo(){
    
     $('#Torneos').html("<option value='Seleccione'>Seleccione</option>");
    if(document.getElementById("Categoria").value === "Seleccione"){
        alert("Categoria no especificada");
        
        return;
    }
    

    var categoria =  document.getElementById("Categoria").value;
    
    
    $.ajax({
        url: "../controlador/SRV_TORNEO_INSCRIPCION.php",
        data: {accion : "getTorneo",categoria: categoria},
        type: "POST",
        datatype: "text",
        success: function (info) {
             
            if(info == 'Fail'){
                alert("No se Encontro Ningun Torneo para la Categoria Seleccionada")
            }
            else{
                //console.log(info);
                var content = JSON.parse(info);
                
                jQuery.each(content, function(i, val) {
                     
                     $('#Torneos').append("<option value='"+val.ID_CONVOCATORIA+"'>"+val.NOMBRE_TORNEO+"</option>");
                     
                  });
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });
}

function getEquipos(){
    
    $('#Equipo').html("<option value='Seleccione'>Seleccione</option>");
   
    
    $.ajax({
        url: "../controlador/SRV_TORNEO_INSCRIPCION.php",
        data: {accion : "getEquipos"},
        type: "POST",
        datatype: "text",
        success: function (info) {
             
            if(info == 'Fail'){
                $('#Equipo').html("<option>No se encontro ningun equipo registrado</option>");
            }
            else if(info =='!Session'){
                alert("Debe Iniciar Sesion");
                window.location.href = "../vista/CUENTAS_LOGIN.HTML";

            }else if (info=='!Type'){
                alert("No se cuenta con los permisos necesarios");
                window.location.href = "../vista";
            }
            else{
                
                var content = JSON.parse(info);
                
                jQuery.each(content, function(i, val) {
                     
                     $('#Equipo').append("<option value='"+val.ID_EQUIPO+"'>"+val.NOMBRE_EQUIPO+"</option>");
                     
                  });
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });
    
   
    
}

function InscribirEquipo(){
    if(document.getElementById("Categoria").value === "Seleccione" || document.getElementById("Torneos").value === "Seleccione" || document.getElementById("Equipo").value === "Seleccione"){
        alert("Faltan Seleccionar Campos");
        
        return;
    }else{
        var id_convocatoria = document.getElementById("Torneos").value;
        var data_categoria = document.getElementById("Categoria").value;
        var data_equipo = document.getElementById("Equipo").value;
        
        
        
        $.ajax({
        url: "../controlador/SRV_TORNEO_INSCRIPCION.php",
        data: {accion : "setTorneo",id_conv: id_convocatoria,id_equi:data_equipo,categoria: data_categoria },
        type: "POST",
        datatype: "text",
        success: function (info) {
             
            if(info == 'Fail'){
               alert("Los datos no fueron registrados")
            }
            else{
                alert("Datos Guardados");
                 $('#Torneos > option[value="Seleccione"]').attr('selected', 'selected');
                $('#Categoria > option[value="Seleccione"]').attr('selected', 'selected');
               
                $('#Equipo > option[value="Seleccione"]').attr('selected', 'selected');
                window.location.href = "../vista";
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
        });
        
        
    }
    
    
    
    
}