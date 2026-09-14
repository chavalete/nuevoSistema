<?php
//echo "llego";exit;
/*
print_r($_REQUEST);
die();
exit;
 */
session_start();
if($_REQUEST['accion']=='imprimir'){
//var_dump($_REQUEST);exit;
	switch($_REQUEST['tipo']){
	case 'ingresos':
		$url= "http://45.33.26.30/limonReparto/generador_pdf/manejadorFacturaLimon.php?crIdMovimiento=".
	$_REQUEST['id'];

		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
	case 'estadoDeDeuda':
            $url= "http://45.33.26.30/newFront/exportador/exportadorEstadoCuenta.php?idCliente=".$_REQUEST['id'];
            //echo $url;exit;
            $arrDevolver = array(
                                    "soyError" => false,
                                    "nivel" => 1,
                                    "mensaje" =>"",
                                    "redirect"=>"$url"
                                );
            break;
        
    case 'cobros':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorOrdenCobro.php?crIdCobro=".$_REQUEST['id'];
		//echo $url;exit;
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
    case 'totales':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorReporteTotales.php?crIdCobro=".$_REQUEST['id'];
		//echo $url;exit;
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
    case 'ordenes':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorOrdenGuia.php?crIdGuia=".$_REQUEST['id'];
		//echo $url;exit;
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
    
	case 'entradas':		
		$url= "http://45.33.26.30/newFront/generador_pdf/impresionEtiquetaEstandar.php?crIdMovimiento=".
$_REQUEST['id'];

		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
	case 'listadoDePendientes':
                $url= "http://45.33.26.30/newFront/generador_pdf/generador_remito_sh.php?crIdMovimiento=".
$_REQUEST['id'];

                $arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
                break;

	case 'busquedaPorCodigo':

		$url= "http://45.33.26.30/newFront/generador_pdf/impresionEtiqueta.php?crIdTrazabilidad=".
$_REQUEST['id'];
		
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
        case 'salidas':
		$params = [
            'crIdMovimiento' => $_REQUEST['id'],
            'crIdSucursal'   => $_SESSION['sucursalId']
        ];
        $url = "http://45.33.26.30/newFront/generador_pdf/manejadorFacturaLimon.php?" . http_build_query($params);

		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
		case 'facturas':
		$url= "http://45.33.26.30/newFront/generador_pdf/manejadorFacturaLimon.php?crIdFactura=".
$_REQUEST['id'];

		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
        case 'pedidos':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorHojaPedido.php?crPedidoId=".
$_REQUEST['id'];
		
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
	case 'hojaDeRuta':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorDetalleHojaRuta.php?crHojaId=".
$_REQUEST['id'];
		
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
    case 'liquidaciones':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorLiquidacion.php?crIdLiquidacion=".
$_REQUEST['id'];

		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
        case 'pedidos':
	default:
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorDetalleEntrada.php?crIdMovimiento=".
$_REQUEST['parametros']['id'];
		//echo $url;exit;
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
    }
    
    switch($_REQUEST['parametros']['tipo']){
	case 'hojaDeRuta':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorDetalleHojaRuta.php?crHojaId=".
$_REQUEST['parametros']['id'];
		
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
	case 'pedidos':
		$url= "http://45.33.26.30/newFront/generador_pdf/generadorDetalleEntrada.php?crIdMovimiento=".
$_REQUEST['parametros']['id'];
		//echo $url;exit;
		$arrDevolver = array(
                                  "soyError" => false,
                                  "nivel" => 1,
                                  "mensaje" =>"",
                                  "redirect"=>"$url"
                              );
		break;
	}    
	//echo $url;exit;
	echo json_encode($arrDevolver);exit;
}

/*
print_r($_REQUEST):
die();
*/
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/modelo/frenteDeFrentes.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/usuarios/FrenteUsuarios.php';

session_start();

//var_dump($_POST);exit;

