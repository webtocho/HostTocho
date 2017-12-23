/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



function enviarIdNoticia(id){
       
    sessionStorage.setItem("idNoticia", id);
     location.href ="../vista/VER_NOTICIA.html";
}