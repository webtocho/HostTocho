var id_equipo;
var id_coach_viejo, id_coach_nuevo;
var nombre_viejo;

var es_coach = false;

var posibles_id = ["-1"];

$( document ).ready(function() {
    //Desde EQUIPOS_VER se nos manda el id del equipo a consultar.
    id_equipo = sessionStorage.getItem("id_equipo");
    if(id_equipo === null){
        document.location.href = "EQUIPOS_VER.html";
        return;
    } else{
        sessionStorage.removeItem("id_equipo");
    }
    id_coach_nuevo = -1;
    
    //Al momento de cargar la página debemos confirmar que es esté accediento sea un COACH o un ADMIN.
    $.ajax({
        url: "../controlador/SRV_GET_SESION.php",
        type: "POST",
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (resultado) {
            if(resultado["id"] == null || resultado["tipo"] == null){
                //Si el usuario no ha iniciado sesión, lo redireccionamos.
                window.location.replace("index.php");
            } else if(resultado["id"] != null && resultado["tipo"] != null){
                if(resultado["tipo"].toUpperCase() == "COACH" || resultado["tipo"].toUpperCase() == "ADMINISTRADOR"){
                    es_coach = (resultado["tipo"].toUpperCase() == "COACH");
                    $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {
                            tipo : "get_equipo",
                            id_equipo : id_equipo,
                            get_id_coach : "1",
                            get_nombre_equipo : "1",
                            get_nombre_coach : "1"
                        },
                        type: "POST",
                        dataType: 'json',
                        async: true,
                        success: function (equipo) {                            
                            if(equipo.hasOwnProperty('error')){
                                alert(equipo["error"]);
                                document.location.href = "EQUIPOS_VER.html";
                            } else {
                                id_coach_viejo = id_coach_nuevo = parseInt(equipo["ID_COACH"]);
                                document.getElementById("nombre").value = nombre_viejo = equipo["NOMBRE_EQUIPO"];
                                document.getElementById("coach").value = equipo["NOMBRE_COACH"];
                            }
                        },
                        error: function (jqXHR, textStatus) {
                            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
                        }
                    });
                } else {
                    $('#contenido').html("<h2>Su cuenta no tiene acceso a esta página.</h2>");
                }
            } else {
                console.log("SRV_GET_SESION.php no funciona como debería.");
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        },
    });
});

function buscarCoaches(){
    var apellido = $.trim(document.getElementById("busqueda_coach").value);
    
    if(apellido.length > 2){
        var opciones = "<option value=\"-1\" selected=\"selected\">Sin resultados</option>";
        
        $.ajax({
            url: "../controlador/SRV_CONSULTAS.php",
            data: {
                tipo : "buscar_coaches",
                criterio : apellido
            },
            type: "POST",
            dataType: 'json',
            async: true,
            success: function (datos_de_usuario) {                
                if(Object.keys(datos_de_usuario).length > 0){
                    opciones = "";
                    posibles_id = ["-1"];
                    
                    $.each(datos_de_usuario , function( index, i ) {
                        posibles_id.push(parseInt(i[0]));
                        opciones += "<option value=\"" + i[0] + "\">" + i[1] + "</option>";
                    });
                }
            },
            complete: function (jqXHR, textStatus) {
                $("#seleccion_coach").html(opciones);
                elegirCoach();
            }
        });
    }
}

function elegirCoach(){
    var select = document.getElementById("seleccion_coach");
    if(posibles_id.indexOf(parseInt( select.options[select.selectedIndex].value ) !== -1)){
        if(parseInt(select.options[select.selectedIndex].value) > 0){
            id_coach_nuevo = parseInt( select.options[select.selectedIndex].value );
            document.getElementById("coach").value = select.options[select.selectedIndex].text;
        }
    }
}

function editarEquipo(){
    var nombre_nuevo;
    
    nombre_nuevo = $.trim(document.getElementById("nombre").value);
    
    if(nombre_nuevo.length === 0){
        alert("El nombre es inválido.");
        return false;
    }
    
    //El objeto que almacena los parámetros que se mandarán en la solicitud PHP.
    var parametros = new FormData();
    //Especificamos la función en el servidor.
    parametros.append("tipo", "editar_equipo");
    //Agregamos el id del coach.
    parametros.append("id_equipo", id_equipo);
    if(id_coach_nuevo !== id_coach_viejo){
        if(es_coach)
            if(confirm("Si continua, ya no tendrá acceso al equipo.\nPara recuperar la autoría del equipo deberá contactar con el administrador o el nuevo dueño.") == false)
                return;
        parametros.append("id_coach", id_coach_nuevo);
    }
    //Agregamos nombre del equipo.
    if(nombre_nuevo !== nombre_viejo)
        parametros.append("nombre", document.getElementById("nombre").value);
    //Agregamos el archivo seleccionado del logotipo del equipo, si es que existe.
    if(document.getElementById("logotipo").files.length !== 0)
        parametros.append("logotipo", document.getElementById('logotipo').files[0]);
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: parametros,
        type: "POST",
        dataType: 'text',
        processData: false,
        contentType: false,
        async: false,
        success: function (respuesta) {
            if(respuesta == "ok"){
                //PENDIENTE
                alert("Equipo modificado con éxito.");
                document.location.href = "EQUIPOS_VER.html";
            } else {
                alert(respuesta);
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        }
    });
}