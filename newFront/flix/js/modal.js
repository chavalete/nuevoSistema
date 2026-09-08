var Modal = (function(){
    var reStyleModal = function(){
        $("#cboxTopLeft").hide();
        $("#cboxTopRight").hide();
        $("#cboxBottomLeft").hide();
        $("#cboxBottomRight").hide();
        $("#cboxMiddleLeft").hide();
        $("#cboxMiddleRight").hide();
        $("#cboxTopCenter").hide();
        $("#cboxBottomCenter").hide();

        $("#cboxContent").css({ 'border'        : '1px solid #2A3946',
                                'border-radius' : '10px',
                                'margin'        : '10px',
                                'padding'       : '5px',
                                
                                'margin-left'   : '30px'
        });
        $('div.send-button').css({ 'margin-bottom' : '5px' });
    };

    var bindModal = function(){
        reStyleModal();

        // Botonera que se encuentra arriba de la grilla de resultados
        var divBotonera = $('div#flix_botonera');
        // Herramientas de la grilla
        var divHerramientas = $('div.herramientasGeneral');

        // Modal de detalles
        $('div.herramienta.detalles', divHerramientas).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/detalles.php",
                data    : {
                    accion      : 'getDetalles',
                    id          : $(this).closest('tr').attr('id'),
                    tipo        : window.parametros.tipo,
                    autogestion : false
                },
                width   : '1200',
                height  : 'auto'
            });
        });        
        
        // Modal de mostrador (salidaStock editable)
        $('div.herramienta.mostrador', divHerramientas).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_salidaStock.php?id=" + $(this).closest('tr').attr('id'),
                width   : '1200',
                height  : 'auto'
            });
        });
        // Modal de mostrador (salidaStock editable)
        $('div.herramienta.mostradorEntradas', divHerramientas).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_entradas_altaStock_edit.php?id=" + $(this).closest('tr').attr('id'),
                width   : '1200',
                height  : 'auto'
            });
        });
        // Modal de autogestion
        $('div.herramienta.autogestion', divHerramientas).live('click', function(){
            if(window.parametros.tipo == 'transaccionesAgrupadas'){
                $.colorbox({
                    href    : "tpl/modal/detallesAgrupador.php",
                    data    : {
                        accion      : 'getDetalles',
                        id          : $(this).closest('tr').attr('id'),
                        tipo        : window.parametros.tipo,
                        autogestion : true
                    },
                    width   : '1200',
                    height  : 'auto'
                });
            }else{
                $.colorbox({
                    href    : "tpl/modal/detalles.php",
                    data    : {
                        accion      : 'getDetalles',
                        id          : $(this).closest('tr').attr('id'),
                        tipo        : window.parametros.tipo,
                        autogestion : true
                    },
                    width   : '1200',
                    height  : 'auto'
                });
            }
        });        
        
        // Movimientos -> Entradas -> Busqueda Avanzada
        $('div.flix_boton.entradas_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_entradas_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Entradas -> Alta de stock
        $('div.flix_boton.entradas_alta_de_stock', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_entradas_altaStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Entradas -> Alta de stock por código
        $('div.flix_boton.entradas_alta_stock_por_codigo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_entradas_altaStockCodigo.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Entradas -> Reimprimir codigo trazabilidad
        $('div.flix_boton.entradas_reimprimir_codigo_de_trazabilidad', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_entradas_reimprimirCodigo.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Entradas -> Exportar Entradas
        $('div.flix_boton.entradas_exportar_entradas', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_entradas_exportarEntradas.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> Busqueda Avanzada
        $('div.flix_boton.salidas_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> Salida stock
        $('div.flix_boton.salidas_remito', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_salidaStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> Roturas
        $('div.flix_boton.salidas_roturas', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_roturasStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> Reventa
        $('div.flix_boton.salidas_reventa', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_reventaStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> Imprimir
        $('div.flix_boton.salidas_imprimir', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_imprimirStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Devoluciones -> Devoluciones stock
        $('div.flix_boton.entradas_devolucion_de_stock', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_devoluciones_devolucionesStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Devoluciones -> Devoluciones stock
        $('div.flix_boton.salidas_devolucion_de_stock', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_devoluciones_devolucionesStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> diferencia de precio
        $('div.flix_boton.salidas_diferencia_de_precio', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_diferenciaDePrecio.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> Exportar salida
        $('div.flix_boton.salidas_exportar_salidas', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_exportarSalidas.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Movimientos -> Salidas -> Preparar pedido
        $('div.flix_boton.salidas_preparar_pedido', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_salidas_prepararPedido.php",
                width   : '500',
                height  : 'auto'
            });
        });
	
	
	// Produccion -> Orden de trabajo -> Busqueda Avanzada
        $('div.flix_boton.ordenDeTrabajo_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/ordenDeTrabajo_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// // Produccion -> Orden de trabajo -> Crear Orden
        $('div.flix_boton.ordenDeTrabajo_crear_orden', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/produccion_ordenDeTrabajo_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// Movimientos -> Salidas -> Preparar pedido
        $('div.flix_boton.ordenDeTrabajo_preparar_orden', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/produccion_ordenDeTrabajo_prepararOrden.php",
                width   : '500',
                height  : 'auto'
            });
        });
	
	
        // Stock -> Consulta -> Busqueda Avanzada
        $('div.flix_boton.consultaStock_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stock_consulta_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Stock -> Consulta -> Exportar
        $('div.flix_boton.consultaStock_exportar_stock', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stock_consulta_exportarStock.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// Stock en Custodia -> Consulta -> Busqueda Avanzada
        $('div.flix_boton.stockEnCustodia_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stockEnCustodia_consulta_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Stock -> Auditor stock -> Busqueda avanzada
        $('div.flix_boton.auditorStock_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stock_auditorStock_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// Stock -> Audtor stock-> Exportar
        $('div.flix_boton.auditorStock_exportar_auditor', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stock_auditorStock_exportarAuditor.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Stock -> Control stock -> Control
        $('div.flix_boton.controlStock_control', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stock_controlStock_control.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Clientes -> Busqueda avanzada
        $('div.flix_boton.clientes_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_clientes_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Clientes -> Crear
        $('div.flix_boton.clientes_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_clientes_crear.php",
                width   : 'auto',
                height  : 'auto'
            });
        });
        // ABMS -> Categorias -> Busqueda avanzada
        $('div.flix_boton.categorias_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_categorias_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Categorias -> Crear
        $('div.flix_boton.categorias_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_categorias_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Productos -> Busqueda avanzada
        $('div.flix_boton.productos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_productos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// ABMS -> Productos -> Crear nuevo
        $('div.flix_boton.productos_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_productos_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // ABMS -> Productos -> Exportar Productos
        $('div.flix_boton.productos_exportar_productos', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_productos_exportarProductos.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // ABMS -> Productos -> actualizar comision
        $('div.flix_boton.productos_actualizar_comision', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_productos_actualizarComision.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// ABMS -> Presentaciones -> Busqueda avanzada
        $('div.flix_boton.presentaciones_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_presentaciones_busquedaAvanzada.php",
                width   : '505',
                height  : 'auto'
            });
        });
	// ABMS -> Presentaciones -> Crear nuevo
        $('div.flix_boton.presentaciones_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_presentaciones_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// ABMS -> ProductosPrensentacion -> Busqueda avanzada
        $('div.flix_boton.productosPresentaciones_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_productos-presentaciones_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// ABMS -> ProductosPresentacion -> Crear nuevo
        $('div.flix_boton.productosPresentaciones_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_productos-presentaciones_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Marcas -> Busqueda avanzada
        $('div.flix_boton.marcas_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_marcas_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Marcas -> Crear nuevo
        $('div.flix_boton.marcas_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_marcas_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Custodia -> Busqueda avanzada
        $('div.flix_boton.custodia_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_custodia_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Custodia -> Crear nuevo
        $('div.flix_boton.custodia_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_custodia_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });        
        // ABMS -> Medicos -> Busqueda avanzada
        $('div.flix_boton.medicos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_medicos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Medicos -> Crear nuevo
        $('div.flix_boton.medicos_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_medicos_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Pacientes -> Busqueda avanzada
        $('div.flix_boton.pacientes_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_pacientes_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Pacientes -> Crear nuevo
        $('div.flix_boton.pacientes_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_pacientes_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Proveedores -> Busqueda avanzada
        $('div.flix_boton.proveedores_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_proveedores_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Proveedores -> Crear nuevo
        $('div.flix_boton.proveedores_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_proveedores_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });  
        // ABMS -> Almacenes -> Busqueda avanzada
        $('div.flix_boton.almacenes_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_almacenes_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Almacenes -> Crear nuevo
        $('div.flix_boton.almacenes_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_almacenes_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Estanterias -> Busqueda avanzada
        $('div.flix_boton.estanterias_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_estanterias_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Estanterias -> Crear nuevo
        $('div.flix_boton.estanterias_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_estanterias_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Sucursales -> Busqueda avanzada
        $('div.flix_boton.sucursales_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_sucursales_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Sucursales -> Crear nuevo
        $('div.flix_boton.sucursales_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_sucursales_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Obras sociles -> Busqueda avanzada
        $('div.flix_boton.obrasSociales_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_obrasSociales_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Obras sociles -> Crear nuevo
        $('div.flix_boton.obrasSociales_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_obrasSociales_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Vendedores -> Busqueda avanzada
        $('div.flix_boton.vendedores_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_vendedores_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMS -> Vendedores -> Crear nuevo
        $('div.flix_boton.vendedores_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_vendedores_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });        
        // ABMS -> Distribuidores -> Busqueda avanzada
        $('div.flix_boton.distribuidores_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_distribuidores_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });

	// ABMS -> Distribuidores -> Crear nuevo
        $('div.flix_boton.distribuidores_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_distribuidores_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });


        // ABMs -> Estado Pedidos -> Busqueda avanzada
        $('div.flix_boton.estados_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_estados_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Estados Pedidos -> Crear nuevo
        $('div.flix_boton.estados_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_estados_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Provincias -> Busqueda avanzada
        $('div.flix_boton.provincias_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_provincias_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Provincias -> Crear nuevo
        $('div.flix_boton.provincias_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_provincias_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Localidades -> Busqueda avanzada
        $('div.flix_boton.localidades_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_localidades_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Localidades -> Crear nuevo
        $('div.flix_boton.localidades_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_localidades_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });

        // ABMs -> Laboratorios -> Busqueda avanzada
        $('div.flix_boton.laboratorios_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_laboratorios_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Laboratorios -> Crear nuevo
        $('div.flix_boton.laboratorios_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_laboratorios_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Municipios -> Busqueda avanzada
        $('div.flix_boton.municipios_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_municipios_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Municipios -> Crear nuevo
        $('div.flix_boton.municipios_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_municipios_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Departamentos -> Busqueda avanzada
        $('div.flix_boton.departamentos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_departamentos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Departamentos -> Crear nuevo
        $('div.flix_boton.departamentos_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_departamentos_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Usuarios -> Busqueda avanzada
        $('div.flix_boton.usuarios_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuarios_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Usuarios -> Crear nuevo
        $('div.flix_boton.usuarios_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuarios_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });        
          // ABMs -> Vehiculos -> Busqueda avanzada
        $('div.flix_boton.vehiculos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_vehiculos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Vehiculos -> Crear nuevo
        $('div.flix_boton.vehiculos_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_vehiculos_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });        
         //ABMS -> Modelos -> crear nuevo
        $('div.flix_boton.modelos_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_modelos_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Modelos -> Busqueda avanzada
        $('div.flix_boton.modelos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_modelos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Mensajeria -> Transacciones -> Busqueda avanzada
        $('div.flix_boton.transacciones_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/mensajeria_transacciones_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });        
        // Mensajeria -> Transacciones -> Reproceso
        $('div.flix_boton.transacciones_reproceso', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/mensajeria_transacciones_reproceso.php",
                width   : '500',
                height  : 'auto'
            });
        });        
        // Mensajeria -> Transacciones Agrupadas -> Busqueda avanzada
        $('div.flix_boton.transaccionesAgrupadas_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/mensajeria_transaccionesAgrupadas_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });        
        // Mensajeria -> Bloqueador -> Exportador Movimientos SAP
        $('div.flix_boton.transaccionesAgrupadas_exportador_movimientos_sap', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/mensajeria_transaccionesAgrupadas_exportadorMovimientosSAP.php",
                width   : '500',
                height  : 'auto'
            });
        });                  
        // Mensajeria -> Bloqueador -> Busqueda avanzada
        $('div.flix_boton.bloqueador_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/mensajeria_bloqueador_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// Mensajeria -> Pendientes de confirmación -> Busqueda avanzada
        $('div.flix_boton.pendientesDeConfirmacion_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/mensajeria_pendientesDeConfirmacion_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // pedidos -> Listaro de pendientes -> Busqueda Avanzada
        $('div.flix_boton.listadoDePendientes_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/seguimiento_listadoDePendientes_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });			
        // pedidos -> Listaro de pendientes -> Busqueda Avanzada
        $('div.flix_boton.pedidos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/pedidos_pedidos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });		
        // pedidos -> Listaro de pendientes -> Busqueda Avanzada
        $('div.flix_boton.pedidos_alta_pedidos', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/pedidos_pedidos_alta_pedidos.php",
                width   : '500',
                height  : 'auto'
            });
        });		
        // pedidos -> Seguimiento historico -> Busqueda Avanzada
        $('div.flix_boton.seguimientoHistorico_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/pedidos_seguimientoHistorico_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });		

        // Distribucion -> Hoja de ruta -> Busqueda Avanzada
        $('div.flix_boton.hojaDeRuta_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/distribucion_hojaDeRuta_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });		
        // Distribucion -> Hoja de ruta -> Alta HDR
        $('div.flix_boton.hojaDeRuta_alta_hdr', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/distribucion_hojaDeRuta_altaHDR.php",
                width   : '500',
                height  : 'auto'
            });
        });		
        // Distribucion -> Hoja de ruta -> Actualizar HDR
        $('div.flix_boton.hojaDeRuta_actualizar_hdr', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/distribucion_hojaDeRuta_actualizarHDR.php",
                width   : '500',
                height  : 'auto'
            });
        });		
        // Distribucion -> Seguimiento -> Busqueda avanzada
        $('div.flix_boton.seguimiento_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/distribucion_seguimiento_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// Utilidades -> Actualizacion de precios -> Busqueda Avanzada
        $('div.flix_boton.actualizacionDePrecios_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/actualizacionDePrecios_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// // Utilidades -> Actualizacion de precioss -> Actualizar
        $('div.flix_boton.actualizacionDePrecios_actualizar', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/utilidades_actualizacionDePrecios_actualizar.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// Administracion -> Facturas  de Compras -> Busqueda avanzada
        $('div.flix_boton.listaDePrecios_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_listadDePrecios_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	// Administracion -> Lista de Precios -> Crear
        $('div.flix_boton.listaDePrecios_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_listaDePrecios_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
	//Administracion exportar listas
	$('div.flix_boton.listaDePrecios_exportar_listas', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_listaDePrecios_exportarListas.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // // Administracion -> Actualizacion de precioss -> Actualizar
        $('div.flix_boton.listaDePrecios_actualizar_productos', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/actualizador_listaDePrecios.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // // Administracion -> Actualizacion de precioss -> Actualizar
        $('div.flix_boton.listaDePrecios_actualizar_subfamilias', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/actualizador_listaDePrecios_subfamilias.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Administracion -> Promociones -> Crear
        $('div.flix_boton.promociones_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_promociones_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Administracion -> Liquidaciones -> Crear
        $('div.flix_boton.liquidaciones_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_liquidaciones_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // ABMs -> Usuarios -> Busqueda avanzada
        $('div.flix_boton.usuarios_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuarios_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> Usuarios -> Crear nuevo
        $('div.flix_boton.usuarios_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuarios_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> usuariosSecciones -> Busqueda avanzada
        $('div.flix_boton.usuariosSecciones_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuariosSecciones_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> usuariosBotonera -> Busqueda avanzada
        $('div.flix_boton.usuariosBotonera_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuariosBotonera_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
        
        // ABMs -> UsuariosSecciones-> Alta de seccion-subseccion
        $('div.flix_boton.usuariosSecciones_alta_de_seccion_subseccion', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuariosSecciones_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> usuariosBotonera-> Alta de botones
        $('div.flix_boton.usuariosBotonera_alta_de_botones', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuariosBotonera_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // ABMs -> usuariosHerramientas-> Alta de herramientas
        $('div.flix_boton.usuariosHerramientas_alta_de_herramientas', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_usuariosHerramientas_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
        // Administracion -> Facturas  de venta -> Busqueda avanzada
         $('div.flix_boton.facturas_busqueda_avanzada', divBotonera).live('click', function(){
                     $.colorbox({
                           href    : "tpl/modal/administracion_facturas_busquedaAvanzada.php",
                           width   : '500',
                           height  : 'auto'
	        });
         });
	// Administracion -> Facturas   -> Crear
         $('div.flix_boton.facturas_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_facturas_alta.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    // Administracion -> Cobros -> Busqueda avanzada
         $('div.flix_boton.cobros_busqueda_avanzada', divBotonera).live('click', function(){
                     $.colorbox({
                           href    : "tpl/modal/administracion_cobros_busquedaAvanzada.php",
                           width   : '500',
                           height  : 'auto'
	        });
         });
	// Administracion -> Cobros   -> Crear
         $('div.flix_boton.cobros_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_cobros_alta.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
         	// Administracion -> Cobros   -> Nota de credito
         $('div.flix_boton.cobros_nota_de_credito', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_nc_alta.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
        // Administracion -> Cobros   -> Rendicion
         $('div.flix_boton.cobros_rendir_caja', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_rendir_caja.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
        // Administracion -> Cobros   -> Devolucion de caja
         $('div.flix_boton.cobros_devolucion_de_caja', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_devolucionDeCaja.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    // Administracion -> Cajas   -> Rendicion
         $('div.flix_boton.cajas_rendir_caja', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_rendir_caja.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    // Administracion -> Bancos -> Busqueda avanzada
         $('div.flix_boton.bancos_busqueda_avanzada', divBotonera).live('click', function(){
                     $.colorbox({
                           href    : "tpl/modal/administracion_bancos_busquedaAvanzada.php",
                           width   : '500',
                           height  : 'auto'
	        });
         });
	// Administracion -> Bancos   -> Crear
         $('div.flix_boton.bancos_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_bancos_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    //Administracion exportar deuda
	$('div.flix_boton.estadoDeDeuda_exportar_listado', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_estadoDeDeuda_exportarListado.php",
                width   : '500',
                height  : 'auto'
            });
        });
    //Administracion exportar deuda Joel
	$('div.flix_boton.estadoDeDeuda_exportar', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_estadoDeDeuda_exportar.php",
                width   : '500',
                height  : 'auto'
            });
        });
    //Administracion -> Estado de deuda -> Busqueda avanzada
        $('div.flix_boton.estadoDeDeuda_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_estadoDeDeuda_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    //MAYOR
    //Administracion exportar mayor
	$('div.flix_boton.mayor_exportar_mayor', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_mayor_exportarMayor.php",
                width   : '500',
                height  : 'auto'
            });
        });
	//exportador Jol
	$('div.flix_boton.mayor_exportar', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_mayor_exportar.php",
                width   : '500',
                height  : 'auto'
            });
        });
    //Administracion -> Mayor -> Busqueda avanzada
        $('div.flix_boton.mayor_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_mayor_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Administracion -> Facturas   -> Crear
         $('div.flix_boton.mayor_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_saldoInicio_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    //Administracion -> Cheques -> Busqueda avanzada
        $('div.flix_boton.cheques_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_cheques_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Administracion -> Cheques   -> Imputar
         $('div.flix_boton.cheques_imputar', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_imputar.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    // Administracion -> Cheques   -> Exportar
         $('div.flix_boton.cheques_exportar_cheques', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_exportarCheques.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    // Administracion -> Cuentas -> Busqueda avanzada
         $('div.flix_boton.cuentas_busqueda_avanzada', divBotonera).live('click', function(){
                     $.colorbox({
                           href    : "tpl/modal/administracion_cuentas_busquedaAvanzada.php",
                           width   : '500',
                           height  : 'auto'
	        });
         });
	// Administracion -> Cuentas   -> Crear
         $('div.flix_boton.cuentas_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_cuentas_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    // Administracion -> Relaciones -> Busqueda avanzada
         $('div.flix_boton.relaciones_busqueda_avanzada', divBotonera).live('click', function(){
                     $.colorbox({
                           href    : "tpl/modal/administracion_relaciones_busquedaAvanzada.php",
                           width   : '500',
                           height  : 'auto'
	        });
         });
	// Administracion -> Cuentas   -> Crear
         $('div.flix_boton.relaciones_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/administracion_relaciones_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    //Abms -> Tipo de Productos -> Busqueda avanzada
        $('div.flix_boton.tipoDeProductos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_tipoDeProductos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Abms -> tipo de Productos   -> Crear
         $('div.flix_boton.tipoDeProductos_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/abms_tipoDeProductos_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    //Guias -> Ordenes -> Busqueda avanzada
        $('div.flix_boton.ordenes_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/guias_ordenesGuias_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    //Guias -> Ordenes ->   -> Crear
         $('div.flix_boton.ordenes_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/guias_ordenesGuias_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
	$('div.flix_boton.clientes_exportar', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_clientes_exportar.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Adm -> Rentabilidad -> Exportar
        $('div.flix_boton.rentabilidad_exportar_rentabilidad', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_rentabilidad_exportarRentabilidad.php",
                width   : '500',
                height  : 'auto'
            });
        });
    //Adm -> Tipo de Rentabilidad -> Busqueda avanzada
        $('div.flix_boton.rentabilidad_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_rentabilidad_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    //Abms -> Subfamilias de Productos -> Busqueda avanzada
        $('div.flix_boton.subfamilias_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_subfamilias_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Abms -> Subfamilias de Productos   -> Crear
         $('div.flix_boton.subfamilias_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/abms_subfamilias_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    //Abms -> Relaciones familias-subfamilias -> Busqueda avanzada
        $('div.flix_boton.familiasSubfamilias_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_relaciones_familias_subfamilias_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Abms -> Relaciones familias-subfamilias   -> Crear
         $('div.flix_boton.familiasSubfamilias_crear_nuevo', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/abms_relaciones_familias_subfamilias_crear.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
    // Utilidades -> Etiquetas-> Imprimir
         $('div.flix_boton.etiquetas_imprimir', divBotonera).live('click', function(){
                     $.colorbox({
                        href    : "tpl/modal/utilidades_etiquetas_imprimir.php",
                        width   : '500',
                        height  : 'auto'
           });
        });
// ABMS -> Codigo de Barra -> Busqueda avanzada
	    $('div.flix_boton.codigoDeBarra_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/abms_codigoDeBarra_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    $('div.flix_boton.consultaStock_exportar_consumo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stock_consulta_exportarConsumo.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Stock Consolidado -> Consulta -> Busqueda Avanzada
        $('div.flix_boton.stockConsolidado_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stockConsolidado_consulta_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    // Administracion -> COMBOS -> Crear
        $('div.flix_boton.combos_crear_nuevo', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/administracion_combos_crear.php",
                width   : '500',
                height  : 'auto'
            });
        });
// Stock  -> ASOCIADOR -> Busqueda Avanzada
	$('div.flix_boton.asociadorStock_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/stock_asociador_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
	$('div.flix_boton.ingresos_busqueda_avanzada', divBotonera).live('click', function(){
            $.colorbox({
                href    : "tpl/modal/movimientos_ingresos_busquedaAvanzada.php",
                width   : '500',
                height  : 'auto'
            });
        });
    };
    

    return{
        Init: bindModal
    };

})();
