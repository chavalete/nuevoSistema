var Flix = (function(){
    // Carga por default de la grilla
    window.loadFlix = function(){
        loadingOn(); /* Pongo el preloader */
        $.post(
            'tpl/flix/flix.php',
            {   
                accion     : window.accion,
                parametros : window.parametros 
            }, 
            function(data){
                emptyFlix(); /* Vacio la grilla */
                $(data).appendTo('div#flix_grid'); /* Cargo el nuevo contenido de la grilla */
                loadingOff(); /* Quito el preloader */
                reSize(); /* Fijo el tamaño de las columnas */
            }
        );
    }
    // Llamada al process.php desde los modales
    window.callService = function(service, params){
        $.ajaxSetup({async: false});
        $.post(
            'inc/process.php',
            {   
                accion     : service,
                parametros : params 
            }, 
            function(data){
                window.rsService = data;
            },
            'json'
        );        
        $.ajaxSetup({async: true});            
    }

    window.callAutocomplete = function(t){
        var field = $(t).attr('name');
        var code  =  $(t).val();
        
        $(t).autocomplete({
            minLength : 2,
            source    : function ( request, response){
                            $(t).attr('loading', 1);
                            $.ajax({
                                type: 'post',
                                url: "inc/process.php",
                                dataType: "json",
                                data: { accion: 'armarAutoCompletar', 
                                        parametros: {
                                            resultadosMostrar: 12,
                                            stringBuscar     : request.term,
                                            tipoBuscar       : field
                                        }
                                },
                                success: function(data) {
                                    if(data != null){
                                        response( $.map(data, function(item) {
                                            var extra1 = null;
                                            if(typeof item.idListaPrecio !== 'null'){
                                                extra1 = item.idListaPrecio;
                                            }
                                            var extra2 = null;
                                            if(typeof item.idFamilia !== 'null'){
                                                extra2 = item.idFamilia;
                                            }
                                            $(t).attr('loading', 0);                                             
                                            return {
                                                label: item.label,
                                                value: item.label,
                                                param: item.value,
                                                extra: {
                                                    idListaPrecio: extra1,
                                                    idFamilia: extra2
                                                }
                                            }
                                        }));
                                    }
                                    $(t).attr('loading', 0);
                                }
                            });
            },
            select    : function(event, ui){
                $(this).attr('params', ui.item.param);
                $(this).attr('title', ui.item.value);
                if(ui.item.extra.idListaPrecio != null){
                    $(this).attr("idListaPrecio", ui.item.extra.idListaPrecio);
                }else{
                    $(this).removeAttr("idListaPrecio");
                }
                if(ui.item.extra.idFamilia != null){
                    $(this).attr("idFamilia", ui.item.extra.idFamilia);
                }else{
                    $(this).removeAttr("idFamilia");
                }
                
            },
            // 7797216001039
            response : function(event, ui){
                setTimeout(() => {
                    if(/^[0-9]{13}$/.test( code ) && $(t).attr('loading') == 0){
                        var targetAU = '';
                        if($('ul[id^=ui-id-]').length == 1){
                            targetAU = $($('ul[id^=ui-id-]')[0]).attr('id');
                        }else{
                            targetAU = $($('ul[id^=ui-id-]')[$('ul[id^=ui-id-]').length - 1]).attr('id');
                        }
                        $('a', $('li', '#'+targetAU).first() ).trigger('click');
                        $('div.add-button.altaStock', '#modal').trigger('click');
                    }
                    if(/^[0-9]{7}$/.test( code ) && $(t).attr('loading') == 0){
                        var targetAU = '';
                        if($('ul[id^=ui-id-]').length == 1){
                            targetAU = $($('ul[id^=ui-id-]')[0]).attr('id');
                        }else{
                            targetAU = $($('ul[id^=ui-id-]')[$('ul[id^=ui-id-]').length - 1]).attr('id');
                        }
                        $('a', $('li', '#'+targetAU).first() ).trigger('click');
                        $('div.add-button.altaStock', '#modal').trigger('click');
                    }
                    if(/^[0-9]{8}$/.test( code ) && $(t).attr('loading') == 0){
                        var targetAU = '';
                        if($('ul[id^=ui-id-]').length == 1){
                            targetAU = $($('ul[id^=ui-id-]')[0]).attr('id');
                        }else{
                            targetAU = $($('ul[id^=ui-id-]')[$('ul[id^=ui-id-]').length - 1]).attr('id');
                        }
                        $('a', $('li', '#'+targetAU).first() ).trigger('click');
                        $('div.add-button.altaStock', '#modal').trigger('click');
                    }
                    if(/^[0-9]{12}$/.test( code ) && $(t).attr('loading') == 0){
                        var targetAU = '';
                        if($('ul[id^=ui-id-]').length == 1){
                            targetAU = $($('ul[id^=ui-id-]')[0]).attr('id');
                        }else{
                            targetAU = $($('ul[id^=ui-id-]')[$('ul[id^=ui-id-]').length - 1]).attr('id');
                        }
                        $('a', $('li', '#'+targetAU).first() ).trigger('click');
                        $('div.add-button.altaStock', '#modal').trigger('click');
                    }
                    if(/^[0-9]{14}$/.test( code ) && $(t).attr('loading') == 0){
                        var targetAU = '';
                        if($('ul[id^=ui-id-]').length == 1){
                            targetAU = $($('ul[id^=ui-id-]')[0]).attr('id');
                        }else{
                            targetAU = $($('ul[id^=ui-id-]')[$('ul[id^=ui-id-]').length - 1]).attr('id');
                        }
                        $('a', $('li', '#'+targetAU).first() ).trigger('click');
                        $('div.add-button.altaStock', '#modal').trigger('click');
                    }
                }, 200);  
            }
        });
    }

    // Validacion de inputs required
    window.validateInputs = function(input, parcial){
        parcial = (typeof parcial == 'undefined') ? false : parcial ;

        if(input.hasClass('autocomplete')){
            if(typeof input.attr('params') == 'undefined' || input.attr('params').trim() == '' || input.val().trim() == ''){
                input.addClass('error');
                if(parcial){
                    $('input#errorParcial', 'table.parcial').val('1');
                }else{
                    $('input#error', 'div#modal.inicio').val('1');    
                }
            }else{
                input.removeClass('error');
            } 
        }else{
            if(input.val().trim() == ''){
                input.addClass('error');
                if(parcial){
                    $('input#errorParcial', 'table.parcial').val('1');
                }else{
                    $('input#error', 'div#modal.inicio').val('1');    
                }
            }else{
                input.removeClass('error');
            }          
        }
    }

    // Reseteo el valor de las variables del flix a las definidas por default
    window.flixResetValues = function(){
        window.parametros = window.parametros_aux;
    }
    // Buscador del header
    var buscadorHeader = function(){
        flixResetValues();
        window.parametros.tipo  = $('select#tipo', 'div#barra-busqueda').val();
        window.parametros.value = $('input#valor', 'div#barra-busqueda').val();
        loadFlix();
    }
    // Bindeo de eventos
    var bindEvents = function(){
        // Accion de busqueda en el header
        $('input#buscar', 'div#header').click(function(){
            if($('input#valor', 'div#header').val().trim() == ''){
                $.msgBox({ title: "Alerta", content: "Ingrese un valor para poder realizar la busqueda" });                
            }else{
                buscadorHeader();
            }
        });
        // Armo el autocomplete de los inputs
        $("input.autocomplete").live('keydown.autocomplete', function(){
            window.callAutocomplete(this);
        });

        // Busquedas avanzadas
        $('.send-button.busquedaAvanzada').live('click', function(){
            flixResetValues();
            
            
            
            $('input, select', ($(this).closest('div#modal'))).each(function(){
                if($(this).val().trim() == ''){
                    window.parametros[$(this).attr('name')] = "";    
                }else if(typeof $(this).attr('params') != 'undefined'){
                    window.parametros[$(this).attr('name')] = $(this).attr('params'); 
                }else{
                    window.parametros[$(this).attr('name')] = $(this).val();    
                }
                window.parametros.desdeBusqueda = true;
            });
            
            if(window.parametros.tipo == 'auditorStock' && window.parametros.productos === '' && window.parametros.codigosAuditor === ''){
                $.msgBox({ title: "Alerta", content: "Debe completar al menos un producto o un codigo." }); 
            }else{
                loadFlix();
                $.colorbox.close();    
            }
        });    
        
        // Mostrar calendario en los inputs
        $( "input.datePicker" ).live('focus', function(){
            $(this).datepicker({
                changeMonth     : true,
                changeYear      : true,
                showOtherMonths : true,
		        dateFormat      : 'dd-mm-yy'
            });  
        });
        // Cambio de seccion
        $('li.menu-option, li.seccion-menu-option', 'ul.menu').click(function(){
            // El Panel Principal (Tablero) tiene su propia pantalla, no pasa
            // por la grilla genérica: navegación normal en vez de AJAX.
            if($(this).attr('params') === 'principal'){
                window.location.href = 'panelPrincipal.php';
                return;
            }
            // Si estamos parados en el Panel Principal y se navega a otra
            // sección, hay que ocultar el dashboard: si no, el contenido
            // nuevo se carga en #flix_grid pero queda tapado debajo. También
            // sacamos el #flix_titulo propio del panel: si no, queda
            // duplicado con el que trae la sección nueva y el topbar se
            // pisa siempre con "Principal" (toma el primero del DOM).
            var panelPrincipal = document.getElementById('panelPrincipal');
            if(panelPrincipal){
                panelPrincipal.style.display = 'none';
                var panelPrincipalTitulo = document.getElementById('flix_titulo');
                if(panelPrincipalTitulo){
                    panelPrincipalTitulo.parentNode.removeChild(panelPrincipalTitulo);
                }
            }
            flixResetValues();
            window.parametros.tipo = $(this).attr('params');
            loadFlix();
        });
        // Ordenar por columna
        $('div.asc', 'div.fieldSorter').live('click', function(){
            window.parametros.ordenarOrden  = 'asc';
            window.parametros.ordenarPor    = $(this).closest('th').attr('params');
            window.parametros.pagina        = 1;
            loadFlix();
        });
        $('div.desc', 'div.fieldSorter').live('click', function(){
            window.parametros.ordenarOrden  = 'desc';
            window.parametros.ordenarPor    = $(this).closest('th').attr('params');
            window.parametros.pagina        = 1;
            loadFlix();
        });        
        // Ir a pagina siguiente
        $('div.next', 'div#flix-paginador').live('click', function(){
            var last = $('span.total_pages', 'div#flix-paginador').html();
            if(window.parametros.pagina != last){
                window.parametros.pagina = Number(window.parametros.pagina) + 1;
                loadFlix();
            }
        });
        // Ir a pagina anterior
        $('div.prev', 'div#flix-paginador').live('click', function(){
            if(window.parametros.pagina > 1){
                window.parametros.pagina = Number(window.parametros.pagina) - 1;
                loadFlix();
            }
        });   
        // Ir a primer pagina
        $('div.first', 'div#flix-paginador').live('click', function(){
            if(window.parametros.pagina != 1){
                window.parametros.pagina = 1;
                loadFlix();
            }
        });
        // Ir a ultima pagina
        $('div.last', 'div#flix-paginador').live('click', function(){
            var last = $('span.total_pages', 'div#flix-paginador').html();
            if(window.parametros.pagina != last){
                window.parametros.pagina = last;
                loadFlix();
            }else{
                $(this).addClass('opacity');
            }
        });         
        // Actualizar actual vista
        $('div#flix-actualizar').live('click', function(){
            loadFlix();
        });           
        // Cambiar cantidad de registros por pagina
        $('select.perPage', 'div#show-per-page').live('change', function(){
            window.parametros.porPagina = $(this).val();
            window.parametros.pagina = 1;
            loadFlix();
        });
        // Restrinjo la escritura del paginador para que sólo se puedan escribir 
        // numeros y que no se puedan poner numeros < 1 y > al total de paginas
        $('input[id=input_perPage]').live('keypress', function(e){
            var keyCode = e.which;
            var insertedPage;
            var last = Number($('span.total_pages', 'div#flix-paginador').html());
            
            if(keyCode < 48 || keyCode > 57){ // keyCode 48 -> 57 son caracteres numericos
                insertedPage = Number($('input[id=input_perPage]').val());
                if(keyCode == 13 && insertedPage != window.parametros.pagina){ // keyCode 13 es ENTER
                    if(insertedPage >= 1 && insertedPage <= last){
                        window.parametros.pagina = insertedPage;
                        loadFlix();
                    }else{
                        $.msgBox({ title: "Alerta", content: "Pagina inexistente" }); 
                    }
                // keyCode 8: backspace
                // e.keyCode 37: flecha izq
                // e.keyCode 39: flecha der
                }else if(keyCode == 8 || e.keyCode == 37 || e.keyCode == 39){
                    return true;
                }else{
                    return false;
                }
            }else{
            }
        });
        // Cancelar edicion de filas
        $('div.editTool.cancel').live('click', function(){
            cancelEditFields($(this).closest('tr'));
        });
    }
    // Herramienta de impresion
    var imprimir = function(){
        $('div.imprimir').live('click', function(){
            var id = $(this).closest('tr').attr('id');
                        
            $.ajax({
                url: 'inc/process.php?accion=imprimir&id='+ id+ '&tipo='+window.parametros.tipo,
                success: function(data){ 
                    if(!data.soyError){
                        window.open(data.redirect, '_blank');
                    }
                },
                dataType: 'json'
            });
        });
    }
    // Habilitar editor en grilla
    var editFields = function(){
        $('div.editable').live('click', function(){
            var trContent = $(this).closest('tr');
            $('td.editable div.showData', trContent).hide();
            $('td.editable input.editData', trContent).show();
            $('td.editable select.editData', trContent).show();            
            $('div.herramientasGeneral', trContent).hide();
            $('div.herramientasEdit', trContent).show();
        });
    }
    // Guardar edicion
    var saveEdit = function(){
        $('div.editTool.acept').live('click', function(){
            var tr      = $(this).closest('tr');
            var id      = tr.attr('id');
            var error   = false;
            var value;
            var params  = { 
                campos  : {},
                id      : id, 
                tipo    : window.parametros.tipo
            };
            
            editLoadingOn(tr);
            
            $('input.editData, select.editData', tr).each(function(){
                if($(this).val().trim() == '' && $(this).hasClass('required')){
                    $(this).addClass('error');
                    if(!error){
                        error = true;
                    }
                }else{
                    $(this).removeClass('error');
                    if($(this).hasClass('autocomplete')){
                        value = $(this).attr('params');
                    }else{
                        value = $(this).val().trim();
                    }
                    params.campos[$(this).attr('name')] = value;
                }
            });
            
            if(!error){
                callService('actualizarRegistro', params);
                if(window.rsService.soyError == true){
                    $.msgBox({ title: "Alerta", content: window.rsService.mensaje });
                }else{
                    $('td.editable', tr).each(function(){
                        var fieldValue = $('.editData', $(this)).val();

                        if($(this).hasClass('select')){
                            //console.log($(this));
                            $('option', $(this)).each(function(){
                                //console.log($(this).val());
                                if($(this).val() == fieldValue){
                                    fieldValue = $(this).html();    
                                }                                
                            });
                        }
                        $('div.showData', $(this)).html(fieldValue);
                    });
                    cancelEditFields();
                    $('td', tr).effect("highlight", {color: '#6EBA84'}, 3000);
                }
            }
            editLoadingOff(tr);
        });
    }
    // Funcion para hacer esperar la ejecucion de JS
    var sleep = function (milliseconds) {
      var start = new Date().getTime();
      for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
          break;
        }
      }
    }    
    // Activo el loading de la grilla
    var editLoadingOn = function(tr){
        $('div.editTool.acept', tr).hide();
        $('div.editTool.cancel', tr).hide();
        $('div.editTool-loading', tr).show();
        $('input', tr).prop('disabled', true);
    }
    // Desactivo el loading de la grilla
    var editLoadingOff = function(tr){
        $('div.editTool-loading', tr).hide();
        $('div.editTool.acept', tr).show();
        $('div.editTool.cancel', tr).show();        
        $('input', tr).prop('disabled', false);
    }    
    // Cancelar edicion de filas de la grilla
    var cancelEditFields = function(tr){
            //var trContent = $(this).closest('tr');
            $('div.showData', tr).show();
            $('input.editData, select.editData', tr).hide();
            $('div.herramientasGeneral', tr).show();
            $('div.herramientasEdit', tr).hide();            
    }
    // Anular registro
    var anular = function(){
        $('div.cancelable').live('click', function(){
            var boton = $(this);
            $.msgBox({
                title: "Estas seguro...",
                content: "Est&aacute; seguro que deseas cancelar el registro seleccionado?",
                type: "confirm",
                buttons: [{ value: "Si" }, { value: "No" }, { value: "Cancelar"}],
                success: function (result) {
                    if (result == "Si") {
                        $.post(
                            'inc/process.php',
                            {   
                                accion  : 'cancelar',
                                id      : boton.closest('tr').attr('id'),
                                tipo    : window.parametros.tipo
                            }, 
                            function(data){
                                if(data.soyError){
                                    $.msgBox({ title: "Error", content: data.mensaje, type: 'error'});    
                                }else{
                                    $.msgBox({ title: "Alerta", content: data.mensaje });    
                                }
                                
                                window.loadFlix();
                            },
                            'json'
                        ); 
                    }
                }
            });
        });
    }
    // Enviar transaccion
    var transaccion = function(){
        $('div.enviarTransaccion').live('click', function(){
            var boton = $(this);
            $.msgBox({
                title: "Estas seguro...",
                content: "Est&aacute; seguro que deseas finalizar el movimiento?",
                type: "confirm",
                buttons: [{ value: "Si" }, { value: "No" }, { value: "Cancelar"}],
                success: function (result) {
                    if (result == "Si") {
                        $.ajaxSetup({async: false});
                        $.post(
                            'inc/process.php',
                            {   
                                accion  : 'enviarTransaccion',
                                id      : boton.closest('tr').attr('id'),
			                    tipo    : window.parametros.tipo
                            }, 
                            function(data){
                                if(data.soyError){
                                    $.msgBox({ title: "Error", content: data.mensaje, type: 'error'});    
                                }else{
                                    $.msgBox({ title: "Alerta", content: data.mensaje });    
                                }
                                window.loadFlix();
                            },
                            'json'
                        ); 
                        $.ajaxSetup({async: true});
                    }
                }
            });            
        });
    }    
    /**
     * Funcion para fijar el tamaño de las columnas de la grilla
     * para que no se modifiquen los tamaños al hacer click en editar
     */
    var reSize = function(){
        $('th.grid-titulo', 'table#grid-content').each(function(){
            $(this).css('width', $(this).css('width'));
        });
        $('input.editData', 'table#grid-content').each(function(){
            var width = Number($(this).closest('td').css('width').slice(0, -2));
            $(this).css('width', (width * 0.9)+'px');
        });        
    }
    
    var emptyFlix = function(){
        $('div#flix_grid').html('');
    }
    // Pongo imagen de "loading"
    var loadingOn = function(){
        $('div#flix_grid').css('opacity', '0.17');
        //$('div#loading_container').show();
    }
    // Quito imagen de "loading"
    var loadingOff = function(){
        $('div#flix_grid').css('opacity', '1');
        //$('div#loading_container').hide();
    }

    // Tooltips de ayuda
    var helpTooltip = function(){
        $('div.tooltip-content').live('mouseleave', function(){
            $('div.tooltip-content').hide();
        });
        $('.tooltip-click').live('click', function(){
            var parent = $(this).closest('div');
            var show   = $('.tooltip-content', parent).css('display');
            var params = (typeof $('.tooltip-content', parent).attr('params') == 'undefined') ? '' : '_' + $('.tooltip-content', parent).attr('params');
            var accion = (typeof $('.tooltip-content', parent).attr('tipo') == 'undefined') ? window.parametros.tipo : $('.tooltip-content', parent).attr('tipo');
            
            if(show == 'block'){
                $('.tooltip-content', parent).hide();
            }else{
                // Si la tooltip ya fue abierta, no la vuelvo a cargar, hago el display directamente
                if(!$('.tooltip-text', parent).length){                    
                    $.post(
                        'tpl/tooltip/' + accion + params + '.php',
                        {},
                        function(data){
                            $(data).appendTo($('.tooltip-content', parent));
                        }
                    );  
                }
              
                $('.tooltip-content', parent).show();
            }
        }); 
    }        
   
    // Inicializo las funciones
    var InitUI = function(){
        loadFlix();
        editFields();
        anular();
        transaccion();
        saveEdit();
        imprimir();
        bindEvents();
        helpTooltip();
        
    }
    
    return {
        Init : InitUI
    }
})();
