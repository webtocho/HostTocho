/* Controlador de la página para buscar cuentas.
   La página debe ser siempre abierta desde un iFrame */

//El ID de la categoría (varonil, femenil), en caso de que se estén buscando jugadores.
var id_cat = null;
//El texto que va a mostrar el botón de cada resultado.
var nb_boton = null;
//En nombre de la función que se va a ejecutar en la página padre (ya que esta se ve en un iFrame).
var nb_funcion = null;

/**
 * Inicializa la página EN CASO DE QUE SEA ABIERTA DESDE UN IFRAME.
 * 
 * Si no manda un tipo, la página permitirá buscar usuarios de cualquier tipo.
 * Si el tipo es "JUGADOR" o "COACH", la página sólo podrá buscar usuarios de dicho tipo.
 * Si el tipo es otra cosa, se muestra un error.
 * @param {string} tipo : Un dato opcional.
 * @param {string} nombre_boton : El texto que tiene el botón que se mostrará en cada fila de los resultados.
 * @param {string} nombre_funcion : El nombre de la función que se llamará en la página que invocó este frame.
 *                                  Dicha función debe recibir un parámetro (el id de una cuenta).
 */
function inicializar(tipo, nombre_boton, nombre_funcion){
    /* Al ejecutar esta función, la página sólo podrá buscar un tipo de cuenta en específico (a menos que tipo == "TODOS").
       Si tipo vale "JUGADOR" o "COACH" se eliminan y muestran los controles necesarios para buscarlos. */
    if(tipo === "JUGADOR"){
        $("#titulo").html($("#titulo").html().replace("usuarios", "jugadores"));
        $("#_tipo").remove();
        $("#tipo").remove();
        
        $("#_sexo").show();
        $("#_edad").show();
        $("#edad").show();
        $("#incluir").show();
    } else if(tipo === "COACH") {
        $("#titulo").html($("#titulo").html().replace("usuarios", "coaches"));
        $("#_tipo").remove();
        $("#tipo").remove();
        
        $("#_sexo").remove();
        $("#_edad").remove();
        $("#edad").remove();
        $("#incluir").remove();
    } else if(tipo === "TODOS") {
        //Nada
    } else {
        $("#contenido").html("Error grave");
    }
    
    nb_boton = nombre_boton;
    nb_funcion = nombre_funcion;
}

/**
 * Permite cambiar la categoría para filtrar a los jugadores (en caso de que se busquen cuentas de este tipo).
 * Esta función está diseñada para ser ejecutada por la página de creación de rosters.
 * @param {int} id - El ID de la nueva categoría.
 */
function cambiarCategoria(id){
    id_cat = id;
}

$( document ).ready(function() {
    //Indicamos que cuando el usuario cambie el tipo de cuenta a buscar, desaparezcan o aparezcan los controles necesarios.
    $("#tipo").change(function(){
        if(document.getElementById("tipo").value === "JUGADOR"){
            $("#_sexo").show();
            $("#_edad").show();
            $("#edad").show();
            $("#incluir").show();
        } else {
            $("#_sexo").hide();
            $("#_edad").hide();
            $("#edad").hide();
            $("#incluir").hide();
        }
    });
    
    $("#tipo").trigger("change");
});

/**
 * Efectúa una búsqueda, de acuerdo a las opciones de filtrado que haya puesto el usuario.
 */
