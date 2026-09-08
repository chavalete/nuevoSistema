<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DetalleLiquidacion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DetalleLiquidacionExtendido extends DetalleLiquidacion
{
    private $_objFuncionesComunes;
    private $_db;
    
    public function __construct(){
    
	$this->_db = NEW FrenteAlmacenamiento();
	$this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
	
        $this->_db->addSelect('*');
        
        $this->_db->addFrom('detalle_liquidaciones');

        $this->_db->addWhere('liquidacion_id = \'' . $id .'\'' );

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
        $this->setLiquidacionId($resultado['liquidacion_id']);
        $this->setFacturaId($resultado['factura_id']);
        $this->setImporteDetalle($resultado['importe_detalle']);
	
    }
    function salvarMe($arrParametros){
	
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposValue('\'' . $arrParametros['facturaId'] .'\'');
        
        if($arrParametros['liquidacionId']!= null){
            $this->_db->addCamposTabla('liquidacion_id');
            $this->_db->addCamposValue('\'' . $arrParametros['liquidacionId'] .'\'');
        }
        
        if($arrParametros['importeDetalle']!= null){
            $this->_db->addCamposTabla('importe_detalle');
            $this->_db->addCamposValue('\'' . $arrParametros['importeDetalle'] .'\'');
        }
        
        $this->_db->addFrom('detalle_liquidaciones');
	
        $this->_db->generarInsert();
        
        return  $this->_db->getQry();
    }
    function actualizarMe($arrParametros){

	
	if($arrParametros['campos']['estados']!=false){
	    $this->_db->addCamposUpdate('estado_id = \'' . $arrParametros['campos']['estados'] .'\'');
	}
	
	
	$this->_db->addFrom('datos_compras');
	$this->_db->addWhere('compra_id = \'' . $arrParametros['id'] .'\'');
	    
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
