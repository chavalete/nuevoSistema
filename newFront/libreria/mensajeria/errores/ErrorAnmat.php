<?php

abstract class ErrorAnmatWeb
{
    private $_errorId;
    private $_errorIdAnmat;
    private $_errorDesc;
    
    public function __construct(){
    }
    
    public function setErrorId($errorId){
    
	$this->_errorId = $errorId;
    }
    public function getErrorId(){
    
	return $this->_errorId;
    }
    public function setErrorIdAnmat($errorIdAnmat){
    
	$this->_errorIdAnmat = $errorIdAnmat;
    }
    public function getErrorIdAnmat(){
    
	return $this->_errorIdAnmat;
    }
    public function setErrorDesc($errorDesc){

	$this->_errorDesc = $errorDesc;
    }
    public function getErrorDesc(){

	return $this->_errorDesc;
    }
    abstract function cargarme($id);
    abstract function salvarme($arr);
    abstract function actualizarme();
}