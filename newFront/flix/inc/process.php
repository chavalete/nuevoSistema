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
    
}
//echo "no entra";
?>
