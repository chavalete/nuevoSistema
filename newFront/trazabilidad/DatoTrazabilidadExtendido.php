<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DatoTrazabilidad.php';
require_once 'TrazabilidadCodigoExtendido.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of datoTrazabilidadExtendido
 *
 * @author root
 */
 
class DatoTrazabilidadExtendido extends DatoTrazabilidad
{
	/**
	 * 
	 * @access private
	 */
    private $_db;
	
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    private function getDataDB($id){
        
        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_trazabilidad');
        $this->_db->addWhere(' trazabilidad_id = ' . $id );
       
        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
        
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"El codigo q intenta cargar no existe"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        return $resultado[0];
    }
    
    public function cargarme($datos){
        
        if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
        //var_dump($resultado);exit;	
        //seters de los objetos qeu faltan (suc, alm, est, prod, lote)
        $this->setTrazabilidadId($resultado['trazabilidad_id']);
        $this->setTrazabilidadFechaAlta($resultado['trazabilidad_fecha_alta']);
        $this->setTrazabilidadCodigo($resultado['trazabilidad_codigo']);
        $this->setEnStock($resultado['en_stock']);
        $this->setEsAgrupador($resultado['es_agrupador']);
        

        $objFrenteStock = new FrenteStock();
        $this->setObjSucursal($objFrenteStock->cargarObjSucursal($resultado['sucursal_id']));
        $this->setObjAlmacen($objFrenteStock->cargarObjAlmacen($resultado['almacen_id']));
        $this->setObjEstanteria($objFrenteStock->cargarObjEstanteria($resultado['estanteria_id']));
        $this->setObjProducto($objFrenteStock->cargarObjProducto($resultado['producto_id']));
        $this->setObjLote($objFrenteStock->cargarObjLote($resultado['lote_id']));
    }
    
    public function salvarme($arrParametros){
            
        //control de existencia e insert con los parametros necesarios
        //Si el array no contiene codigo los genero
        if($arrParametros['codigos'] == null){
            $objCodigo = new TrazabilidadCodigoExtendido();
            $qryTrazaCodigo = $objCodigo->generarCodigo();
            $this->setTrazabilidadCodigo($objCodigo->getTrazabilidadCodigo());
            if(count($objCodigo->getArrError()) >  0){
                $this->setArrError($objCodigo->getArrError());
                return;
            }
            //echo "generada en  dMELMAC : " . $this->getTrazabilidadCodigo() . "<br>";
        }else{
            $this->cargarPorCodigo($arrParametros['codigo']);
            //control de existencia para los codigos de origen
            if($this->getTrazabilidadId() > 0 ){
                $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'El Codigo: ' . $arrParametros['codigo'] . ' ya fue dado de alta con anterididad, verifique los movimientos del mismo.'
		    );
		echo json_encode($arrDevolver);exit;
            }
            $this->setTrazabilidadCodigo($arrParametros['codigo']);
            //echo "generado en Origen : " . $this->getTrazabilidadCodigo() . "<br>";
        }
        //genero y seteo el trazabilidad_id
        $this->_db->addSelect('seq_datos_trazabilidad_traza_id');
        $this->_db->generarProximo();
        $resultado = $this->_db->ejecutar();
        $this->setTrazabilidadId($resultado[0]['nextval']);
        
        //inserto el registro en datos trazabilidad 
        $this->_db->addFrom('datos_trazabilidad');
        $this->_db->addCamposTabla('trazabilidad_id, trazabilidad_codigo, sucursal_id, almacen_id, estanteria_id, producto_id , lote_id');
        
        $this->_db->addCamposValue($this->getTrazabilidadId() . ',\'' . $this->getTrazabilidadCodigo() . '\',' . $arrParametros['sucursal_id'] . ',' . $arrParametros['almacen_id'] . ',' . $arrParametros['estanteria_id'] . ',' . $arrParametros['producto_id'] . ',' . $arrParametros['lote_id'] );
        
        if($arrParametros['es_agrupador']){
            $this->_db->addCamposTabla('es_agrupador');
            $this->_db->addCamposValue('true');
        }
        
        $this->_db->generarInsert();
	//echo $this->_db->getQry();
        return $qryTrazaCodigo . $this->_db->getQry();
        
    }
    
    public function actualizarme(){
        //actualizacion de un campo del objeto previamente cargado
    }
    
    public function cargarPorCodigo($codigo){
        
        //select por codigo contra datosTrazabilidad para traer el id , despues se llama al cargarme(id)
        $this->_db->addSelect('trazabilidad_id');
        $this->_db->addFrom('datos_trazabilidad');
        $this->_db->addWhere(' trazabilidad_codigo = \'' . $codigo . '\'');
        $this->_db->addWhere(' cancelado = FALSE');
        $this->_db->addOrderBy('trazabilidad_id DESC');
	$this->_db->generarSelect();
        //echo $this->_db->getQry();
        
        $resultado = $this->_db->ejecutar();
        
        //verificamos que el movimiento exista en la db
        if(count($resultado) > 0){
            $this->cargarme($resultado[0]['trazabilidad_id']);
        }
    }
}


