/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var team1;
var team2;
var rolGame;

function guardar_cedula(id_equipo_1,id_equipo_2,id_rol_juego,id_convocatoria){
    /*Guardando los datos en el LocalStorage*/
      //  id_equipo_1=1;
       // id_equipo_2=2;
       // id_rol_juego=3;
        sessionStorage.setItem("id_equipo_1", id_equipo_1);
        sessionStorage.setItem("id_equipo_2", id_equipo_2);
        sessionStorage.setItem("id_rol_juego", id_rol_juego);
        sessionStorage.setItem("id_convocatoria", id_convocatoria);
       
        location.href ="../vista/FORMULARIO_CEDULA.html";
}