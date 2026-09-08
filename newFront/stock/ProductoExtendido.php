<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/Producto.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
CLASE DE PRODUCTOS
*/

class ProductoExtendido extends Producto
{
    private $_marcaNombre;
    private $_proveNombre;
    private $_codigoReferencia;
    
    private $_objFuncionesComunes;
    
    private $_productoAnexo;
    private $_productoMensajeriaMensaje;
    private $_productoMensajeriaDesc;
    private $_productoNoMensajeriaDesc;
    private $_productoActivoMensaje;
    private $_productoActivoDesc;
    private $_productoNoActivoDesc;
    private $_pm;
    private $_productoCostoDolar;
    private $_productoPrecioDolar;
    private $_stockMinimo;
    private $_stockAlerta;
    private $_productoComision;
    private $familiaNombre;
    private $_actualizarPrecio;
    private $_precioXCaja;
    private $_unidadBulto;
    private $_esCombo;

    public function setEsCombo($valor){
        $this->_esCombo = $valor;
    }
    public function getEsCombo(){
        return $this->_esCombo;
    }
    public function setUnidadBulto($unidad){
        $this->_unidadBulto = $unidad;
    }
    public function getUnidadBulto(){
        return $this->_unidadBulto;
    }
    
    public function setPrecioXCaja($precioXCaja){
        $this->_precioXCaja = $precioXCaja;
    }
    public function getPrecioXCaja(){
        return $this->_precioXCaja;
    }
    public function setActualizarPrecio($actualizar){
        $this->_actualizarPrecio = $actualizar;
    }
    public function getActualizarPrecio(){
        return $this->_actualizarPrecio;
    }
    
    public function setFamiliaNombre($nombre){
        $this->_familiaNombre = $nombre;
    }
    public function getFamiliaNombre(){
        return $this->_familiaNombre;
    }
    
