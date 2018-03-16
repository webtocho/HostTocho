/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//funcion que se ejecutar cuando se cargue la pagina
   $(document).ready(function() {
       //funcion que obtendra los integrantes de cada equipo con sus respectivas anotaciones
       llenar_tablas();   
    });
    
    
    //variables que contendran las anotaciones de cada jugador
    var TablaTeam1;
    var TablaTeam2;
   //variables que contendran el numero de integrantes de cada equipo
    var NumeroDeIntegrasteDelEquipo1;
    var NumeroDeIntegrasteDelEquipo2;
    //variable que almacenara la id del rol del juego
    var ROL_JUEGO;
    //variable que almacenara la id del rol del roster del equipo 1
    var ID_ROSTER_team1;
    //variable que almacenara la id del rol del roster del equipo 2
    var ID_ROSTER_team2;
    //variable que almacenara la id de la convocatoria
    var ID_CONVOCSTORIA;
   //funcion que obtendra los integrantes de cada equipo con sus respectivas anotaciones
function llenar_tablas(){
        //Recupero la id de lequipo 1 de un variables de sessionStorage y la almacenoen la variable team1
       var team1 = sessionStorage.getItem("id_equipo_1");
       //Recupero la id de lequipo 2 de un variables  de sessionStorage y la almacenoen la variable team2
       var team2 = sessionStorage.getItem("id_equipo_2");
        //Recupero la id del rol del juego de un variables  de sessionStorage y la almacenoen la variable ROL_JUEGO
        ROL_JUEGO =sessionStorage.getItem("id_rol_juego");
         //Recupero la id del rol del juego de un variables  de sessionStorage y la almacenoen la variable ROL_JUEGO
       ID_CONVOCSTORIA=sessionStorage.getItem("id_convocatoria");
       //ajax que hace la petion al php para recuperar el nombre del equipo 1
       $.ajax({
           //url al php que donde se hace la peticioon
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
          //aqui decimos a cual case ingresara del php
            tipo:"ComprobarLogin",
        },
        //le decimos que los datos lo envie de tipo post y de tipo texto
        type: "POST",
        datatype: "text",
        //esto se ejecutara si la peticion fue exitosa
        success: function(resultado) {
        },
        //esto se ejecutara si hubo un error
        error: function(jqXHR, textStatus) {
           mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO","incorrecto");
        }
    });
         //ajax que hace la petion al php para recuperar el nombre del equipo 1
       $.ajax({
             //url al php que donde se hace la peticioon 1
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
              //aqui decimos a cual case ingresara del php
            tipo:"Obtener_nombre_equipo",
              //le enviamos el id del equipo 1
            team:team1,
        },
         //le decimos que los datos lo envie de tipo post y de tipo texto
        type: "POST",
        datatype: "text",
         //lo que se ejecutara antes de hacer la patiion 
        beforeSend: function (xhr) {
             //ponemos una ventana mientras se hace la peticion
            $('#esperando').modal();
        },
         //esto se ejecutara si la peticion fue exitosa 
        success: function(resultado) {
            //vacias la tambla del equipo 1 y agregamos lo que nos respondio el php con el metodo append
            $('#label_equipo_1').empty(); 
            $('#label_equipo_1').append(resultado);
             
        },
         //esto se ejecutara si hubo un error
        error: function(jqXHR, textStatus) {
           mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO","incorrecto");
        }
    });
     //ajax que hace la petion al php para recuperar el nombre del equipo 2
      $.ajax({ 
          //url al php que donde se hace la peticion
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
              //aqui decimos a cual case ingresara del php
            tipo:"Obtener_nombre_equipo",
              //le enviamos el id del equipo 2
            team:team2,
        },
        //le decimos que los datos lo envie de tipo post y de tipo texto
        type: "POST",
        datatype: "text",
          // metodo ssuccess que se ejecutara 
        success: function(resultado) {
            $('#label_equipo_2').empty(); //Vaciamos el contenido de la tabla
            $('#label_equipo_2').append(resultado);//agregamos el contenido con el metoso append
            
        },
        //funcion que se ejecutar si ocurre un error
        error: function(jqXHR, textStatus) {
            //metodo que mostrara un mensaje si ocurrio un error o no
           mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO","incorrecto");
        }
    });
   //ajax que hace la petion al php para recuperar los jugadores del equipo 1
     $.ajax({ 
          //url al php que donde se hace la peticion
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
              //aqui decimos a cual case ingresara del php
            tipo:"Obtener_jugador_equipo",
             //le enviamos el id del equipo 1, el rol de juego,
             // un tipo de bandera que nos indicara si es del TEAM1 o del TEAM2 y la id de la convocatoria
            team:team1,
             ROL:ROL_JUEGO,
             TIPO:"TEAM1",
             ID_CONVOCSTORIA:ID_CONVOCSTORIA,
        },
         //le decimos que los datos lo envie de tipo post y de tipo texto
        type: "POST",
        datatype: "text",
        //funcion llama success, se ejecutar si la peticion se realizo con exito
        success: function(resultado) {
            //deciframos el json que nos delvovio el php
             contenido = JSON.parse(resultado);
             //Vaciamos el contenido de la tabla
            $('#formulario_equipo_1').empty(); 
            //llenamos la tabla con los datos que nos devolvio el json
            $('#formulario_equipo_1').append(contenido[0]);
            //asignamos el numero de integrantes del equipo 1
            NumeroDeIntegrasteDelEquipo1=contenido[2];
            //guardamos la tabla del equipo 1
            TablaTeam1=contenido[1];
        },
        //funcion que se ejecutara cuando la peticion devuelva un eror
        error: function(jqXHR, textStatus) {
              //metodo que mostrara un mensaje de error
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER LOS JUGADORES DEL EQUIPO","incorrecto");
        }
    });
    //ajax que hace la petion al php para recuperar los jugadores del equipo 2
     $.ajax({ 
          //url al php que donde se hace la peticion
           url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            //aqui decimos a cual case ingresara del php
            tipo:"Obtener_jugador_equipo",
             //le enviamos el id del equipo 1, el rol de juego,
             // un tipo de bandera que nos indicara si es del TEAM1 o del TEAM2 y la id de la convocatoria
            team:team2,
            TIPO:"TEAM2",
            ROL:ROL_JUEGO,
             ID_CONVOCSTORIA:ID_CONVOCSTORIA,
        },
         //le decimos que los datos lo envie de tipo post y de tipo texto
        type: "POST",
        datatype: "text",
        //funcion llama success, se ejecutar si la peticion se realizo con exito
        success: function(resultado) {
             //deciframos el json que nos delvovio el php
             contenido = JSON.parse(resultado);
             //Vaciamos el contenido de la tabla
            $('#formulario_equipo_2').empty(); 
             //llenamos la tabla con los datos que nos devolvio el json
            $('#formulario_equipo_2').append(contenido[0]);
            //asignamos el numero de integrantes del equipo 2
            NumeroDeIntegrasteDelEquipo2=contenido[2];
             //guardamos la tabla del equipo 1
             TablaTeam2=contenido[1];
            //quitamos la ventana modal, para que el usuario ya pueda interactuar
             $('#esperando').modal('hide');
        },
         //funcion que se ejecutara cuando la peticion devuelva un eror
        error: function(jqXHR, textStatus) {
            //metodo que mostrara un mensaje de error
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER LOS JUGADORES DEL EQUIPO","incorrecto");
        }
    });
        //ajax que hace la petion al php para abilitar o desabilitar el boton guardar
       $.ajax({ 
           //url al php que donde se hace la peticion
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
             //aqui decimos a cual case ingresara del php
            tipo:"GET_BOTON",
             //le enviamos el id del rol del juego y las id's del equipo 1 y 2
            ROL:ROL_JUEGO,
            TEAM1:team1,
            TEAM2:team2, 
        },
         //le decimos que los datos lo envie de tipo post y de tipo texto
        type: "POST",
        datatype: "text",
         //funcion llama success, se ejecutar si la peticion se realizo con exito
        success: function(resultado) {
            //vaciamos el div que contendra el boton
            $('#BOTON_GUARDAR').empty(); 
            //agregamos el boton
            $('#BOTON_GUARDAR').append(resultado);
        },
        //funcion que se ejecutara cuando la peticion devuelva un eror
        error: function(jqXHR, textStatus) {
              //metodo que mostrara un mensaje de error
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, PORFAVOR RECARGUE LA PAGINA.","incorrecto");
        }
    });
    }

