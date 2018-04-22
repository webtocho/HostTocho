$(document).ready(function () {
    /*
     * Hacemos una petición para obtener la información de la cuenta y asi saber si tiene
     * los permisos necesarios para acceder de lo contrario sera expulsado de la pagina.
     */
    $.post("../controlador/SRV_SESION_GET.php", {tipos: ["ADMINISTRADOR"]}, null, "text")
            .done(function (res) {
                switch (parseInt(res)) {
                    case 0:
                        detallesConvocatoria();
                        break;
                    default:
                        expulsar();
                        return;
                }
            })
            .fail(function () {
                expulsar();
            });
});

/**
 * Realiza una peticion al servidor para obtener los datos pertenecientes a un convocatoria
 * la cual ha sido seleccionada por el administrador y asi poder ver la lista de los equipos inscritos
 */
function detallesConvocatoria() {
    var id = sessionStorage.getItem("id_convocatoria");
    var repeticion = 1;
    console.log(repeticion);
    $.ajax({
        url: "../controlador/SRV_CONVOCATORIAS.php",
        data: {
            tipo: "consulta_especifica",
            id: id
        },
        type: "POST",
        datatype: "text",
        success: function (resultado) {
            $('#info_convocatoria').empty();
            $('#info_convocatoria').append(resultado);
            $('#enventos').empty();
            $('#enventos').append('<a href="#body" id="gototop" class="desing">Ir Arriba</a>' +
                    '<a href="#body" class="desing" onclick="abrirPantallaParaEditarConsulta(' + id + ')">   Cambiar fechas</a>' +
                    '<a href="#body" class="desing" onclick="CREAR_ROL_JUEGOS(' + id + ',' + repeticion + ')">   Generar rol</a>' +
                    '<style type="text/css">' +
                    '@media(max-width: 550px){' +
                    '.desing{' +
                    'width: 100%;' +
                    'background-color: gray;' +
                    'color: #ffffff;' +
                    'padding: 10px;}}</style>');
            llenarTablaEquipos(id);
        },
        error: function (jqXHR, textStatus) {
            //alert("Error de ajax");
        }
    });
}
/**
 * Realiza un peticion al servidor para obtener la lista de equipos inscritos a una convocatoria
 * la cual ha sido previamente seleccionada por el administrador
 * @param {string} id es el identificador de la convocatoria, para saber cual se ha seleccionado.
 */
function llenarTablaEquipos(id) {
    $.ajax({
        url: "../controlador/SRV_CONVOCATORIAS.php",
        data: {
            tipo: "recuperar_equipos_inscritos",
            id: id
        },
        type: "POST",
        datatype: "text",
        success: function (resultado) {
            $('#contenidoTabla').empty();
            $('#contenidoTabla').append(resultado);
        },
        error: function (jqXHR, textStatus) {
            //alert("Error de ajax");
        }
    });
}
/**
 * Despliega un ventana modal o pantalla emergente para poder modificar la fecha de cierre de una convocatoria
 * @param {string} id es el identificador de la convocatoria que se ha seleccionado
 */
function abrirPantallaParaEditarConsulta(id) {
    $('#tituloVentanaEmergente').empty();
    $('#tituloVentanaEmergente').append("Modificar fecha de cierre");
    document.getElementById("formulario").onsubmit = function () {
        editarFecha(id);
        return false;
    };
    //Mostramos la ventana emergente
    $('#ventanaEmergente').modal();
}
/**
 * Realiza petición al servidor y envía datos los cuales permiten al administrador
 * poder editar la fecha de cierre de una convocatoria
 * @param {string} id es el identificador de la convocatoria que se ha seleccionado
 */
function editarFecha(id) {
    $('#ventanaEmergente').modal('hide');
    var nueva_fecha = document.getElementById("nueva_fecha").value;
    $.ajax({
        url: "../controlador/SRV_CONVOCATORIAS.php",
        data: {
            tipo: "modificar",
            nueva_fecha: nueva_fecha,
            id: id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="../modelo/img/RC_IF_CARGANDO.gif" ></center>');
        },
        success: function (resultado) {
            if (resultado == "ok") {
                mostrarAlerta("Cambio realizado con exito", "correcto");
            } else {
                mostrarAlerta(resultado, "fallido");
            }
        },
        error: function (jqXHR, textStatus) {
            mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.", "fallido");
        }
    });
}
/**
 * Despliega un ventana modal o pantalla emergente para poder cambiar el status de un roster inscrito
 * a pagado para indicar que dicho equipo ya ha pagado su inscripcion
 * @param {string} id es el identificador del roster de un equipo inscrito a una convocatoria.
 */
function abrir_pantalla_para_poner_pago(id) {
    document.getElementById("botonConfirmacion").onclick = function () {
        poner_pagado(id);
    };
    $('#ventanaConfirmacion').modal();
}
/**
 * Realiza petición al servidor y envía datos los cuales permiten al administrador poder editar el estatus
 * de un roster perteneciente a un equipo, para indicar que ha pagado su inscripción
 * @param {string} id es el identificador del roster de un equipo inscrito a una convocatoria.
 */
function poner_pagado(id) {
    $('#ventanaConfirmacion').modal('hide');
    $.ajax({
        url: "../controlador/SRV_CONVOCATORIAS.php",
        data: {
            tipo: "poner_pagado",
            id: id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="../modelo/img/RC_IF_CARGANDO.gif" ></center>');
        },
        success: function (resultado) {
            if (resultado == "ok") {
                mostrarAlerta("Cambio realizado con exito", "correcto");
                $('#eventos' + id).empty();
                $('#eventos' + id).append("<a class='news' href='#body'><h5>PAGADO</h5></a>");
            } else {
                mostrarAlerta(resultado, "fallido");
            }
        },
        error: function (jqXHR, textStatus) {
            mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.", "fallido");
        }
    });
}
/**
 * Despliega un ventana modal o pantalla emergente para poder expulsar a un roster inscrito
 * si el equipo no ha pagado su incripcion.
 * @param {string} id es el identificador del roster de un equipo inscrito a una convocatoria.
 */
function abrir_pantalla_para_expulsar(id) {
    document.getElementById("botonConfirmacion").onclick = function () {
        expulsar(id);
    };
    $('#ventanaConfirmacion').modal();
}
/**
 * Realiza petición al servidor y envía datos los cuales permiten al administrador expulsar
 * al roster de un equipo, el cual se encuentra inscrito a una convocatoria.
 * @param {string} id es el identificador del roster de un equipo inscrito a una convocatoria.
 */
function expulsar(id) {
    $('#ventanaConfirmacion').modal('hide');
    $.ajax({
        url: "../controlador/SRV_CONVOCATORIAS.php",
        data: {
            tipo: "expulsar",
            id: id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="../modelo/img/RC_IF_CARGANDO.gif" ></center>');
        },
        success: function (resultado) {
            if (resultado == "ok") {
                mostrarAlerta("Cambio realizado con exito", "correcto");
                fila = document.getElementById(id);
                padre = fila.parentNode;
                padre.removeChild(fila);
            } else {
                mostrarAlerta(resultado, "fallido");
            }
        },
        error: function (jqXHR, textStatus) {
            mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.", "fallido");
        }
    });
}