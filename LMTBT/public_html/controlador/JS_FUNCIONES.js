/* Este archivo contiene funciones que varios scripts pueden utilizar */

/* Echa a un usuario de una página a la que no tiene acceso, redireccionándolo al index.
 * Esta función se debe ejecutar desde un frame en el primer nivel (que esté en la pág. del marco).
 * 
 * [Página principal (marco, menú) - Nivel 0]
 * ├── [Frame - 1er nivel]
 * │    ├── [Frame - 2o nivel]
 * ┴    ├── [Frame - 2o nivel]
 */
function expulsar(){
    window.location.replace("index.php");
}

/**
 * Revisa si el valor de un input de tipo 'number' es válido, de no ser así,
 * lo corrige. Se sugiere ejecutarlo en los 'onchange', así: 
 * onchange="validarNum(this)"
 * @param {Numbrer} Un input de tipo "number".
 */
function validarNum(num){
    if(num.value < parseInt(num.min))
        num.stepUp();
    else if(num.value > parseInt(num.max))
        num.stepDown();
    else if(num.value % parseInt(num.step) !== 0)
        num.value = num.value - (num.value % parseInt(num.step));
}

/**
 * Hace que la primera letra de una cadena sea mayúscula y el resto, minúscula.
 * Ej: "hOLa MUnDO" -> "Hola mundo"
 * @param {string} s : Una cadena cualquiera.
 * @return {string} La cadena modificada.
 */
function capitalizar(s) {
    return s.charAt(0).toUpperCase() + s.slice(1).toLowerCase();
}

/**
 * Crea el código HTML de un modal de Bootstrap y lo agrega a la página actual.
 * Recibe 4 booleanos.
 * 
 * El modal, como tal, tiene el id 'modal'.
 * Si add_btn_cerrar_sup o add_titulo es verdadero, el modal tiene una cabecera con el id de 'modal-header'.
 * Si add_titulo es verdadero, el título tiene el id 'modal-title'.
 * El cuerpo del modal tiene el id de 'modal-body'.
 * Si add_footer es verdadero, el modal tiene un pie con el id de 'modal-footer'.
 * 
 * @param {bool} add_btn_cerrar_sup Si el modal tendrá un botón de cierre ubicado en la esquina superior derecha.
 * @param {bool} add_titulo Indica si el modal tendrá un título.
 * @param {bool} add_footer Indica si el modal tendrá una sección inferior.
 * @param {bool} add_btn_cerrar_inf Indica si el modal tendrá un botón de cierre en la esquina inferior (no tiene efecto si add_footer es falso).
 */
function crearModal(add_btn_cerrar_sup, add_titulo, add_footer, add_btn_cerrar_inf){
    //Si la página ya tiene un modal, lo borramos.
    $("#modal").remove();

    var codigoHTML = "<div class='modal fade' id='modal' role='dialog'>" +
            "<div class='modal-dialog'>" +
                "<div class='modal-content'>";
    
    if(add_btn_cerrar_sup || add_titulo){
        codigoHTML += "<div class='modal-header' id='modal-header'>" +
                          (add_btn_cerrar_sup ? "<button type='button' class='close' data-dismiss='modal'>&times;</button>" : "") +
                          (add_titulo ? "<h4 class='modal-title' id='modal-title'>Título</h4>" : "") +
                      "</div>";
    }
    
    codigoHTML += "<div class='modal-body' id='modal-body'></div>";
    
    if(add_footer){
        codigoHTML += "<div class='modal-footer' id='modal-footer'>" +
                          (add_btn_cerrar_inf ? "<button type='button' class='btn btn-default' data-dismiss='modal'>Cerrar</button>" : "") +
                      "</div>";
    }
    
    codigoHTML += "</div>" +
            "</div>" +
        "</div>";
    
    $(document.body).append(codigoHTML);
}