function guardarT(ID_USUARIO,team,bolean,id){
    var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
     
        TablaTeam1[x][1]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
       
        TablaTeam2[x][1]=document.getElementById(id).value;
    }
}
function guardarS(ID_USUARIO,team,bolean,id){
   var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][2]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][2]=document.getElementById(id).value;
    }
}
function guardarI(ID_USUARIO,team,bolean,id){
  var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][3]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][3]=document.getElementById(id).value;
    }
}
function guardarA(ID_USUARIO,team,bolean,id){
     var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][4]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][4]=document.getElementById(id).value;
    }
}
function guardarC1(ID_USUARIO,team,bolean,id){
     var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][5]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][5]=document.getElementById(id).value;
    }
}
function guardarC2(ID_USUARIO,team,bolean,id){
  var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][6]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][6]=document.getElementById(id).value;
    }
}
function guardarC3(ID_USUARIO,team,bolean,id){
    var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][7]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][7]=document.getElementById(id).value;
    }
}
function guardarPA(ID_USUARIO,team,bolean,id){
     var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][8]=document.getElementById(id).value;
      
    }else if(team=="TEAM2"){
         TablaTeam2[x][8]=document.getElementById(id).value;
    }
}
function guardarSA(ID_USUARIO,team,bolean,id){
       var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][9]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][9]=document.getElementById(id).value;
    }
}
function guardarI4(ID_USUARIO,team,bolean,id){
     var x=buscar(ID_USUARIO,team);
    if(team=="TEAM1"){
        TablaTeam1[x][10]=document.getElementById(id).value;
    }else if(team=="TEAM2"){
         TablaTeam2[x][10]=document.getElementById(id).value;
    }
}



