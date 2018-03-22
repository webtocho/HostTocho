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
//funcion que gurda los puntos de TACKLE en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarT(ID_USUARIO,team,bolean,id){
    //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
    var x=buscar(ID_USUARIO,team);
    //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
       //inserta el dato del iput del formulario
        TablaTeam1[x][1]=document.getElementById(id).value;
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
       //inserta el dato del iput del formulario
        TablaTeam2[x][1]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de SACKS en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarS(ID_USUARIO,team,bolean,id){
    //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
   var x=buscar(ID_USUARIO,team);
   //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][2]=document.getElementById(id).value;
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][2]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de INTERCEPCIONES en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarI(ID_USUARIO,team,bolean,id){
     //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
  var x=buscar(ID_USUARIO,team);
  //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][3]=document.getElementById(id).value;
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][3]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de ANOTACIONES en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarA(ID_USUARIO,team,bolean,id){
    //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
     var x=buscar(ID_USUARIO,team);
      //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
         //inserta el dato del iput del formulario
        TablaTeam1[x][4]=document.getElementById(id).value;
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
         //inserta el dato del iput del formulario
         TablaTeam2[x][4]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de CONVERSION 1 en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarC1(ID_USUARIO,team,bolean,id){
    //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
     var x=buscar(ID_USUARIO,team);
      //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][5]=document.getElementById(id).value;
    }else if(team=="TEAM2"){ //lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][5]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de CONVERSION 2 en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarC2(ID_USUARIO,team,bolean,id){
    //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
  var x=buscar(ID_USUARIO,team);
   //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][6]=document.getElementById(id).value;
    }else if(team=="TEAM2"){ //lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][6]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de CONVERSION 3 en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarC3(ID_USUARIO,team,bolean,id){
    //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
    var x=buscar(ID_USUARIO,team);
    //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][7]=document.getElementById(id).value;
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][7]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de PASE DE ANOTACION en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarPA(ID_USUARIO,team,bolean,id){
    //Busca la posicion de x del jugador en la tabla segun el equipo al que pertenezca
     var x=buscar(ID_USUARIO,team);
     //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][8]=document.getElementById(id).value; 
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][8]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de SAFETY en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarSA(ID_USUARIO,team,bolean,id){
     //lo inserta segun el equipo donde este el jugador
       var x=buscar(ID_USUARIO,team);
       //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][9]=document.getElementById(id).value;
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][9]=document.getElementById(id).value;
    }
}
//funcion que gurda los puntos de INTERCEPCION en una matriz, recibe la ide del jugador, su equipo y la id de la tabla
function guardarI4(ID_USUARIO,team,bolean,id){
     //lo inserta segun el equipo donde este el jugador
     var x=buscar(ID_USUARIO,team);
     //lo inserta segun el equipo donde este el jugador
    if(team=="TEAM1"){
        //inserta el dato del iput del formulario
        TablaTeam1[x][10]=document.getElementById(id).value;
    }else if(team=="TEAM2"){//lo inserta segun el equipo donde este el jugador
        //inserta el dato del iput del formulario
         TablaTeam2[x][10]=document.getElementById(id).value;
    }
}


