<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Proveedor.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class ProveedorExtendido extends Proveedor
{
    private $_categoriaNombre;
    private $_objFuncionesComunes;
    private $_proveActivoMensaje;
    private $_proveActivoDesc;
    private $_proveNoActivoDesc;
    private $_proveGln;
    private $_proveContacto;
    
    public function setProveContacto($contacto){
        $this->_proveContacto = $contacto;
    }
    public function getProveContacto(){
        return $this->_proveContacto;
    }
    
    public function setProveActivoDesc($proveActivoDesc){
	$this->_proveActivoDesc = $proveActivoDesc;
    }
    public function getProveActivoDesc(){
	return $this->_proveActivoDesc;
    }
    public function setProveNoActivoDesc($proveNoActivoDesc){
	$this->_proveNoActivoDesc = $proveNoActivoDesc;
    }
    public function getProveNoActivoDesc(){
	return $this->_proveNoActivoDesc;
    }
    public function setProveActivoMensaje($proveActivoMensaje){
	$this->_proveActivoMensaje= $proveActivoMensaje;
    }
    public function getProveActivoMensaje(){
	return $this->_proveActivoMensaje;
    }
    public function __construct(){
	$this->_objFuncionesComunes = NEW FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function setCategoriaNombre($categoriaNombre) {
	$this->_categoriaNombre = $categoriaNombre;
    }
    public function getCategoriaNombre(){
	return $this->_categoriaNombre;
    }
    public function setProveGln($gln){
	$this->_proveGln = $gln;
    }
    public function getProveGln(){
	return $this->_proveGln;
    }

    public function getDataDB($id){
	
	$this->_db->addSelect('prove_id');
	$this->_db->addSelect('prove_nombre');
	$this->_db->addSelect('prove_cuit');
	$this->_db->addSelect('prove_gln');
	$this->_db->addSelect('categoria_id');
	$this->_db->addSelect('prove_direccion');
	$this->_db->addSelect('prove_contacto');
	$this->_db->addSelect('datos_proveedores.observaciones');
	$this->_db->addSelect('prove_activo AS prove_activo');
	$this->_db->addSelect('\'Activo\'  AS prove_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS prove_no_activo_desc');
	$this->_db->addSelect('CASE WHEN prove_activo THEN \'Activo\' ELSE \'Inactivo\' END AS prove_activo_mensaje');
	$this->_db->addSelect('datos_proveedores.prove_telefono');
	$this->_db->addFrom('datos_proveedores');

	$this->_db->addWhere('prove_id = \'' . $id .'\'' );

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

	$this->setProveId($resultado['prove_id']);
	$this->setProveNombre($resultado['prove_nombre']);
	$this->setCategoriaId($resultado['categoria_id']);
	$this->setProveActivoDesc($resultado['prove_activo_desc']);
	$this->setProveNoActivoDesc($resultado['prove_no_activo_desc']);
	$this->setProveActivoMensaje($resultado['prove_activo_mensaje']);
	$this->setProveActivo($resultado['prove_activo']);
	$this->setObs($resultado['observaciones']);
	$this->setProveDireccion($resultado['prove_direccion']);
	$this->setProveTelefono($resultado['prove_telefono']);
	$this->setProveCuit($resultado['prove_cuit']);
	$this->setProveGln($resultado['prove_gln']);
	$this->setProveContacto($resultado['prove_contacto']);

    }
    function salvarMe($arrParametros){
	
	$this->_db->addCamposTabla('seq_datos_proveedor_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setProveId($id[0]['nextval']);
	$this->setProveNombre($arrParametros['proveedores']);
	$this->setProveDireccion($arrParametros['direccion']);
	$this->setObs($arrParametros['observaciones']);
	$this->setCategoriaId($arrParametros['categoria']);
	$this->setProveTelefono($arrParametros['proveTelefono']);
	$this->setProveCuit($arrParametros['proveCuit']);
	$this->setProveGln($arrParametros['gln']);
	$this->setProveContacto($arrParametros['proveContacto']);
       
        $this->_db->addFrom('datos_proveedores');

        $this->_db->addCamposTabla('prove_id');
        $this->_db->addCamposValue($this->getProveId());
        
        $this->_db->addCamposTabla('prove_nombre');
        $this->_db->addCamposValue('\'' . $this->getProveNombre() . '\'');
        
        
        if($this->getObs()!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        }
        if($this->getProveDireccion()!= null){
            $this->_db->addCamposTabla('prove_direccion');
            $this->_db->addCamposValue('\'' . $this->getProveDireccion() .'\'');
        }
        if($this->getProveTelefono()!= null){
            $this->_db->addCamposTabla('prove_telefono');
            $this->_db->addCamposValue('\'' . $this->getProveTelefono() .'\'');
        }
        if($this->getProveCuit()!= null){
            $this->_db->addCamposTabla('prove_cuit');
            $this->_db->addCamposValue('\'' . $this->getProveCuit() .'\'');
        }
        if($this->getProveGln()!= null){
            $this->_db->addCamposTabla('prove_gln');
            $this->_db->addCamposValue('\'' . $this->getProveGln() .'\'');
        }
        if($this->getProveContacto()!= null){
            $this->_db->addCamposTabla('prove_contacto');
            $this->_db->addCamposValue('\'' . $this->getProveContacto() .'\'');
        }
        
	$this->_db->addCamposTabla('prove_usuario_alta');
	$this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->generarInsert();
        //echo $this->_db->getQry();
	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el proveedor'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Proveedor Cargado'
		    );
		echo json_encode($arrDevolver);exit;
	}
    }
    function actualizarMe($arrParametros){

	$this->_db->addCamposUpdate('prove_nombre = \'' . $arrParametros['campos']['prove_nombre'] .'\'');
	if(is_numeric($arrParametros['campos']['categoria_id'])){
	    $this->_db->addCamposUpdate('categoria_id = \'' . $arrParametros['campos']['categoria_id'] .'\'');
	}
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');
	$this->_db->addCamposUpdate('prove_direccion = \'' . $arrParametros['campos']['prove_direccion'] .'\'');
	$this->_db->addCamposUpdate('prove_cuit = \'' . $arrParametros['campos']['prove_cuit'] .'\'');
	$this->_db->addCamposUpdate('prove_telefono = \'' . $arrParametros['campos']['prove_telefono'] .'\'');
	if(is_numeric($arrParametros['campos']['prove_gln'])){
            $this->_db->addCamposUpdate('prove_gln= \'' . $arrParametros['campos']['prove_gln'] .'\'');
        }
            if($arrParametros['campos']['prove_activo'] == 1){
		$this->_db->addCamposUpdate('prove_activo = true');
	}else{
		$this->_db->addCamposUpdate('prove_activo = false');
	}
	$this->_db->addFrom('datos_proveedores');
	$this->_db->addWhere('prove_id = \'' . $arrParametros['id'] .'\'');
	    
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

	$this->_db->addSelect('prove_id');
	
	$this->_db->addFrom('datos_proveedores');
	
	$this->_db->addWhere('prove_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['prove_id'];
    }
}