    public function setStockMinimo($stockMinimo){
	$this->_stockMinimo = $stockMinimo;
    }
    public function getStockMinimo(){
	return $this->_stockMinimo;
    }
    
    
    public function setProductoCostoDolar($productoCostoDolar){
	$this->_productoCostoDolar = $productoCostoDolar;
    }
    public function getProductoCostoDolar(){
	return $this->_productoCostoDolar;
    }
    public function setProductoPrecioDolar($productoPrecioDolar){
	$this->_productoPrecioDolar = $productoPrecioDolar;
    }
    public function getProductoPrecioDolar(){
	return $this->_productoPrecioDolar;
    }
    public function setPm($pm){
	$this->_pm = $pm;
    }
    public function getPm(){
	return $this->_pm;
    }
    public function setProductoActivoDesc($productoActivoDesc){
	$this->_productoActivoDesc = $productoActivoDesc;
    }
    public function getProductoActivoDesc(){
	return $this->_productoActivoDesc;
    }
    public function setProductoNoActivoDesc($productoNoActivoDesc){
	$this->_productoNoActivoDesc = $productoNoActivoDesc;
    }
    public function getProductoNoActivoDesc(){
	return $this->_productoNoActivoDesc;
    }
    public function setProductoActivoMensaje($productoActivoMensaje){
	$this->_productoActivoMensaje = $productoActivoMensaje;
    }
    public function getProductoActivoMensaje(){
	return $this->_productoActivoMensaje;
    }
    public function setProductoMensajeria($productoMensajeria){
	$this->_productoAnexo = $productoMensajeria;
    }
    public function getProductoMensajeria(){
	return $this->_productoAnexo;
    }
    public function setProductoMensajeriaDesc($productoMensajeriaDesc){
	$this->_productoMensajeriaDesc= $productoMensajeriaDesc;
    }
    public function getProductoMensajeriaDesc(){
	return $this->_productoMensajeriaDesc;
    }
    public function setProductoNoMensajeriaDesc($productoNoMensajeriaDesc){
	$this->_productoNoMensajeriaDesc= $productoNoMensajeriaDesc;
    }
    public function getProductoNoMensajeriaDesc(){
	return $this->_productoNoMensajeriaDesc;
    }
    public function setProductoMensajeriaMensaje($productoMensajeriaMensaje){
	$this->_productoMensajeriaMensaje= $productoMensajeriaMensaje;
    }
    public function getProductoMensajeriaMensaje(){
	return $this->_productoMensajeriaMensaje;
    }   
    public function __construct(){
	$this->_objFuncionesComunes = NEW FuncionesComunes();
	$this->_db = new FrenteAlmacenamiento();
    }
    public function setProveNombre($proveNombre){
	$this->_proveNombre = $proveNombre;
    }
    public function getProveNombre(){
	return $this->_proveNombre;
    }
    public function setCodigoReferencia($codigoReferencia){
	$this->_codigoReferencia = $codigoReferencia;
    }
    public function getCodigoReferencia(){
	return $this->_codigoReferencia;
    }
    public function setMarcaNombre($marcaNombre){
	$this->_marcaNombre = $marcaNombre;
    }
    public function getMarcaNombre(){
	return $this->_marcaNombre;
    }
    public function setExcluirSiDesc($excluirSiDesc){
	$this->_excluirSiDesc = $excluirSiDesc;
    }
    public function getExcluirSiDesc(){
	return $this->_excluirSiDesc;
    }
    public function setExcluirNoDesc($excluirNoDesc){
	$this->_excluirNoDesc = $excluirNoDesc;
    }
    public function getExcluirNoDesc(){
	return $this->_excluirNoDesc;
    }
    public function setExcluirMensaje($mensaje){
	$this->_excluirMensaje = $mensaje;
    }
    public function getExcluirMensaje(){
	return $this->_excluirMensaje;
    }
    public function setExcluir($excluir){
        $this->_excluir =  $excluir;
    }
    public function getExcluir(){
        return $this->_excluir;
    }
    public function setStockAlerta($stockAlerta){
        $this->_stockAlerta=  $stockAlerta;
    }
    public function getStockAlerta(){
        return $this->_stockAlerta;
    }
    public function setProductoComision($productoComision){
        $this->_productoComision=  $productoComision;
    }
    public function getProductoComision(){
        return $this->_productoComision;
    }
    public function getDataDB($id){
	
	$this->_db->addSelect('producto_id');
	$this->_db->addSelect('producto_nombre');
	$this->_db->addSelect('producto_presentacion');
	$this->_db->addSelect('marca_id');
	$this->_db->addSelect('prove_id');
	$this->_db->addSelect('codigo_referencia');
	$this->_db->addSelect('producto_activo AS producto_activo');
	$this->_db->addSelect('\'Activo\'  AS producto_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS producto_no_activo_desc');
	$this->_db->addSelect('CASE WHEN producto_activo THEN \'Activo\' ELSE \'Inactivo\' END AS producto_activo_mensaje');
	$this->_db->addSelect('pm');
	$this->_db->addSelect('producto_gtin');
        $this->_db->addSelect('producto_anexo AS producto_mensajeria');
        $this->_db->addSelect('\'Si\'  AS producto_mensajeria_desc');
        $this->_db->addSelect('\'No\'  AS producto_no_mensajeria_desc');
        $this->_db->addSelect('CASE WHEN producto_anexo THEN \'Si\' ELSE \'No\' END AS producto_mensajeria_mensaje');
	
	$this->_db->addSelect('to_char(producto_fecha_alta, \'DD-MM-YYYY\') AS producto_fecha_alta');
	$this->_db->addSelect('producto_usuario_alta');
        $this->_db->addSelect('unidades');
        $this->_db->addSelect('producto_pventa');
        $this->_db->addSelect('producto_pcosto');
        $this->_db->addSelect('producto_pcosto_dolar');
        $this->_db->addSelect('producto_pventa_dolar');
        $this->_db->addSelect('stock_minimo');
        $this->_db->addSelect('stock_alerta');
        $this->_db->addSelect('producto_comision');
        $this->_db->addSelect('CASE WHEN actualizar_costo THEN \'Si\' ELSE \'No\' END AS actualizar_precio');
        $this->_db->addSelect('es_combo');
	
	$this->_db->addFrom('datos_productos');
	
	$this->_db->addWhere('producto_id = ' . $id);
	
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
	$resultado = $this->_db->ejecutar();
	
	return $resultado[0];
    
    }
    