//funcion el el cual guardo los datos de las tablas, recibe como parametros
//el ID del rol del juego, t los ID'S de cada equipo
function llenar_rol_juego(ID_ROL,ID_TEAM_1,ID_TEAM_2){
    //la comunicacion lo hacemos por ajax
      $.ajax({ 
          //le indicamos  que php hara la peticion
        url: "../controlador/SRV_MOSTRAR_DATOS_CEDULA.php",
        //enviamos los datos
        data:{
            //tipo indicara a que CASE del php entrara
            tipo:"GUARDAR_DATOS",
            //enviamos la ID rol del juego
            ID_ROL:ID_ROL,
            //enviamos las ID's de cada equipo
            TEAM1:ID_TEAM_1,
            TEAM2:ID_TEAM_2,
            //enviamos las tablas de cada equipo con los datos de los jugadores
            TablaTeam1:TablaTeam1,
            TablaTeam2:TablaTeam2,
            //enviamos el numero de jugadores de cada equipo
            NumeroDeIntegrasteDelEquipo1:NumeroDeIntegrasteDelEquipo1,
            NumeroDeIntegrasteDelEquipo2:NumeroDeIntegrasteDelEquipo2,
            //enviamos la ID de la convocatoria
            ID_CONVOCSTORIA:ID_CONVOCSTORIA,
        },
        //indicamos que los datos se enviaran con el metodo POST y que sera de tipo TEXTO
        type: "POST",
        datatype: "text",
          // Metodo que se ejecura si la peticion es correcta
        success: function(resultado) {
          
        //si no hubo problemas en php, este enviara un "ok"
          if(resultado=="ok"){//si lo que nos devuelve el php es "ok" 
              //mostramos un mensaje que la peticion se realizo con exito
               mostrarAlerta("DATOS GUARDADOS CORRECTAMENTE.","correcto");
               //actulizamos la tabla de cada jugador
               llenar_tablas();
               // actulizamos las estadisticas
               ActualizarEstadisticas(ID_CONVOCSTORIA);
          }else{
              //mostrara un mensaje de error cuando algo falle en el php
               mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO GUARDAR LOS DATOS.","incorrecto");
          }
        },
        //funcion que es ejecutara cuando la patecion no se pudo estableceer
        error: function(jqXHR, textStatus) {
            //mostramos un mensaje con un error de servidor
          mostrarAlerta("HUVO UN ERROR INTERNO DEL SERVIDOR, INTENTELO MAS TARDE.","incorrecto");
        }
    });
}
//  funcion que nos ayudara a aumentar los datos de los inputs
//recibe el tipo de dato, id del input, el id del jugador y de que team el jugador
function add(tipo,id,idJugador,team){
   // recuperamos el valor del input
    var dato=document.getElementById(id).value;
    //aumentamos en uno su valor
    dato++;
    //asignamos el valor    
    document.getElementById(id).value=dato;
    //guardamos el dato aumentado dependiendo de que tipo datos es, y dependiendo del jugador y equipo
    //dependiedo del tipo, sera la funcion que se ejecutara y actulizara la tabla
        switch(tipo){
            case "T":
                //metodo anteriormente documentado
                guardarT(idJugador,team,true,id);
            break;
            case "S":
                //metodo anteriormente documentado
                guardarS(idJugador,team,true,id);
            break;
            case "I":
                //metodo anteriormente documentado
              guardarI(idJugador,team,true,id);
            break;
            case "A":
                //metodo anteriormente documentado
                guardarA(idJugador,team,true,id);
            break;
            case "C1":
                //metodo anteriormente documentado
               guardarC1(idJugador,team,true,id);
            break;
             case "C2":
                 //metodo anteriormente documentado
               guardarC2(idJugador,team,true,id);
            break;
            case "C3":
                //metodo anteriormente documentado
               guardarC3(idJugador,team,true,id);
            break;
            case "PA":
                //metodo anteriormente documentado
               guardarPA(idJugador,team,true,id);
            break;
            case "SA":
                //metodo anteriormente documentado
               guardarSA(idJugador,team,true,id);
            break;
            case "I4":
                //metodo anteriormente documentado
               guardarI4(idJugador,team,true,id);
            break;
        }
}
//  funcion que nos ayudara a reducir los datos de los inputs
//recibe el tipo de dato, id del input, el id del jugador y de que team el jugador
function reduce(tipo,id,idJugador,team){
  // recuperamos el valor del input
    var dato=document.getElementById(id).value;
    //controlamos si el valor es diferente de cero, para evitar numeros negativos
	if(dato!=0){
            //disminuimos el valor del input
	dato--;
        //actualizamos el input con el nuevo dato
	document.getElementById(id).value=dato;
         //guardamos el dato aumentado dependiendo de que tipo datos es, y dependiendo del jugador y equipo
    //dependiedo del tipo, sera la funcion que se ejecutara y actulizara la tabla
         switch(tipo){
            case "T":
                  //metodo anteriormente documentado
                guardarT(idJugador,team,false,id);
            break;
              //metodo anteriormente documentado
            case "S":
                  //metodo anteriormente documentado
                guardarS(idJugador,team,false,id);
            break;
            case "I":
                  //metodo anteriormente documentado
              guardarI(idJugador,team,false,id);
            break;
            case "A":
                  //metodo anteriormente documentado
                guardarA(idJugador,team,false,id);
            break;
            case "C1":
                  //metodo anteriormente documentado
               guardarC1(idJugador,team,false,id);
            break;
             case "C2":
                   //metodo anteriormente documentado
               guardarC2(idJugador,team,false,id);
            break;
            case "C3":
                  //metodo anteriormente documentado
               guardarC3(idJugador,team,false,id);
            break;
            case "PA":
                  //metodo anteriormente documentado
               guardarPA(idJugador,team,false,id);
            break;
            case "SA":
                  //metodo anteriormente documentado
               guardarSA(idJugador,team,false,id);
            break;
            case "I4":
                  //metodo anteriormente documentado
               guardarI4(idJugador,team,false,id);
            break;
        }
    }
}

//esta funcion recibe como parametros el id del jugador, y a que equipo pertenece
function buscar(idJugador,team){
    //variables que nos ayudan a recorrer la tabla
    var i,j;
    //buscamos en la tabla 1
    if(team=="TEAM1"){
        //recorremos las filas de la tabla
        for(i=0;i<NumeroDeIntegrasteDelEquipo1;i++){
            //recorremos las columnas de la tabla
         for(j=0;j<12;j++){
             //verificamos si en la posicion i,j se encuntra el jugador deseado
             if(TablaTeam1[i][j]==idJugador){
                 //retornamos la posicon en x de la tabla
                 return i;
             }
         }   
        }
    }
    //buscamos en la tabla 2
    if(team=="TEAM2"){
         //recorremos las filas de la tabla
        for(i=0;i<NumeroDeIntegrasteDelEquipo2;i++){
             //verificamos si en la posicion i,j se encuntra el jugador deseado
         for(j=0;j<12;j++){
             //retornamos la posicon en x de la tabla
             if(TablaTeam2[i][j]==idJugador){
                 //retornamos la posicon en x de la tabla
                 return i;
             }
         }   
        }
    }
}

