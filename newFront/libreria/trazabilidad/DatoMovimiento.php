<?php
/**
 * class DatoMovimiento
 * 
 */
abstract class DatoMovimiento 
{
	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access private
	 */
    private $_idMovimiento;

	/**
	 * 
	 * @access private
	 */
	private $_movimientoFechaHora;

	/**
	 * 
	 * @access private
	 */
	private $_objTipoMovimiento;
    
    /**
	 * 
	 * @access private
	 */
	private $_objProve;

	/**
	 * 
	 * @access private
	 */
    private $_facturaNro;

	/**
	 * 
	 * @access private
	 */
	private $_remitoNro;

	/**
	 * 
	 * @access private
	 */
	private $_colObjDetalleMovimiento;
    
    /**
	 * 
	 * @access private
	 */
	private $_objCliente;
    
    /**
	 * 
	 * @access private
	 */
	private $_objMedico;
    
    /**
	 * 
	 * @access private
	 */
	private $_objPaciente;
    
    /**
	 * 
	 * @access private
	 */
	private $_objObraSocial;
	
	private $_objCustodia;
    
        /**
	 * 
	 * @access private
	 */
	private $_deIdMovimiento;

    /**
	 * 
	 * @access private
	 */
	private $_aIdMovimiento;

    /**
	 * 
	 * @access private
	 */
	private $_movimientoUsuarioId;
    
    /**
	 * 
	 * @access private
	 */
	private $_arrError = Array();
	private $_objVendedor;
	private $_objDistribuidor;
	private $_obs;
	private $objPedido;
	private $_objEstado;
    private $_recepcionLiberadaFechaHora;
    private $_recepcionLiberadaUsuarioId;
    private $_recepcionLiberada;
    private $_recepcionLiberadaDesc;
    private $_recepcionBloqueadaDesc;
    private $_recepcionMensaje;

    
    public function setObs($obs){
	$this->_obs = $obs;
    
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setIdMovimiento ($id){
        $this->_idMovimiento = $id;
    }
    
    public function getIdMovimiento (){
        return $this->_idMovimiento;
    }
    
    
    public function setDeIdMovimiento ($id){
        $this->_deIdMovimiento = $id;
    }
    
    public function getDeIdMovimiento (){
        return $this->_deIdMovimiento;
    }
    
    public function setAIdMovimiento ($id){
        $this->_aIdMovimiento = $id;
    }
    
    public function getAIdMovimiento (){
        return $this->_aIdMovimiento;
    }
    
    
    public function setBajaIdMovimiento($id){
        $this->_bajaIdMovimiento = $id;
    }
    
    public function getBajaIdMovimiento (){
        return $this->_bajaIdMovimiento;
    }
        
    public function setFacturaNro ($nro){
        $this->_facturaNro = $nro;
    }
    
    public function getFacturaNro (){
        return $this->_facturaNro;
    }
    
    public function setMovimientoFechaHora ($fechaHora){
        $this->_movimientoFechaHora = $fechaHora;
    }
    
    public function getMovimientoFechaHora (){
        return $this->_movimientoFechaHora;
    }
        
    public function setMovimientoUsuarioId ($userId){
        $this->_movimientoUsuarioId = $userId;
    }
    
    public function getMovimientoUsuarioId (){
        return $this->_movimientoUsuarioId;
    }
        
    public function setObjProve ($obj){
        $this->_objProve = $obj;
    }
    
    public function getObjProve (){
        return $this->_objProve;
    }
    
    public function setObjTipoMovimiento ($obj){
        $this->_objTipoMovimiento = $obj;
    }
    
    public function getObjTipoMovimiento (){
        return $this->_objTipoMovimiento;
    }
    
    
    public function setRemitoNro ($remito){
        $this->_remitoNro = $remito;
    }
    
    public function getRemitoNro (){
        return $this->_remitoNro;
    }
    
    public function setColObjDetalleMovimiento($objDetalleMovimiento){
        $this->_colObjDetalleMovimiento[] = $objDetalleMovimiento;
    }
    
    public function getColObjDetalleMovimiento(){
        return $this->_colObjDetalleMovimiento;
    }
    
    public function setObjCliente ($obj){
        $this->_objCliente = $obj;
    }
    
    public function getObjCliente (){
        return $this->_objCliente;
    }
    
    public function setObjPaciente ($obj){
        $this->_objPaciente = $obj;
    }
    
    public function getObjPaciente (){
        return $this->_objPaciente;
    }
    
    public function setObjMedico ($obj){
        $this->_objMedico = $obj;
    }
    
    public function getObjMedico (){
        return $this->_objMedico;
    }
    
    public function setObjObraSocial ($obj){
        $this->_objObraSocial = $obj;
    }
    
    public function getObjObraSocial (){
        return $this->_objObraSocial;
    }
    
    
    
    public function setArrError($arrError){
        $this->_arrError= $arrError;
    }

    public function getArrError(){
        return $this->_arrError;
    }
    public function setObjVendedor ($obj){
        $this->_objVendedor = $obj;
    }
    
    public function getObjVendedor(){
        return $this->_objVendedor;
    }
    public function setObjDistribuidor ($obj){
        $this->_objDistribuidor = $obj;
    }
    
    public function getObjDistribuidor(){
        return $this->_objDistribuidor;
    }
    public function setObjPedido($objPedido){
	$this->_objPedido = $objPedido;
    }
    public function getObjPedido(){
	return $this->_objPedido;
    }
    public function setObjCustodia($objCustodia){
	$this->_objCustodia= $objCustodia;
    }
    public function getObjCustodia(){
	return $this->_objCustodia;
    }
    public function setObjEstado($objEstado){
	$this->_objEstado = $objEstado;
    }
    public function getObjEstado(){
	return $this->_objEstado;
    }
    public function setRecepcionLiberadaFechaHora($fecha){
        $this->_recepcionLiberadaFechaHora = $fecha;
    }
    public function getRecepcionLiberadaFechaHora(){
        return $this->_recepcionLiberadaFechaHora;
    }
    public function setRecepcionLiberadaUsuarioId($id){
        $this->_recepcionLiberadaUsuarioId= $id;
    }
    public function getRecepcionLiberadaUsuarioId(){
        return $this->_recepcionLiberadaUsuarioId;
    }
    public function setRecepcionLiberada($liberada){
        $this->_recepcionLiberada = $liberada;
    }
    public function getRecepcionLiberada(){
        return $this->_recepcionLiberada;
    }
    public function setRecepcionLiberadaDesc($liberada){
        $this->_recepcionLiberadaDesc = $liberada;
    }
    public function getRecepcionLiberadaDesc(){
        return $this->_recepcionLiberadaDesc;
    }
    public function setRecepcionBloqueadaDesc($liberada){
        $this->_recepcionBloqueadaDesc = $liberada;
    }
    public function getRecepcionBloqueadaDesc(){
        return $this->_recepcionBloqueadaDesc;
    }
    public function setRecepcionMensaje($mensaje){
        $this->_recepcionMensaje= $mensaje;
    }
    public function getRecepcionMensaje(){
        return $this->_recepcionMensaje;
    }

    //metodos comunes
    abstract function cargarme($id);
    abstract function salvarme($arrParametros);
    abstract function actualizarme($arrParametros);


} // end of DatoMovimientoAlta
?>