    public function cargarMe($datos){

        if(is_numeric($datos)){
            $resultado = $this->getDataDB($datos);
        }else{
            $resultado = $datos;
        }
        
        //**agregar objeto marca y proveedor**//

        $this->setProductoId($resultado[producto_id]);
        $this->setProductoNombre($resultado[producto_nombre]);
        $this->setProductoPresentacion($resultado[producto_presentacion]);
        $this->setMarcaId($resultado[marca_id]);
        $this->setProveId($resultado[prove_id]);
        $this->setProductoActivoDesc($resultado['producto_activo_desc']);
        $this->setProductoNoActivoDesc($resultado['producto_no_activo_desc']);
        $this->setProductoActivoMensaje($resultado['producto_activo_mensaje']);
        $this->setProductoActivo($resultado['producto_activo']);
        $this->setProductoAlta($resultado[producto_fecha_alta]);
        $this->setCodigoReferencia($resultado[codigo_referencia]);
        $this->setMarcaNombre($resultado[marca_nombre]);
        $this->setProveNombre($resultado[prove_nombre]);
        $this->setProductoUsuarioAlta($resultado[producto_usuario_alta]);
        $this->setProductoMensajeriaDesc($resultado['producto_mensajeria_desc']);
        $this->setProductoNoMensajeriaDesc($resultado['producto_no_mensajeria_desc']);
        $this->setProductoMensajeriaMensaje($resultado['producto_mensajeria_mensaje']);
        $this->setProductoMensajeria($resultado['producto_anexo']);
        $this->setPm($resultado['pm']);
        $this->setProductoGtin($resultado['producto_gtin']);
        $this->setUnidades($resultado['unidades']);
        $this->setProductoPcosto($resultado['producto_pcosto']);
        $this->setProductoPrecio($resultado['producto_pventa']);
        $this->setProductoPrecioDolar($resultado['producto_pventa_dolar']);
        $this->setProductoCostoDolar($resultado['producto_pcosto_dolar']);
        $this->setStockMinimo($resultado['stock_minimo']);
        $this->setFamiliaNombre($resultado['familia_nombre']);
        $this->setExcluirSiDesc($resultado['excluir_reporte_stock_si_desc']);
        $this->setExcluirNoDesc($resultado['excluir_reporte_stock_no_desc']);
        $this->setExcluirMensaje($resultado['excluir_reporte_stock_mensaje']);
        $this->setExcluir($resultado['excluir_reporte_stock']);
        $this->setStockAlerta($resultado['stock_alerta']);
        $this->setProductoComision($resultado['producto_comision']);
        $this->setActualizarPrecio($resultado['actualizar_precio']);
	$this->setPrecioXCaja($resultado['precio_promocion']);
	$this->setUnidadBulto($resultado['unidad_bulto']);
    $this->setEsCombo($resultado['es_combo']);

    }
    public function salvarMe($arrParametros){

        $this->_db = NEW FrenteAlmacenamiento('dmelmac');

        $this->_db->addCamposTabla('seq_datos_productos_id');
        $this->_db->generarProximo();

        $id = $this->_db->ejecutar();

        $this->setProductoId($id[0]['nextval']);
        $this->setProductoNombre($arrParametros['productos']);
        $this->setProductoPresentacion($arrParametros['presentacion']);
        $this->setCodigoReferencia($arrParametros['codigoReferencia']);
        $this->setMarcaId($arrParametros['marcas']);
        $this->setProveId($arrParametros['proveedores']);
        $this->setPm($arrParametros['pm']);
        $this->setProductoGtin($arrParametros['productoGtin']);
        $this->setProductoMensajeria($arrParametros['productoMensajeria']);
        $this->setUnidades($arrParametros['unidades']);
        $this->setProductoPcosto($arrParametros['productoPcosto']);
        $this->setProductoPrecio($arrParametros['productoPrecio']);
        $this->setProductoCostoDolar($arrParametros['productoPcostoDolar']);
        $this->setStockMinimo($arrParametros['stockMinimo']);
        $this->setStockAlerta($arrParametros['stockAlerta']);
        $this->setProductoComision($arrParametros['comision']);
        $this->setPrecioXCaja($arrParametros['productoPrecioXCaja']);
        
        $this->_db->addFrom('datos_productos');

        $this->_db->addCamposTabla('producto_id');
        $this->_db->addCamposValue($this->getProductoId());
        
        if( $this->getProductoNombre() != null){
            $this->_db->addCamposTabla('producto_nombre');
            $this->_db->addCamposValue('\'' . $this->getProductoNombre() . '\'');
        } else {
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'No se cargo el nombre del producto'
		    );
		echo json_encode($arrDevolver);exit;
        }
        