function llenar_rol_juego(ID_ROL,ID_TEAM_1,ID_TEAM_2){
      $.ajax({ 
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        data:{
            tipo:"GUARDAR_DATOS",
            ID_ROL:ID_ROL,
            TEAM1:ID_TEAM_1,
            TEAM2:ID_TEAM_2,
            TablaTeam1:TablaTeam1,
            TablaTeam2:TablaTeam2,
            NumeroDeIntegrasteDelEquipo1:NumeroDeIntegrasteDelEquipo1,
            NumeroDeIntegrasteDelEquipo2:NumeroDeIntegrasteDelEquipo2,
            ID_CONVOCSTORIA:ID_CONVOCSTORIA,
        },
        type: "POST",
        
        datatype: "text",
          // // async:false,
        success: function(resultado) {
         console.log("El error prro: "+resultado);
          if(resultado=="si"){
               mostrarAlerta("DATOS GUARDADOS CORRECTAMENTE.","correcto");
               llenar_tablas();
          }else{
               mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO GUARDAR LOS DATOS.","incorrecto");
          }
        },
        error: function(jqXHR, textStatus) {
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, INTENTELO MAS TARDE.","incorrecto");
        }
    });
    ActualizarEstadisticas(ID_CONVOCSTORIA);
}

function add(tipo,id,idJugador,team){
   // alert(tipo+"\n"+id+"\n"+idJugador+"\n"+team);
    var dato=document.getElementById(id).value;
	dato++;
	document.getElementById(id).value=dato;
        switch(tipo){
            case "T":
                guardarT(idJugador,team,true,id);
            break;
            case "S":
                guardarS(idJugador,team,true,id);
            break;
            case "I":
              guardarI(idJugador,team,true,id);
            break;
            case "A":
                guardarA(idJugador,team,true,id);
            break;
            case "C1":
               guardarC1(idJugador,team,true,id);
            break;
             case "C2":
               guardarC2(idJugador,team,true,id);
            break;
            case "C3":
               guardarC3(idJugador,team,true,id);
            break;
            case "PA":
               guardarPA(idJugador,team,true,id);
            break;
            case "SA":
               guardarSA(idJugador,team,true,id);
            break;
            case "I4":
               guardarI4(idJugador,team,true,id);
            break;
        }
}

function reduce(tipo,id,idJugador,team){
  //  alert(tipo+"\n"+id+"\n"+idJugador+"\n"+team);
    var dato=document.getElementById(id).value;
	if(dato!=0){
	dato--;
	document.getElementById(id).value=dato;
         switch(tipo){
            case "T":
                guardarT(idJugador,team,false,id);
            break;
            case "S":
                guardarS(idJugador,team,false,id);
            break;
            case "I":
              guardarI(idJugador,team,false,id);
            break;
            case "A":
                guardarA(idJugador,team,false,id);
            break;
            case "C1":
               guardarC1(idJugador,team,false,id);
            break;
             case "C2":
               guardarC2(idJugador,team,false,id);
            break;
            case "C3":
               guardarC3(idJugador,team,false,id);
            break;
            case "PA":
               guardarPA(idJugador,team,false,id);
            break;
            case "SA":
               guardarSA(idJugador,team,false,id);
            break;
            case "I4":
               guardarI4(idJugador,team,false,id);
            break;
        }
    }
}


function buscar(idJugador,team){
    var i,j;
    if(team=="TEAM1"){
        for(i=0;i<NumeroDeIntegrasteDelEquipo1;i++){
         for(j=0;j<12;j++){
             if(TablaTeam1[i][j]==idJugador){
                 return i;
             }
         }   
        }
    }
    if(team=="TEAM2"){
        for(i=0;i<NumeroDeIntegrasteDelEquipo2;i++){
         for(j=0;j<12;j++){
             if(TablaTeam2[i][j]==idJugador){
                 return i;
             }
         }   
        }
    }
}

