<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/libreria/Categoria.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE CATEGORIAS
*/

class CategoriaExtendido extends Categoria
{
    private $_categoriaActivoMensaje;
    private $_categoriaActivoDesc;
    private $_categoriaNoActivoDesc;
    
    public function __construct(){
    	$this->_db = new FrenteAlmacenamiento('dmelmac');
    }
    public function setCategoriaActivoDesc($categoriaActivoDesc){
	$this->_categoriaActivoDesc = $categoriaActivoDesc;
    }
    public function getCategoriaActivoDesc(){
	return $this->_categoriaActivoDesc;
    }
    public function setCategoriaNoActivoDesc($categoriaNoActivoDesc){
	$this->_categoriaNoActivoDesc = $categoriaNoActivoDesc;
    }
    public function getCategoriaNoActivoDesc(){
	return $this->_categoriaNoActivoDesc;
    }
    public function setCategoriaActivoMensaje($categoriaActivoMensaje){
	$this->_categoriaActivoMensaje= $categoriaActivoMensaje;
    }
    public function getCategoriaActivoMensaje(){
	return $this->_categoriaActivoMensaje;
    }
    public function getDataDB($id){
	
	$this->_db->addSelect('categoria_id');
	$this->_db->addSelect('categoria_nombre');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('categoria_activa AS categoria_activo');
	$this->_db->addSelect('\'Activo\'  AS categoria_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS categoria_no_activo_desc');
	$this->_db->addSelect('CASE WHEN categoria_activa THEN \'Activo\' ELSE \'Inactivo\' END AS categoria_activo_mensaje');
	$this->_db->addFrom('datos_categorias');

	$this->_db->addWhere('categoria_id = \'' . $id .'\'' );

	$this->_db->generarSelect();

	$resultado =  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }


    public function cargarMe($datos){
    
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setCategoriaId($resultado['categoria_id']);
	$this->setCategoriaNombre($resultado['categoria_nombre']);
	$this->setObs($resultado['observaciones']);
	$this->setCategoriaActivoDesc($resultado['categoria_activo_desc']);
	$this->setCategoriaNoActivoDesc($resultado['categoria_no_activo_desc']);
	$this->setCategoriaActivoMensaje($resultado['categoria_activo_mensaje']);
	$this->setCategoriaActiva($resultado['categoria_activo']);
    }
	
    public function salvarMe($arrParametros){


	$this->_db->addCamposTabla('seq_datos_categorias_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setCategoriaId($id[0]['nextval']);
	$this->setCategoriaNombre($arrParametros['categorias']);
	$this->setObs($arrParametros['observaciones']);
       
        $this->_db->addFrom('datos_categorias');

        $this->_db->addCamposTabla('categoria_id');
        $this->_db->addCamposTabla('categoria_nombre');
        $this->_db->addCamposTabla('observaciones');
	

        $this->_db->addCamposValue($this->getCategoriaId());
        $this->_db->addCamposValue('\'' . $this->getCategoriaNombre() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
	
        $this->_db->generarInsert();

	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"Error al guardar la categoria"
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 0,
		      "mensaje" =>"Categoria cargada con exito"
		    );
	}
	echo json_encode($arrDevolver);
    }
	
    public function actualizarMe($arrParametros){
	

	$this->_db->addCamposUpdate('categoria_nombre = \'' . $arrParametros['campos']['categoria_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	if($arrParametros['campos']['categoria_activa'] == 1){
		$this->_db->addCamposUpdate('categoria_activa = true');
	}else{
		$this->_db->addCamposUpdate('categoria_activa = false');
	}
	
	$this->_db->addFrom('datos_categorias');
	$this->_db->addWhere('categoria_id = \'' . $arrParametros['id'] .'\'');
	    
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

	$this->_db->addSelect('categoria_id');
	
	$this->_db->addFrom('datos_categorias');
	
	$this->_db->addWhere('categoria_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['categoria_id'];
    }
}