switch($_POST['accion']){
//echo "entra";
    case 'upload':
    
	    $imp = new ImportadorRemesa($_FILES['archivo']['tmp_name']);
	    $imp->cargarRemesa();
	    if(count($imp->getArrError()) > 0 ){
		    echo 1;
		    exit;
	    }
	    $imp->salvarRemesa();
	    if(count($imp->getArrError()) > 0 ){
		    echo 1;
	    }else{
		    echo 0;
	    }
	    break;

    case 'login':
    
	    $frenteUsuarios = NEW FrenteUsuarios();
	    $frenteUsuarios->validarLogin($_POST['parametros']);
        //var_dump($_POST['parametros']);
	    if($frenteUsuarios->getColUsuarios() > 0){

		    foreach($frenteUsuarios->getColUsuarios() AS $usuario){
		    
			    $_SESSION['usuarioValidado']='t';
			    $_SESSION['usuarioNombre']= $usuario->getNombreCompleto();
			    $_SESSION['usuarioId'] = $usuario->getUsuarioId();
			    $_SESSION['usuarioAdmin'] = $usuario->getEsPrivilegiado();
                $_SESSION['sucursalId'] = $_POST['parametros']['sucursalId'];
			    $_SESSION['sistema'] = "limonMoreno";
                unset($_SESSION['pestana_activa']);
                switch($_SESSION['sucursalId']){
                    case 2: $_SESSION['sistemaNombre']="MORENO";
                    break;
                    case 3: $_SESSION['sistemaNombre']="ROSAS";
                    break;
                    case 4: $_SESSION['sistemaNombre']="LIMONETA";
                    break;
                }
                    $_SESSION['paginaInicio'] = $usuario->getSubseccionInicio(); // pagina de inico del usuario
                    //secciones
                    $colObjSeccion = $frenteUsuarios->getColSecciones();
                    $ObjSeccion = $colObjSeccion[$usuario->getUsuarioId()];
                    $_SESSION['secciones'] = $ObjSeccion->getArrSecciones();
                    //subsecciones
                    $colObjSubseccion = $frenteUsuarios->getColSubsecciones();
                    $ObjSubseccion = $colObjSubseccion[$usuario->getUsuarioId()];
                    $_SESSION['subsecciones'] = $ObjSubseccion->getArrSubsecciones();
                    //botonera
                    $colObjBotonera = $frenteUsuarios->getColBotonera();
                    $ObjBotonera= $colObjBotonera[$usuario->getUsuarioId()];
                    $_SESSION['botonera'] = $ObjBotonera->getArrBotonera();
            
                    $frenteUsuarios->registrarIngreso($usuario->getUsuarioId());
                    
			    echo 1;
			    
		    }
	    }else{
		    echo 0;
		    exit;
	    }
	    break;
    case 'hacerBusqueda':
	    $frente = NEW frenteDeFrentes($_POST['parametros']);
	    $data = $frente->getDataDevolver();
	    break;
    case 'armarAutoCompletar':
	    NEW frenteDeFrentes();
	    break;
    case 'cancelar':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'alertar':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'actualizarRegistro':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'actualizarProducto':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'hacerAlta':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'lectorCodigo':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'bloquearRegistro':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'getDetalles':
	    $frente = NEW frenteDeFrentes($_POST['parametros']);
	    $data = $frente->getDataDevolver();
	    break;
    case 'enviarTransaccion':
	    $frente = NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'prepararPedido':	
	    $frente = NEW frenteDeFrentes($_POST['parametros']);
            //var_dump($_POST['parametros']);exit;
	    $data = $frente->getDataDevolver();
	    break;
    case 'validarPedido':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'prepararOrden':	
	    $frente = NEW frenteDeFrentes($_POST['parametros']);
	    //var_dump($_POST['parametros']);exit;
	    $data = $frente->getDataDevolver();
	    break;
    case 'validarOrden':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'traerDatos':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'renovar':
            NEW frenteDeFrentes($_POST['parametros']);
            break;
    case 'custodia':
            NEW frenteDeFrentes($_POST['parametros']);
            break;
    case 'traerPrecioProducto':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'traerPrecioProducto':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'traerDatosMovimiento':
            NEW frenteDeFrentes($_POST['parametros']);
            break;
    case 'traerDatosMovimientoEntradas':
            NEW frenteDeFrentes($_POST['parametros']);
            break;
    case 'buscarPrecioVenta':
	    NEW frenteDeFrentes($_POST['parametros']);
	    break;
    case 'obtenerDatosPanelPrincipal':
        // ESQUELETO temporal para el dashboard "Tablero > Principal" (Claude).
        // Devuelve datos de prueba con la forma exacta que espera
        // js/panelPrincipal.js -- reemplazar el cuerpo por las consultas
        // reales (ventas, rentabilidad, cuentas por cobrar/pagar, stock,
        // clientes/proveedores con mayor deuda) usando
        // $_POST['parametros']['fechaDesde'] y ['fechaHasta']. No pasa por
        // frenteDeFrentes a proposito: es un endpoint aislado, como
        // avisoPrecios_check.php.
        header('Content-Type: application/json');
        $fechaDesde = $_POST['parametros']['fechaDesde'] ?? null;
        $fechaHasta = $_POST['parametros']['fechaHasta'] ?? null;
        echo json_encode(array(
            'soyError' => false,
            'kpis' => array(
                'ventas' => 5840000,
                'gananciaNeta' => 1890000,
                'margenBruto' => 42.3,
                'margenNeto' => 28.5,
                'cuentasPorCobrar' => array('total' => 2360000, 'vencida' => 826000, 'proximaAVencer' => 1062000, 'vigente' => 472000),
                'cuentasPorPagar'  => array('total' => 4180000, 'vencida' => 752000, 'proximaAVencer' => 2383000, 'vigente' => 1045000)
            ),
            'serieVentasGanancia' => array(
                'meses'    => array('Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep'),
                'ventas'   => array(3120000,3380000,3610000,3890000,4140000,4400000,4210000,4560000,4890000),
                'ganancia' => array(890000,970000,1050000,1130000,1210000,1290000,1200000,1340000,1480000)
            ),
            'estadoStock' => array('ok' => 64, 'warn' => 27, 'bad' => 9),
            'clientesConMayorDeuda' => array(
                array('nombre' => 'Farmacia San Martín', 'total' => 2140000, 'vencida' => 1180000, 'estado' => 'bad'),
                array('nombre' => 'Distribuidora Sur Dental', 'total' => 1860000, 'vencida' => 640000, 'estado' => 'warn'),
                array('nombre' => 'Óptica y Ortodoncia Belgrano', 'total' => 1520000, 'vencida' => 0, 'estado' => 'ok')
            ),
            'proveedoresConMayorDeuda' => array(
                array('nombre' => 'Laboratorios Andina S.A.', 'total' => 3120000, 'vencida' => 890000, 'estado' => 'bad'),
                array('nombre' => 'Insumos Dentales del Plata', 'total' => 2340000, 'vencida' => 0, 'estado' => 'ok'),
                array('nombre' => 'Quimix Argentina', 'total' => 1780000, 'vencida' => 520000, 'estado' => 'warn')
            ),
            '_debug_rango_recibido' => array('fechaDesde' => $fechaDesde, 'fechaHasta' => $fechaHasta)
        ));
        exit;

}
//echo "no entra";
?>
