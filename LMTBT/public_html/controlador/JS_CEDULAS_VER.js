//Función que se ejecuta cuando se cargue la página.
$(document).ready(function () {
    //Función que obtendrá los integrantes de cada equipo con sus respectivas anotaciones.
    llenar_tablas();
});
$('[data-toggle="tooltip"]').tooltip();
//Variables que contendrán las anotaciones de cada jugador.
var TablaTeam1;
var TablaTeam2;
//Variables que contendrán el número de integrantes de cada equipo.
var NumeroDeIntegrasteDelEquipo1;
var NumeroDeIntegrasteDelEquipo2;
//Variable que almacenará la id del rol del juego.
var ROL_JUEGO;
//Variable que almacenará la id del rol del roster del equipo 1.
var ID_ROSTER_team1;
//Variable que almacenará la id del rol del roster del equipo 2.
var ID_ROSTER_team2;
//Variable que almacenará la id de la convocatoria.
var ID_CONVOCSTORIA;

//Función que obtendrá los integrantes de cada equipo con sus respectivas anotaciones.
function llenar_tablas() {
    //Recupero la id de lequipo 1 de un variables de sessionStorage y la almaceno en la variable team1
    var team1 = sessionStorage.getItem("id_equipo_1");
    //Recupero la id de lequipo 2 de un variables  de sessionStorage y la almaceno en la variable team2
    var team2 = sessionStorage.getItem("id_equipo_2");
    //Recupero la id del rol del juego de un variables  de sessionStorage y la almaceno en la variable ROL_JUEGO
    ROL_JUEGO = sessionStorage.getItem("id_rol_juego");
    //Recupero la id del rol del juego de un variables  de sessionStorage y la almaceno en la variable ROL_JUEGO
    ID_CONVOCSTORIA = sessionStorage.getItem("id_convocatoria");
    //Ajax que hace la petición al php para recuperar el nombre del equipo 1
    $.ajax({
        //Url al php que donde se hace la petición
        url: "../controlador/SRV_CEDULAS_MOSTRAR.php",
        data: {
            //Aquí decimos a cuál case ingresará del php.
            tipo: "ComprobarLogin",
        },
        //Le decimos que los datos lo envie de tipo post y de tipo texto.
        type: "POST",
        datatype: "text",
        //Esto se ejecutará si la peticion fue exitosa.
        success: function (resultado) {
        },
        //Esto se ejecutará si hubo un error
        error: function (jqXHR, textStatus) {
            mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO", "incorrecto");
        }
    });
    //Ajax que hace la petición al php para recuperar el nombre del equipo 1.
    $.ajax({
        //Url al php donde se hace la petición 1
        url: "../controlador/SRV_CEDULAS_MOSTRAR.php",
        data: {
            //Aquí decimos a cual case ingresará del php.
            tipo: "Obtener_nombre_equipo",
            //Le enviamos el id del equipo 1.
            team: team1,
        },
        //Le decimos que los datos los envie de tipo post y la respuesta sea de tipo texto.
        type: "POST",
        datatype: "text",
        //Lo que se ejecutara antes de hacer la patición. 
        beforeSend: function (xhr) {
            //Ponemos una ventana mientras se hace la petición.
            $('#esperando').modal();
        },
        //Esto se ejecutará si la petición fue exitosa. 
        success: function (resultado) {
            //Se vacia la tabla del equipo 1 y agregamos lo que nos respondio el php con el método append.
            $('#label_equipo_1').empty();
            $('#label_equipo_1').append(resultado);

        },
        //Esto se ejecutará si hubo un error.
        error: function (jqXHR, textStatus) {
            mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO", "incorrecto");
        }
    });
    //Ajax que hace la petición al php para recuperar el nombre del equipo 2.
    $.ajax({
        //Url al php que donde se hace la petición.
        url: "../controlador/SRV_CEDULAS_MOSTRAR.php",
        data: {
            //Aquí decimos a cúal case ingresará del php.
            tipo: "Obtener_nombre_equipo",
            //Le enviamos el id del equipo 2.
            team: team2,
        },
        //Le decimos que los datos lo envie de tipo post y que la respuesta sea de tipo texto.
        type: "POST",
        datatype: "text",
        // Función success que se ejecutará. 
        success: function (resultado) {
            $('#label_equipo_2').empty(); //Vaciamos el contenido de la tabla.
            $('#label_equipo_2').append(resultado);//Agregamos el contenido con el método append.

        },
        //Función que se ejecutará si ocurre un error.
        error: function (jqXHR, textStatus) {
            //Función que mostrará un mensaje si ocurrió un error o no.
            mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER EL NOMBRE DEL EQUIPO", "incorrecto");
        }
    });
    //Ajax que hace la petición al php para recuperar los jugadores del equipo 1
    $.ajax({
        //Url al php que donde se hace la petición.
        url: "../controlador/SRV_CEDULAS_MOSTRAR.php",
        data: {
            //Aquí decimos a cuál case ingresara del php.
            tipo: "Obtener_jugador_equipo",
            /* Le enviamos el id del equipo 1, el rol de juego,
               un tipo de bandera que nos indicara si es del TEAM1 o del TEAM2 y la id de la convocatoria */
            team: team1,
            ROL: ROL_JUEGO,
            TIPO: "TEAM1",
            ID_CONVOCSTORIA: ID_CONVOCSTORIA,
        },
        //Le decimos que los datos los envíe de tipo post y se reciba un texto.
        type: "POST",
        datatype: "text",
        //Función llamada success, se ejecutará si la petición se realizó con éxito.
        success: function (resultado) {
            //Desciframos el json que nos delvovió el php.
            contenido = JSON.parse(resultado);
            //Vaciamos el contenido de la tabla.
            $('#formulario_equipo_1').empty();
            //Llenamos la tabla con los datos que nos devolvió el json.
            $('#formulario_equipo_1').append(contenido[0]);
            //Asignamos el numero de integrantes del equipo 1.
            NumeroDeIntegrasteDelEquipo1 = contenido[2];
            //Guardamos la tabla del equipo 1.
            TablaTeam1 = contenido[1];
        },
        //Función que se ejecutará cuando la petición devuelva un error.
        error: function (jqXHR, textStatus) {
            //Función que mostrará un mensaje de error.
            mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER LOS JUGADORES DEL EQUIPO", "incorrecto");
        }
    });
    //Ajax que hace la petición al php para recuperar los jugadores del equipo 2.
    $.ajax({
        //Url al php que donde se hace la petición.
        url: "../controlador/SRV_CEDULAS_MOSTRAR.php",
        data: {
            //Aquí decimos a cuál case ingresará del php.
            tipo: "Obtener_jugador_equipo",
            /* Le enviamos el id del equipo 1, el rol de juego,
               un tipo de bandera que nos indicara si es del TEAM1 o del TEAM2 y la id de la convocatoria */
            team: team2,
            TIPO: "TEAM2",
            ROL: ROL_JUEGO,
            ID_CONVOCSTORIA: ID_CONVOCSTORIA,
        },
        //Le decimos que los datos los envíe de tipo post y de tipo texto.
        type: "POST",
        datatype: "text",
        //Función llamada success, se ejecutará si la petición se realizó con éxito.
        success: function (resultado) {
            //Desciframos el json que nos devolvió el php.
            contenido = JSON.parse(resultado);
            //Vaciamos el contenido de la tabla
            $('#formulario_equipo_2').empty();
            //Llenamos la tabla con los datos que nos devolvió el Json.
            $('#formulario_equipo_2').append(contenido[0]);
            //Asignamos el numero de integrantes del equipo 2.
            NumeroDeIntegrasteDelEquipo2 = contenido[2];
            //Guardamos la tabla del equipo 1.
            TablaTeam2 = contenido[1];
            //Quitamos la ventana modal, para que el usuario ya pueda interactuar.
            $('#esperando').modal('hide');
        },
        //Función que se ejecutara cuando la petición devuelva un error.
        error: function (jqXHR, textStatus) {
            //Función que mostrará un mensaje de error.
            mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO OBTENER LOS JUGADORES DEL EQUIPO", "incorrecto");
        }
    });
    //Ajax que hace la petición al php para habilitar o deshabilitar el boton guardar.
    $.ajax({
        //Url al php que donde se hace la petición.
        url: "../controlador/SRV_CEDULAS_MOSTRAR.php",
        data: {
            //Aquí decimos a cuál case ingresará del php.
            tipo: "GET_BOTON",
            //le enviamos el id del rol del juego y las id's del equipo 1 y 2
            ROL: ROL_JUEGO,
            TEAM1: team1,
            TEAM2: team2,
        },
        //Le decimos que los datos lo envíe del tipo post y el resultado será de tipo texto.
        type: "POST",
        datatype: "text",
        //Función llamada success, se ejecutará si la petición se realizó con éxito.
        success: function (resultado) {
            //Vaciamos el div que contendrá el boton.
            $('#BOTON_GUARDAR').empty();
            //Agregamos el botón.
            $('#BOTON_GUARDAR').append(resultado);
        },
        //Función que se ejecutará cuando la petición devuelva un error.
        error: function (jqXHR, textStatus) {
            //Función que mostrará un mensaje de error.
            mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR,POR FAVOR, RECARGUE LA PAGINA.", "incorrecto");
        }
    });
}
//Función que guarda los puntos de TACKLE en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarT(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca.
    var x = buscar(ID_USUARIO, team);
    //Lo inserta según el equipo donde esté el jugador.
    if (team == "TEAM1") {
        //Inserta el dato del input del formulario
        TablaTeam1[x][1] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //Inserta el dato del input del formulario
        TablaTeam2[x][1] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de SACKS en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarS(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca
    var x = buscar(ID_USUARIO, team);
    //Lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //Inserta el dato del input del formulario
        TablaTeam1[x][2] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //Inserta el dato del input del formulario
        TablaTeam2[x][2] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de INTERCEPCIONES en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarI(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca
    var x = buscar(ID_USUARIO, team);
    //Lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //Inserta el dato del input del formulario
        TablaTeam1[x][3] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //Inserta el dato del input del formulario
        TablaTeam2[x][3] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de ANOTACIONES en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarA(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca
    var x = buscar(ID_USUARIO, team);
    //lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //inserta el dato del input del formulario
        TablaTeam1[x][4] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //inserta el dato del input del formulario
        TablaTeam2[x][4] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de CONVERSION 1 en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarC1(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca
    var x = buscar(ID_USUARIO, team);
    //lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //inserta el dato del input del formulario
        TablaTeam1[x][5] = document.getElementById(id).value;
    } else if (team == "TEAM2") { //lo inserta según el equipo donde esté el jugador
        //inserta el dato del input del formulario
        TablaTeam2[x][5] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de CONVERSION 2 en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarC2(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca
    var x = buscar(ID_USUARIO, team);
    //lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //inserta el dato del input del formulario
        TablaTeam1[x][6] = document.getElementById(id).value;
    } else if (team == "TEAM2") { //lo inserta según el equipo donde esté el jugador
        //inserta el dato del input del formulario
        TablaTeam2[x][6] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de CONVERSION 3 en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarC3(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca
    var x = buscar(ID_USUARIO, team);
    //lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //inserta el dato del input del formulario
        TablaTeam1[x][7] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //inserta el dato del input del formulario
        TablaTeam2[x][7] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de PASE DE ANOTACION en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarPA(ID_USUARIO, team, bolean, id) {
    //Busca la posición de x del jugador en la tabla según el equipo al que pertenezca
    var x = buscar(ID_USUARIO, team);
    //lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //inserta el dato del input del formulario
        TablaTeam1[x][8] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //inserta el dato del input del formulario
        TablaTeam2[x][8] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de SAFETY en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarSA(ID_USUARIO, team, bolean, id) {
    //lo inserta según el equipo donde esté el jugador
    var x = buscar(ID_USUARIO, team);
    //lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //inserta el dato del input del formulario
        TablaTeam1[x][9] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //inserta el dato del input del formulario
        TablaTeam2[x][9] = document.getElementById(id).value;
    }
}
//Función que guarda los puntos de INTERCEPCION en una matriz, recibe el ID del jugador, su equipo y el ID de la tabla.
function guardarI4(ID_USUARIO, team, bolean, id) {
    //lo inserta según el equipo donde esté el jugador
    var x = buscar(ID_USUARIO, team);
    //lo inserta según el equipo donde esté el jugador
    if (team == "TEAM1") {
        //inserta el dato del input del formulario
        TablaTeam1[x][10] = document.getElementById(id).value;
    } else if (team == "TEAM2") {//lo inserta según el equipo donde esté el jugador
        //inserta el dato del input del formulario
        TablaTeam2[x][10] = document.getElementById(id).value;
    }
}


//Función el el cual guardo los datos de las tablas, recibe como parametros
//el ID del rol del juego, t los ID'S de cada equipo
function llenar_rol_juego(ID_ROL, ID_TEAM_1, ID_TEAM_2) {
    //La comunicacion se hace por ajax
    $.ajax({
        //Le indicamos a qué php se hará la peticion
        url: "../controlador/SRV_CEDULAS_MOSTRAR.php",
        //Enviamos los datos
        data: {
            //tipo indicara a qué CASE del php entrara
            tipo: "GUARDAR_DATOS",
            //enviamos la ID rol del juego
            ID_ROL: ID_ROL,
            //enviamos las ID's de cada equipo
            TEAM1: ID_TEAM_1,
            TEAM2: ID_TEAM_2,
            //enviamos las tablas de cada equipo con los datos de los jugadores
            TablaTeam1: TablaTeam1,
            TablaTeam2: TablaTeam2,
            //enviamos el número de jugadores de cada equipo
            NumeroDeIntegrasteDelEquipo1: NumeroDeIntegrasteDelEquipo1,
            NumeroDeIntegrasteDelEquipo2: NumeroDeIntegrasteDelEquipo2,
            //enviamos la ID de la convocatoria
            ID_CONVOCSTORIA: ID_CONVOCSTORIA,
        },
        //indicamos que los datos se enviarán con el metodo POST y que sera de tipo TEXTO
        type: "POST",
        datatype: "text",
        // Función que se ejecura si la petición es correcta.
        success: function (resultado) {

            //Si no hubo problemas en php, este enviará un "ok"
            if (resultado == "ok") {//si lo que nos devuelve el php es "ok" 
                // mostramos un mensaje que la petición se realizó con éxito.
                mostrarAlerta("DATOS GUARDADOS CORRECTAMENTE.", "correcto");
                // Actulizamos la tabla de cada jugador
                llenar_tablas();
                // Actulizamos las estadísticas.
                ActualizarEstadisticas(ID_CONVOCSTORIA);
            } else {
                // Mostrará un mensaje de error cuando algo falle en el php.
                mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR, NO SE PUDO GUARDAR LOS DATOS.", "incorrecto");
            }
        },
        //Función que es ejecutará cuando la pateción no se pudo estableceer.
        error: function (jqXHR, textStatus) {
            //Mostramos un mensaje con un error de servidor.
            mostrarAlerta("HUBO UN ERROR INTERNO DEL SERVIDOR, INTENTELO MÁS TARDE.", "incorrecto");
        }
    });
}
//  Función que nos ayudara a aumentar los datos de los inputs
//recibe el tipo de dato, id del input, el id del jugador y de que team el jugador
function add(tipo, id, idJugador, team) {
    // recuperamos el valor del input
    var dato = document.getElementById(id).value;
    //aumentamos en uno su valor
    dato++;
    //asignamos el valor    
    document.getElementById(id).value = dato;
    //guardamos el dato aumentado dependiendo de qué tipo datos es, y dependiendo del jugador y equipo
    //dependiedo del tipo, sera la función que se ejecutara y actulizará la tabla.
    switch (tipo) {
        case "T":
            //Función anteriormente documentada.
            guardarT(idJugador, team, true, id);
            break;
        case "S":
            //Función anteriormente documentada.
            guardarS(idJugador, team, true, id);
            break;
        case "I":
            //Función anteriormente documentada.
            guardarI(idJugador, team, true, id);
            break;
        case "A":
            //Función anteriormente documentada.
            guardarA(idJugador, team, true, id);
            break;
        case "C1":
            //Función anteriormente documentada.
            guardarC1(idJugador, team, true, id);
            break;
        case "C2":
            //Función anteriormente documentada.
            guardarC2(idJugador, team, true, id);
            break;
        case "C3":
            //Función anteriormente documentada.
            guardarC3(idJugador, team, true, id);
            break;
        case "PA":
            //Función anteriormente documentada.
            guardarPA(idJugador, team, true, id);
            break;
        case "SA":
            //Función anteriormente documentada.
            guardarSA(idJugador, team, true, id);
            break;
        case "I4":
            //Función anteriormente documentada.
            guardarI4(idJugador, team, true, id);
            break;
    }
}
//  Función que nos ayudara a reducir los datos de los inputs
//recibe el tipo de dato, id del input, el id del jugador y de que team el jugador
function reduce(tipo, id, idJugador, team) {
    // recuperamos el valor del input
    var dato = document.getElementById(id).value;
    //controlamos si el valor es diferente de cero, para evitar numeros negativos
    if (dato != 0) {
        //disminuimos el valor del input
        dato--;
        //actualizamos el input con el nuevo dato
        document.getElementById(id).value = dato;
        //guardamos el dato aumentado dependiendo de que tipo datos es, y dependiendo del jugador y equipo
        //dependiedo del tipo, sera la función que se ejecutara y actulizara la tabla
        switch (tipo) {
            case "T":
                //Función anteriormente documentada.
                guardarT(idJugador, team, false, id);
                break;
                //Función anteriormente documentada.
            case "S":
                //Función anteriormente documentada.
                guardarS(idJugador, team, false, id);
                break;
            case "I":
                //Función anteriormente documentada.
                guardarI(idJugador, team, false, id);
                break;
            case "A":
                //Función anteriormente documentada.
                guardarA(idJugador, team, false, id);
                break;
            case "C1":
                //Función anteriormente documentada.
                guardarC1(idJugador, team, false, id);
                break;
            case "C2":
                //Función anteriormente documentada.
                guardarC2(idJugador, team, false, id);
                break;
            case "C3":
                //Función anteriormente documentada.
                guardarC3(idJugador, team, false, id);
                break;
            case "PA":
                //Función anteriormente documentada.
                guardarPA(idJugador, team, false, id);
                break;
            case "SA":
                //Función anteriormente documentada.
                guardarSA(idJugador, team, false, id);
                break;
            case "I4":
                //Función anteriormente documentada.
                guardarI4(idJugador, team, false, id);
                break;
        }
    }
}

//Esta función recibe como parametros el ID del jugador, y a qué equipo pertenece.
function buscar(idJugador, team) {
    //Variables que nos ayudan a recorrer la tabla
    var i, j;
    //Buscamos en la tabla 1
    if (team == "TEAM1") {
        //Recorremos las filas de la tabla.
        for (i = 0; i < NumeroDeIntegrasteDelEquipo1; i++) {
            //Recorremos las columnas de la tabla.
            for (j = 0; j < 12; j++) {
                //Verificamos si en la posición i,j se encuntra el jugador deseado.
                if (TablaTeam1[i][j] == idJugador) {
                    //Retornamos la posición en x de la tabla
                    return i;
                }
            }
        }
    }
    //Buscamos en la tabla 2
    if (team == "TEAM2") {
        //Recorremos las filas de la tabla
        for (i = 0; i < NumeroDeIntegrasteDelEquipo2; i++) {
            //Verificamos si en la posición i,j se encuntra el jugador deseado
            for (j = 0; j < 12; j++) {
                //Retornamos la posicon en x de la tabla
                if (TablaTeam2[i][j] == idJugador) {
                    //Retornamos la posicon en x de la tabla
                    return i;
                }
            }
        }
    }
}

