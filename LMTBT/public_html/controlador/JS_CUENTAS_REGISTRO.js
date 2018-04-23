$( document ).ready(function(){    
    /**
     * Hacemos una petición para obtener la información de la cuenta y asi poder
     * asignarle permisos para poder registrar usuarios de ciertos tipos de cuenta.
     */
    $('#tipo_cuenta').empty();           
      $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR","COACH"]}, null, "text")
        .done(function(res){
            switch(parseInt(res)){
                case 0:
                    $('#tipo_cuenta').append("<option value=''>Seleccione tipo de cuenta</option>");
                    $('#tipo_cuenta').append("<option value='ADMINISTRADOR'>ADMINISTRADOR</option>"+"<option value='COACH'>COACH</option>"+
                    "<option value='JUGADOR'>JUGADOR</option>"+"<option value='FOTOGRAFO'>FOTOGRAFO</option>"+"<option value='CAPTURISTA'>CAPTURISTA</option>");                    
                break;
                case 1:
                    $('#tipo_cuenta').append("<option value='JUGADOR'>JUGADOR</option>");
                break;                
                default:                    
                    expulsar();
                return;
            }
        })
        .fail(function() {
            $('#tipo_cuenta').append("<option value='JUGADOR'>JUGADOR</option>");   
        });
});
/**
 * Recupera los datos del formulario con la información del usuario que desea registrarse,
 * envia los datos al servidor para poder almacenerlos y crear una cuenta nueva.
 */
$(document).on('submit','#myForm',function(event){  
    //previene que la pagina se recargue al momento de ejecutar el evento del boton
    event.preventDefault();
    
    //obtiene los datos del formulario
    var correo = document.getElementById("Correo").value;
    var password = document.getElementById("Password").value;
    var nombre = document.getElementById("nombre").value;
    var apellido_paterno = document.getElementById("ApellidoPaterno").value;
    var apellido_materno = document.getElementById("ApellidoMaterno").value;
    var tipo_cuenta = document.getElementById("tipo_cuenta").value;
    var sexo = document.getElementById("sexo").value;
    //comprueba que la información otenida del formulario no este vacia
    if(correo.trim().length>0 && password.trim().length>0 && nombre.trim().length>0 && apellido_paterno.trim().length>0 && apellido_materno.trim().length>0 && tipo_cuenta.trim().length>0 && sexo.trim().length>0){
        $.ajax({
            url: "../controlador/SRV_CUENTAS.php",
            data: {
                fn : "registrar",
                correo: correo,
                password:password,            
                nombre:nombre,
                apellido_paterno:apellido_paterno,
                apellido_materno:apellido_materno,
                tipo_cuenta:tipo_cuenta,
                sexo:sexo
            },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr) {           
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="../modelo/img/RC_IF_CARGANDO.gif" ></center>');
            document.getElementById('btn-submitdos').disabled = true;            
        },
        //si la respuesta es correcta se notifica y vacia los campos del formulario para un nuevo registro.
        success: function(respuesta) {
            console.log(respuesta);
            if(respuesta == "ok") {               
                mostrarAlerta("Registro realizado con éxito.","correcto");
                document.getElementById('btn-submitdos').disabled = false;                
                document.getElementById('Correo').value = "";
                document.getElementById('Password').value = "";
                document.getElementById('nombre').value = "";
                document.getElementById('ApellidoPaterno').value = "";
                document.getElementById('ApellidoMaterno').value = "";
                document.getElementById('sexo').value = "";
                document.getElementById('tipo_cuenta').value = "";
                setTimeout(mandarAinicio, 5000);
            }else{               
                mostrarAlerta(respuesta,"fallido");
                document.getElementById('btn-submitdos').disabled = false;
            }
        },
        error: function(jqXHR, textStatus){
            mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Inténtelo de nuevo más tarde.","fallido");
            document.getElementById('btn-submitdos').disabled = false;
        }
    });
    }else{
        //Crea una cadena con el mensaje de error en caso de que algun campo este vacio
        var mensaje = "Por favor Complete lo siguiente:";
        if(correo.trim().length==0) mensaje+="\nCorreo electronico";
        if(password.trim().length==0) mensaje+="\nContraseña";
        if(nombre.trim().length==0) mensaje+="\nNombre";
        if(apellido_paterno.trim().length==0) mensaje+="\nApellido Paterno";
        if(apellido_materno.trim().length==0) mensaje+="\nApellido Materno";
        if(tipo_cuenta.trim().length==0) mensaje+="\nTipo de cuenta";
        if(sexo.trim().length==0) mensaje+="\nSexo";
        mostrarAlerta(mensaje,"fallido");
    }
});
/**
 * Reedirecciona a la pagina de incio
 */
function mandarAinicio(){
    window.location.replace("index.html");
}