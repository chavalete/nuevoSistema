<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/Almacen.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class AlmacenExtendido extends Almacen
{    
    private $_sucursalNombre;
    private $_objFuncionesComunes;
    private $_almacenActivoMensaje;
    private $_almacenActivoDesc;
    private $_almacenNoActivoDesc;
    
    public function setAlmacenActivoDesc($almacenActivoDesc){
	$this->_almacenActivoDesc = $almacenActivoDesc;
    }
    public function getAlmacenActivoDesc(){
	return $this->_almacenActivoDesc;
    }
    public function setAlmacenNoActivoDesc($almacenNoActivoDesc){
	$this->_almacenNoActivoDesc = $almacenNoActivoDesc;
    }
    public function getAlmacenNoActivoDesc(){
	return $this->_almacenNoActivoDesc;
    }
    public function setAlmacenActivoMensaje($almacenActivoMensaje){
	$this->_almacenActivoMensaje= $almacenActivoMensaje;
    }
    public function getAlmacenActivoMensaje(){
	return $this->_almacenActivoMensaje;
    }

    public function __construct(){
	$this->_objFuncionesComunes = NEW FuncionesComunes();
	$this->_db = new FrenteAlmacenamiento('dmelmac');
    }

    public function setSucursalNombre($sucursalNombre){
	$this->_sucursalNombre = $sucursalNombre;
    }
    public function getSucursalNombre(){
	return $this->_sucursalNombre;
    }
    public function getDataDB($id){
    
	$this->_db->addSelect('da.almacen_id');
	$this->_db->addSelect('da.almacen_nombre');
	$this->_db->addSelect('ds.sucursal_id');
	$this->_db->addSelect('ds.sucursal_nombre');
	$this->_db->addSelect('da.observaciones');
	$this->_db->addSelect('da.almacen_activo AS almacen_activo');
	$this->_db->addSelect('\'Activo\'  AS almacen_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS almacen_no_activo_desc');
	$this->_db->addSelect('CASE WHEN da.almacen_activo THEN \'Activo\' ELSE \'Inactivo\' END AS almacen_activo_mensaje');
	
	$this->_db->addFrom('datos_almacenes da');
	$this->_db->addFrom('INNER JOIN datos_sucursales ds USING(sucursal_id)');

	$this->_db->addWhere('almacen_id = \'' . $id .'\'' );

	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	$resultado =  $this->_db->ejecutar();
	
	return $resultado[0];

    }
    public function cargarMe($datos){
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	
	$this->setAlmacenId($resultado['almacen_id']);
	$this->setAlmacenNombre($resultado['almacen_nombre']);
	$this->setSucursalId($resultado['sucursal_id']);
	$this->setSucursalNombre($resultado['sucursal_nombre']);
	$this->setAlmacenActivoDesc($resultado['almacen_activo_desc']);
	$this->setAlmacenNoActivoDesc($resultado['almacen_no_activo_desc']);
	$this->setAlmacenActivoMensaje($resultado['almacen_activo_mensaje']);
	$this->setAlmacenActivo($resultado['almacen_activo']);
	$this->setObs($resultado['observaciones']);

    }
    public function salvarMe($arrParametros){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposTabla('seq_datos_almacenes_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setAlmacenId($id[0]['nextval']);
	$this->setAlmacenNombre($arrParametros['almacenes']);
	$this->setObs($arrParametros['observaciones']);
	$this->setSucursalId($arrParametros['sucursales']);
       
        $this->_db->addFrom('datos_almacenes');

        $this->_db->addCamposTabla('almacen_id');
        $this->_db->addCamposTabla('almacen_nombre');
        $this->_db->addCamposTabla('sucursal_id');
        $this->_db->addCamposTabla('observaciones');
	

	
        $this->_db->addCamposValue($this->getAlmacenId());
        $this->_db->addCamposValue('\'' . $this->getAlmacenNombre() . '\'');
        $this->_db->addCamposValue($this->getSucursalId());
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
	
        $this->_db->generarInsert();

	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el almacen'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Almacen cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}
    }
    public function actualizarMe($arrParametros){
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('almacen_nombre = \'' . $arrParametros['campos']['almacen_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	if($arrParametros['campos']['almacen_activo'] == 1){
		$this->_db->addCamposUpdate('almacen_activo = true');
	}else{
		$this->_db->addCamposUpdate('almacen_activo = false');
	}
	$this->_db->addFrom('datos_almacenes');
	$this->_db->addWhere('almacen_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//asigamos el resulta que es un array a una variable
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

    public function buscarPorNombre($nombre){

	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addSelect('almacen_id');
	
	$this->_db->addFrom('datos_almacenes');
	
	$this->_db->addWhere('almacen_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['almacen_id'];
    }
}
