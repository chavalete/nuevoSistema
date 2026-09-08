<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Cliente.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/CategoriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/CondicionVentaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/CondicionIvaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class ClienteExtendido extends Cliente
{    
    private $_categoriaNombre;
    private $_objFuncionesComunes;
    private $_clienteRespInscDesc;
    private $_clienteNoRespInscDesc;
    private $_clienteIvaMensaje;
    private $_clienteActivoMensaje;
    private $_clienteActivoDesc;
    private $_clienteNoActivoDesc;
    private $_clienteCondVenta;
    private $_clienteFormaPago;
    private $_objCondicionVenta;
    private $_objListaDePrecios;
    private $_horarioEntrega;
    private $_listaId;
	private $_aIdSucursal;
    private $_objCondicionIva;
    private $_condicionIvaId;
    
    
    public function __construct(){
	$this->_db = new FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = NEW FuncionesComunes();
    }
    public function setObjCondicionIva($objCondicionIva){
        $this->_objCondicionIva = $objCondicionIva;
    }
    public function getObjCondicionIva(){
        return $this->_objCondicionIva;
    }
    public function setCondicionIvaId($ivaId){
        $this->_condicionIvaId = $ivaId;
    }
    public function getCondicionIvaId(){
        return $this->_condicionIvaId;
    }
    public function setAIdSucursal($idSucursal){
		$this->_aIdSucursal = $idSucursal;
	}
	public function getAIdSucursal(){
		return $this->_aIdSucursal;
	}
    public function setListaId($listaId){
        $this->_listaId = $listaId;
    }
    public function getListaId(){
        return $this->_listaId;
    }
    public function setCategoriaNombre($categoriaNombre){
	$this->_categoriaNombre = $categoriaNombre;
    }
    public function getCategoriaNombre(){
	return $this->_categoriaNombre;
    }
    public function setClienteRespInscDesc($clienteRespInscDesc){
	$this->_clienteRespInscDesc = $clienteRespInscDesc;
    }
    public function getClienteRespInscDesc(){
	return $this->_clienteRespInscDesc;
    }
    public function setClienteNoRespInscDesc($clienteNoRespInscDesc){
	$this->_clienteNoRespInscDesc = $clienteNoRespInscDesc;
    }
    public function getClienteNoRespInscDesc(){
	return $this->_clienteNoRespInscDesc;
    }
    public function setClienteIvaMensaje($clienteIvaMensaje){
	$this->_clienteIvaMensaje= $clienteIvaMensaje;
    }
    public function getClienteIvaMensaje(){
	return $this->_clienteIvaMensaje;
    }
    public function setClienteActivoDesc($clienteActivoDesc){
	$this->_clienteActivoDesc = $clienteActivoDesc;
    }
    public function getClienteActivoDesc(){
	return $this->_clienteActivoDesc;
    }
    public function setClienteNoActivoDesc($clienteNoActivoDesc){
	$this->_clienteNoActivoDesc = $clienteNoActivoDesc;
    }
    public function getClienteNoActivoDesc(){
	return $this->_clienteNoActivoDesc;
    }
    public function setClienteActivoMensaje($clienteActivoMensaje){
	$this->_clienteActivoMensaje= $clienteActivoMensaje;
    }
    public function getClienteActivoMensaje(){
	return $this->_clienteActivoMensaje;
    }
    public function setClienteCondVenta($clienteCondVenta){
    	$this->_clienteCondVenta = $clienteCondVenta;
    }
    public function getClienteCondVenta(){
	return $this->_clienteCondVenta;
    }
    public function setClienteFormaPago($clienteFormaPago){
	$this->_clienteFormaPago = $clienteFormaPago;
    }
    public function getClienteFormaPago(){
	return $this->_clienteFormaPago;
    }
    public function setObjCondicionVenta($objCondicionVenta){
        $this->_objCondicionVenta = $objCondicionVenta;
    }
    
    public function getObjCondicionVenta(){
        return $this->_objCondicionVenta;
    }
    public function setObjListaDePrecios($objLista){
	$this->_objListaDePrecios = $objLista;
    }
    public function getObjListaDePrecios(){
	return $this->_objListaDePrecios;
    } 
    public function setHorarioEntrega($horario){
        $this->_horarioEntrega = $horario;
    }
    public function getHorarioEntrega(){
        return $this->_horarioEntrega;
    }
    public function getDataDB($id){

	$this->_db->addSelect('cliente_id');
	$this->_db->addSelect('cliente_nombre');
	$this->_db->addSelect('cliente_cuit');
	$this->_db->addSelect('categoria_id');
	$this->_db->addSelect('cliente_activo AS cliente_activo');
	$this->_db->addSelect('\'Activo\'  AS cliente_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS cliente_no_activo_desc');
	$this->_db->addSelect('cliente_direccion');
	$this->_db->addSelect('cliente_telefono');
	$this->_db->addSelect('cliente_contacto');
	$this->_db->addSelect('cliente_resp_insc');
	$this->_db->addSelect('\'Resp Inscp\' AS cliente_resp_insc_desc');
	$this->_db->addSelect('\'Exento\' AS cliente_no_resp_insc_desc');
	$this->_db->addSelect('CASE WHEN cliente_resp_insc THEN \'Resp Insc\' ELSE \'Exento\' END AS cliente_iva_mensaje');
	$this->_db->addSelect('CASE WHEN cliente_activo THEN \'Activo\' ELSE \'Inactivo\' END AS cliente_activo_mensaje');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('condicion_venta_id AS condicion_venta_id');
	$this->_db->addSelect('cliente_forma_pago');
	$this->_db->addSelect('cliente_calle');
	$this->_db->addSelect('cliente_altura');
	$this->_db->addSelect('cliente_piso');
	$this->_db->addSelect('cliente_depto');
	$this->_db->addSelect('cliente_gln');
	$this->_db->addSelect('cliente_codigo_postal');
	$this->_db->addSelect('cliente_loca_id');
	$this->_db->addSelect('vendedor_id');
	$this->_db->addSelect('horario_entrega');
	$this->_db->addSelect('lista_id');
	$this->_db->addSelect('de_sucursal_id');
	$this->_db->addSelect('a_sucursal_id');
	$this->_db->addSelect('condicion_iva_id');
	
	$this->_db->addFrom('datos_clientes');

	$this->_db->addWhere('cliente_id = \'' . $id .'\'' );

	$this->_db->generarSelect();
	//echo $this->_db->getQry();
	$resultado =  $this->_db->ejecutar();
	
	$objLocalidad = new LocalidadExtendido();
        if($resultado[0]['cliente_loca_id'] >= 0 ){
            $objLocalidad->cargarMe($resultado[0]['cliente_loca_id']);
        }
        //var_dump($objLocalidad);
        $this->setObjLocalidad($objLocalidad);
	
	return $resultado[0];
    
    }
    
    
    public function cargarMe($datos){
    

	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
//var_dump($resultado);exit;
	$this->setClienteId($resultado['cliente_id']);
	
	$this->setClienteNombre($resultado['cliente_nombre']);
	$this->setClienteCuit($resultado['cliente_cuit']);
	$this->setCategoriaId($resultado['categoria_id']);
	$this->setClienteActivo($resultado['cliente_activo']);
	$this->setCategoriaNombre($resultado['categoria_nombre']);
	$this->setClienteDireccion($resultado['cliente_direccion']);
	$this->setClienteTelefono($resultado['cliente_telefono']);
	$this->setClienteContacto($resultado['cliente_contacto']);
	$this->setClienteRespInsc($resultado['cliente_resp_insc']);
	$this->setClienteRespInscDesc($resultado['cliente_resp_insc_desc']);
	$this->setClienteNoRespInscDesc($resultado['cliente_no_resp_insc_desc']);
	$this->setClienteIvaMensaje($resultado['cliente_iva_mensaje']);
	$this->setClienteActivoDesc($resultado['cliente_activo_desc']);
	$this->setClienteNoActivoDesc($resultado['cliente_no_activo_desc']);
	$this->setClienteActivoMensaje($resultado['cliente_activo_mensaje']);
	$this->setClienteFormaPago($resultado['cliente_forma_pago']);
	$this->setClienteCondVenta($resultado['condicion_venta_id']);
	$this->setObs($resultado['observaciones']);
	$this->setClienteCalle($resultado['cliente_calle']);
	$this->setClienteAltura($resultado['cliente_altura']);
	$this->setClientePiso($resultado['cliente_piso']);
	$this->setClienteDepto($resultado['cliente_depto']);
	$this->setClienteCodigoPostal($resultado['cliente_codigo_postal']);
	$this->setClienteGln($resultado['cliente_gln']);
	$this->setVendedorId($resultado['vendedor_id']);
	$this->setHorarioEntrega($resultado['horario_entrega']);
	$this->setListaId($resultado['lista_id']);
	$this->setAIdSucursal($resultado['a_sucursal_id']);
	$this->setCondicionIvaId($resultado['condicion_iva_id']);

    }
    public function salvarMe($parametros)    {
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposTabla('seq_datos_clientes_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();
	
	$this->setClienteId($id[0]['nextval']);
	$this->setClienteNombre($parametros['cliente']);
	$this->setObs($parametros['observaciones']);
	$this->setClienteDireccion($parametros['direccion']);
	$this->setClienteTelefono($parametros['telefono']);
	$this->setClienteCuit($parametros['cuit']);
	$this->setClienteContacto($parametros['contacto']);
	$this->setClienteRespInsc($parametros['respInsc']);
	$this->setCategoriaId($parametros['categorias']);
	$this->setClienteCondVenta($parametros[condicion]);
	$this->setClienteFormaPago($parametros[formaPago]);
	$this->setClienteGln($parametros[gln]);
	$this->setClienteCalle($parametros[calles]);
	$this->setClienteAltura($parametros[alturas]);
	$this->setClientePiso($parametros[piso]);
	$this->setClienteDepto($parametros[depto]);
	$this->setClienteLocalidad($parametros[localidades]);
	$this->setClienteCodigoPostal($parametros[codigoPostal]);
	$this->setVendedorId($parametros[vendedores]);
	$this->setHorarioEntrega($parametros[horarioEntrega]);
	$this->setCondicionIvaId($parametros[condicionIva]);
       
        $this->_db->addFrom('datos_clientes');

        $this->_db->addCamposTabla('cliente_id');
        $this->_db->addCamposValue($this->getClienteId());
        
        $this->_db->addCamposTabla('cliente_nombre');
        $this->_db->addCamposValue('\'' . $this->getClienteNombre() . '\'');
        
        
        if($this->getObs()!=null){
	    $this->_db->addCamposTabla('observaciones');
	    $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
	}
	 
        if($this->getClientePiso()!=null){
	    $this->_db->addCamposTabla('cliente_piso');
	    $this->_db->addCamposValue('\'' . $this->getClientePiso() .'\'');
	}
	if($this->getClienteDepto()!=null){
	    $this->_db->addCamposTabla('cliente_depto');
	    $this->_db->addCamposValue('\'' . $this->getClienteDepto() .'\'');
	}
	if($this->getClienteCodigoPostal()!=null){
	    $this->_db->addCamposTabla('cliente_codigo_postal');
	    $this->_db->addCamposValue('\'' . $this->getClienteCodigoPostal() .'\'');
	}
	if($this->getClienteLocalidad()!=null){
	    $this->_db->addCamposTabla('cliente_loca_id');
	    $this->_db->addCamposValue('\'' . $this->getClienteLocalidad() .'\'');
	}
	if($this->getClienteGln()!=null){
	    $this->_db->addCamposTabla('cliente_gln');
	    $this->_db->addCamposValue('\'' . $this->getClienteGln() .'\'');
	}
	if($this->getClienteTelefono()!=null){
	    $this->_db->addCamposTabla('cliente_telefono');
	    $this->_db->addCamposValue('\'' . $this->getClienteTelefono() .'\'');
	}
	if($this->getClienteCuit()!=null){
	    $this->_db->addCamposTabla('cliente_cuit');
	    $this->_db->addCamposValue('\'' . $this->getClienteCuit() .'\'');
	}
	if($this->getClienteContacto()!=null){
	    $this->_db->addCamposTabla('cliente_contacto');
	    $this->_db->addCamposValue('\'' . $this->getClienteContacto() .'\'');
	}
	if($this->getClienteRespInsc()!=0){
	    $this->_db->addCamposTabla('cliente_resp_insc');
	    $this->_db->addCamposValue('\'' . $this->getClienteRespInsc() .'\'');
	}
	
        $this->_db->addCamposTabla('cliente_usuario_alta');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
        
        if($this->getClienteCondVenta()!=null){
	    $this->_db->addCamposTabla('condicion_venta_id');
	    $this->_db->addCamposValue('\'' . $this->getClienteCondVenta() .'\'');
	}
	if($this->getClienteFormaPago()!=null){
	    $this->_db->addCamposTabla('cliente_forma_pago');
	    $this->_db->addCamposValue('\'' . $this->getClienteFormaPago() .'\'');
	}
	if($this->getVendedorId()!=null){
	    $this->_db->addCamposTabla('vendedor_id');
	    $this->_db->addCamposValue('\'' . $this->getVendedorId() .'\'');
	}
	if($parametros['listaId']!=null){
	    $this->_db->addCamposTabla('lista_id');
	    $this->_db->addCamposValue('\'' . $parametros['listaId'] .'\'');
	}
	if($this->getHorarioEntrega()!=null){
	    $this->_db->addCamposTabla('horario_entrega');
	    $this->_db->addCamposValue('\'' . $this->getHorarioEntrega() .'\'');
	}
	if($this->getClienteDireccion()!=null){
	    $this->_db->addCamposTabla('cliente_direccion');
	    $this->_db->addCamposValue('\'' . $this->getClienteDireccion() .'\'');
	}
	if($this->getCondicionIvaId()!=null){
            $this->_db->addCamposTabla('condicion_iva_id');
            $this->_db->addCamposValue('\'' . $this->getCondicionIvaId() .'\'');
        }
   $this->_db->addCamposTabla('sucursal_id');
   $this->_db->addCamposValue('\'' . $_SESSION['sucursalId'] .'\'');
    $this->_db->generarInsert();
                
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

        

        if (count($this->_db->getArrError()) > 0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el cliente'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Cliente cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    public function actualizarMe($arrParametros){

    
    //echo $arrParametros['campos'][horario_entrega];exit;
    

	if($arrParametros['campos'][cliente_nombre]!=NULL){
	    $this->_db->addCamposUpdate('cliente_nombre = \'' . $arrParametros['campos']['cliente_nombre'] .'\'');
	}
	
	if(is_numeric($arrParametros['campos']['categoria_id'])){
		$this->_db->addCamposUpdate('categoria_id = \'' . $arrParametros['campos']['categoria_id'] .'\'');
	}
	if($arrParametros['campos']['cliente_activo'] == 1){
		$this->_db->addCamposUpdate('cliente_activo = true');
	}else{
		$this->_db->addCamposUpdate('cliente_activo = false');
	}
	if($arrParametros['campos']['cliente_resp_insc'] == 1){
		$this->_db->addCamposUpdate('cliente_resp_insc = true');
	}else{
		$this->_db->addCamposUpdate('cliente_resp_insc = false');
	}
	if($arrParametros['campos']['cliente_resp_insc'] == 1){
		$this->_db->addCamposUpdate('cliente_resp_insc = true');
	}else{
		$this->_db->addCamposUpdate('cliente_resp_insc = false');
	}
	if($arrParametros['campos'][cliente_contacto]!=NULL){
	    $this->_db->addCamposUpdate('cliente_contacto = \'' . $arrParametros['campos']['cliente_contacto'] .'\'');
	}
	if($arrParametros['campos'][observaciones]!=NULL){
	    $this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');
	}
	if($arrParametros['campos'][cliente_cuit]!=NULL){
	    $this->_db->addCamposUpdate('cliente_cuit = \'' . $arrParametros['campos']['cliente_cuit'] .'\'');
	}
	if($arrParametros['campos'][cliente_direccion]!=NULL){
	    $this->_db->addCamposUpdate('cliente_direccion = \'' . $arrParametros['campos']['cliente_direccion'] .'\'');
	}
	if($arrParametros['campos'][cliente_telefono]!=NULL){
	    $this->_db->addCamposUpdate('cliente_telefono = \'' . $arrParametros['campos']['cliente_telefono'] .'\'');
	}
	if($arrParametros['campos'][cliente_calle]!=NULL){
	    $this->_db->addCamposUpdate('cliente_calle = \'' . $arrParametros['campos']['cliente_calle'] .'\'');
	}
	if($arrParametros['campos'][cliente_altura]!=NULL){
	    $this->_db->addCamposUpdate('cliente_altura= \'' . $arrParametros['campos']['cliente_altura'] .'\'');
	}
	if($arrParametros['campos'][cliente_piso]!=NULL){
	    $this->_db->addCamposUpdate('cliente_piso= \'' . $arrParametros['campos']['cliente_piso'] .'\'');
	}
	if($arrParametros['campos'][cliente_departamento]!=NULL){
	    $this->_db->addCamposUpdate('cliente_depto= \'' . $arrParametros['campos']['cliente_departamento'] .'\'');
	}
	if($arrParametros['campos'][cliente_codigo_postal]!=NULL){
	    $this->_db->addCamposUpdate('cliente_codigo_postal= \'' . $arrParametros['campos']['cliente_codigo_postal'] .'\'');
	}
	if($arrParametros['campos'][horario_entrega]!=NULL){
	    $this->_db->addCamposUpdate('horario_entrega= \'' . $arrParametros['campos']['horario_entrega'] .'\'');
	}
	if($arrParametros['campos']['localidades']!=NULL && $arrParametros['campos']['localidades']!='false'){
	    $this->_db->addCamposUpdate('cliente_loca_id = \'' . $arrParametros['campos']['localidades'] .'\'');
	}
	if($arrParametros['campos']['listaDePrecios']!='undefined' && $arrParametros['campos']['listaDePrecios']!='false' && $arrParametros['campos']['listaDePrecios']!=null){
	    $this->_db->addCamposUpdate('lista_id = \'' . $arrParametros['campos']['listaDePrecios'] .'\'');
	}
        if($arrParametros['campos']['vendedoresClientes']!='undefined' && $arrParametros['campos']['vendedoresClientes']!='false' && $arrParametros['campos']['vendedoresClientes']!=NULL){
	    $this->_db->addCamposUpdate('vendedor_id = \'' . $arrParametros['campos']['vendedoresClientes'] .'\'');
	}
	if(is_numeric($arrParametros['campos']['condicionIva'])){
		$this->_db->addCamposUpdate('condicion_iva_id = \'' . $arrParametros['campos']['condicionIva'] .'\'');
	}
	$this->_db->addFrom('datos_clientes');
	$this->_db->addWhere('cliente_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate(); 
	//echo $this->_db->getQry();exit;
	//asigamos el resulta que es un array a una variable
	$resultado =$this->_db->ejecutar();
	
	
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
