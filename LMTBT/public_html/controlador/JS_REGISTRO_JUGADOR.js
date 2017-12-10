/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * @author Mauricio Armando Pérez Hernández
 * */

function registra_jugador() {
	var correo = document.getElementById("email").value;
	var password = document.getElementById("password").value;
	var nombre = document.getElementById("nombre").value;
	var apellido_paterno = document.getElementById("apellido_paterno").value;
	var apellido_materno = document.getElementById("apellido_materno").value;
	var sexo = document.getElementById("sexo");
	var sexo2 = sexo.options[sexo.selectedIndex].value;

	if (correo.length > 0 && password.length > 0 && nombre.length > 0 && apellido_paterno.length > 0 && apellido_materno.length > 0 && sexo2.length > 0) {
		var patron_e_mail = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!patron_e_mail.test(correo)) {
			alert("Email inválido.");
			return;
		}
		$.ajax({
			url: "../controlador/SRV_JUGADOR_REGISTRAR.php",
			data: {tipo: "registra_jugador",
				correo: correo,
				password: password,
				nombre: nombre,
				apellido_paterno: apellido_paterno,
				apellido_materno: apellido_materno,
				fecha_nacimiento: "",
				sexo: sexo2,
				tipo_sangre: "",
				telefono: "",
				foto_perfil: "",
				facebook: "",
				instagram: "",
				twiter: "",
				tipo_usuario: 3
			},
			type: "POST",
			datatype: "text",
			beforeSend: function (xhr) {},
			success: function (respuesta) {
				if (respuesta == "ok") {
					alert("Registro realizado con exito.");
				} else {
					alert("Ha ocurrido un error al guardar la informacion. Intentelo de nuevo mas tarde.");					
				}
			},
			error: function (jqXHR, textStatus) {}
		});

		alert(nombre + "\n" + apellido_materno + "\n" + apellido_paterno + "\n" + sexo2 + "\n" + correo + "\n" + password);
	} else {
		var mensaje = "Por favor Complete lo siguiente:";
		if (nombre.length == 0)
			mensaje += "\nNombre";
		if (apellido_paterno.length == 0)
			mensaje += "\nApellido Paterno";
		if (apellido_materno.length == 0)
			mensaje += "\nApellido Materno";
		if (sexo2.length == 0)
			mensaje += "\nSexo";
		if (correo.length == 0)
			mensaje += "\nCorreo electronico";
		if (password.length == 0)
			mensaje += "\nContraseña";
		alert(mensaje);
	}
}