<?php 

abstract class Remesa
{
    private $_remesaId;
    private $_remesaNombre;
    private $_remesaNro;
    private $_fechaImportacion;
    private $_usuarioId;

    public function __construct()
    {
    }
    public function setRemesaId($id)
    {
	$this->_remesaId = $id;
    }
    public function getRemesaId()
    {
	return $this->_remesaId;
    }
    public function setRemesaNombre($nombre)
    {
	$this->_remesaNombre = $nombre;
    }
    public function getRemesaNombre()
    {
	return $this->_remesaNombre;
    }
    public function setFechaImportacion($fechaAlta)
    {
	$this->_fechaImportacion = $fechaAlta;
    }
    public function getFechaImportacion()
    {
	return $this->_fechaImportacion;
    }

    public function setRemesaNro($nro)
    {
	$this->_remesaNro = $nro;
    }
    public function getRemesaNro()
    {
	return $this->_remesaNro;
    }

    abstract function cargarMe($id);
    abstract function salvarMe($arrParamametros);
    abstract function actualizarMe($arrParametros);
}