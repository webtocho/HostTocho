//Realiza el llamado a la funcion scrollFunction() si el scroll se hay presionado
window.onscroll = function() {scrollFunction()};
//Cuando el usuario despliega 20px desde la parte superior del documento, muestre el botón
function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
    } else {
        document.getElementById("myBtn").style.display = "none";
    }
}
//Cuando el usuario hace clic en el botón, desplácese hasta la parte superior del documento
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}