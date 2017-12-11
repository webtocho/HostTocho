var id_equipo;

//Recibe una cadena y la devuelve de tal forma que tenga la primera letra mayúscula, y el resto sean minúsculas.
function capitalize(string) {
	return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

$(document).ready(function () {
	//Desde EQUIPOS_VER se nos manda el id del equipo a consultar.
	id_equipo = sessionStorage.getItem("id_equipo");
	if (id_equipo === null) {
		document.location.href = "EQUIPOS_VER.html";
		return;
	} else {
		sessionStorage.removeItem("id_equipo");
	}

	$.ajax({
		url: "../controlador/SRV_GET_SESION.php",
		type: "POST",
		dataType: 'json',
		async: true,
		beforeSend: function (xhr) {

		},
		success: function (resultado) {
			if (resultado["id"] == null || resultado["tipo"] == null) {
				//Si el usuario no ha iniciado sesión, lo redireccionamos.
				window.location.replace("index.php");
			} else if (resultado["id"] != null && resultado["tipo"] != null) {
				if (resultado["tipo"].toUpperCase() == "COACH" || resultado["tipo"].toUpperCase() == "ADMINISTRADOR") {
					$.ajax({
						url: "../controlador/SRV_CONSULTAS.php",
						data: {
							tipo: "get_equipo",
							id_equipo: id_equipo,
							get_nombre_equipo: "1",
							get_nombre_coach: "1",
							get_logotipo_equipo: "1",
							get_id_y_categoria_rosters: "1"
						},
						type: "POST",
						dataType: 'json',
						async: true,
						success: function (equipo) {
							if (equipo.hasOwnProperty('error')) {
								alert(equipo["error"]);
								document.location.href = "EQUIPOS_VER.html";
							} else {
								$("#nombre").html(equipo["NOMBRE_EQUIPO"]);
								$("#coach").html(equipo["NOMBRE_COACH"]);
								document.getElementById("logotipo").src = "data:image/png;base64," + equipo["LOGOTIPO_EQUIPO"];

								if (Object.keys(equipo["id_y_categoria_rosters"]).length > 0) {
									$("#lista_rosters").html("");
									$.each(equipo["id_y_categoria_rosters"], function (index, i) {
										$("#lista_rosters").append("<li><a href='ROSTERS_DETALLES.html' onclick='irAPaginaDeDetallesDeRoster(" + i[0] + ")'>" + capitalize(i[1]) + "</a></li>");
									});
								}
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
		complete: function (jqXHR, textStatus) {

		}
	});
});

function irAPaginaDeEdicion() {
	//Este método permite guardar una variable de sesión del lado del cliente.
	sessionStorage.setItem("id_equipo", id_equipo);
	document.location.href = "EQUIPOS_EDICION.html";
}

function irAPaginaDeCrearRoster() {
	sessionStorage.setItem("id_equipo", id_equipo);
	sessionStorage.setItem("nombre_equipo", $("#nombre").html());
	document.location.href = "ROSTERS_CREAR.html";
}

function irAPaginaDeDetallesDeRoster(id_roster) {
	sessionStorage.setItem("id_equipo", id_equipo);
	sessionStorage.setItem("nombre_equipo", $("#nombre").html());
	sessionStorage.setItem("id_roster", id_roster);
	return true;
}
// para eliminar un equipo, puede se que el equipo este participando en un torneo actualmente, de ser asi no se podra eliminar, de lo contrario si.
function EliminarEquipo() {
	if (confirm('¿Estas seguro de eliminar este equipo?')) {
		$.ajax({
			url: "../controlador/SRV_EQUIPOS_ELIMINACION.php",
			data: {"tipo": "eliminar_equipo",
				id_equipo: id_equipo
			},
			type: "POST",
			datatype: "text",
			beforeSend: function (xhr) {},
			success: function (respuesta) {
				if (respuesta == "ok") {
					alert("El equipo fue eliminado con exito");
					document.location.href = "EQUIPOS_VER.html";
				} else if (respuesta == "no") {
					alert("El equipo no puede ser eliminado ya que participa en un torneo actualmente o no se encuentran registros de este equipo para eliminar");
					document.location.href = "EQUIPOS_VER.html";
				} else {
					alert(respuesta);
					document.location.href = "index.php";
				}
			},
			error: function (jqXHR, textStatus) {}
		});
	} else {
		document.location.href = "EQUIPOS_VER.html";
	}
}
