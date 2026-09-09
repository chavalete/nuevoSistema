var ThemeToggle = (function(){
    var STORAGE_KEY = 'flixTema'; // 'claro' | 'oscuro'

    var aplicar = function(tema){
        if(tema === 'oscuro'){
            $('html').attr('data-theme', 'oscuro');
            $('#themeToggle').attr('title', 'Cambiar a modo claro').attr('aria-label', 'Cambiar a modo claro');
        }else{
            $('html').removeAttr('data-theme');
            $('#themeToggle').attr('title', 'Cambiar a modo oscuro').attr('aria-label', 'Cambiar a modo oscuro');
        }
    };

    var init = function(){
        var guardado = null;
        try{ guardado = localStorage.getItem(STORAGE_KEY); }catch(e){}
        aplicar(guardado === 'oscuro' ? 'oscuro' : 'claro');

        $('#themeToggle').click(function(){
            var actual = $('html').attr('data-theme') === 'oscuro' ? 'oscuro' : 'claro';
            var nuevo = actual === 'oscuro' ? 'claro' : 'oscuro';
            aplicar(nuevo);
            try{ localStorage.setItem(STORAGE_KEY, nuevo); }catch(e){}
        });
    };

    return { init: init };
})();

$(document).ready(function(){
    ThemeToggle.init();
});
