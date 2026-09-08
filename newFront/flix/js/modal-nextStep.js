var nextStep = (function(){
    var title;
    var resize;
    var original_size;
    var abm_name; // OJO! Esta variable es usada en las tooltips, cuidado si se quiere modificar su valor
    var nextDiv = $('div#modal.nextStep');
    var done = false; // Para parchar error de doble ejecucion de "loadParcialABM"

    var loadParcialABM = function(){
        if(!done){
            $.post(
                'tpl/modal/abms_'+ abm_name +'_crear_form.php',
                {}, 
                function(data){
                    $(data).appendTo($('div.modal_content', nextDiv));
                }
            );
        }
    }
    
    var showNext = function(){
        $('div#modal.inicio').hide();
        $('span.title', nextDiv).html(title);
        $('div.tooltip-content', nextDiv).attr('tipo', abm_name);
        nextDiv.show();
        loadParcialABM();
        done = true;
        $.colorbox.resize({height: resize});            
    }
    
    var showPrev = function(){
        $('div.modal_content', nextDiv).html('');
        $('span.title', nextDiv).html('');
        $('div.tooltip-content', nextDiv).html('');
        $('div#modal.inicio').show();
        nextDiv.hide();
        $.colorbox.resize({height: original_size})
        done = false;        
    }
    
    var bindEvents = function(){
        $('div.addButton', 'div.modal_content').click(function(){
            if($(this).hasClass('crear-clientes')){
                title    = 'Alta de clientes';
                abm_name = 'clientes';
                resize   = '730';
                original_size = '490';
            }else if($(this).hasClass('crear-medicos')){
                title    = 'Alta de medicos';
                abm_name = 'medicos';
                resize   = '280';            
                original_size = '530';               
            }else if($(this).hasClass('crear-pacientes')){
                title    = 'Alta de pacientes';
                abm_name = 'pacientes';
                resize   = '480';  
                original_size = '930';             
            }else if($(this).hasClass('crear-obrasSociales')){
                title    = 'Alta de obras sociales';
                abm_name = 'obrasSociales';
                resize   = '280';  
                original_size = '530';              
            }else if($(this).hasClass('crear-proveedores')){
                title    = 'Alta de proveedores';
                abm_name = 'proveedores';
                resize   = '520';  
                original_size = '740';               
            }else if($(this).hasClass('crear-productos')){
                title    = 'Alta de productos';
                abm_name = 'productos';
                resize   = '530';  
                original_size = '930';              
            }else if($(this).hasClass('crear-estanterias')){
                title    = 'Alta de estanterias';
                abm_name = 'estanterias';
                resize   = '380';  
                original_size = '740';              
            }else if($(this).hasClass('crear-imputacion')){
                title    = 'Alta de imputaciones';
                abm_name = 'imputaciones';
                resize   = '280';  
                original_size = '480';              
            }else{
                $.msgBox({ title: "Error", content: data.mensaje, type: 'Error! Contacte al administrador'});
            }        
            
            if(abm_name != undefined){
                showNext();
            }

        });

        $('div.send-button.crear-volver', 'div#modal.nextStep').click(function(){
            window.rsAbm = '';
            if(!abmValidar()){
                $.ajaxSetup({async: false});
                abmCrear();
                $.ajaxSetup({async: true});
                $.msgBox({ title: "Alerta", content: window.rsAbm.mensaje });

                if(!window.rsAbm.soyError){
                    showPrev();
                }
            }            
        });
        $('div.send-button.volver-abm', 'div#modal.nextStep').click(function(){
            showPrev();
        });        
        
    }
    
    var InitUI = function(){
        bindEvents();
    }
    
    return {
        Init: InitUI
    }
})();
