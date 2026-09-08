<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/inc.Frentes.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class frenteDeFrentes 
{
	private $_colModelo;
	private $_colData = Array();
	private $_colOrder;
	private $_titulo;
	private $_arrDataDevolver = Array();
 	
	public function __construct($parametros = null)	{
            //var_dump($_POST);exit;
            if($_POST['accion'] == 'hacerBusqueda') {
                    //**todos tenemos modelos, entonces lo cargamos**//
                    
                    $frenteModelo = new FrenteModelo();
                    $frenteModelo->buscarModeloPorNombre($_POST['parametros']);
                    //var_dump($frenteModelo->getDetallesModelo());exit;
                    $this->setModelo($frenteModelo->getDetallesModelo());
                    
                    switch ($parametros['tipo']){
                        case "pendientesDeConfirmacion":
                                    $frenteTransaccionesPendientes = new FrenteTransaccionesPendientes();
                                    $frenteTransaccionesPendientes->buscarTransaccion($_POST['parametros']);
                                    $this->setOrder($frenteTransaccionesPendientes->getOrdenTransacciones());
                                    $this->setColData($frenteTransaccionesPendientes);
                                    $titulo =   'Listado de Pendientes';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     => $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;    
                        case "seguimientoHistorico":

                                    $frentePedidosSeguimiento = new FrentePedidosSeguimiento();
                                    $frentePedidosSeguimiento->consultarPedidos($_POST['parametros']);
                                    $this->setOrder($frentePedidosSeguimiento->getOrdenPedidos());
                                    $this->setColData($frentePedidosSeguimiento);
                                    $titulo =   'Listado de historico de pedidos';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     => $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                        case "transacciones":
                                    $frenteTransacciones = new FrenteTransacciones();
                                    $frenteTransacciones->buscarTransaccion($_POST['parametros']);
                                    $this->setOrder($frenteTransacciones->getOrdenTransacciones());
                                    $this->setColData($frenteTransacciones);
                                    $titulo =   'Listado de Transacciones'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                        case "transaccionesAgrupadas":
                                    $frenteTransaccionesAgrupadas = new FrenteTransaccionesAgrupadas();
                                    $frenteTransaccionesAgrupadas->buscarTransaccionAgrupada($_POST['parametros']);
                                    $this->setOrder($frenteTransaccionesAgrupadas->getOrdenTransaccionesAgrupadas());
                                    $this->setColData($frenteTransaccionesAgrupadas);
                                    $titulo =   'Listado de Transacciones Agrupadas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                        case "bloqueador":
                                    $frenteMensajeria = new FrenteMensajeriaAnmat();
                                    $frenteMensajeria->buscarMensajeria($_POST['parametros']);
                                    $this->setOrder($frenteMensajeria->getOrdenMensajeria());
                                    $this->setColData($frenteMensajeria);
                                    $titulo =   'Bloqueo de Dispensas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "clientes":
                                    $frenteClientes = new FrenteClientes();
                                    $frenteClientes->buscarClientes($_POST['parametros']);
                                    $this->setOrder($frenteClientes->getOrdenClientes());
                                    $this->setColData($frenteClientes);
                                    $titulo =   'Listado de Clientes';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "categorias":
                                    $frenteCategorias = new FrenteCategorias();
                                    $frenteCategorias->buscarCategorias($_POST['parametros']);
                                    $this->setOrder($frenteCategorias->getOrdenCategorias());
                                    $this->setColData($frenteCategorias);
                                    $titulo =   'Listado de Categorias';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "estados":
                                    $frenteEstados = new FrenteEstados();
                                    $frenteEstados->buscarEstados($_POST['parametros']);
                                    $this->setOrder($frenteEstados->getOrdenEstados());
                                    $this->setColData($frenteEstados);
                                    $titulo =   'Listado de Estados';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "proveedores":
                                    $frenteProveedores = new FrenteProveedores();
                                    $frenteProveedores->buscarProveedores($_POST['parametros']);
                                    $this->setOrder($frenteProveedores->getOrdenProveedores());
                                    $this->setColData($frenteProveedores);
                                    $titulo =   'Listado de Proveedores';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "productos":
                                    $frenteProductos = new FrenteProductos();
                                    $frenteProductos->buscarProductos($_POST['parametros']);
                                    $this->setOrder($frenteProductos->getOrdenProductos());
                                    $this->setColData($frenteProductos);
                                    $titulo =   'Listado de Productos';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "laboratorios":
                                    $frenteMarcas = new FrenteMarcas();
                                    $frenteMarcas->buscarMarcas($_POST['parametros']);
                                    $this->setOrder($frenteMarcas->getOrdenMarcas());
                                    $this->setColData($frenteMarcas);
                                    $titulo =   'Listado de marcas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "marcas":
                                    $frenteMarcas = new FrenteMarcas();
                                    $frenteMarcas->buscarMarcas($_POST['parametros']);
                                    $this->setOrder($frenteMarcas->getOrdenMarcas());
                                    $this->setColData($frenteMarcas);
                                    $titulo =   'Listado de marcas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "medicos":
                                    $frenteMedicos = new FrenteMedicos();
                                    $frenteMedicos->buscarMedicos($_POST['parametros']);
                                    $this->setOrder($frenteMedicos->getOrdenMedicos());
                                    $this->setColData($frenteMedicos);
                                    $titulo =   'Listado de Medicos';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "pacientes":
                                    $frentePacientes = new FrentePacientes();
                                    $frentePacientes->buscarPacientes($_POST['parametros']);
                                    $this->setOrder($frentePacientes->getOrdenPacientes());
                                    $this->setColData($frentePacientes);
                                    $titulo =   'Listado de Pacientes';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "almacenes":
                                    $frenteAlmacenes = new FrenteAlmacenes();
                                    $frenteAlmacenes->buscarAlmacenes($_POST['parametros']);
                                    $this->setOrder($frenteAlmacenes->getOrdenAlmacenes());
                                    $this->setColData($frenteAlmacenes);
                                    $titulo =   'Listado de Almacenes';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "estanterias":
                                    $frenteEstanterias = new FrenteEstanterias();
                                    $frenteEstanterias->buscarEstanterias($_POST['parametros']);
                                    $this->setOrder($frenteEstanterias->getOrdenEstanterias());
                                    $this->setColData($frenteEstanterias);
                                    $titulo =   'Listado de Estanterías';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "obrasSociales":
                                    $frenteObrasSociales = new FrenteObrasSociales();
                                    $frenteObrasSociales->buscarObrasSociales($_POST['parametros']);
                                    $this->setOrder($frenteObrasSociales->getOrdenObrasSociales());
                                    $this->setColData($frenteObrasSociales);
                                    $titulo =   'Listado de Obras Sociales';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "sucursales":
                                    $frenteSucursales = new FrenteSucursales();
                                    $frenteSucursales->buscarSucursales($_POST['parametros']);
                                    $this->setOrder($frenteSucursales->getOrdenSucursales());
                                    $this->setColData($frenteSucursales);
                                    $titulo =   'Listado de Sucursales';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "entradas":

                                    $FrenteMovimiento = new FrenteMovimiento();
                                    $FrenteMovimiento->buscarMovimiento($_POST['parametros']);
                                    $this->setOrder($FrenteMovimiento->getOrdenMovimientos());
                                    $this->setColData($FrenteMovimiento); 
                                    $titulo =   'Listado de Entradas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                     $this->setDataDevolver($dataDevolver);
                                    break;
                            case "entradasCodigos":
                                    $FrenteMovimiento = new FrenteMovimiento();
                                    $FrenteMovimiento->buscarMovimiento($_POST['parametros']);
                                    $this->setOrder($FrenteMovimiento->getOrdenMovimientos());
                                    $this->setColData($FrenteMovimiento); 
                                    $titulo =   'Listado de Entradas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "busquedaPorCodigo":
                                    $FrenteEtiquetaTrazabilidad = new FrenteEtiquetaTrazabilidad();
                                    $FrenteEtiquetaTrazabilidad->buscarMovimientoPorCodigo($_POST['parametros']);
                                    $this->setOrder($FrenteEtiquetaTrazabilidad->getOrdenEtiquetas());
                                    $this->setColData($FrenteEtiquetaTrazabilidad); 
                                    $titulo =   'Etiquetas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "salidas":
                                    $FrenteMovimiento = new FrenteMovimiento();
                                    $FrenteMovimiento->buscarMovimiento($_POST['parametros']);
                                    $this->setOrder($FrenteMovimiento->getOrdenMovimientos());
                                    $this->setColData($FrenteMovimiento); 
                                    $titulo =   'Listado de Salidas';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "auditorStock":
                                    $FrenteAuditor = new FrenteAuditor();
                                    $FrenteAuditor->buscarMovimiento($_POST['parametros']);

                                    $this->setOrder($FrenteAuditor->getOrdenMovimientos());
                                    $this->setColData($FrenteAuditor); 

                                    $titulo =   'Auditor de Stock';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "importacion":
                                    $FrenteRemesa = new FrenteRemesa();
                                    $FrenteRemesa->buscarRemesa($_POST['parametros']);
                                    $this->setOrder($FrenteRemesa->getOrdenRemesa());
                                    $this->setColData($FrenteRemesa); 
                                    $titulo =   'Importar Remesa';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "consultaStock":
                                    $FrenteStock = new FrenteStock();
                                    $FrenteStock->consultarStock($_POST['parametros']);
                                    $this->setOrder($FrenteStock->getOrdenStock());
                                    $this->setColData($FrenteStock); 
                                    $titulo =   'Consultas de Stock';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "stockConsolidado":
                                    $Frente = new FrenteStockConsolidado();
                                    $Frente->consultarStock($_POST['parametros']);
                                    $this->setOrder($Frente->getOrdenStock());
                                    $this->setColData($Frente);
                                    $titulo =   'Stock Consolidado';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "controlStock":
                                    $FrenteControlStockEstanteria = new FrenteControlStockEstanteria();
                                    $FrenteControlStockEstanteria->buscarControlStock($_POST['parametros']);
                                    $this->setOrder($FrenteControlStockEstanteria->getOrdenStock());
                                    $this->setColData($FrenteControlStockEstanteria); 
                                    $titulo =   'Control de stock';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "listadoDePendientes":
                                    $FrenteMovimientoSeguimiento = new FrenteMovimientoSeguimiento();
                                    $FrenteMovimientoSeguimiento->buscarMovimiento($_POST['parametros']);
                                    $this->setOrder($FrenteMovimientoSeguimiento->getOrdenMovimientos());
                                    $this->setColData($FrenteMovimientoSeguimiento); 
                                    $titulo =   'Listado de Pedidos';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "vendedores":
                                    $frenteVendedores = new FrenteVendedores();
                                    $frenteVendedores->buscarVendedores($_POST['parametros']);
                                    $this->setOrder($frenteVendedores->getOrdenVendedores());
                                    $this->setColData($frenteVendedores);
                                    $titulo =   'Listado de Vendedores';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "distribuidores":
                                    $frenteDistribuidores= new FrenteDistribuidores();
                                    $frenteDistribuidores->buscarDistribuidores($_POST['parametros']);
                                    $this->setOrder($frenteDistribuidores->getOrdenDistribuidores());
                                    $this->setColData($frenteDistribuidores);
                                    $titulo =   'Listado de Distribuidores';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "vehiculos":
                                    $frenteVehiculos= new FrenteVehiculos();
                                    $frenteVehiculos->buscarVehiculos($_POST['parametros']);
                                    $this->setOrder($frenteVehiculos->getOrdenVehiculos());
                                    $this->setColData($frenteVehiculos);
                                    $titulo =   'Listado de Vehiculos';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "pedidos":
                                    $frentePedidos = new FrentePedidos();
                                    $frentePedidos->buscarPedido($_POST['parametros']);
                                    $this->setOrder($frentePedidos->getOrdenPedidos());
                                    $this->setColData($frentePedidos);
                                    $titulo =   'Listado de Pedidos'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "provincias":
                                    $frenteProvincias = new FrenteProvincias();
                                    $frenteProvincias->buscarProvincias($_POST['parametros']);
                                    $this->setOrder($frenteProvincias->getOrdenProvincias());
                                    $this->setColData($frenteProvincias);
                                    $titulo =   'Listado de Provincias'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "departamentos":
                                    $frenteProvincias = new FrenteProvincias();
                                    $frenteProvincias->buscarProvincias($_POST['parametros']);
                                    $this->setOrder($frenteProvincias->getOrdenProvincias());
                                    $this->setColData($frenteProvincias);
                                    $titulo =   'Listado de departamentos'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "localidades":
                                    $frenteLocalidades = new FrenteLocalidades();
                                    $frenteLocalidades->buscarLocalidades($_POST['parametros']);
                                    $this->setOrder($frenteLocalidades->getOrdenLocalidades());
                                    $this->setColData($frenteLocalidades);
                                    $titulo =   'Listado de Localidades'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "hojaDeRuta":
                                    $frenteHdr = new FrenteHdr();
                                    $frenteHdr->buscarHdr($_POST['parametros']);
                                    $this->setOrder($frenteHdr->getOrdenHdr());
                                    $this->setColData($frenteHdr);
                                    $titulo =   'Listado de hojas de ruta'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
			    case "custodia":
                                    $frenteCustodiantes = new FrenteCustodiantes();
                                    $frenteCustodiantes->buscarCustodiantes($_POST['parametros']);
                                    $this->setOrder($frenteCustodiantes->getOrdenCustodiantes());
                                    $this->setColData($frenteCustodiantes);
                                    $titulo =   'Listado de Custodias'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
			    case "stockEnCustodia":
                                    $frenteStockCustodiantes = new FrenteStockCustodiantes();
                                    $frenteStockCustodiantes->buscarStockCustodiantes($_POST['parametros']);
                                    $this->setOrder($frenteStockCustodiantes->getOrdenStockCustodiantes());
                                    $this->setColData($frenteStockCustodiantes);
                                    $titulo =   'Stock en Custodia'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
			    case "presentaciones":
				
                                    $FrentePresentaciones = new FrentePresentaciones();
                                    $FrentePresentaciones->buscarPresentaciones($_POST['parametros']);
                                    
                                    $this->setOrder($FrentePresentaciones->getOrdenPresentaciones());
                                    $this->setColData($FrentePresentaciones);
                                    $titulo =   'Listado de Presentaciones'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
			    case "productosPresentaciones":
                                    $FrentePresentacionesProductos = new FrentePresentacionesProductos();
                                    $FrentePresentacionesProductos->buscarPresentacionesProductos($_POST['parametros']);
                                    $this->setOrder($FrentePresentacionesProductos->getOrdenPresentacionesProductos());
                                    $this->setColData($FrentePresentacionesProductos);
                                    $titulo =   'Listado de productos y presentaciones'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
			    case "ordenDeTrabajo":
                                    $frenteOrdenesTrabajo = new FrenteOrdenesTrabajo();
                                    $frenteOrdenesTrabajo->buscarOrdenTrabajo($_POST['parametros']);
                                    $this->setOrder($frenteOrdenesTrabajo->getOrdenOrdenesTrabajo());
                                    $this->setColData($frenteOrdenesTrabajo);
                                    $titulo =   'Listado de Ordenes de trabajo'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
			    case "actualizacionDePrecios":
                                    $frenteCotizacionDolar= new frenteCotizacionDolar();
                                    $frenteCotizacionDolar->buscarCotizaciones($_POST['parametros']);
                                    $this->setOrder($frenteCotizacionDolar->getOrdenCotizaciones());
                                    $this->setColData($frenteCotizacionDolar);
                                    $titulo =   'Listado de cotizaciones del dolar'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
				case "listaDePrecios":
			    
                                    if($_POST['parametros']['tipoVisa']!=2){
                                        $frenteListaDePrecios= new FrenteListasDePrecios();
                                        //echo "entra";exit;
                                        $frenteListaDePrecios->buscarListas($_POST['parametros']);
                                        $this->setOrder($frenteListaDePrecios->getOrdenLista());
                                        $this->setColData($frenteListaDePrecios);
                                    }else{
                                        UNSET($frenteModelo);
                                        $frenteModelo = new FrenteModelo();
                                        $parametros['tipo']='listasAgrupadas';
                                        $frenteModelo->buscarModeloPorNombre($parametros);
                                        
                                        $this->setModelo($frenteModelo->getDetallesModelo());
                                        
                                        $frenteListaDePrecios= new FrenteListasDePreciosAgrupadas();
                                        
                                        $frenteListaDePrecios->buscarListasPorNombre($_POST['parametros']);
                                        $this->setOrder($frenteListaDePrecios->getOrdenLista());
                                        $this->setColData($frenteListaDePrecios);
                                    }
                                    $titulo =  'Lista de precios ' . $frenteListaDePrecios->getListaNombre(); 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )


                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                        case "modelos":
                                    $FrenteModeloAdmin= new FrenteModeloAdmin();
                                    //echo "entra";exit;
                                    $FrenteModeloAdmin->buscarModelo($_POST['parametros']);
                                    $this->setOrder($FrenteModeloAdmin->getOrden());
                                    $this->setColData($FrenteModeloAdmin);
                                    $titulo =  'Listado Modelos ';// $frente->getModeloNombre(); 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "usuarios":
                                    $FrenteUsuarios = new FrenteUsuarios();
                                    //echo "entra";exit;
                                    $FrenteUsuarios->buscarUsuario($_POST['parametros']);
                                    $this->setOrder($FrenteUsuarios->getOrdenUsuarios());
                                    $this->setColData($FrenteUsuarios);
                                    $titulo =  'Listado de usuarios';// $frente->getModeloNombre(); 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                        case "usuariosSecciones":
                                    $Frente= new FrentePermisos();
                                    //echo "entra";exit;
                                    $Frente->buscarUsuariosSecciones($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =  'Listado de usuarios secciones ';// $frente->getModeloNombre(); 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;               
                            case "usuariosBotonera":
                                    $Frente= new FrenteBotones();
                                    //echo "entra";exit;
                                    $Frente->buscarUsuariosBotonera($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =  'Listado de usuarios botonera ';// $frente->getModeloNombre(); 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;                                                             
                            case "usuariosHerramientas":
                                    $Frente= new FrenteHerramientas();
                                    //echo "entra";exit;
                                    $Frente->buscarUsuariosHerramientas($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =  'Listado de usuarios herramientas ';// $frente->getModeloNombre(); 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                            case "remitos":
                                    $FrenteRemitos= new FrenteRemitos();
                                    $FrenteRemitos->buscarRemitos($_POST['parametros']);
                                    $this->setOrder($FrenteRemitos->getOrdenRemitos());
                                    $this->setColData($FrenteRemitos);
                                    $titulo =   'Listado de remitos'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "facturas":
                                    $FrenteFacturas= new FrenteFacturas();
                                    $FrenteFacturas->buscarFacturas($_POST['parametros']);
                                    $this->setOrder($FrenteFacturas->getOrdenFacturas());
                                    $this->setColData($FrenteFacturas);
                                    $titulo =   'Listado de Remitos'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "cobros":
                                    $FrenteCobros= new FrenteCobros();
                                    $FrenteCobros->buscarCobros($_POST['parametros']);
                                    $this->setOrder($FrenteCobros->getOrdenCobros());
                                    $this->setColData($FrenteCobros);
                                    $titulo =   'Listado de cobros'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "bancos":
                                    $FrenteBancos= new FrenteBancos();
                                    $FrenteBancos->buscarBancos($_POST['parametros']);
                                    $this->setOrder($FrenteBancos->getOrdenBancos());
                                    $this->setColData($FrenteBancos);
                                    $titulo =   'Listado de bancos'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "estadoDeDeuda":
                                    $Frente = new FrenteEstadoDeuda();
                                    $Frente->buscarDeudores($_POST['parametros']);
                                    $this->setOrder($Frente->getOrdenDeudores());
                                    $this->setColData($Frente);
                                    $titulo =   'Estado de cuenta clientes'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "mayor":
                                    $Frente = new FrenteMayor();
                                    $Frente->consultarMayor($_POST['parametros']);
                                    $this->setOrder($Frente->getOrdenMayor());
                                    $this->setColData($Frente);
                                    $titulo =   'Mayor'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "cheques":
                                    $Frente = new FrenteCheques();
                                    $Frente->buscarCheques($_POST['parametros']);
                                    $this->setOrder($Frente->getOrdenCheques());
                                    $this->setColData($Frente);
                                    $titulo =   'Cheques en cartera'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "cuentas":
                                    $Frente = new FrenteCuentas();
                                    $Frente->buscarCuentas($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Listado de cuentas'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "relaciones":
                                    $Frente = new FrenteClientesCuentas();
                                    $Frente->buscarRelaciones($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Listado de relacion clientes ->  cuentas'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "tipoDeProductos":
                                    $Frente = new FrenteFamiliaProductos();
                                    $Frente->buscarFamilia($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Familia de Productos'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "ordenes":
                                    $Frente = new FrenteOrdenesGuias();
                                    $Frente->buscarOrden($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Ordenes de guias'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "totales":
                                    $Frente = new FrenteTotales();
                                    $Frente->buscarTotales($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Total deuda y stock'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                               case "rentabilidad":
                                    $Frente = new FrenteRentabilidad();
                                    $Frente->consultar($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Rentabilidad'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "subfamilias":
                                    $Frente = new FrenteSubFamiliasProductos();
                                    $Frente->buscar($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Subfamilias de Productos'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "familiasSubfamilias":
                                    $Frente = new FrenteFamiliasSubfamilias();
                                    $Frente->buscar($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Relacion familias->subfamilias'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "promociones":
                                    $Frente = new FrentePromociones();
                                    $Frente->buscarPromociones($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Promociones'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "liquidaciones":
                                    $Frente = new FrenteLiquidaciones();
                                    $Frente->buscarLiquidaciones($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Liquidaciones de vendedores'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "etiquetas":
                                    $Frente = new FrenteEtiquetas();
                                    $Frente->consultar($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Imprimir etiquetas'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                                case "cajas":
                                    $Frente = new FrenteCajas();
                                    $Frente->buscarCajas($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Listado de cajas'; 
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            "botones"   =>  $botones
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
				    break;
			case "codigoDeBarra":
                                    $frente = new FrenteProductosCodigoBarras();
                                    $frente->buscarProductos($_POST['parametros']);
                                    $this->setOrder($frente->getOrdenProductos());
                                    $this->setColData($frente);
                                    $titulo =   'Codigo de Barra';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                        case "ingresos":
                                    $Frente = new FrenteIngresos();
                                    $Frente->buscarMovimiento($_POST['parametros']);
                                    $this->setOrder($Frente->getOrdenMovimientos());
                                    $this->setColData($Frente);
                                    $titulo =   'Listado de Ingresos';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                     $this->setDataDevolver($dataDevolver);
                                    break;

                        case "combos":
                                    $Frente = new FrenteCombos();
                                    $Frente->buscar($_POST['parametros']);
                                    $this->setOrder($Frente->getOrden());
                                    $this->setColData($Frente);
                                    $titulo =   'Listado de Combos';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                     $this->setDataDevolver($dataDevolver);
				    break;
			case "asociadorStock":
                                    $FrenteStock = new FrenteAsociadorStock();
                                    $FrenteStock->consultarStock($_POST['parametros']);
                                    $this->setOrder($FrenteStock->getOrdenStock());
                                    $this->setColData($FrenteStock);
                                    $titulo =   'Asociacion Limoneta -> Nuevo sistema';
                                    //devuelvo: El modelo de columna, las celdas, el orden de las mismas (setear default )
                                    $dataDevolver   =   array
                                        (
                                            "colModel"  =>  $this->getModelo(),
                                            "colData"   =>  $this->getColData(),
                                            "colOrder"  =>  $this->getOrder(),
                                            "title"     =>  $titulo,
                                            //TODO setear los botones desde aca
                                        );
                                    $this->setDataDevolver($dataDevolver);
                                    break;
                    }
                }
            if($_POST['accion'] =='armarAutoCompletar'){

                    switch ($_POST[parametros][tipoBuscar]){

                            case "origenNombre":			    
                                        $frenteGln = new FrenteGln();
                                        $frenteGln->buscarGln($_POST['parametros']);
                                        echo json_encode($frenteGln->getDetallesAutocompletar());
                                    break;
                            case "gln_origen":			    
                                        $frenteGln = new FrenteGln();
                                        $frenteGln->buscarGln($_POST['parametros']);
                                        echo json_encode($frenteGln->getDetallesAutocompletar());
                                    break;
                            case "destinoNombre":			    
                                        $frenteGln = new FrenteGln();
                                        $frenteGln->buscarGln($_POST['parametros']);
                                        echo json_encode($frenteGln->getDetallesAutocompletar());
                                    break;
                            case "nombreProducto":			    
                                        $frenteProductosAnmat = new FrenteProductosAnmat();
                                        $frenteProductosAnmat->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductosAnmat->getDetallesAutocompletar());
                                    break;
                            case "clientes":			    
                                        $frenteClientes = new FrenteClientes();
                                        $frenteClientes->buscarClientes($_POST['parametros']);
                                        echo json_encode($frenteClientes->getDetallesAutocompletar());
                                    break;
                            case "cliente_id":			    
                                        $frenteClientes = new FrenteClientes();
                                        $frenteClientes->buscarClientes($_POST['parametros']);
                                        echo json_encode($frenteClientes->getDetallesAutocompletar());
                                    break;
                            case "categorias":			    
                                        $frenteCategorias = new FrenteCategorias();
                                        $frenteCategorias->buscarCategorias($_POST['parametros']);
                                        echo json_encode($frenteCategorias->getDetallesAutocompletar());
                                    break;
                            case "categoria_nombre":			    
                                        $frenteCategorias = new FrenteCategorias();
                                        $frenteCategorias->buscarCategorias($_POST['parametros']);
                                        echo json_encode($frenteCategorias->getDetallesAutocompletar());
                                    break;
                            case "categoria_id":			    
                                        $frenteCategorias = new FrenteCategorias();
                                        $frenteCategorias->buscarCategorias($_POST['parametros']);
                                        echo json_encode($frenteCategorias->getDetallesAutocompletar());
                                    break;
                            case "condicion":			    
                                        $frenteCondicionVenta= new FrenteCondicionVenta();
                                        $frenteCondicionVenta->buscarCondicionVenta($_POST['parametros']);
                                        echo json_encode($frenteCondicionVenta->getDetallesAutocompletar());
                                    break;
                            case "marcas":			    
                                        $frenteMarcas = new FrenteMarcas();
                                        $frenteMarcas->buscarMarcas($_POST['parametros']);
                                        echo json_encode($frenteMarcas->getDetallesAutocompletar());
                                    break;
                            case "marca_id":			    
                                        $frenteMarcas = new FrenteMarcas();
                                        $frenteMarcas->buscarMarcas($_POST['parametros']);
                                        echo json_encode($frenteMarcas->getDetallesAutocompletar());
                                    break;
                            case "proveedores":			    
                                        $frenteProveedores = new FrenteProveedores();
                                        $frenteProveedores->buscarProveedores($_POST['parametros']);
                                        echo json_encode($frenteProveedores->getDetallesAutocompletar());
                                    break;
                            case "prove_id":			    
                                        $frenteProveedores = new FrenteProveedores();
                                        $frenteProveedores->buscarProveedores($_POST['parametros']);
                                        echo json_encode($frenteProveedores->getDetallesAutocompletar());
                                    break;
                            case "prove_nombre":			    
                                        $frenteProveedores = new FrenteProveedores();
                                        $frenteProveedores->buscarProveedores($_POST['parametros']);
                                        echo json_encode($frenteProveedores->getDetallesAutocompletar());
                                    break;
                            case "proveedoresProductos":			    
                                        $frenteProveedores = new FrenteProveedores();
                                        $frenteProveedores->buscarProveedores($_POST['parametros']);
                                        echo json_encode($frenteProveedores->getDetallesAutocompletar());
                                    break;
                            case "proveedor":			    
                                        $frenteProveedores = new FrenteProveedores();
                                        $frenteProveedores->buscarProveedores($_POST['parametros']);
                                        echo json_encode($frenteProveedores->getDetallesAutocompletar());
                                    break;
                            case "sucursales":			    
                                        $frenteSucursales = new FrenteSucursales();
                                        $frenteSucursales->buscarSucursales($_POST['parametros']);
                                        echo json_encode($frenteSucursales->getDetallesAutocompletar());
                                    break;
                            case "sucursalMensajeria":			    
                                        $frenteSucursalesMensajeria = new FrenteSucursalesMensajeria();
                                        $frenteSucursalesMensajeria->buscarSucursales($_POST['parametros']);
                                        echo json_encode($frenteSucursalesMensajeria->getDetallesAutocompletar());
                                    break;
                            case "sucursal_nombre":			    
                                        $frenteSucursales = new FrenteSucursales();
                                        $frenteSucursales->buscarSucursales($_POST['parametros']);
                                        echo json_encode($frenteSucursales->getDetallesAutocompletar());
                                    break;
                            case "almacenes":			    
                                        $frenteAlmacenes = new FrenteAlmacenes();
                                        $frenteAlmacenes->buscarAlmacenes($_POST['parametros']);
                                        echo json_encode($frenteAlmacenes->getDetallesAutocompletar());
                                    break;
                            case "almacen_id":			    
                                        $frenteAlmacenes = new FrenteAlmacenes();
                                        $frenteAlmacenes->buscarAlmacenes($_POST['parametros']);
                                        echo json_encode($frenteAlmacenes->getDetallesAutocompletar());
                                    break;
                            case "estanterias":			    
                                        $frenteEstanterias = new FrenteEstanterias();
                                        $frenteEstanterias->buscarEstanterias($_POST['parametros']);
                                        echo json_encode($frenteEstanterias->getDetallesAutocompletar());
                                    break;
                            case "estanteriaEntradas":			    
                                        $frenteEstanterias = new FrenteEstanterias();
                                        $frenteEstanterias->buscarEstanterias($_POST['parametros']);
                                        echo json_encode($frenteEstanterias->getDetallesAutocompletar());
                                    break;

                            case "estanteriasControl":			    
                                        $frenteEstanterias = new FrenteEstanterias();
                                        $frenteEstanterias->buscarEstanterias($_POST['parametros']);
                                        echo json_encode($frenteEstanterias->getDetallesAutocompletar());
                                    break;

                            case "estanteriasEntradasCodigos":			    
                                        $frenteEstanterias = new FrenteEstanterias();
                                        $frenteEstanterias->buscarEstanterias($_POST['parametros']);
                                        echo json_encode($frenteEstanterias->getDetallesAutocompletar());
                                    break;
                            case "productos":			    
                                        $frenteProductos = new FrenteProductos();
                                        $frenteProductos->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductos->getDetallesAutocompletar());
                                    break;
                            case "productosAbm":
                                        $frenteProductos = new FrenteProductos();
                                        $frenteProductos->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductos->getDetallesAutocompletar());
                                    break;
                            case "productosCombo":
                                        $frenteProductos = new FrenteProductos();
                                        $frenteProductos->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductos->getDetallesAutocompletar());
                                    break;

                            case "productosCant":			    
                                        $frenteProductos = new FrenteProductos();
                                        $frenteProductos->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductos->getDetallesAutocompletarConTotal());
                                    break;
                            case "productosNombre":			    
                                        $frenteProductos = new FrenteProductos();
                                        $frenteProductos->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductos->getDetallesAutocompletarConTotal());
                                    break;
                            case "nombreProducto":			    
                                        $frenteProductosAnmat = new FrenteProductosaAnmat();
                                        $frenteProductosAnmat->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductosAnmat->getDetallesAutocompletar());
                                    break;
                            /*case "consultaStock":			    
                                        $frenteProductos = new FrenteProductos();
                                        $frenteProductos->buscarProductos($_POST['parametros']);
                                        echo json_encode($frenteProductos->getDetallesAutocompletar());
                                    break;
                            */
                            case "medicos":			    
                                        $frenteMedicos = new FrenteMedicos();
                                        $frenteMedicos->buscarMedicos($_POST['parametros']);
                                        echo json_encode($frenteMedicos->getDetallesAutocompletar());
                                    break;
                            case "pacientes":			    
                                        $frentePacientes = new FrentePacientes();
                                        $frentePacientes->buscarPacientes($_POST['parametros']);
                                        echo json_encode($frentePacientes->getDetallesAutocompletar());
                                    break;
                            case "pacientesSalidas":			    
                                        $frentePacientes = new FrentePacientes();
                                        $frentePacientes->buscarPacientes($_POST['parametros']);
                                        echo json_encode($frentePacientes->getDetallesAutocompletar());
                                    break;
                            case "obrasSociales":			    
                                        $frenteObrasSociales = new FrenteObrasSociales();
                                        $frenteObrasSociales->buscarObrasSociales($_POST['parametros']);
                                        echo json_encode($frenteObrasSociales->getDetallesAutocompletar());
                                    break;
                            case "tipoMovimientoMensajeria":			    
                                        $frenteTipoMovimientoAnmat = new FrenteTipoMovimientoAnmat();
                                        $frenteTipoMovimientoAnmat->buscarTipoMovimientoAnmat($_POST['parametros']);
                                        echo json_encode($frenteTipoMovimientoAnmat->getDetallesAutocompletar());
                                    break;
                            case "tipo_movimiento_id":			    
                                        $frenteTipoMovimientoAnmat = new FrenteTipoMovimientoAnmat();
                                        $frenteTipoMovimientoAnmat->buscarTipoMovimientoAnmat($_POST['parametros']);
                                        echo json_encode($frenteTipoMovimientoAnmat->getDetallesAutocompletar());
                                    break;
                            case "vendedores":			    
                                        $frenteVendedores = new FrenteVendedores();
                                        $frenteVendedores->buscarVendedores($_POST['parametros']);
                                        //var_dump($frenteVendedores->getDetallesAutocompletar()))
                                        echo json_encode($frenteVendedores->getDetallesAutocompletar());
                            case "subfamilias":			    
                                        $frente = new FrenteSubFamiliasProductos();
                                        $frente->buscar($_POST['parametros']);
                                        echo json_encode($frente->getDetallesAutocompletar());
					break;
			   case "vendedoresClientes":			    
                                        $frenteVendedores = new FrenteVendedores();
                                        $frenteVendedores->buscarVendedores($_POST['parametros']);
                                        echo json_encode($frenteVendedores->getDetallesAutocompletar());
                                    break;
                            case "distribuidores":			    
                                        $frenteDistribuidores = new FrenteDistribuidores();
                                        $frenteDistribuidores->buscarDistribuidores($_POST['parametros']);
                                        echo json_encode($frenteDistribuidores->getDetallesAutocompletar());
                                    break;
                            case "vehiculos":			    
                                        $frenteVehiculos= new FrenteVehiculos();
                                        $frenteVehiculos->buscarVehiculos($_POST['parametros']);
                                        echo json_encode($frenteVehiculos->getDetallesAutocompletar());
                                    break;
                            case "estados":			    
                                        $frenteEstados = new FrenteEstados();
                                        $frenteEstados->buscarEstados($_POST['parametros']);
                                        echo json_encode($frenteEstados->getDetallesAutocompletar());
                                    break;
                            case "provincias":			    
                                        $frenteProvincias = new FrenteProvincias();
                                        $frenteProvincias->buscarProvincias($_POST['parametros']);
                                        echo json_encode($frenteProvincias->getDetallesAutocompletar());
                                    break;
                            case "localidades":			    
                                        $frenteLocalidades = new FrenteLocalidades();
                                        $frenteLocalidades->buscarLocalidades($_POST['parametros']);
                                        //echo "llega";
                                        echo json_encode($frenteLocalidades->getDetallesAutocompletar());
                                    break;
                            case "custodiantes":			    
                                        $frenteCustodiantes= new FrenteCustodiantes();
                                        $frenteCustodiantes->buscarCustodiantes($_POST['parametros']);
                                        echo json_encode($frenteCustodiantes->getDetallesAutocompletar());
                                    break;
                            case "presentaciones":			    
                                        $frentePresentaciones= new FrentePresentaciones();
                                        $frentePresentaciones->buscarPresentaciones($_POST['parametros']);
                                        echo json_encode($frentePresentaciones->getDetallesAutocompletar());
                                    break;
                            case "productosPresentacion":			    
                                        $frentePresentacionesProductos= new FrentePresentacionesProductos();
                                        $frentePresentacionesProductos->buscarPresentacionesProductos($_POST['parametros']);
                                        echo json_encode($frentePresentacionesProductos->getDetallesAutocompletar());
                                    break;
                            case "listaDePrecios":			    
                                        $FrenteListasDePrecios = new FrenteListasDePrecios();
                                        $FrenteListasDePrecios ->buscarListasPorNombre($_POST['parametros']);
                                        echo json_encode($FrenteListasDePrecios->getDetallesAutocompletar());
                                    break;
                                    
                            case "modelos":			    
                                        $FrenteModeloAdmin = new FrenteModeloAdmin();
                                        $FrenteModeloAdmin->buscarModelo($_POST['parametros']);
                                        echo json_encode($FrenteModeloAdmin->getDetallesAutocompletar());
                                    break;
                            case "usuarios":			    
                                        $FrenteUsuarios = new FrenteUsuarios();
                                        $FrenteUsuarios->buscarUsuario($_POST['parametros']);
                                        echo json_encode($FrenteUsuarios->getDetallesAutocompletar());
                                    break;
                            case "usuariosPermisos":			    
                                        $Frente= new FrentePermisos();
                                        $Frente->buscarUsuariosSecciones($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "usuariosBotonera":			    
                                        $Frente= new FrenteBotones();
                                        $Frente->buscarUsuariosBotonera($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;                                    
                            case "usuariosHerramientas":			    
                                        $Frente= new FrenteHerramientas();
                                        $Frente->getDetallesAutocompletar($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "subseccion_inicio":			    
                                        $FrenteModeloAdmin = new FrenteModeloAdmin();
                                        $FrenteModeloAdmin->buscarModelo($_POST['parametros']);
                                        echo json_encode($FrenteModeloAdmin->getDetallesAutocompletarSubseccion());
                                    break;
                            case "remitos":			    
                                        $FrenteRemitos = new FrenteRemitos();
                                        $FrenteRemitos->buscarRemitos($_POST['parametros']);
                                        echo json_encode($FrenteRemitos->getDetallesAutocompletar());
                                    break;
                            case "facturas":			    
                                        $FrenteFacturas = new FrenteFacturas();
                                        $FrenteFacturas->buscarFacturas($_POST['parametros']);
                                        echo json_encode($FrenteFacturas->getDetallesAutocompletar());
                                    break;
                            case "facturasPendientes":			    
                                        $FrenteFacturas = new FrenteFacturas();
                                        $FrenteFacturas->buscarFacturas($_POST['parametros']);
                                        echo json_encode($FrenteFacturas->getDetallesAutocompletarFacturasPendientes());
                                    break;
                            case "bancos":			    
                                        $FrenteBancos = new FrenteBancos();
                                        $FrenteBancos->buscarBancos($_POST['parametros']);
                                        echo json_encode($FrenteBancos->getDetallesAutocompletar());
                                    break;
                            case "formas":			    
                                        $Frente = new FrenteFormasPagos();
                                        $Frente->buscarFormas($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "facturaLetra":			    
                                        $FrenteFacturas = new FrenteFacturas();
                                        echo json_encode($FrenteFacturas->getNroFactura($_POST['parametros']));
                                    break;
                            case "remitosPendientes":			    
                                        $Frente = new FrenteRemitos();
                                        echo json_encode($Frente->getRemitosPendientes($_POST['parametros']));
                                    break;
                            case "facturas":			    
                                        $Frente = new FrenteFacturas();
                                        $Frente->buscarFacturas($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "ordenNro":			    
                                        $Frente = new FrenteCobros();
                                        $Frente->buscarCobros($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "cheques":			    
                                        $Frente = new FrenteCheques();
                                        $Frente->buscarCheques($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "imputaciones":			    
                                        $Frente = new FrenteImputaciones();
                                        $Frente->buscar($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "imputacionesDos":			    
                                        $Frente = new FrenteImputaciones();
                                        $Frente->buscar($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "estadosDos":			    
                                        $frenteEstados = new FrenteEstados();
                                        $frenteEstados->buscarEstados($_POST['parametros']);
                                        echo json_encode($frenteEstados->getDetallesAutocompletar());
                                    break;
                            case "cuentas":			    
                                        $Frente = new FrenteCuentas();
                                        $Frente->buscarCuentas($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "relaciones":			    
                                        $Frente = new FrenteClientesCuentas();
                                        $Frente->buscarRelaciones($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "familias":			    
                                        $Frente = new FrenteFamiliaProductos();
                                        $Frente->buscarFamilia($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                            case "condicionIva":
                                        $Frente = new FrenteCondicionIva();
                                        $Frente->buscarCondicionIva($_POST['parametros']);
                                        echo json_encode($Frente->getDetallesAutocompletar());
                                    break;
                        }
                }

            if($_POST['accion'] =='hacerAlta'){

                        switch ($parametros[tipo]){


                                case "importarRemesa":
                                            $imp = new ImportadorRemesa($parametros);
                                            $imp->cargarRemesa();
                                            $imp->salvarRemesa();
                                            break;
                                case "clientes":
                                            $frenteClientes = new FrenteClientes();
                                            $frenteClientes->salvarMe($parametros);
                                        break;
                                case "proveedores":
                                            $frenteProveedores = new FrenteProveedores();
                                            $frenteProveedores->salvarMe($parametros);
                                        break;

                                case "categorias":			    
                                            $frenteCategorias = new FrenteCategorias();
                                            $frenteCategorias->salvarMe($parametros);
                                        break;
                                case "estados":			    
                                            $frenteEstados = new FrenteEstados();
                                            $frenteEstados->salvarMe($parametros);
                                        break;
                                case "marcas":			    
                                            $frenteMarcas = new FrenteMarcas();
                                            $frenteMarcas->salvarMe($parametros);
                                        break;
                                case "productos":
                                            $frenteProductos = new FrenteProductos();
                                            $frenteProductos->salvarMe($parametros);
                                        break;
                                case "almacenes":
                                            $frenteAlmacenes = new FrenteAlmacenes();
                                            $frenteAlmacenes->salvarMe($parametros);
                                        break;
                                case "estanterias":
                                            $frenteEstanterias = new FrenteEstanterias();
                                            $frenteEstanterias->salvarMe($parametros);
                                        break;
                                case "pacientes":
                                            $frentePacientes = new FrentePacientes();
                                            $frentePacientes->salvarMe($parametros);
                                        break;
                                case "medicos":
                                            $frenteMedicos = new FrenteMedicos();
                                            $frenteMedicos->salvarMe($parametros);
                                        break;
                                case "obrasSociales":
                                            $frenteObrasSociales = new FrenteObrasSociales();
                                            $frenteObrasSociales->salvarMe($parametros);
                                        break;
                                case "sucursales":
                                            $frenteSucursales = new FrenteSucursales();
                                            $frenteSucursales->salvarMe($parametros);
                                        break;
                                case "entradas":
                                        if($_POST['idMovimientoAnt']==null){
                                            $FrenteMovimiento = new FrenteMovimiento();
                                            $FrenteMovimiento->generarMovimiento($parametros);
                                        }else{
                                            $FrenteMovimiento = new FrenteMovimiento();
                                            $FrenteMovimiento->editarEntrada($_POST);
                                        }
                                        break;
                                case "devoluciones":
                                            $FrenteMovimiento = new FrenteMovimiento();
                                            $FrenteMovimiento->generarMovimiento($parametros);
                                        break;
                                case "salidas":
                                            if($_POST['idMovimientoAnt']==null){
                                                $FrenteMovimiento = new FrenteMovimiento();
                                                $FrenteMovimiento->generarMovimiento($parametros);
                                            }else{
                                                $FrenteMovimiento = new FrenteMovimiento();
                                                $FrenteMovimiento->editarMovimiento($_POST);
                                            }
                                        break;
                                case "reventa":
                                            $FrenteMovimiento = new FrenteMovimiento();
                                            $FrenteMovimiento->generarMovimiento($parametros);
                                        break;
                                case "controlStock":
                                            $FrenteControlStockEstanteria = new FrenteControlStockEstanteria();
                                            $FrenteControlStockEstanteria->generarControlStock($_POST['parametros']);
                                case "vendedores":
                                            $frenteVendedores = new FrenteVendedores();
                                            $frenteVendedores->salvarMe($parametros);
                                        break;
                                case "distribuidores":
                                            $frenteDistribuidores= new FrenteDistribuidores();
                                            $frenteDistribuidores->salvarMe($parametros);
                                        break;
                                case "vehiculos":
                                            $frenteVehiculos= new FrenteVehiculos();
                                            $frenteVehiculos->salvarMe($parametros);
                                        break;
                                case "pedidos":
                                            $frentePedidos= new FrentePedidos();
                                            $frentePedidos->generarPedido($parametros);
                                        break;
                                case "provincias":
                                            $frenteProvincias= new FrenteProvincias();
                                            $frenteProvincias->salvarMe($parametros);
                                        break;
                                case "localidades":
                                            $frenteLocalidades= new FrenteLocalidades();
                                            $frenteLocalidades->salvarMe($parametros);
                                        break;
                                case "hdr":
                                            $frenteHdr= new FrenteHdr();
                                            if($_POST['parametros']['operacion']=='alta'){
                                                $frenteHdr->generarHdr($parametros);
                                            }else{
                                                $frenteHdr->actualizar($parametros);
                                            }
                                        break;
                                case "custodiantes":
                                                $frenteCustodiantes= new FrenteCustodiantes();
                                                $frenteCustodiantes->salvarMe($parametros);
                                            break;
                                case "presentaciones":
                                                $frentePresentaciones= new FrentePresentaciones();
                                                $frentePresentaciones->salvarMe($parametros);
                                            break;
                                case "productosPresentaciones":
                                                $frentePresentacionesProductos= new FrentePresentacionesProductos();
                                                $frentePresentacionesProductos->salvarMe($parametros);
                                            break;
                                case "ordenDeTrabajo":
                                                $frenteOrdenesTrabajo= new FrenteOrdenesTrabajo();
                                                $frenteOrdenesTrabajo->generarOrdenTrabajo($parametros);
                                            break;
                                case "cotizacionDolar":
                                                $frenteCotizacionDolar= new frenteCotizacionDolar();
                                                $frenteCotizacionDolar->salvarMe($parametros);
                                            break;
                                case "listaDePrecios":
                                                $frenteListaDePrecios= new FrenteListasDePrecios();
                                                $frenteListaDePrecios->salvarMe($parametros);
                                            break;
                                case "modelos":
                                            $FrenteModeloAdmin= new FrenteModeloAdmin();
                                            $FrenteModeloAdmin->salvarMe($parametros);
                                        break;
                                case "usuarios":
                                            $FrenteUsuarios= new FrenteUsuarios();
                                            $FrenteUsuarios->salvarMe($parametros);
                                        break;
                                 case "usuariosPermisos":
                                //echo "lll";exit;
                                            $Frente= new FrentePermisos();
                                            $Frente->salvarUsuariosPermisos($parametros);
                                        break;                   
                                case "usuariosBotonera":
                                //echo "lll";exit;
                                            $Frente= new FrenteBotones();
                                            $Frente->salvarUsuariosBotones($parametros);
                                        break;          
                                case "usuariosHerramientas":
                                //echo "lll";exit;
                                            $Frente= new FrenteHerramientas();
                                            $Frente->salvarUsuariosHerramientas($parametros);
                                        break; 
                                case "facturas":
                                            $FrenteFacturas= new FrenteFacturas();
                                            $FrenteFacturas->generarFactura($parametros);
                                        break;
                                case "cobros":
                                            $FrenteCobros= new FrenteCobros();
                                            $FrenteCobros->generarCobro($parametros);
                                        break;
                                case "cobrosNc":
                                            $FrenteCobros= new FrenteCobros();
                                            $FrenteCobros->generarCobro($parametros);
                                        break;
                                case "devolucionDeCaja":
                                            $FrenteCobros= new FrenteCobros();
                                            $FrenteCobros->devolucionDeCaja($parametros);
                                        break;
                                case "bancos":
                                            $FrenteBancos= new FrenteBancos();
                                            $FrenteBancos->salvarMe($parametros);
                                        break;
                                case "mayor":
                                            $Frente= new FrenteMayor();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "imputaciones":
                                            $Frente= new FrenteImputaciones();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "cheques":
                                            $Frente= new FrenteCheques();
                                            $Frente->imputarCheques($parametros);
                                        break;
                                case "actualizarListas":
                                            $Frente= new FrenteActualizadorLista();
                                            $Frente->actualizar($parametros);
                                        break;
                                case "actualizarListasSubfamilias":
                                            $Frente= new FrenteActualizadorLista();
                                            $Frente->actualizarSubfamilias($parametros);
                                        break;
                                case "cuentas":
                                            $Frente= new FrenteCuentas();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "relaciones":
                                            $Frente= new FrenteClientesCuentas();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "tipoDeProductos":
                                            $Frente= new FrenteFamiliaProductos();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "ordenes":
                                            $Frente= new FrenteOrdenesGuias();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "subfamilias":
                                            $Frente= new FrenteSubFamiliasProductos();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "familiasSubfamilias":
                                            $Frente= new FrenteFamiliasSubfamilias();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "promociones":
                                            $Frente= new FrentePromociones();
                                            $Frente->salvarMe($parametros);
                                        break;
                                case "liquidaciones":
                                            $Frente= new FrenteLiquidaciones();
                                            $Frente->generarLiquidacion($parametros);
                                        break;
                                case "roturas":
                                            $Frente= new FrenteMovimiento();
                                            $Frente->agregarRoturas($parametros);
                                        break;
                                case "diferenciaDePrecio":
                                            $Frente= new FrenteMovimiento();
                                            $Frente->cargarDiferenciaDePrecio($parametros);
                                        break;
                                case "actualizarComision":
                                            $frenteProductos = new FrenteProductos();
                                            $frenteProductos->actualizarComision($parametros);
                                        break;
                                case "combos":
                                            $frente = new FrenteCombos();
                                            $frente->crearCombos($parametros);
                                        break;
                    }
                }

            if($_POST['accion'] =='actualizarRegistro'){

                switch($_POST['parametros']['tipo']){

                    case "obrasSociales":
                            $frenteObrasSociales = new FrenteObrasSociales();
                            $frenteObrasSociales->actualizarMe($parametros);
                            break;
                    case "sucursales":
                            $frenteSucursales = new FrenteSucursales();
                            $frenteSucursales->actualizarMe($parametros);
                            break;
                    case "estanterias":
                            $frenteEstanterias = new FrenteEstanterias();
                            $frenteEstanterias->actualizarMe($parametros);
                            break;
                    case "almacenes":
                            $frenteAlmacenes = new FrenteAlmacenes();
                            $frenteAlmacenes->actualizarMe($parametros);
                            break;
                    case "proveedores":
                            $frenteProveedores = new FrenteProveedores();
                            $frenteProveedores->actualizarMe($parametros);
                            break;
                    case "clientes":
                            $frenteClientes = new FrenteClientes();
                            $frenteClientes->actualizarMe($parametros);
                            break;
                    case "pacientes":
                            $frentePacientes = new FrentePacientes();
                            $frentePacientes->actualizarMe($parametros);
                            break;
                    case "medicos":
                            $frenteMedicos = new FrenteMedicos();
                            $frenteMedicos->actualizarMe($parametros);
                            break;
                    case "marcas":
                            $frenteMarcas = new FrenteMarcas();
                            $frenteMarcas->actualizarMe($parametros);
                            break;
                    case "productos":
                            $frenteProductos = new FrenteProductos();
                            $frenteProductos->actualizarMe($parametros);
                            break;
                    case "categorias":
                            $frenteCategorias = new FrenteCategorias();
                            $frenteCategorias->actualizarMe($parametros);
                            break;
                    case "transacciones":
                            $frenteAutogestion= new FrenteAutogestion();
                            $frenteAutogestion->actualizarme($parametros);
                            break;
                    case "transaccionesAgrupadas":
                            $frenteAutogestionAgrupada= new FrenteAutogestionAgrupada();
                            $frenteAutogestionAgrupada->actualizarme($parametros);
                            break;
                    case "listadoDePendientes":
                            $frenteMovimientoSeguimiento= new FrenteMovimientoSeguimiento();
                            $frenteMovimientoSeguimiento->actualizarme($parametros);
                            break;
                    case "vendedores":
                            $frenteVendedores = new FrenteVendedores();
                            $frenteVendedores->actualizarMe($parametros);
                            break;
                    case "distribuidores":
                            $frenteDistribuidores= new FrenteDistribuidores();
                            $frenteDistribuidores->actualizarMe($parametros);
                            break;
                    case "estados":
                            $frenteEstados = new FrenteEstados();
                            $frenteEstados->actualizarMe($parametros);
                            break;
                    case "provincias":
                            $frenteProvincias = new FrenteProvincias();
                            $frenteProvincias->actualizarMe($parametros);
                            break;
                    case "localidades":
                            $frenteLocalidades = new FrenteLocalidades();
                            $frenteLocalidades->actualizarMe($parametros);
                            break;
                    case "custodia":
                            $frenteCustodiantes= new FrenteCustodiantes();
                            $frenteCustodiantes->actualizarMe($parametros);
                            break;
                    case "presentaciones":
                            $frentePresentaciones= new FrentePresentaciones();
                            $frentePresentaciones->actualizarMe($parametros);
                            break;
                    case "productosPresentaciones":
                            $frentePresentacionesProductos= new FrentePresentacionesProductos();
                            $frentePresentacionesProductos->actualizarMe($parametros);
                            break;
                    case "ordenDeTrabajo":
                            $frenteOrdenesTrabajo= new FrenteOrdenesTrabajo();
                            $frenteOrdenesTrabajo->actualizarMe($parametros);
                            break;
                    case "listaDePrecios":
                            if (array_key_exists('lista_mayorista', $parametros['campos'])) {
                                $FrenteListasDePreciosAgrupadas= new FrenteListasDePreciosAgrupadas();
                                $FrenteListasDePreciosAgrupadas->marcarListaMayorista($parametros);
                            }else{
                                $frenteListaDePrecios= new FrenteListasDePrecios();
                                $frenteListaDePrecios->actualizarMe($parametros);
                            }
                            break;	
                    case "modelos":
                            $FrenteModeloAdmin= new FrenteModeloAdmin();
                            $FrenteModeloAdmin->actualizarMe($parametros);
                            break;
                    case "usuarios":
                            $Frente= new FrenteUsuarios();
                            $Frente->actualizarMe($parametros);
                            break;
                    case "usuariosSecciones":
                            $Frente= new FrentePermisos();
                            $Frente->actualizarMe($parametros);
                            break;
                    case "usuariosBotonera":
                            $Frente= new FrenteBotones();
                            $Frente->actualizarMe($parametros);
                            break;
                    case "usuariosHerramientas":
                            $Frente= new FrenteHerramientas();
                            $Frente->actualizarMe($parametros);
                            break;
                    case "listaDePrecios":
                            $frenteListaDePrecios= new FrenteListasDePrecios();
                            $frenteListaDePrecios->actualizarMe($parametros);
                            break;
                    case "cheques":
                            $frente= new FrenteCheques();
                            $frente->actualizarMe($parametros);
                            break;
                    case "ordenes":
                            $frente= new FrenteOrdenesGuias();
                            $frente->actualizarMe($parametros);
                            break;
                    case "salidas":
                            $frente= new FrenteMovimiento();
                            $frente->actualizarObs($parametros);
                            break;
                    case "entradas":
                            $frente= new FrenteMovimiento();
                            $frente->actualizarObs($parametros);
                            break;
                    case "relaciones":
                            $frente= new FrenteClientesCuentas();
                            $frente->actualizarRelacion($parametros);
                            break;
                    case "tipoDeProductos":
                            $frente= new FrenteFamiliaProductos();
                            $frente->actualizarOrden($parametros);
			    break;
		   case "subfamilias":
                            $frente= new FrenteSubFamiliasProductos();
                            $frente->actualizar($parametros);
			    break;
		   case "codigoDeBarra":
                            $Frente = new FrenteProductosCodigoBarras();
                            $Frente->actualizarCodigo($parametros);
			    break;
                   case "cobros":
                            $Frente = new FrenteCobros();
                            $Frente->actualizar($parametros);
                            break;
                    case "combos":
                            $Frente = new FrenteCombos();
                            $Frente->actualizarCabecera($parametros);
			    break;
		    case "ingresos":
                            $Frente = new FrenteIngresos();
                            $Frente->actualizarObs($parametros);
			    break;
		    case "asociadorStock":
                            $Frente = new FrenteAsociadorStock();
                            $Frente->asociar($parametros);
                            break;

                }
            }
            if($_POST['accion']=='actualizarProducto'){

               switch ($_POST['parametros']['tipo']) {
                    case "modelos":
                            $FrenteModeloAdmin= new FrenteModeloAdmin();
                            $FrenteModeloAdmin->actualizarDetalles($parametros);
                            break;
                    case "usuarios":
                            $FrenteUsuarios= new FrenteUsuarios();
                            $FrenteUsuarios->actualizarMe($parametros);
                            break;
                    case "usuariosSecciones":
                    //echo "llega",exit;
                            $Frente = new FrentePermisos();
                            $Frente->actualizarGeneral($parametros);
                            break;
                    case "usuariosSubsecciones":
                            $Frente = new FrentePermisos();
                            $Frente->actualizarGeneral($parametros);
                            break;
                    case "usuariosBotonera":
                            $Frente = new FrenteBotones();
                            $Frente->actualizarBotones($parametros);
                            break;
                    case "ingresos":
                            $Frente = new FrenteIngresos();
                            $Frente->actualizar($parametros);
                            break;
                    case "combos":
                            $Frente = new FrenteCombos();
                            $Frente->actualizarDetalle($parametros);
                            break;
                     default:
                        $frenteAutogestion = new FrenteAutogestion();
                        $frenteAutogestion->actualizarDetalles($parametros);    
                        break;
                    
                }

            }

            if($_POST['accion'] =='getDetalles') {

                    switch($_POST['tipo']){

                            case "entradas":
                                   $frenteMovimiento = new FrenteMovimiento();
                                   $this->setDataDevolver($frenteMovimiento->armarDetalles($_POST['id']));
                                    break;
                            case "salidas":
                                   $frenteMovimiento = new FrenteMovimiento();
                                   $this->setDataDevolver($frenteMovimiento->armarDetalles($_POST['id']));
                                    break;
                            case "listadoDePendientes":
                                   $frenteMovimientoSeguimiento = new FrenteMovimientoSeguimiento();
                                   $this->setDataDevolver($frenteMovimientoSeguimiento->armarDetalles($_POST['id']));
                                    break;
                            case "pendientesDeConfirmacion":
                                    $frenteTransaccionesPendientes = new FrenteTransaccionesPendientes();
                                    $this->setDataDevolver($frenteTransaccionesPendientes->armarDetalles($_POST['id']));
                                    break;
                            case "transacciones":
                                    if($_POST['autogestion']== 'false'){
                                            $frenteTransacciones = new FrenteTransacciones();
                                            $this->setDataDevolver($frenteTransacciones->armarDetalles($_POST['id']));
                                            break;
                                    }else{
                                            $frenteAutogestion = new FrenteAutogestion();
                                            $this->setDataDevolver($frenteAutogestion->armarDetalles($_POST['id']));
                                            break;
                                    }
                            case "transaccionesAgrupadas":
                                    if($_POST['autogestion']== 'false'){
                                        $frenteTransaccionesAgrupadas = new FrenteTransaccionesAgrupadas();
                                        $this->setDataDevolver($frenteTransaccionesAgrupadas->armarDetallesAgrupados($_POST['id']));
                                        break;
                                    }else{
                                        $frenteAutogestionAgrupada = new FrenteAutogestionAgrupada();
                                        $this->setDataDevolver($frenteAutogestionAgrupada->armarDetalles($_POST['id']));
                                        break;
                                    }
                            case "bloqueador":
                                   $frenteMensajeria = new FrenteMensajeriaAnmat();
                                   $this->setDataDevolver($frenteMensajeria->armarDetalles($_POST['id']));
                                    break;
                            case "auditorStock":
                                   $frenteAuditor = new FrenteAuditor();
                                   $this->setDataDevolver($frenteAuditor->armarDetalles($_POST['id']));
                                    break;
                            case "pedidos":
                                   $frentePedidos = new FrentePedidos();
                                   $this->setDataDevolver($frentePedidos->armarDetalles($_POST['id']));
                                    break;
                            case "hojaDeRuta":
                                   $frenteHdr= new FrenteHdr();
                                   $this->setDataDevolver($frenteHdr->armarDetalles($_POST['id']));
                                    break;
                            case "clientes":
                                   $frenteClientes= new FrenteClientes();
                                   $this->setDataDevolver($frenteClientes->armarDetalles($_POST['id']));
                                    break;
                            case "stockEnCustodia":
                                    $frenteStockCustodiantes= new FrenteStockCustodiantes();
                                    $this->setDataDevolver($frenteStockCustodiantes->armarDetalles($_POST['id']));
                                    break;
                            case "ordenDeTrabajo":
                                    $frenteOrdenesTrabajo= new FrenteOrdenesTrabajo();
                                    $this->setDataDevolver($frenteOrdenesTrabajo->armarDetalles($_POST['id']));
                                    break;
                            case "modelos":
                                   $FrenteModeloAdmin= new FrenteModeloAdmin();
                                   $this->setDataDevolver($FrenteModeloAdmin->armarDetalles($_POST['id']));
                                   break;
                            case "usuarios":
                                   $FrenteUsuarios= new FrenteUsuarios();
                                   $this->setDataDevolver($FrenteUsuarios->armarDetalles($_POST['id']));
                                    break;
                            case "usuariosSecciones":
                            //echo "eee";exit;
                                   $frente = new FrentePermisos();
                                   $this->setDataDevolver($frente->armarDetalles($_POST['id']));
                                    break;
                            case "usuariosBotonera":
                            //echo "eee";exit;
                                   $frente = new FrenteBotones();
                                   $this->setDataDevolver($frente->armarDetalles($_POST['id']));
                                    break;
                            case "usuariosHerramientas":
                            //echo "eee";exit;
                                   $frente = new FrenteHerramientas();
                                   $this->setDataDevolver($frente->armarDetalles($_POST['id']));
                                    break;
                            case "remitos":
                                    $FrenteRemitos= new FrenteRemitos();
                                    $this->setDataDevolver($FrenteRemitos->armarDetalles($_POST['id']));
                                    break;
                            case "facturas":
                                    $FrenteFacturas= new FrenteFacturas();
                                    $this->setDataDevolver($FrenteFacturas->armarDetalles($_POST['id']));
                                    break;
                            case "cobros":
                                    $Frente = new FrenteCobros();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "estadoDeDeuda":
                                    $Frente = new FrenteEstadoDeuda();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "mayor":
                                    $Frente = new FrenteMayor();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "rentabilidad":
                                    $Frente = new FrenteRentabilidad();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "liquidaciones":
                                    $Frente = new FrenteLiquidaciones();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "promociones":
                                    $Frente = new FrentePromociones();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "stockConsolidado":
                                    $Frente = new FrenteStockConsolidado();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "consultaStock":
                                    $Frente = new FrenteStock();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "ingresos":
                                    $Frente = new FrenteIngresos();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                            case "combos":
                                    $Frente = new FrenteCombos();
                                    $this->setDataDevolver($Frente->armarDetalles($_POST['id']));
                                    break;
                    }
                }

            if($_POST['accion'] == 'cancelar'){

                switch($_POST['tipo']){

                    case "transacciones":
                                $frenteTransacciones = new FrenteTransacciones();
                                $frenteTransacciones->noAutogestionar($_POST['id']);
                                break;
                    case "transaccionesAgrupadas":
                                $frenteTransaccionesAgrupadas = new FrenteTransaccionesAgrupadas();
                                $frenteTransaccionesAgrupadas->noAutogestionar($_POST['id']);
                                break;
                    case "entradas":
                                $_POST[tipoMovimientoId] =6;
                                $_POST[tipo] ="anulaciones";
                                $FrenteMovimiento = new FrenteMovimiento();
                                $FrenteMovimiento->generarMovimiento($_POST);
                                break;
                    case "salidas":
                                $_POST[tipoMovimientoId] =6;
                                $_POST[tipo] ="anulaciones";
                                $FrenteMovimiento = new FrenteMovimiento();
                                $FrenteMovimiento->generarMovimiento($_POST);
                                break;
                    case "pedidos":
                                $frentePedidos= new FrentePedidos();
                                $frentePedidos->cancelarPedido($_POST['id']);
                                break;
                    case "ordenDeTrabajo":
                                $frenteOrdenesTrabajo= new FrenteOrdenesTrabajo();
                                $frenteOrdenesTrabajo->cancelarOrdenTrabajo($_POST['id']);
                                break;
                    case "listaDePrecios":
                                $frente= new FrenteListasDePreciosAgrupadas();
                                $frente->cancelar($_POST['id']);
                                break;
                    case "cobros":
                                $frente= new FrenteCobros();
                                $frente->cancelarCobro($_POST['id']);
                                break;
                    case "cuentas":
                                $frente= new FrenteCuentas();
                                $frente->cancelarCuenta($_POST['id']);
                                break;
                    case "relaciones":
                                $frente= new FrenteClientesCuentas();
                                $frente->cancelarRelacion($_POST['id']);
                                break;
                    case "tipoDeProductos":
                                $frente= new FrenteFamiliaProductos();
                                $frente->cancelarFamilia($_POST['id']);
                                break;
                    case "ordenes":
                                $frente= new FrenteOrdenesGuias();
                                $frente->cancelarGuia($_POST['id']);
                                break;
                    case "subfamilias":
                                $frente= new FrenteSubFamiliasProductos();
                                $frente->cancelarSubFamilia($_POST['id']);
                                break;
                    case "liquidaciones":
                                $frente= new FrenteLiquidaciones();
                                $frente->cancelar($_POST['id']);
                                break;
                    case "promociones":
                                $frente= new FrentePromociones();
                                $frente->cancelarPromocion($_POST['id']);
                                break;
                    case "combos":
                                $frente= new FrenteCombos();
                                $frente->cancelar($_POST['id']);
                                break;
                    case "hojaDeRuta":
                                $frente= new FrenteHdr();
                                $frente->cancelar($_POST['id']);
                                break;
                    case "familiasSubfamilias":
                                $frente= new FrenteFamiliasSubfamilias();
                                $frente->cancelar($_POST['id']);
                                break;

                }
            }

            if($_POST['accion']=='bloquearPorMovimiento'){
                    $frenteMensajeria= new FrenteMensajeriaAnmat();
                    $frenteMensajeria->bloquearPorMovimiento($parametros);
            }
            if($_POST['accion']=='bloquearRegistro'){

                    $frenteMensajeria= new FrenteMensajeriaAnmat();
                    $frenteMensajeria->bloquearDetalles($parametros);
            }
            if($_POST['accion']=='renovar'){
                    $FrenteMovimiento= new FrenteMovimiento();
                    $parametros['tipo'] = "renovar";

                    $FrenteMovimiento->generarMovimiento($parametros);
            }
            if($_POST['accion']=='custodia'){
                    $FrenteMovimiento= new FrenteMovimiento();
                    $parametros['tipo'] = "custodia";

                    $FrenteMovimiento->generarMovimiento($parametros);
            }
            if($_POST['accion'] =='enviarTransaccion') {
                    $frente = NEW FrenteIngresos();
                    $frente->liberarRecepcion($_POST['id']);
            }
            if($_POST['accion'] =='lectorCodigo') {
                $FrenteMovimiento = new FrenteMovimiento();
                $FrenteMovimiento->cargarCodigoTrazabilidad($parametros);

            }
            if($_POST['accion']=='prepararPedido'){
                $frentePedidos= new FrentePedidos();
                $this->setDataDevolver($frentePedidos->prepararPedido($_POST['parametros']));		
            }
            if($_POST['accion']=='validarPedido'){
                $frentePedidos= new FrentePedidos();
                $frentePedidos->validarPedido($_POST['parametros']);
            }
            if($_POST['accion'] == 'traerDatos'){
                switch($_POST['tipo']){
                    case "clientes":
                        $frenteClientes = new FrenteClientes();
                        $frenteClientes->buscarDireccion($_POST['id']);
                    break;    
                    case "productos":
                        $frenteProductos= new FrenteProductos();
                        $frenteProductos->buscarCosto($_POST['id']);
                    break;   
		    case "subfamilia":
                        $frenteProductos= new FrenteProductos();
                        $frenteProductos->buscarCostoFamilia($_POST['id']);
                    break; 
                    case "productosReferencia":
                        $frenteProductos= new FrenteProductos();
                        $frenteProductos->buscarReferencia($_POST['id']);
                    break;
                    case "facturas":
                        $frente= new FrenteFacturas();
                        $frente->buscarImporte($_POST['id']);
                    break;
                    default:
                        $frenteProductos= new FrenteProductos();
                        $frenteProductos->buscarCosto($_POST['parametros']);
            } 
	    }
	    if($_POST['accion']=='prepararOrden'){
                $frenteOrdenesTrabajo= new FrenteOrdenesTrabajo();
                $this->setDataDevolver($frenteOrdenesTrabajo->prepararOrdenTrabajo($_POST['parametros']));		
        }
        if($_POST['accion']=='validarOrden'){
            $frenteOrdenesTrabajo= new FrenteOrdenesTrabajo();
            $frenteOrdenesTrabajo->validarOrden($_POST['parametros']);
        }
        if($_POST['accion'] == 'traerPrecioProducto'){
                $frenteProductos= new FrenteProductos();
                $frenteProductos->traerPrecioProducto($_POST['parametros']);
        }
        if($_POST['accion'] == 'buscarPrecioVenta'){
                $frenteProductos= new FrenteProductos();
                $frenteProductos->buscarPrecioVenta($_POST['parametros']);
        }
        if($_POST['accion'] == 'traerDatosMovimiento'){
            $frente= new FrenteMovimiento();
            $frente->traerDetallesMostrador($_POST['id']);
        }
        if($_POST['accion'] == 'traerDatosMovimientoEntradas'){
            $frente= new FrenteMovimiento();
            $frente->traerDetallesMostradorEntradas($_POST['id']);
        }

	}
	public function setModelo($arrModelo)
	{
	
	    $this->_colModelo =  $arrModelo;
	}
	public function getModelo()
	{
	    return $this->_colModelo;
	}
	public function setOrder($arrOrder)
	{
	    $this->_colOrder = $arrOrder;
	}
	public function setColData($objData)
	{
	    $this->_colData['page'] = $objData->getPagina();
	    $this->_colData['rows'] = $objData->getDetalles();				
	    $this->_colData['total'] = $objData->getTotal();
			
	}
	public function getColData()
	{
	    return $this->_colData;
	}
	public function getOrder()
	{
	    return $this->_colOrder;
	}
	public function setDataDevolver($arr){

	    $this->_arrDataDevolver = $arr;

	}	

	public function getDataDevolver(){

	    return $this->_arrDataDevolver;
	}
	//**FIN BUSQUEDAS**//
	//**AUTOCOMPLETAR**//
}
?>