        if( $this->getProductoPresentacion() != null){
           $this->_db->addCamposTabla('producto_presentacion'); 
           $this->_db->addCamposValue('\'' . $this->getProductoPresentacion() . '\'');
        }
        if( $this->getMarcaId() != null){
            $this->_db->addCamposTabla('marca_id');
            $this->_db->addCamposValue($this->getMarcaId());
        }
        if( $this->getProveId() != null){
            $this->_db->addCamposTabla('prove_id');
            $this->_db->addCamposValue($this->getProveId());
        }
        if( $this->getCodigoReferencia() != null){
            $this->_db->addCamposTabla('codigo_referencia');
            $this->_db->addCamposValue('\'' . $this->getCodigoReferencia() . '\'');
        }
        /*if( $this->getProductoGtin() != null && strlen($this->getProductoGtin())==13){
            $this->_db->addCamposTabla('producto_gtin');
            $this->_db->addCamposValue('\'' . $this->getProductoGtin() . '\'');
        }else{
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'No se cargo correctamente el codigo de barras'
		    );
		echo json_encode($arrDevolver);exit;
        }*/
        
	if( $this->getProductoGtin() != null){
            $this->_db->addCamposTabla('producto_gtin');
            $this->_db->addCamposValue('\'' . $this->getProductoGtin() . '\'');
	}

        if( $this->getUnidades() != null){
            $this->_db->addCamposTabla('unidades');
            $this->_db->addCamposValue('\'' . $this->getUnidades() . '\'');
        }
        
        if( $this->getProductoMensajeria() != null){
            $this->_db->addCamposTabla('producto_anexo');
            $this->_db->addCamposValue('\'' . $this->getProductoMensajeria() . '\'');
        }
	if( $this->getProductoMensajeria() != null){
            $this->_db->addCamposTabla('producto_anexo');
            $this->_db->addCamposValue('\'' . $this->getProductoMensajeria() . '\'');
        }
        if( $this->getProductoPcosto() != null){
            $this->_db->addCamposTabla('producto_pcosto');
            $this->_db->addCamposValue('\'' . $this->getProductoPcosto() . '\'');
        }
        if( $this->getProductoPrecio() != null){
            $this->_db->addCamposTabla('producto_pventa');
            $this->_db->addCamposValue('\'' . $this->getProductoPrecio() . '\'');
        }
        if( $this->getStockMinimo() != null){
            $this->_db->addCamposTabla('stock_minimo');
            $this->_db->addCamposValue('\'' . $this->getStockMinimo() . '\'');
        }
        if( $this->getStockAlerta() != null){
            $this->_db->addCamposTabla('stock_alerta');
            $this->_db->addCamposValue('\'' . $this->getStockAlerta() . '\'');
        }
        if( $this->getProductoComision() != null){
            $this->_db->addCamposTabla('producto_comision');
            $this->_db->addCamposValue('\'' . $this->getProductoComision() . '\'');
        }
	
        
        $FrenteListaDePrecios = NEW FrenteListasDePrecios();
        $arrQry['lista'] = $FrenteListaDePrecios->addDetalle($this);
	
        $arrQry['stock']="INSERT INTO stock_completo VALUES  (2,1,1,1,{$this->getProductoId()},0, 0,0);";
        $arrQry['stock'].="INSERT INTO stock_completo VALUES  (3,1,1,1,{$this->getProductoId()},0, 0,0);";
        $arrQry['stock'].="INSERT INTO stock_completo VALUES  (4,1,1,1,{$this->getProductoId()},0, 0,0);";
        
