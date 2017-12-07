$(document).ready(function(){
    mostrar_convocatorias_activas();
});

function lista_convocatorias_inactivas(){
     var id_convocatoria = document.getElementById("lista_convocatorias_inactivas").value;
     if(id_convocatoria != -1){
        $.ajax({
               url: "../controlador/SRV_CONSULTAS.php",
               data: {tipo: "roles_juegos_convocatoria_seleccionada",
                          id_convocatoria:id_convocatoria     
               },
               type: "POST",
               datatype: "text",
               beforeSend: function (xhr) {},
               success: function (respuesta) {  
                    if(respuesta != "no"){
                        $('#roles_juegos_convocatoria_seleccionada').html(respuesta);  
                    } else {
                        document.location.href = "index.php";
                    }
            },
            error: function (jqXHR, textStatus) {}
        });
    }
}
function mostrar_convocatorias_activas(){
    $.ajax({
            url: "../controlador/SRV_CONSULTAS.php",
            data: {"tipo": "lista_convocatorias_inactivas"},
            type: "POST",
            datatype: "text",
            beforeSend: function (xhr) {},
            success: function (respuesta) {   
                 if (respuesta != "no") {
                        $('#lista_convocatorias_inactivas').empty();         
                        $('#lista_convocatorias_inactivas').html(respuesta);  
                    } else if(respuesta === "no"){
                        document.location.href = "index.php";                              
                    }
            },
            error: function (jqXHR, textStatus) {
            }
    });
}

