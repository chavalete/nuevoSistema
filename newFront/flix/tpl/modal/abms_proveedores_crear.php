<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de proveedores</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="crear" style="top: 30px; left: -300px; width: 320px; height: 120px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->            
    </div>
    <div class="modal_content">
        <?php include('abms_proveedores_crear_form.php'); ?>
        
        <hr class="modal-divisor" />
        <div class="send-button abmCrear">Crear</div>
    </div>
</div>

<script type="text/javascript">
    $('div.abmCrear').click(function(){
        window.rsAbm = '';
        if(!abmValidar()){
            $.ajaxSetup({async: false});
            abmCrear();
            $.ajaxSetup({async: true});
            $.msgBox({ title: "Alerta", content: window.rsAbm.mensaje});
            if(!window.rsAbm.soyError){
                $.colorbox.close();
                window.loadFlix();
            }
        }
    });
</script>
