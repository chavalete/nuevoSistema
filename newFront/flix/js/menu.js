var Menu = (function(){
    var accordion = function(){
        $('ul li:has(ul)').hover(
            function(){
                $(this).find('ul').show('fast');
                $('span.arrow', $(this)).addClass('hover');
            },
            function(){
                $(this).find('ul').hide();
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

