/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function eviar_id_convocatoria(id){
    sessionStorage.setItem("id_convocatoria", id);
    location.href ="../vista/VER_NOTICIA.html";
}