/**
 * Este método está hecho para ejecutase en el evento "oninput"
 * (es decir, el evento ejecutado cuando el usuario cambia el valor)
 * de los inputs de las páginas de edición/modificación. Así:
 * oninput="oninput_input_edicion(this);"
 * 
 * Nota: El dato original se refiere al dato de lo que se modifica
 * (una cuenta, un equipo, etc.), antes de la edición.
 * 
 * Cada vez que se hace un cambio en el input, cambia su color a:
 * - Rojo si originalmente había un dato, pero ha sido eliminado.
 * - Celeste si originalmente había un dato, y este se acaba de modificar.
 * - Blanco si originalmente había un dato y este está intacto.
 * - Rojo claro si originalmente no había un dato, y el usuario no ha introducido nada.
 * - Verde si originalmente no había un dato, pero ha sido agregado.
 * 
 * Para que esto funcione se deben cumplir lo siguiente:
 * - La propiedad booleana 'required' del input, indica si originalmente tenía un valor o no.
 * - En caso de que el input tenga un valor original, se encuentra almacenado en su propiedad "defaultValue".
 * - Este evento está asignado al input desde el HTML.
 * 
 * Advertencia: El evento no es ejecutado si el valor de input se modifica por medio
 * de Javascript, si hace eso, debe ejecutar: $(input).trigger("input");
 * 
 * @param {Objeto de Javascript} input : Un elemento del tipo 'input'.
 */
function oninput_input_edicion(input){    
    if(input.required){
        if(input.defaultValue === input.value)
            $("#" + input.id).css("background-color", "white");
        else if(input.value === "")
            $("#" + input.id).css("background-color", "#ff0000");
        else
            $("#" + input.id).css("background-color", "#b3d9ff");
    } else {
        if(input.defaultValue === input.value)
            $("#" + input.id).css("background-color", "#ff9999");
        else
            $("#" + input.id).css("background-color", "#80ffaa");
    }
}

/**
 * Este es un evento complemento para los inputs de tipo 'text'
 * que hagan uso de oninput_input_edicion. Se coloca de la siguiente forma:
 * oninput="onchange_input_edicion_txt(this);"
 * 
 * Elimina espacios innecesarios después de que el usuario escriba algo en él.
 * 
 * @param {Objeto de Javascript} input Un elemento del tipo 'input' y tipo 'text'.
 */
function onchange_input_edicion_txt(input){
    if(input.type === "text"){
        eliminar_espacios_input(input);
        $(input).trigger("input");
    }
}

/**
 * Elimina espacios innecesarios del valor (un string) de un input.
 * - Espacios al principio y fin del valor.
 * - Espacios de más en medio del valor.
 * 
 * EJEMPLOS:
 * "   Hola  mundo  " -> "Hola mundo"
 * "Crema     y  café" -> "Crema y café"
 * 
 * @param {Objeto de Javascript} input Un elemento del tipo 'input'.
 */
function eliminar_espacios_input(input){
    input.value = $.trim(input.value);
    input.value = input.value.replace(/ +(?= )/g,'');
}

/**
 * Permite inicializar un input de una página de edición.
 * Use está función sólo tambien usa la función 'oninput_input_edicion'
 * de este script.
 * 
 * @param {Objeto de Javascript} input : Un elemento del tipo 'input'.
 * @param {string} dato_original : (Opcional) El valor original del campo.
 */
function inicializar_input_edicion(input, dato_original = null){
    if(dato_original !== null){
        input.defaultValue = input.value = dato_original;
        input.required = true;
    } else
        input.required = false;
    
    $(input).trigger("input");
}

/**
 * Recibe una cadena con el mensaje a mostrar en la pantalla. Esto se debe ejecutar al momento
 * en que nosotros obtenemos respuesta del servidor dependiendo el resultado.
 * Dicho mensaje se mostrara por tres segundos.
 * Debe existir un div con id alertaSucces
 * @param {string, string} texto : texto que desea mostrar, tipo: "correcto" alerta en verde "error" elerta en rojo.
 */
function mostrarAlerta(texto,tipo){
    if(tipo === "correcto"){
        alerta ="<div class='alert alert-success'><strong>Success!</strong> "+texto+" </div>";
    }else{
        alerta ="<div class='alert alert-danger'><strong>Failed!</strong> "+texto+" </div>";
    }   
    $('#alertaSucces').empty();
    $('#alertaSucces').append(alerta);
    setTimeout(borrarAlert, 5000);
}

/*
 * Elimina el mensaje de la pantalla.
 */
function borrarAlert(){
    $('#alertaSucces').empty();     
}