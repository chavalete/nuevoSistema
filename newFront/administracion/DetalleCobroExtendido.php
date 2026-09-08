<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DetalleCobro.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DetalleCobroExtendido extends DetalleCobro
{
    private $_objFuncionesComunes;
    private $_db;
    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
        $this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
	
        $this->_db->addSelect('*');
        
        $this->_db->addFrom('detalles_cobros');
        $this->_db->addFrom('INNNER JOIN datos_formas_pagos USING(forma_pago_id)');
        $this->_db->addFrom('INNNER JOIN datos_bancos USING(banco_id)');

        $this->_db->addWhere('cobro_id = \'' . $id .'\'' );

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
        $this->setCobroId($resultado['cobro_id']);
        $this->setImporteDetalle($resultado['importe_pago']);
        $this->setDetalleCobroId($resultado['detalle_factura_id']);
        $this->setFacturaId($resultado['factura_id']);
        $this->setFormaPagoNombre($resultado['forma_desc']);
        $this->setFormaPagoid($resultado['forma_pago_id']);
        $this->setBancoNombre($resultado['banco_nombre']);
        $this->setBancoId($resultado['banco_id']);
        $this->setComprobanteNro($resultado['comprobante_nro']);
        $this->setFechaCheque($resultado['fecha_cheque']);
        $this->setFacturaPendiente($resultado['factura_faltan_fact']);
        
	
    }
    function salvarMe($arrParametros){
	
        if($arrParametros['cobroId']!=null){
            $this->_db->addCamposTabla('cobro_id');
            $this->_db->addCamposValue('\'' . $arrParametros['cobroId'] .'\'');
        }
        if($arrParametros['formaPagoId']!= null){
            $this->_db->addCamposTabla('forma_pago_id');
            $this->_db->addCamposValue('\'' . $arrParametros['formaPagoId'] .'\'');
        }
        if($arrParametros['comprobanteNro']!= null){
            $this->_db->addCamposTabla('comprobante_nro');
            $this->_db->addCamposValue('\'' . $arrParametros['comprobanteNro'] .'\'');
        }
        if($arrParametros['bancoId']!= 'undefined' && $arrParametros['bancoId']!=null){
            $this->_db->addCamposTabla('banco_id');
            $this->_db->addCamposValue('\'' . $arrParametros['bancoId'] .'\'');
        }
        if($arrParametros['fechaCheque']!= null && $arrParametros['fechaCheque']!= '//'){
            $this->_db->addCamposTabla('fecha_cheque');
            $this->_db->addCamposValue('\'' . $arrParametros['fechaCheque'] .'\'');
        }
        if($arrParametros['facturaPendiente']!= null){
            $this->_db->addCamposTabla('factura_faltan_fact');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaPendiente'] .'\'');
        }
        if($arrParametros['importeDetalle']!= null){
            $this->_db->addCamposTabla('importe_pago');
            $this->_db->addCamposValue('\'' . $arrParametros['importeDetalle'] .'\'');
        }
        
        $this->_db->addFrom('detalle_cobros');
	
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();
        
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
