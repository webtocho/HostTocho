/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
   $(document).ready(function() {
       llenar_tablas();   
    });
    
    var ROL_JUEGO;
    var ID_ROSTER_team1;
    var ID_ROSTER_team2;
   var ID_DEL_JUGADOR;
   var bandera;
   var bandera2=false;
   var ID_CONVOCSTORIA;
   
function llenar_tablas(){
         /* sessionStorage.setItem("id_equipo_1", 1);
        sessionStorage.setItem("id_equipo_2", 2);
        sessionStorage.setItem("id_rol_juego", 1);
        sessionStorage.setItem("id_convocatoria", 1);*/
        
       var team1 = sessionStorage.getItem("id_equipo_1");
       var team2 = sessionStorage.getItem("id_equipo_2");
       var rolGame =sessionStorage.getItem("id_rol_juego");
       ID_CONVOCSTORIA=sessionStorage.getItem("id_convocatoria");
      ROL_JUEGO=rolGame;
       $.ajax({
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"Obtener_nombre_equipo",
            team:team1,
        },
        type: "POST",
        datatype: "text",
        //  async:false,
        beforeSend: function (xhr) {
            $('#esperando').modal();
        },
        success: function(resultado) {
            $('#label_equipo_1').empty(); //Vaciamos el contenido de la tabla
            $('#label_equipo_1').append(resultado);
             
        },
        error: function(jqXHR, textStatus) {
           mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO","incorrecto");
        }
    });
      $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"Obtener_nombre_equipo",
            team:team2,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
            $('#label_equipo_2').empty(); //Vaciamos el contenido de la tabla
            $('#label_equipo_2').append(resultado);
            
        },
        error: function(jqXHR, textStatus) {
           mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO","incorrecto");
        }
    });
  
     $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"Obtener_jugador_equipo",
            team:team1,
             ROL:rolGame,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
           // $('#formulario_equipo_1').empty(); //Vaciamos el contenido de la tabla
            $('#formulario_equipo_1').append(resultado);
            
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER LOS JUGADORES DEL EQUIPO","incorrecto");
        }
    });
     $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"Obtener_jugador_equipo",
            team:team2,
            ROL:rolGame,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
           // $('#formulario_equipo_1').empty(); //Vaciamos el contenido de la tabla
            $('#formulario_equipo_2').append(resultado);
             $('#esperando').modal('hide');
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER LOS JUGADORES DEL EQUIPO","incorrecto");
        }
    });
 //  ocultar_cargando();
  //alert("la pagina ya esta lista para usarse.");
       $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"GET_BOTON",
            ROL:ROL_JUEGO,
            TEAM1:team1,
            TEAM2:team2,
           
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
              $('#BOTON_GUARDAR').empty(); 
            $('#BOTON_GUARDAR').append(resultado);
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, PORFAVOR RECARGUE LA PAGINA.","incorrecto");
        }
    });
    }





function guardarT(id,ID_USUARIO){
    var dato=document.getElementById(id).value;
          $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"guardarT",
            DATO:dato,
            ID_USUARIO:ID_USUARIO,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {

        },
        error: function(jqXHR, textStatus) {
           mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, AL GUARDAR EL DATO","incorrecto");
        }
    });
}
function guardarS(id,ID_USUARIO){
    
    var dato=document.getElementById(id).value;
          $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"guardarS",
            DATO:dato,
            ID_USUARIO:ID_USUARIO,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, AL GUARDAR EL DATO","incorrecto");
        }
    });
}

function guardarI(id,ID_USUARIO){
       var dato=document.getElementById(id).value;
          $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"guardarI",
            DATO:dato,
            ID_USUARIO:ID_USUARIO,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) { 
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, AL GUARDAR EL DATO","incorrecto");
        }
    });
}
function guardarA(id,ID_USUARIO){
         var dato=document.getElementById(id).value;
          $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"guardarA",
            DATO:dato,
            ID_USUARIO:ID_USUARIO,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, AL GUARDAR EL DATO","incorrecto");
        }
    });
}
function guardarC1(id,ID_USUARIO){
         var dato=document.getElementById(id).value;
          $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"guardarC1",
            DATO:dato,
            ID_USUARIO:ID_USUARIO,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
        },
        error: function(jqXHR, textStatus) {
         mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, AL GUARDAR EL DATO","incorrecto");
        }
    });
}
function guardarC2(id,ID_USUARIO){
         var dato=document.getElementById(id).value;
          $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"guardarC2",
            DATO:dato,
            ID_USUARIO:ID_USUARIO,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
        },
        error: function(jqXHR, textStatus) {
         mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, AL GUARDAR EL DATO","incorrecto");
        }
    });
}
function guardarPT(id,ID_USUARIO){
        var dato=document.getElementById(id).value;
          $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"guardarPT",
            DATO:dato,
            ID_USUARIO:ID_USUARIO,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, AL GUARDAR EL DATO","incorrecto");
        }
    });
}


function llenar_rol_juego(ID_ROL,ID_TEAM_1,ID_TEAM_2){
   // alert(ID_ROL+"\n"+ID_TEAM_1+"\n"+ID_TEAM_2);
      $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"GUARDAR_DATOS",
            ID_ROL:ID_ROL,
            TEAM1:ID_TEAM_1,
            TEAM2:ID_TEAM_2,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
         
          if(resultado=="ok"){
               mostrarAlerta("DATOS GUARDADOS CORRECTAMENTE.","correcto");
          }
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO GUARDAR LOS DATOS.","incorrecto");
        }
    });
    ActualizarEstadisticas(ID_CONVOCSTORIA);
}