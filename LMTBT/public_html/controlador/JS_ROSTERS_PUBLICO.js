$(document).ready(function() {
    CargarEquipos();
});
function CargarEquipos(){
    //Funcion para cargar los equipos en un select que se mostrara al publico
    $.ajax({
        url: "../controlador/SRV_PUBLICO_EQUIPOS_CARGAR.php",
        beforeSend: function (xhr){
                 $('#Datos').empty();
                 $('#Datos').append("<img src='./images/cargando_naranja.gif' >");
        },
        success: function (res) {
            $('#Equipos').empty();
            $('#Equipos').append(res);
            $('#Roster').hide();
            $('#Datos').empty();
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}
function CargarRoster(id){
    //Funcion para cargar los rosters de un equipo seleccionado en el select anterior
    $.ajax({
        data: {id_equipo:id},
        url: "../controlador/SRV_PUBLICO_ROSTER_CARGAR.php",
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr){
                 $('#Datos').empty();
                 $('#Datos').append("<img src='./images/cargando_naranja.gif' >");
        },
        success: function (res) {
            html = JSON.parse(res);
            $('#Roster').empty();
            $('#logo').empty();
            $('#logo').append(html[0]);
            $('#Roster').append(html[1]);
            $('#Roster').show();
            $('#Datos').empty();
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}
function CargarDatos(id){
    //Funcion para cargar los datos de un roster (jugadores,fotos)
    $.ajax({
        data: {id_roster:id},
        url: "../controlador/SRV_PUBLICO_DATOS_CARGAR.php",
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr){
                 $('#Datos').empty();
                 $('#Datos').append("<img src='./images/cargando_naranja.gif' >");
        },
        success: function (res) {
            $('#Datos').empty();
            $('#Datos').append(res);
            setTimeout(Jugadores(id), 1000);
        },
        error: function (jqXHR, textStatus) {
           alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}
var equipo=[];
//Esta funcion nos genera un arreglo con los id's de los jugadores del roster seleccionado
function Jugadores(id){
    var team;
    $.ajax({//hacemos la peticion mediante ajax para obtener a todos los jugadores del roster
        url: "../controlador/SRV_PUBLICO_JUGADORES_CARGAR.php",
        data: {id_roster :id },
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            if(resultado==-1) {
                equipo=null;//si nos retorna -1 es que en la convocatoria no se han inscrito equipos
                alert("No se han encontrado jugadores en el roster solicitado");
            }
            else  { 
                team=new Array(resultado);//si nos regresa otra cosa generamos un arreglo con los jugadores
                team=team[0].split(',');//generamos bien el arreglo temporal
                alert("team:"+team);
                for(var i=0; i<team.length; i++){
                    equipo[i]=(parseInt(team[i]));//vamos agregando el id de los jugadores al arreglo
                }
                setTimeout(JugadoresCargar(id), 1000);//mandamos a llamar la funcion que obtiene informacion de los jugadores uno por uno
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Ha ocurrido un error al conectarse con el servidor.\nIntentelo de nuevo mas tarde.")
        }       
    });
}
//Esta funcion recupera la informacion de los jugadores uno por uno
function JugadoresCargar(id){
    var ids="";
    for(var j=0;j<equipo.length;j++){
        ids+=equipo[j]+",";
        $.ajax({//hacemos la peticion mediante ajax
            url: "../controlador/SRV_PUBLICO_JUGADOR_INFO.php",
            data: {id_roster :id, id_jugador:equipo[j] },//mandamos el id del roster asi como el del jugador
            type: "POST",
            dataType: 'text',
            success: function (resultado) {
                $('#Datos').append(resultado);
            },
            error: function (jqXHR, textStatus) {
                //alert("Ha ocurrido un error al conectarse con el servidor.\nIntentelo de nuevo mas tarde.")
            }       
        });
    }
    alert(ids);
}
