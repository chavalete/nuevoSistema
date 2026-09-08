<?php
/*
CLASE DE CONDICION DE VENTA
*/
abstract class CondicionVenta
{

    private $_condicionVentaId; /*integer*/
    private $_condicionVentdaNombre;/*varchar*/
    private $_condicionVentaActiva;/*boolean default true*/
    private $_condicionVentaFechaAlta;/*date*/
    private $_condicionVentaUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	

    public function __construct()
    {
    }
    public function setCondicionVentaId($id){
	$this->_condicionVentaId = $id;
    }
    public function getCondicionVentaId(){
	return $this->_condicionVentaId;
    }
    public function setCondicionVentaNombre($nombre){
	$this->_condicionVentaNombre = $nombre;
    }
    public function getCondicionVentaNombre(){
	return $this->_condicionVentaNombre;
    }
    public function setCondicionVentaActiva($condicionVentaActiva){
	$this->_condicionVentaActiva = $condicionVentaActiva;
    }
    public function getCondicionVentaActiva(){
	return $this->_condicionVentaActiva;
    }
    public function setCondicionVentaFechaAlta($fechaAlta){
	$this->_condicionVentaFechaAlta = $fechaAlta;
    }
    public function getCondicionVentaFechaAlta(){
	return $this->_condicionVentaFechaAlta;
    }
    public function setCondicionVentaUsuarioAlta($usuarioId){
	$this->_condicionVentaUsuarioAlta = $usuarioId;
    }
    public function getCondicionVentaUsuarioAlta(){
	return $this->_condicionVentaUsuarioAlta;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParamatros);
    
    abstract public function actualizarMe($arrParamatros);
}