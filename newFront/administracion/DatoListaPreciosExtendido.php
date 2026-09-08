<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/ListaPrecios.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DatoListaPreciosExtendido extends ListaPrecios
{
    private $_objFuncionesComunes;
    private $_db;
    private $_colObjDetalleLista;
    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
    }

    public function setColObjDetalleLista($objDetalleLista){   
        $this->_colObjDetalleLista[$objDetalleLista->getProductoId()] = $objDetalleLista;
    }

    public function getColObjDetalleLista(){
        return $this->_colObjDetalleLista;
    }

    public function setListaMayoristaDesc($desc){
        $this->_listaMayoristaDesc = $desc;
    }
    public function getListaMayoristaDesc(){
        return $this->_listaMayoristaDesc;
    }
    public function setListaMayoristaNoDesc($noMayorista){
        $this->_listaNoMayoristaDesc = $noMayorista;
    }
    public function getListaMayoristaNoDesc(){
        return $this->_listaNoMayoristaDesc;
    }
    public function setListaMayoristaMensaje($mensaje){
        $this->_listaMayoristaMensaje= $mensaje;
    }
    public function getListaMayoristaMensaje(){
        return $this->_listaMayoristaMensaje;
    }
    public function setListaMayorista($listaMayorista){
        $this->_listaMayorista = $listaMayorista;
    }
    public function getListaMayorista(){
        return $this->_listaMayorista;
    }
    
    public function getDataDB($id){
	
	$this->_db->addSelect('lista_id');
	$this->_db->addSelect('lista_fecha_hora');
	$this->_db->addSelect('lista_nombre');
	$this->_db->addSelect('lista_padre_id');
	$this->_db->addSelect('lista_usuario_id');
	$this->_db->addSelect('lista_activa');
	$this->_db->addSelect('lista_cancelada');
	$this->_db->addSelect('lista_cancelada_fecha_hora');
	$this->_db->addSelect('lista_cancelada_usuario_id');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('lista_mayorista');
	$this->_db->addSelect('\'Si\'  AS lista_mayorista_desc');
	$this->_db->addSelect('\'No\'  AS lista_no_mayorista_desc');
	$this->_db->addSelect('CASE WHEN lista_mayorista THEN \'Si\' ELSE \'No\' END AS lista_mayorista_mensaje');
	
	
	$this->_db->addFrom('datos_listas_precios');

	$this->_db->addWhere('lista_id = \'' . $id .'\'' );

	$this->_db->generarSelect();
	
	//echo $this->_db->getQry();

	$resultado =  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }
    function cargarMe($datos) {
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	$this->setListaId($resultado['lista_id']);
	$this->setListaFechaHora($resultado['lista_fecha_hora']);
	$this->setListaNombre($resultado['lista_nombre']);
	$this->setListaPadreId($resultado['lista_padre_id']);
	$this->setListaActiva($resultado['lista_activa']);
	$this->setListaCancelada($resultado['lista_cancelada']);
	$this->setListaCanceladaFechaHora($resultado['lista_cancelada_fecha_hora']);
	$this->setListaCanceladaUsuarioId($resultado['lista_cancelada_usuario_id']);
	$this->setObs($resultado['observaciones']);
	$this->setListaMayorista($resultado['lista_mayorista']);
	$this->setListaMayoristaDesc($resultado['lista_mayorista_desc']);
	$this->setListaMayoristaNoDesc($resultado['lista_no_mayorista_desc']);
	$this->setListaMayoristaMensaje($resultado['lista_mayorista_mensaje']);
	

    }
    function salvarMe($arrParametros){
	      
        $this->_db->addCamposTabla('seq_listas_precios_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();
	$this->setListaId($id[0]['nextval']);
        

        $this->_db->addCamposTabla('lista_id');
        $this->_db->addCamposValue($this->getlistaId());
        
        if($arrParametros['lista']!= null){
            $this->_db->addCamposTabla('lista_nombre');
            $this->_db->addCamposValue('\'' . $arrParametros['lista'] .'\'');
        }
        if($arrParametros['listaPadreId']!= null){
            $this->_db->addCamposTabla('lista_padre_id');
            $this->_db->addCamposValue('\'' . $arrParametros['listaPadreId'] .'\'');
        }
        
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        $this->_db->addCamposTabla('lista_usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('datos_listas_precios');
	
        $this->_db->generarInsert();
        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();;
        
    }
    function actualizarMe($arrParametros){
	
	if($arrParametros['campos']['facturaNro']!=null){
	    $this->_db->addCamposUpdate('factura_nro = \'' . $arrParametros['campos']['facturaNro'] .'\'');
	}
	if($arrParametros['campos']['facturaFecha']!=null){
	    $ano = substr($arrParametros['campos']['facturaFecha'],6,4);
	    
	    if($ano == '2016' || $ano =='2015'){
		$mes = substr($arrParametros['campos']['facturaFecha'],3,2);
		$dia = substr($arrParametros['campos']['facturaFecha'],0,2);
		$facturaFecha = $ano."/".$mes ."/".$dia;
		$this->_db->addCamposUpdate('factura_fecha = \'' . $facturaFecha .'\'');
	    }
	}
	$this->_db->addFrom('datos_compras_facturas');
	$this->_db->addWhere('factura_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//echo $this->_db->getQry();
	//asigamos el resulta que es un array a una variable
	$resultado = $this->_db->ejecutar();
	
	
	$this->_db->addCamposUpdate('factura_nro  = dcf.factura_nro,fecha = dcf.factura_fecha');
	$this->_db->addFrom('cta_cte_stock');
	$this->_db->addTablaFrom('datos_compras_facturas dcf');
	$this->_db->addWhere('dcf.factura_id = \'' . $arrParametros['id'] .'\'');
	$this->_db->addWhere('cta_cte_stock.factura_id = \'' . $arrParametros['id'] .'\'');
	$this->_db->addWhere('cta_cte_stock.prove_id IS NOT NULL');
	
	$this->_db->generarUpdateFrom();   
	//echo $this->_db->getQry();exit;
	
	$resultado = $this->_db->ejecutar();
	
	if(count($resultado)> 0){
		$arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 0,
		    "mensaje" =>"Actualizado"
		    );
	}else{

		$arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"Error en la actualizacion" 
		    );
	}
	echo json_encode($arrDevolver);

    }
}
