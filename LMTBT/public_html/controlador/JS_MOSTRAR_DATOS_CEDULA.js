/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


   $(document).ready(function() {
         $('#ventanaEmergente').modal();
       llenar_tablas();   
        $('#ventanaEmergente').modal('hide');
    });
    
    var ROL_JUEGO;
    var ID_ROSTER_team1;
    var ID_ROSTER_team2;
   var ID_DEL_JUGADOR;
   var bandera;
   var bandera2=false;
   var ID_CONVOCSTORIA;
function llenar_tablas(){
  // alert("espere un momento porfavor");

            //sessionStorage.setItem("id_equipo_1",prro);
            //sessionStorage.setItem("id_equipo_2",prro);
            //sessionStorage.setItem("iid_rol_juego",prro);
            
       var team1 = sessionStorage.getItem("id_equipo_1");
       var team2 = sessionStorage.getItem("id_equipo_2");
       var rolGame =sessionStorage.getItem("id_rol_juego");
       ID_CONVOCSTORIA=sessionStorage.getItem("id_convocatoria");
      ROL_JUEGO=rolGame;
       $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"Obtener_nombre_equipo",
            team1:team1,
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
           alert("Error de ajax");
        }
    });
      $.ajax({
        
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"Obtener_nombre_equipo",
            team1:team2,
        },
        type: "POST",
        datatype: "text",
          // // async:false,
        success: function(resultado) {
            $('#label_equipo_2').empty(); //Vaciamos el contenido de la tabla
            $('#label_equipo_2').append(resultado);
            
        },
        error: function(jqXHR, textStatus) {
           alert("Error de ajax");
        }
    });
    $.ajax({
         
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"Obtener_jugador_equipo",
            team1:team1,
            
        },
        type: "POST",
        datatype: "text",
       
        success: function(resultado) {
            $('#tabla_equipo_1').empty(); 
            $('#tabla_equipo_1').append(resultado);
      
            
        },
        error: function(jqXHR, textStatus) {
           alert("Error de ajax");
        }
    });
     $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"Obtener_jugador_equipo2",
            team1:team2,
            
        },
        type: "POST",
        datatype: "text",
       //      async:false,
        success: function(resultado) {
            $('#tabla_equipo_2').empty(); 
            $('#tabla_equipo_2').append(resultado); 
            
          
   
        },
        error: function(jqXHR, textStatus) {
           alert("Error de ajax");
        }
    });
      $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"Obtenerid_roster",
            ID_ROSTER:team1,
          
        },
        type: "POST",
        datatype: "text",
        //  async:false,
        success: function(resultado) { 
            ID_ROSTER_team1=resultado;
        },
        error: function(jqXHR, textStatus) {
           alert("Error de ajax");
        }
    });
     $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data:{
            tipo:"Obtenerid_roster",
            ID_ROSTER:team2,
          
        },
        type: "POST",
        datatype: "text",
       //  async:false,
        success: function(resultado) { 
            ID_ROSTER_team2=resultado;
             $('#esperando').modal('hide');
        },
        error: function(jqXHR, textStatus) {
           alert("Error de ajax");
        }
    });
    
 //  ocultar_cargando();
  //alert("la pagina ya esta lista para usarse.");
      
    }

function AbrirPantalla(id){
    ID_DEL_JUGADOR=id;
    bandera=false;
     $('#ventanaEmergente').modal();
}
function AbrirPantalla2(id){
    ID_DEL_JUGADOR=id;
    bandera=true;
     $('#ventanaEmergente').modal();
}
function CerrarPantalla(){
        vaciar_campos();
       $('#ventanaEmergente').modal('hide');
}

