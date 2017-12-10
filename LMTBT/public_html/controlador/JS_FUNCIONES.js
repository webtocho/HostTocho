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
 * Permite saber si el usuario está loguedo y tiene un tipo de cuenta en específico.
 * @param {Array} tipos : Un arreglo con el o los tipos de usuario a comprobar.
 *                        PE: {"COACH", "ADMINISTRADOR"} {"JUGADOR"}
 * @return {bool/null} - null si el usuario no inició sesión o hubo un error al hacer la comprobación.
 *                     - true si el usuario está logueado y es de alguno de los tipos señalados.
 *                     - false si el usuario está logueado, pero no es del tipo correcto.
 */
function comprobarSesion(tipos){
    if(tipos.length === 0)
        return null;
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos : JSON.stringify(tipos)}, "text")
        .done(function(res) {
            if(res === "si")
                return true;
            else if(res === "no")
                return false;
            else
                return null;
        })
        .fail(function() {
            return null;
        });
}

/**
 * Revisa si el valor de un 'number' es válido, de no ser así,
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