function buscar(){
    //Los parámetros que se van a mandar en la petición.
    var parametros = {};
    parametros["fn"] = "buscar";
    
    //Metemos a los parámetros los filtros de nombre, apellidos y si se desea que en los resultados se muestre la foto de perfil.
    document.getElementById("nb").value = $.trim(document.getElementById("nb").value);
    document.getElementById("ap").value = $.trim(document.getElementById("ap").value);
    parametros["nb"] = document.getElementById("nb").value;
    parametros["ap"] = document.getElementById("ap").value;
    parametros["i_f"] = (document.getElementById("i_f").checked ? "1" : "0");
    
    if((document.getElementById("tipo") === null && document.getElementById("incluir") !== null) || (document.getElementById("tipo") !== null && document.getElementById("tipo").value === "JUGADOR")){
        //Si estamos buscando jugadores, agregamos sus filtros exclusivos a la petición.
        
        parametros["tipo"] = "JUGADOR";
        
        if(id_cat != null)
            parametros["id_cat"] = id_cat;
        
        if(document.getElementById("_sexo") !== null)
            parametros["sexo"] = $('input[name=sexo]:checked').val();
        
        if(document.getElementById("edad") !== null)
            parametros["edad"] = document.getElementById("edad").value;
        
        //Indicamos qué datos de los jugadores se van a incluir en los resultados.
        parametros["i_c"] = (document.getElementById("i_c").checked ? "1" : "0");
        parametros["i_s"] = (document.getElementById("i_s").checked ? "1" : "0");
        parametros["i_e"] = (document.getElementById("i_e").checked ? "1" : "0");
    } else if (document.getElementById("tipo") === null && document.getElementById("incluir") === null)
        parametros["tipo"] = "COACH";
    else
        parametros["tipo"] = document.getElementById("tipo").value;
    
    //Hacemos una primera petición al servidor para saber cuántos resultados (en total) arroja la búsqueda.
    $.ajax({
        url: "../controlador/SRV_CUENTAS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        beforeSend: function() {
            $("#filtros :input").prop("disabled", true);
            $("#resultados").html("<img src='../modelo/img/RC_IF_CARGANDO.gif'>");
        },
        success: function (cantidadTotalDeResultados) {
            //Preparamos la paginación (con el plugin pagination.js), para que el usuario vea los resultados de la búsqueda por página.
            $('#paginas').pagination({
                dataSource: '../controlador/SRV_CUENTAS.php',
                locator: 'items',
                totalNumber: parseInt(cantidadTotalDeResultados),
                pageSize: document.getElementById("rp").value,
                ajax: {
                    type: "POST",
                    data: parametros,
                    beforeSend: function() {
                        $("#filtros :input").prop("disabled", true);
                        $("#resultados").html("<img src='../modelo/img/RC_IF_CARGANDO.gif'>");
                    }
                },
                formatAjaxError: function (xhr, status, errorThrown) {
                    $("#resultados").html("<h5>" + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")") +  "</h5>");
                    $("#filtros :input").prop("disabled", false);
                    return;
                },
                callback: function(data, pagination) {
                    /* Caja vez que el usuario cambie de página se hace una llamada al servidor, "data" contiene los resultados de la búsqueda.
                       En esta función ("callback") se muestran los resultados al usuario. */
                    
                    if(Object.keys(data).length === 0){
                        $("#resultados").html("<h5>Sin resultados</h5>");
                    } else {
                        //Borramos los resultados anteriores.
                        $("#resultados").html("");
                        //Estas variables nos dicen qué datos se van a mostrar (según las opciones del usuario).
                        var incluir_correo, incluir_sexo, incluir_edad, incluir_foto;
                        //Se le dan valor a las 4 variables.
                        incluir_correo = incluir_sexo = incluir_edad = incluir_foto = false;
                        incluir_foto = document.getElementById("i_f").checked;
                        if((document.getElementById("tipo") === null && document.getElementById("incluir") !== null) || (document.getElementById("tipo") !== null && document.getElementById("tipo").value === "JUGADOR")){
                            incluir_correo = document.getElementById("i_c").checked;
                            incluir_sexo = document.getElementById("i_s").checked;
                            incluir_edad = document.getElementById("i_e").checked;
                        }
                        
                        //Creamos la tabla como elemento de DOM.
                        var tabla = document.createElement("TABLE");
                        var cabecera = tabla.createTHead().insertRow(-1);
                        var cuerpo = document.createElement("TBODY");
                        tabla.appendChild(cuerpo);
                        //Colocamos las cabeceras de la tabla.
                        cabecera.insertCell(-1).innerHTML = "<b>Apellidos</b>";
                        cabecera.insertCell(-1).innerHTML = "<b>Nombre</b>";
                        if(incluir_correo)
                            cabecera.insertCell(-1).innerHTML = "<b>Correo</b>";
                        if(incluir_sexo)
                            cabecera.insertCell(-1).innerHTML = "<b>Género</b>";
                        if(incluir_edad)
                            cabecera.insertCell(-1).innerHTML = "<b>Edad</b>";
                        if(incluir_foto)
                            cabecera.insertCell(-1).innerHTML = "<b>Fotografía</b>";
                        
                        if(nb_boton !== null && nb_funcion !== null){
                            //Si se ejecutó el método "inicializar" se agrega esto a la cabecera.
                            cabecera.insertCell(-1).innerHTML = "<b>Acciones</b>";
                        }
                        
                        var j;
                        
                        $.each(data, function( index, i ) {
                            //Recorremos los resultados jugador por jugador, agregando su información a la tabla.
                            //"j" nos ayuda a recorrer el arreglo de cada resultado.
                            j = 0;
                            var fila = cuerpo.insertRow(-1);
                            var id = i[j++];
                            
                            fila.insertCell(-1).innerHTML = i[j++];
                            fila.insertCell(-1).innerHTML = i[j++];
                            
                            //Metemos a la tabla la información extra que el usuario decidió obtener.
                            if(incluir_correo)
                                fila.insertCell(-1).innerHTML = i[j++];
                            if(incluir_sexo)
                                fila.insertCell(-1).innerHTML = (i[j++] === "M" ? "Masculino" : "Femenino");
                            if(incluir_edad)
                                fila.insertCell(-1).innerHTML = i[j++];
                            if(incluir_foto){
                                var foto = i[j++];
                                
                                if(foto === null)
                                    fila.insertCell(-1).innerHTML = "<img src=\"../modelo/img/RC_IF_ANONIMO.png\" width='75'/>";
                                else
                                    fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + foto +"\" width='75'/>";
                            }
                            
                            if(nb_boton !== null && nb_funcion !== null){
                                //Si se ejecutó la función "inicializar", agregamos un botón por cada resultado, que llame una función del padre de esta página (ya que se muestra en un iFrame).
                                fila.insertCell(-1).innerHTML = "<button class='btn btn-info' onclick='window.parent." + nb_funcion + "(" + id + ");'>" + nb_boton + "</button>";
                            }
                        });
                        
                        //Le damos estilo a la tabla y la mostramos en pantalla.
                        $(tabla).addClass("table table-striped table-bordered");
                        document.getElementById("resultados").appendChild(tabla);
                    }
                    
                    $("#filtros :input").prop("disabled", false);
                }
            });
        },
        error: function (xhr, status) {
            $("#resultados").html("<h5>" + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")") +  "</h5>");
        },
        complete : function(){
            $("#filtros :input").prop("disabled", false);
        }
    });
}