/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

idCategoria=0; 

$( document ).ready(function() {
    // con este metodo recuperamos el tipo de sesion iniciada, si la sesion no es de tipo administrador
    // expulsaremos al usuario que haya entrado a la pagina
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                    cargarCategorias();  
                    break;
                default:
                    expulsar();
                    return;
            }
        })
        .fail(function() {
            expulsar();
        });

}); 
 
 
 // esta funcion carga todas las categorias que existan en la base de datos
function cargarCategorias(){
    
       $.ajax({
            url: "../controlador/SRV_CATEGORIAS_OBTENER_ELIMINAR.php",
            data: {tipo: "get"},
            type: "POST",
            datatype: "text",
            success: function (respuesta) {
                 $('#tabla_categorias').empty();
                 $('#tabla_categorias').append(respuesta);
            },
            error: function (jqXHR, textStatus) {
                alert("error obtener");
            }
        });
}

// esta funcion elimina la categoria que este guardada en la variable idCategoria,
//esta variable tiene un set llamado seIdCategoria
function eliminarCategoria(){
      $.ajax({
            url: "../controlador/SRV_CATEGORIAS_OBTENER_ELIMINAR.php",
            data: {tipo: "delete", id:idCategoria},
            type: "POST",
            datatype: "text",
            success: function (respuesta) {
                if(respuesta=="1"){
                    cargarCategorias();
                    mostrarAlerta("Se ha eliminado correctamente","correcto");
                }else{
                      mostrarAlerta("Ha ocurrido un error, intentelo de nuevo","error");
                }
               
            },
            error: function (jqXHR, textStatus) {
                  mostrarAlerta("Ha ocurrido un error, intentelo de nuevo","error");
               
            }
        });   
      
}

// con este metodo se guarda el valor de la categoria seleccionada 
function setIdCategoria(id, nombre){
    
    idCategoria =id;
    $('#categoriaNombre').empty();
    $('#categoriaNombre').append(nombre);
    
}

//esta funcion obtiene el nombre de la categoria desde el input nombreCategoria y lo inserta en la base de datos
function agregarCategoria(){
    nombre = $('#nombreCategoria').val().trim();
    
    if(nombre.length>0){
        $.ajax({
             url: "../controlador/SRV_CATEGORIAS_OBTENER_ELIMINAR.php",
             data: {tipo: "add",nombre: nombre},
             type: "POST",
             datatype: "text",
             success: function (respuesta) {
                if (respuesta.toString()=="1"){
                    mostrarAlerta("Se ha agregado correctamente","correcto");
                    cargarCategorias();
                }else{
                     mostrarAlerta("Ha ocurrido un error, intentelo de nuevo","error");
                }//error
             },
             error: function (jqXHR, textStatus) {
                 mostrarAlerta("Ha ocurrido un error, intentelo de nuevo","error");

             }
         });
    }
        
          $('#nombreCategoria').val("");
}
