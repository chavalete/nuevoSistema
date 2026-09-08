<?php

abstract class Gln
{
    private $_gln;
    private $_glnNombre;
    private $_glnCuit;
    private $_idAgente;
    private $_tipoAgente;
    
    public function __construct(){
    }
    
    public function setGln($gln){

	$this->_gln = $gln;

    }
    public function getGln(){

	return $this->_gln;
    }
    public function setGlnNombre($nombre){

	$this->_glnNombre = $nombre;
    }
    public function getGlnNombre(){

	return $this->_glnNombre;
    }
    public function setGlnCuit($glnCuit){

	$this->_glnCuit;
    }
    public function getGlnCuit(){

	return $this->_glnCuit;
    }
    public function setIdAgente($idAgente){

	$this->_idAgente = $idAgente;
    }
    public function getIdAgente(){

	return $this->_idAgente;
    }
    public function setTipoAgente($tipoAgente){

	$this->_tipoAgente = $tipoAgente;
    }
    public function getTipoAgente(){

	return $this->_tipoAgente;
    }
    abstract function cargarme($id);
    abstract function salvarme();
    abstract function actualizarme();
}