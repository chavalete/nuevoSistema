$(document).ready(function(){
    var sincronizarTitulo = function(){
        var titulo = $('#flix_titulo').first().text();
        if(titulo){
            $('#topbar-title').text($.trim(titulo));
        }
    };

    sincronizarTitulo();

    // El contenido de #flix_grid se reemplaza por AJAX (ver flix.js), así que
    // reviso después de cada request si cambió el título de la sección.
    $(document).ajaxComplete(function(){
        setTimeout(sincronizarTitulo, 50);
    });
});
