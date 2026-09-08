<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/libreria/CondicionIva.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE CATEGORIAS
*/

class CondicionIvaExtendido extends CondicionIva
{
    
    public function __construct(){
        $this->_db = new FrenteAlmacenamiento('dmelmac');
    }
    
    public function getDataDB($id){
	
	

	$this->_db->addSelect('*');
	$this->_db->addFrom('datos_condiciones_iva');

	$this->_db->addWhere('condicion_iva_id = \'' . $id .'\'' );

	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	$resultado=  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }


    public function cargarMe($datos){
    
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setCondicionIvaId($resultado['condicion_iva_id']);
	$this->setDescripcion($resultado['descripcion']);

    }
	
    public function salvarMe($arrParametros){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposTabla('seq_datos_condicion_venta_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setCondicionVentaId($id[0]['nextval']);
	$this->setCondicionVentaNombre($arrParametros['condicion_ventas']);
	$this->setObs($arrParametros['observaciones']);
       
        $this->_db->addFrom('datos_condicion_venta');

        $this->_db->addCamposTabla('condicion_venta_id');
        $this->_db->addCamposTabla('condicion_venta_nombre');
        $this->_db->addCamposTabla('observaciones');
	

        $this->_db->addCamposValue($this->getCondicionVentaId());
        $this->_db->addCamposValue('\'' . $this->getCondicionVentaNombre() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
	
        $this->_db->generarInsert();

	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"Error al guardar la condicion_venta"
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 0,
		      "mensaje" =>"CondicionVenta cargada con exito"
		    );
	}
	echo json_encode($arrDevolver);
    }
	
    public function actualizarMe($arrParametros){
	
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('condicion_venta_nombre = \'' . $arrParametros['campos']['condicion_venta_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	if($arrParametros['campos']['condicion_venta_activa'] == 1){
		$this->_db->addCamposUpdate('condicion_venta_activa = true');
	}else{
		$this->_db->addCamposUpdate('condicion_venta_activa = false');
	}
	
	$this->_db->addFrom('datos_condicion_venta');
	$this->_db->addWhere('condicion_venta_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//echo $this->_db->getQry();//asigamos el resulta que es un array a una variable
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
	echo json_encode($arrDevolver);exit;
    }
    public function buscarPorNombre($nombre){

	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addSelect('condicion_venta_id');
	
	$this->_db->addFrom('datos_condicion_venta');
	
	$this->_db->addWhere('condicion_venta_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['condicion_venta_id'];
    }
}
