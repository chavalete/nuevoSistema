<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/Estanteria.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class EstanteriaExtendido extends Estanteria
{    
    private $_almacenNombre;
    private $_objFuncionesComunes;
    private $_sucursalId;
    private $_estanteriaActivoMensaje;
    private $_estanteriaActivoDesc;
    private $_estanteriaNoActivoDesc;
    
    public function setEstanteriaActivoDesc($estanteriaActivoDesc){
	$this->_estanteriaActivoDesc = $estanteriaActivoDesc;
    }
    public function getEstanteriaActivoDesc(){
	return $this->_estanteriaActivoDesc;
    }
    public function setEstanteriaNoActivoDesc($estanteriaNoActivoDesc){
	$this->_estanteriaNoActivoDesc = $estanteriaNoActivoDesc;
    }
    public function getEstanteriaNoActivoDesc(){
	return $this->_estanteriaNoActivoDesc;
    }
    public function setEstanteriaActivoMensaje($estanteriaActivoMensaje){
	$this->_estanteriaActivoMensaje= $estanteriaActivoMensaje;
    }
    public function getEstanteriaActivoMensaje(){
	return $this->_estanteriaActivoMensaje;
    }
    public function setSucursalId($id){
        $this->_sucursalId = $id;
    }
    public function getSucursalId(){
        return $this->_sucursalId;
    }
    public function __construct(){
	$this->_objFuncionesComunes = NEW FuncionesComunes();
	$this->_db = new FrenteAlmacenamiento('dmelmac');
    }
    public function setAlmacenNombre($almacenNombre){
	$this->_almacenNombre = $almacenNombre;
    }
    public function getAlmacenNombre(){
	return $this->_almacenNombre;
    }
    public function getDataDB($id){
	
	$this->_db->addSelect('estanteria_id');
	$this->_db->addSelect('estanteria_nombre');
	$this->_db->addSelect('almacen_id');
	$this->_db->addSelect('datos_estanterias.observaciones');
	$this->_db->addSelect('estanteria_activa AS estanteria_activo');
	$this->_db->addSelect('\'Activo\'  AS estanteria_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS estanteria_no_activo_desc');
	$this->_db->addSelect('CASE WHEN estanteria_activa THEN \'Activo\' ELSE \'Inactivo\' END AS estanteria_activo_mensaje');
	$this->_db->addSelect('sucursal_id');
        
	$this->_db->addFrom('datos_estanterias');
	$this->_db->addFrom('INNER JOIN datos_almacenes USING(almacen_id)');

	$this->_db->addWhere('estanteria_id = \'' . $id .'\'' );

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
	//var_dump($resultado);
	$this->setEstanteriaId($resultado['estanteria_id']);
	$this->setEstanteriaNombre($resultado['estanteria_nombre']);
	$this->setAlmacenId($resultado['almacen_id']);
	$this->setObs($resultado['observaciones']);
	$this->setAlmacenNombre($resultado['almacen_nombre']);
	$this->setEstanteriaActivoDesc($resultado['estanteria_activo_desc']);
	$this->setEstanteriaNoActivoDesc($resultado['estanteria_no_activo_desc']);
	$this->setEstanteriaActivoMensaje($resultado['estanteria_activo_mensaje']);
	$this->setEstanteriaActiva($resultado['estanteria_activo']);
        $this->setSucursalId($resultado['sucursal_id']);
    }
    public function salvarMe($arrParametros){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposTabla('seq_datos_estanterias_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setEstanteriaId($id[0]['nextval']);
	$this->setEstanteriaNombre($arrParametros['estanterias']);
	$this->setObs($arrParametros['observaciones']);
	$this->setAlmacenId($arrParametros['almacenes']);
       
        $this->_db->addFrom('datos_estanterias');

        $this->_db->addCamposTabla('estanteria_id');
        $this->_db->addCamposTabla('estanteria_nombre');
        $this->_db->addCamposTabla('almacen_id');
        $this->_db->addCamposTabla('observaciones');
	

        $this->_db->addCamposValue($this->getEstanteriaId());
        $this->_db->addCamposValue('\'' . $this->getEstanteriaNombre() . '\'');
        $this->_db->addCamposValue($this->getAlmacenId());
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
	
        $this->_db->generarInsert();

	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar la estanteria'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Estanteria cargada con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    public function actualizarMe($arrParametros){
    
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('estanteria_nombre = \'' . $arrParametros['campos']['estanteria_nombre'] .'\'');
	if($arrParametros['campos']['estanteria_activa'] == 1){
		$this->_db->addCamposUpdate('estanteria_activa = true');
	}else{
		$this->_db->addCamposUpdate('estanteria_activa = false');
	}
	
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	$this->_db->addFrom('datos_estanterias');
	$this->_db->addWhere('estanteria_id = \'' . $arrParametros['id'] .'\'');
	    
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
}