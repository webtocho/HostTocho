var id_cat = null;
var nb_boton = null;
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
    } else {
        $("#contenido").html("Error grave");
    }
    
    nb_boton = nombre_boton;
    nb_funcion = nombre_funcion;
}

function cambiarCategoria(id){
    id_cat = id;
}

$( document ).ready(function() {
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

function buscar(){
    var parametros = {};
    parametros["fn"] = "buscar";
    
    document.getElementById("nb").value = $.trim(document.getElementById("nb").value);
    document.getElementById("ap").value = $.trim(document.getElementById("ap").value);
    parametros["nb"] = document.getElementById("nb").value;
    parametros["ap"] = document.getElementById("ap").value;
    parametros["i_f"] = (document.getElementById("i_f").checked ? "1" : "0");
    
    if((document.getElementById("tipo") === null && document.getElementById("incluir") !== null) || (document.getElementById("tipo") !== null && document.getElementById("tipo").value === "JUGADOR")){
        parametros["tipo"] = "JUGADOR";
        
        if(id_cat != null)
            parametros["id_cat"] = id_cat;
        
        if(document.getElementById("_sexo") !== null)
            parametros["sexo"] = $('input[name=sexo]:checked').val();
        
        if(document.getElementById("edad") !== null)
            parametros["edad"] = document.getElementById("edad").value;
        
        parametros["i_c"] = (document.getElementById("i_c").checked ? "1" : "0");
        parametros["i_s"] = (document.getElementById("i_s").checked ? "1" : "0");
        parametros["i_e"] = (document.getElementById("i_e").checked ? "1" : "0");
    } else if (document.getElementById("tipo") === null && document.getElementById("incluir") === null)
        parametros["tipo"] = "COACH";
    else
        parametros["tipo"] = document.getElementById("tipo").value;
    
    $.ajax({
        url: "../controlador/SRV_CUENTAS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        beforeSend: function() {
            $("#filtros :input").prop("disabled", true);
            $("#resultados").html("<img src='img/RC_IF_CARGANDO.gif'>");
        },
        success: function (res) {
            $('#paginas').pagination({
                dataSource: '../controlador/SRV_CUENTAS.php',
                locator: 'items',
                totalNumber: parseInt(res),
                pageSize: document.getElementById("rp").value,
                ajax: {
                    type: "POST",
                    data: parametros,
                    beforeSend: function() {
                        $("#filtros :input").prop("disabled", true);
                        $("#resultados").html("<img src='img/RC_IF_CARGANDO.gif'>");
                    }
                },
                formatAjaxError: function (xhr, status, errorThrown) {
                    $("#resultados").html("<h5>" + (xhr.status == 500 ? xhr.responseText : "(" + xhr.status + " " + status + ")") +  "</h5>");
                    $("#filtros :input").prop("disabled", false);
                    return;
                },
                callback: function(data, pagination) {
                    if(Object.keys(data).length === 0){
                        $("#resultados").html("<h5>Sin resultados</h5>");
                    } else {
                        $("#resultados").html("");
                        var incluir_correo, incluir_sexo, incluir_edad, incluir_foto;
                        incluir_correo = incluir_sexo = incluir_edad = incluir_foto = false;
                        incluir_foto = document.getElementById("i_f").checked;
                        
                        if((document.getElementById("tipo") === null && document.getElementById("incluir") !== null) || (document.getElementById("tipo") !== null && document.getElementById("tipo").value === "JUGADOR")){
                            incluir_correo = document.getElementById("i_c").checked;
                            incluir_sexo = document.getElementById("i_s").checked;
                            incluir_edad = document.getElementById("i_e").checked;
                        }
                        
                        var tabla = document.createElement("TABLE");
                        var cabecera = tabla.createTHead().insertRow(-1);
                        var cuerpo = document.createElement("TBODY");
                        tabla.appendChild(cuerpo);
                        
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
                            cabecera.insertCell(-1).innerHTML = "<b>Acciones</b>";
                        }
                        
                        var j;
                        
                        $.each(data, function( index, i ) {
                            j = 0;
                            var fila = cuerpo.insertRow(-1);
                            var id = i[j++];
                            
                            fila.insertCell(-1).innerHTML = i[j++];
                            fila.insertCell(-1).innerHTML = i[j++];
                            if(incluir_correo)
                                fila.insertCell(-1).innerHTML = i[j++];
                            if(incluir_sexo)
                                fila.insertCell(-1).innerHTML = (i[j++] === "M" ? "Masculino" : "Femenino");
                            if(incluir_edad)
                                fila.insertCell(-1).innerHTML = i[j++];
                            if(incluir_foto){
                                var foto = i[j++];
                                
                                if(foto === null)
                                    fila.insertCell(-1).innerHTML = "<img src=\"img/RC_IF_ANONIMO.png\" width='75'/>";
                                else
                                    fila.insertCell(-1).innerHTML = "<img src=\"data:image/png;base64," + foto +"\" width='75'/>";
                            }
                            
                            if(nb_boton !== null && nb_funcion !== null){
                                fila.insertCell(-1).innerHTML = "<button class='btn btn-info' onclick='window.parent." + nb_funcion + "(" + id + ");'>" + nb_boton + "</button>";
                            }
                        });
                        
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