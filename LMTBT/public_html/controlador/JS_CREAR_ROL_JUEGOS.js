var equipos=[];
function Get_Teams(id_convocatoria){
    var team;
    $.ajax({
        url: "../controlador/SRV_OBTENER_EQUIPOS_INSCRITOS_ROL.php",
        data: {convocatoria :id_convocatoria },
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            if(resultado==-1) {
                equipos=null;
                alert("El rol de juegos ya existe o no hay equipos inscritos");
            }
            else  { 
                team=new Array(resultado);
                team=team[0].split(',');
                for(var i=0; i<team.length; i++){
                    equipos[i]=(parseInt(team[i]));
                }
            }
        },
        error: function (jqXHR, textStatus) {
            equipos = null;
            alert("Ha ocurrido un error al conectarse con el servidor.\nIntentelo de nuevo mas tarde.")
        }       
    });
}
function CREAR_ROL_JUEGOS(id_convocatoria){//es funcion es la que se debe llamar cuando cierre la convocatoria, recibe de parametro el id de la convocatoria
    Get_Teams(id_convocatoria);
    setTimeout(function(){Made_Round_Teams(id_convocatoria);},1000);
}
function Made_Round_Teams(id_convocatoria){
   if(equipos!=null){
       var auxT=equipos.length;
       var impar=auxT%2;
       if(impar!=0){
        equipos.push(0);// si el numero de equipos es impar aqui se agrega un equipo extra (cero) que es el BAY
        ++auxT;
       }
       var totalP=(auxT*(auxT-1))/2;//total de partidos del rol de juegos conforme el num de equipos
       var local=[totalP];
       var visita=[totalP];
       var modIF=(auxT/2);
       var indiceInverso=auxT-2;
       var i,j;
       for(i=0;i<totalP;i++){
           if (i%modIF===0){
                if(i%2===0){
                    local[i]=equipos[i%(auxT-1)];
                    visita[i]=equipos[auxT-1];
                }
                else{
                    local[i]=equipos[auxT-1];
                    visita[i]=equipos[i%(auxT-1)];
                }
            }
            else{
                local[i]=equipos[i%(auxT-1)];
                visita[i]=equipos[indiceInverso];           
                --indiceInverso;
                if (indiceInverso<0){
                    indiceInverso=auxT-2;
                }
            }
        }
        Insert_Round(local,visita,auxT,id_convocatoria);
   }
}

function Insert_Round(locales,visita,tam,id_convocatoria){
    if(equipos!=null){
        var jsonLocal = JSON.stringify(locales);
        var jsonVisita = JSON.stringify(visita);
        $.ajax({
            url: "../controlador/SRV_INSERTAR_ROL_JUEGOS.php",
            data: {convocatoria:id_convocatoria,
                   local:jsonLocal,
                   visitante:jsonVisita,
                   tam:tam },
            type: "POST",
            cache: false,
            success: function (resultado){            
                modificar_torneo_rol_generado(id_convocatoria);
                modificar_torneo_activo(id_convocatoria);
            },
            error: function (jqXHR, textStatus) {
                alert("Ha ocurrido un error al guardar la informacion. Intentelo de nuevo mas tarde.");
            }       
    });
}
}
function modificar_torneo_rol_generado(id_convocatoria){        
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"modificar_torneo_rol_generado",
            id:id_convocatoria
        },
        type: "POST",
        datatype: "text",
        success: function(resultado){            
            if(resultado == "ok"){
                alert("cambio realizado con exito");
            }
        },
        error: function(jqXHR, textStatus) {
           alert("Error de ajax");
        }
    });
}
function modificar_torneo_activo(id_convocatoria){        
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"modificar_torneo_activo",
            id:id_convocatoria
        },
        type: "POST",
        datatype: "text",
        success: function(resultado){            
            if(resultado == "ok"){
                alert("cambio realizado con exito");
            }
        },
        error: function(jqXHR, textStatus) {
           alert("Error de ajax");
        }
    });
}