        $this->_db->generarInsert();
        
        $arrQry['producto'] = $this->_db->getQry();
        
	$qryFinal = $arrQry['producto'] . $arrQry['lista'] .$arrQry['stock'];
	
	$this->_db->setQry($qryFinal);
	//echo $this->_db->getQry();exit;
	$resultado=$this->_db->ejecutarTransaccion();
	
	
	    if (count($this->_db->getArrError()) > 0){
                    $arrDevolver = array(
            "soyError" => true,
            "nivel" => 1,
            "mensaje" =>'Error al cargar producto'
        );
                }else{
                    $arrDevolver = array(
                        "soyError" => false,
                        "nivel" => 1,
                        "mensaje" =>'Producto cargado con exito'
                    );
                }  
                echo json_encode($arrDevolver);exit;

    }
    public function actualizarMe($arrParametros){

	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('producto_nombre = \'' . $arrParametros['campos']['producto_nombre'] .'\'');
	$this->_db->addCamposUpdate('producto_presentacion = \'' . $arrParametros['campos']['producto_presentacion'] .'\'');
	//$this->_db->addCamposUpdate('codigo_referencia = \'' . $arrParametros['campos']['codigo_referencia'] .'\'');
	$this->_db->addCamposUpdate('unidades = \'' . $arrParametros['campos']['unidades'] .'\'');
	$this->_db->addCamposUpdate('producto_gtin = \'' . $arrParametros['campos']['producto_gtin'] .'\'');
	//$this->_db->addCamposUpdate('producto_pventa= \'' . $arrParametros['campos']['producto_pventa'] .'\'');
	$this->_db->addCamposUpdate('producto_pcosto= \'' . $arrParametros['campos']['producto_pcosto'] .'\'');
	if(is_numeric($arrParametros['campos']['proveedores'])){
	    $this->_db->addCamposUpdate('prove_id = \'' . $arrParametros['campos']['proveedores'] .'\'');
	}
	if(is_numeric($arrParametros['campos']['marcas'])){
	    $this->_db->addCamposUpdate('marca_id = \'' . $arrParametros['campos']['marcas'] .'\'');
	}
	  if($arrParametros['campos']['unidad_bulto']){
            $this->_db->addCamposUpdate('unidad_bulto= \'' . $arrParametros['campos']['unidad_bulto'] .'\'');
        }

	if(is_numeric($arrParametros['campos']['familias'])){
	    $this->_db->addCamposUpdate('familia_id = \'' . $arrParametros['campos']['familias'] .'\'');
	}
	if($arrParametros['campos']['producto_activo'] == 1){
		$this->_db->addCamposUpdate('producto_activo = true');
	}else{
		$this->_db->addCamposUpdate('producto_activo = false');
	}
	if($arrParametros['campos']['excluir_reporte_stock'] == 1){
		$this->_db->addCamposUpdate('excluir_reporte_stock = true');
	}else{
		$this->_db->addCamposUpdate('excluir_reporte_stock = false');
	}
	if($arrParametros['campos']['producto_anexo'] == 1){
		$this->_db->addCamposUpdate('producto_anexo = true');
	}else{
		$this->_db->addCamposUpdate('producto_anexo = false');
	}
 	//$this->_db->addCamposUpdate('stock_minimo = \'' . $arrParametros['campos']['stock_minimo'] .'\'');
 	$this->_db->addCamposUpdate('stock_alerta = \'' . $arrParametros['campos']['stock_alerta'] .'\'');
 	//$this->_db->addCamposUpdate('producto_comision = \'' . $arrParametros['campos']['producto_comision'] .'\'');
	
	$this->_db->addFrom('datos_productos');
	$this->_db->addWhere('producto_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();
	//echo $this->_db->getQry();exit;
	//asigamos el resulta que es un array a una variable
	$resultado = $this->_db->ejecutar();
	
	if ($resultado !== false && $resultado > 0) {
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
