/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$( document ).ready(function() {
    CargarUsuarios();  
 });

function CargarUsuarios(){
    var tipo_usuario=1;
    var datos="";
    switch(tipo_usuario){
        case 1://admin
            datos="<option value=2>COACH</option><option value=3>JUGADOR</option><option value=4>FOTOGRAFO</option><option value=5>CAPTURISTA</option> "
        break;
        case 2://coach
            datos="<option value=2>COACH</option><option value=3>JUGADOR</option>"
        break;
        case 3://coach
            datos="<option value=3>JUGADOR</option>"
        break;
    }
      
    $('#tipo_usuario').empty();
    $('#tipo_usuario').append(datos); 
}

function restaurar(){
    $('#Correo').val("");
    $('#Password').val("");
    $('#nombre').val("");
    $('#ApellidoPaterno').val("");
    $('#ApellidoMaterno').val("");
  

}

function RegistrarCuenta(){
    var correo = document.getElementById("Correo").value;
    var password = document.getElementById("Password").value;
    var nombre = document.getElementById("nombre").value;
    var apellido_paterno = document.getElementById("ApellidoPaterno").value;
    var apellido_materno = document.getElementById("ApellidoMaterno").value;
    var sexo= document.querySelector('input[name = "sexo"]:checked').value;
    var tipo_usuario = document.getElementById("tipo_usuario").value;
    if(correo.trim().length>0 && password.trim().length>0 && nombre.trim().length>0 && apellido_paterno.trim().length>0 && apellido_materno.trim().length>0){

        $.ajax({
            url: "../controlador/SRV_CUENTA_REGISTRAR.php",
            data: {tipo:"registrarCuenta",
            correo: correo,
            password:password,
            tipo_usuario:tipo_usuario,
            estado:"DESCONECTADO",
            nombre:nombre,
            apellido_paterno:apellido_paterno,
            apellido_materno:apellido_materno,
            sexo:sexo
            },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr) {

        },
        success: function(respuesta) {
            console.log(respuesta);
            if(respuesta == "ok") {
                restaurar();
                mostrarAlerta("Registro realizado con exito.","correcto");
                
            }else{
                 mostrarAlerta("Ha ocurrido un error, intentelo de nuevo","error");
                alert(respuesta);
            }
        },
        error: function(jqXHR, textStatus) {
               mostrarAlerta("Ha ocurrido un error, intentelo de nuevo","error");
        }
    });


    }else{
        var mensaje = "Por favor Complete lo siguiente:";
        if(correo.trim().length==0) mensaje+="\nCorreo electronico";
        if(password.trim().length==0) mensaje+="\nContraseña";
        if(nombre.trim().length==0) mensaje+="\nNombre";
        if(apellido_paterno.trim().length==0) mensaje+="\nApellido Paterno";
        if(apellido_materno.trim().length==0) mensaje+="\nApellido Materno";
        alert(mensaje);
    }

}

