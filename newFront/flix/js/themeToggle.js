var ThemeToggle = (function(){
    var STORAGE_KEY = 'flixTema'; // 'claro' | 'oscuro'

    var aplicar = function(tema){
        if(tema === 'claro'){
            $('html').attr('data-theme', 'claro');
            $('#themeToggle').attr('title', 'Cambiar a modo oscuro').attr('aria-label', 'Cambiar a modo oscuro');
        }else{
            $('html').removeAttr('data-theme');
            $('#themeToggle').attr('title', 'Cambiar a modo claro').attr('aria-label', 'Cambiar a modo claro');
        }
    };

    var init = function(){
        var guardado = null;
        try{ guardado = localStorage.getItem(STORAGE_KEY); }catch(e){}
        aplicar(guardado === 'claro' ? 'claro' : 'oscuro');

        $('#themeToggle').click(function(){
            var actual = $('html').attr('data-theme') === 'claro' ? 'claro' : 'oscuro';
            var nuevo = actual === 'claro' ? 'oscuro' : 'claro';
            aplicar(nuevo);
            try{ localStorage.setItem(STORAGE_KEY, nuevo); }catch(e){}
        });
    };

    return { init: init };
})();

$(document).ready(function(){
    ThemeToggle.init();
});
