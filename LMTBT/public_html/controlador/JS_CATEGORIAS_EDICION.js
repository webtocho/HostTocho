/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

idCategoria=0; 

$( document ).ready(function() {
    cargarCategorias();  
 });
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

function setIdCategoria(id, nombre){
    
    idCategoria =id;
    $('#categoriaNombre').empty();
    $('#categoriaNombre').append(nombre);
    
}
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