function guardar_datos(){
   
    var Anotaciones = document.getElementById("Anotaciones").value;
     var Pases = document.getElementById("Pases").value;
      var Tackles = document.getElementById("Tackles").value;
       var Faults = document.getElementById("Faults").value;
       if(Anotaciones.length>0 && Pases.length>0 && Tackles.length>0 && Faults.length>0){
           if(bandera==false){
               alert(Anotaciones+"\n"+Pases+"\n"+Tackles+"\n"+Faults+"\n del equipo1");
         if(bandera2==false){
             $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"insertar_cedulas",
                         ID_ROL_DEL_JUEGO:ROL_JUEGO,
                         ID_DEL_JUGADOR:ID_DEL_JUGADOR,
                         ID_DEL_ROSTER:ID_ROSTER_team1,
                         ANOTACION:Anotaciones,
                         PASE:Pases,
                         TACKLE:Tackles,
                         FAULT:Faults,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                                $('#esperando').modal();
                         },
                     success: function(resultado) {
                          $('#esperando').modal('hide');
                            if(resultado==="ok"){
                                alert("datos modificados exitosamente.");
                            }
                            
                        },
                        error: function(jqXHR, textStatus) {
                        //alert("Error de ajax");
                        }
                      });
                  }else{
                $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"modificar_cedulas",
                         ID_GMAER:ID_DEL_JUGADOR,
                         ANOTACION:Anotaciones,
                         PASE:Pases,
                         TACKLE:Tackles,
                         FAULT:Faults,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                                $('#esperando').modal();
                         },
                     success: function(resultado) {
                          $('#esperando').modal('hide');
                            if(resultado==="ok"){
                                alert("datos modificados exitosamente.");
                            }
                        },
                        error: function(jqXHR, textStatus) {
                        //alert("Error de ajax");
                        }
                      }); 
                  } 
                    bandera2=false;
        }else{
                alert(Anotaciones+"\n"+Pases+"\n"+Tackles+"\n"+Faults+"\n del equipo2");
               if(bandera2==false){ 
                   
                 $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"insertar_cedulas",
                         ID_ROL_DEL_JUEGO:ROL_JUEGO,
                         ID_DEL_JUGADOR:ID_DEL_JUGADOR,
                         ID_DEL_ROSTER:ID_ROSTER_team2,
                         ANOTACION:Anotaciones,
                         PASE:Pases,
                         TACKLE:Tackles,
                         FAULT:Faults,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                                $('#esperando').modal();
                         },
                     success: function(resultado) {
                            $('#esperando').modal('hide');
                            if(resultado==="ok"){
                                alert("datos guardados exitosamente.");
                            }
             
                        },
                        error: function(jqXHR, textStatus) {
                        //alert("Error de ajax");
                        }
                      });
           
            }else{
                 $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"modificar_cedulas",
                         ID_GMAER:ID_DEL_JUGADOR,
                         ANOTACION:Anotaciones,
                         PASE:Pases,
                         TACKLE:Tackles,
                         FAULT:Faults,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                                $('#esperando').modal();
                                //alert("id_jugagor: "+ID_GAMER+"\n anotacion: "+Anotaciones+"\n pases: "+Pases+"\n tackles: "+Tackles+"\n faults: "+Faults);
                         },
                     success: function(resultado) {
                          $('#esperando').modal('hide');
                            if(resultado==="ok"){
                                alert("datos modificados exitosamente.");
                            }
                        },
                        error: function(jqXHR, textStatus) {
                        //alert("Error de ajax");
                        }
                      });
            }
            bandera2=false;
        }
            
            CerrarPantalla();
       }else{
         var mensaje = "Por favor Complete lo siguiente:";
         if(Anotaciones.length==0) mensaje+="\nAnotaciones";
        if(Pases.length==0) mensaje+="\nApellido Pases";
        if(Tackles.length==0) mensaje+="\nTackles";
        if(Faults.length==0) mensaje+="\nFaults";
        alert(mensaje);
       }
      vaciar_campos();
}

function Mostrar_Datos(id){
     $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"recuperar_cedulas",
                         ID_JUGADOR:id,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                            $('#esperando').modal();
                              
                         },
                     success: function(resultado) {
                        $('#esperando').modal('hide');
                        var separador = ","; // un espacio en blanco
                         var arregloDeSubCadenas = resultado.split(separador);
                           alert("ANOTACIONES: "+arregloDeSubCadenas[0]+"\n PASES: "+arregloDeSubCadenas[1]+"\n TACKLES: "+arregloDeSubCadenas[2]+"\n FAULTS: "+arregloDeSubCadenas[3]);
                         },
                        error: function(jqXHR, textStatus) {
                        //alert("Error de ajax");
                        }
                      });
}

function Editar_datos(id){
    bandera2=true;
     $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"recuperar_cedulas",
                         ID_JUGADOR:id,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                            $('#esperando').modal();
                              
                         },
                     success: function(resultado) {
                        $('#esperando').modal('hide');
                       var separador = ","; // un espacio en blanco
                        var arregloDeSubCadenas = resultado.split(separador);
                       document.getElementById("Anotaciones").value=arregloDeSubCadenas[0];
                       document.getElementById("Pases").value=arregloDeSubCadenas[1];
                        document.getElementById("Tackles").value=arregloDeSubCadenas[2];
                         document.getElementById("Faults").value=arregloDeSubCadenas[3]; 
                          AbrirPantalla(id);
                        },
                        error: function(jqXHR, textStatus) {
                        //alert("Error de ajax");
                        }
                      });
}
function Editar_datos2(id){
     bandera2=true;
     $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"recuperar_cedulas",
                         ID_JUGADOR:id,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                            $('#esperando').modal();
                              
                         },
                     success: function(resultado) {
                        $('#esperando').modal('hide');
                       var separador = ","; // un espacio en blanco
                        var arregloDeSubCadenas = resultado.split(separador);
                       document.getElementById("Anotaciones").value=arregloDeSubCadenas[0];
                       document.getElementById("Pases").value=arregloDeSubCadenas[1];
                        document.getElementById("Tackles").value=arregloDeSubCadenas[2];
                         document.getElementById("Faults").value=arregloDeSubCadenas[3]; 
                          AbrirPantalla2(id);
                        },
                        error: function(jqXHR, textStatus) {
                        //alert("Error de ajax");
                        }
                      });
}

function llenar_rol_juego(){
        $.ajax({
                    url: "../controlador/SRV_CONSULTAS.php",
                     data:{
                         tipo:"totales_goles_equipo_1",
                         ID_ROSTER1:ID_ROSTER_team1,
                         ID_ROSTER2:ID_ROSTER_team2,
                         ID_ROL_JUEGO:ROL_JUEGO,
                        },
                      type: "POST",
                         datatype: "text",
                          beforeSend: function (xhr) {
                            $('#esperando').modal();
                              
                         },
                     success: function(resultado) {
                        $('#esperando').modal('hide');
                        id_convocatoria=ID_CONVOCSTORIA;
                       
                         ActualizarEstadisticas(id_convocatoria);
                        },
                        error: function(jqXHR, textStatus) {
                       // alert("Error de ajax");
                        }
                      });
  
}
function vaciar_campos(){
        document.getElementById("Anotaciones").value="";
       document.getElementById("Pases").value="";
       document.getElementById("Tackles").value="";
       document.getElementById("Faults").value=""; 
}