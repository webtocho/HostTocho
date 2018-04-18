var equipos=[];

function Get_Teams(id_convocatoria){
    //Funcion para obtener los equipos inscritos a una convocatoria
    var team;
    $.ajax({//hacemos la peticion mediante ajax
        url: "../controlador/SRV_ROLES_JUEGO_OBTENER_EQUIPOS_INSCRITOS.php",
        data: {convocatoria :id_convocatoria },
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            if(resultado==-1) {
                equipos=null;//si nos retorna -1 es que en la convocatoria no se han inscrito equipos
                alert("El rol de juegos ya existe o no hay equipos inscritos");
            }
            else  { 
                team=new Array(resultado);//si nos regresa otra cosa generamos un arreglo con los equipos
                team=team[0].split(',');//generamis bien el arreglo temporal
                for(var i=0; i<team.length; i++){
                    equipos[i]=(parseInt(team[i]));//vamos agregando el id de los equipos al arreglo de equipos
                }
            }
        },
        error: function (jqXHR, textStatus) {
            equipos = null;
            alert("Ha ocurrido un error al conectarse con el servidor.\nIntentelo de nuevo mas tarde.")
        }       
    });
}
function CREAR_ROL_JUEGOS(id_convocatoria,repeticion){
    repeticion =parseInt( $( '#vueltas' ).val() );
    console.log(repeticion, "repeticion");
    //Funcion que se llama cuando cierre la convocatoria, recibe de parametro el id de la convocatoria
    Get_Teams(id_convocatoria);
    setTimeout(function(){Made_Round_Teams(id_convocatoria,repeticion);},1000);
}
function Made_Round_Teams(id_convocatoria,repeticion){
    //Funcion que genera el round robin
   if(equipos!=null){
       var auxT=equipos.length;//cantidad de equipos en la convocatoria
       var impar=auxT%2;//comprobamos si es un numero par o impar de equipos
       if(impar!=0){
        equipos.push(0);// si el numero de equipos es impar aqui se agrega un equipo extra (cero) que es el BAY
        ++auxT;
       }
       var totalP=(auxT*(auxT-1))/2;//total de partidos del rol de juegos conforme el num de equipos
       var local=[totalP];//creamos un arreglo con el tamaño necesario
       var visita=[totalP];//tanto para local como para visita
       var modIF=(auxT/2);//este algoritmo se puede encontrar de forma expliada en wikipedia, sin embargo esta es nuestra implementacion
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
        Insert_Round(local,visita,auxT,id_convocatoria,repeticion);
   }
}

function Insert_Round(locales,visita,tam,id_convocatoria,repeticion){
    //Funcion que guarda el round robin generado, y manda cuantas veces se repetira
    if(equipos!=null){
        var jsonLocal = JSON.stringify(locales);//convertimos a json los arreglos
        var jsonVisita = JSON.stringify(visita);
        $.ajax({//enviamos la informacion mediante ajax
            url: "../controlador/SRV_ROLES_JUEGO_INSERTAR.php",
            data: {convocatoria:id_convocatoria,
                   local:jsonLocal,
                   visitante:jsonVisita,
                   tam:tam,
                   repeticion: repeticion},
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
    //funcion para modificar que el torneo ya tiene rol de juego
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
    //funcion para pasar a activo un torneo
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
