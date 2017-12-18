
$( document ).ready(function() {
     $('select').material_select();
     getTorneo();
     (document).getElementById("torneo").onchange = getTable;
     
     
});


function getTorneo(){
     $('#torneo').html("<option value='Seleccione' disabled selected>Seleccione Torneo</option>");
      $.ajax({
        url: "../controlador/SRV_ASIGNACION_HORARIOS.php",
        data: {accion : "getTorneo"},
        type: "POST",
        datatype: "text",
        success: function (info) {
           
           info = info.trim()
            if(info == 'Failx'){
                alert("No se Encontro Ningun Torneo");
                
            }
            else{
               
                     
                     $('#torneo').append(info);
                     $('select').material_select();
                  
            }
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });
     $('select').material_select();
}

function getTable(){
    $('#roles').html('<thead><tr><th>Equipo</th><th>VS</th><th>Equipo</th><th>Fecha</th><th>Hora</th><th>Campo</th><th></th> </tr> </thead><tbody id="Rol"></tbody>');
    if(document.getElementById("torneo").value === "Seleccione"){
        alert("Torneo no especificado");
        
        return;
    }
    var id = document.getElementById("torneo").value;
    $.ajax({
        url: "../controlador/SRV_ASIGNACION_HORARIOS.php",
        data: {accion : "getTable",id : id},
        type: "POST",
        datatype: "text",
        success: function (info) {
           info= info.trim();
            
            
            if(info == 'Fail'){
                alert("No se Obtuvieron Registros");
                
            }
           
            else if(info == 'Failx'){
                alert("No hay datos en la tabla ");
               
            }else if(info== '!Session'){
                alert("Inicie Sesion Para continuar");
                window.location.href="CUENTAS_LOGIN.html";
            }
            else if(info=="!Type"){
                alert("Usted no Tiene Permisos");
                window.location.href="index.php";
            }
            else{
                
                  $('#Rol').append(info);
               
            }
            
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });
     
}
    
function editTable(){
//console.log( $('td.edit').parent());var oIDvar oIDint i=0;
var i=0;
var id_rol;
    $('a.edit').on('click',function(){
        if(i==0){
           id_rol = ($(this).attr("id"));
           console.log(id_rol);
           localStorage.setItem("id_rol",id_rol);
           window.location.href="MODIFICAR_HORARIOS.html";
          i++;
      }
      
    });
    

  
        
    
}



    //total=  $("a.edit");
    //total = $("tr").find("td:").text();
  
 
 


    
