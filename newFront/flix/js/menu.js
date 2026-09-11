var Menu = (function(){
    var accordion = function(){
        $('ul li:has(ul)').hover(
            function(){
                // stop(true, true) cancela cualquier show/hide en cola antes
                // de arrancar el nuevo: sin esto, pasar el mouse rápido por
                // varios ítems apila animaciones y se siente "acelerado".
                $(this).find('ul').stop(true, true).show('fast');
                $('span.arrow', $(this)).addClass('hover');
            },
            function(){
                $(this).find('ul').stop(true, true).hide();
                $('span.arrow', $(this)).removeClass('hover');
            }
        );
    }
    
    var bindEvents = function(){
        $('li.menu-option', 'ul.menu').click(function(){
            $(this).closest('ul').hide('slow');
        });
    }
    
    var InitUI = function(){
        accordion();
        bindEvents();
    }
    
    return {
        Init: InitUI
    }
})();

