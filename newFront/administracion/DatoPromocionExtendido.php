<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DatoPromocion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DatoPromocionExtendido extends DatoPromocion
{
    private $_objFuncionesComunes;
    private $_db;


    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
        $this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_promociones');
        $this->_db->addWhere('promocion_id = \'' . $id .'\'' );
        $this->_db->generarSelect();

        $resultado =  $this->_db->ejecutar();
        
        return $resultado[0];
    
    }
    function cargarMe($datos) {
        //var_dump($datos);
        if(is_numeric($datos)){
            $resultado = $this->getDataDB($datos);
        }else{
            $resultado = $datos;
        }
        $this->setPromocionId($resultado['promocion_id']);
        $this->setPromocionFecha($resultado['promocion_fecha']);
        $this->setPromocionNombre($resultado['promocion_nombre']);
        $this->setPromocionCantidad($resultado['promocion_cantidad']);
        $this->setPromocionPrecio($resultado['promocion_precio']);
        $this->setSubfamiliaId($resultado['promocion_subfamilia_id']);
        $this->setPromocionActiva($resultado['promocion_activa']);
        $this->setObservaciones($resultado['observaciones']);
        $this->setSubfamiliaNombre($resultado['subfamilia_nombre']);
        $this->setPromocionVencimiento($resultado['fecha_vencimiento']);
        $this->setProductoNombre($resultado['producto_nombre']);
        $this->setPorcentajeDesc($resultado['porcentaje_descuento']);
        $this->setFechaInicio($resultado['fecha_inicio']);
        $this->setIdSucursal($resultado['sucursal_id']);
        
        
    }
    function salvarMe($arrParametros){
	       
        if($arrParametros['promocionNombre']!= null){
            $this->_db->addCamposTabla('promocion_nombre');
            $this->_db->addCamposValue('\'' . $arrParametros['promocionNombre'] .'\'');
        }
        if($arrParametros['promocionCantidad']!= null){
            $this->_db->addCamposTabla('promocion_cantidad');
            $this->_db->addCamposValue('\'' . $arrParametros['promocionCantidad'] .'\'');
        }
        if($arrParametros['promocionPrecio']!= null){
            $this->_db->addCamposTabla('promocion_precio');
            $this->_db->addCamposValue('\'' . $arrParametros['promocionPrecio'] .'\'');
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        if($arrParametros['subfamiliaId']!=null){
            $this->_db->addCamposTabla('promocion_subfamilia_id');
            $this->_db->addCamposValue('\'' . $arrParametros['subfamiliaId'] .'\'');
        }
        if($arrParametros['fechaVencimiento']!=null){
            $this->_db->addCamposTabla('fecha_vencimiento');
            $this->_db->addCamposValue('\'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaVencimiento']) .'\'');
        }
        if($arrParametros['productoId']!=null){
            $this->_db->addCamposTabla('producto_id');
            $this->_db->addCamposValue('\'' . $arrParametros['productoId'] .'\'');
        }
        if($arrParametros['idSucursal']>1){
            $this->_db->addCamposTabla('sucursal_id');
            $this->_db->addCamposValue('\'' . $arrParametros['idSucursal'] .'\'');
        }
        if($arrParametros['fechaInicio']!=null){
            $this->_db->addCamposTabla('fecha_inicio');
            $this->_db->addCamposValue('\'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaInicio']) .'\'');
        }
        if($arrParametros['porcentajeDescuento']!=null){
            $this->_db->addCamposTabla('porcentaje_descuento');
            $this->_db->addCamposValue('\'' . $arrParametros['porcentajeDescuento'] .'\'');
        }


        $this->_db->addCamposTabla('promocion_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->addFrom('datos_promociones');
	
        $this->_db->generarInsert();
        //echo $this->_db->getQry();exit;
    
        $resultado = $this->_db->ejecutar();

        

        if($resultado==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al crear la prommcion'
		    );
            echo json_encode($arrDevolver);exit;
        }else{
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 1,
                "mensaje" =>'Promocion con exito'
                );
            echo json_encode($arrDevolver);exit;
        }
    }
    
    function actualizarMe($arrParametros){

        if($arrParametros['importeCancelado']!=false){
            $this->_db->addCamposUpdate('factura_faltan_fact = factura_faltan_fact - \'' . $arrParametros['campos']['estados'] .'\'');
        }
        $this->_db->addFrom('datos_facturas');
        $this->_db->addWhere('factura_id = \'' . $arrParametros['facturaId'] .'\'');
	    
        //generar qry
        $this->_db->generarUpdate();   
        
        return $this->_db->getQry();

    }
}
