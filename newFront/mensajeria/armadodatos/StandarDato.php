<?php
/*
 * Para armar un objeto con los parametros
 * @author chava
 */
class standarDato
{
    private $_posicion;
    private $_nombreCampo;
    private $_tipoDato;
    private $_longitud;
    private $_descripcion;
    private $_campoObligatorio;

    public function __construct($arr_parametro){
        $this->setStandar($arr_parametro);
    }
    
    //seteo de datos
    public function setStandar($arr_parametro){
        $this->_posicion = $arr_parametro[posicion_id];
        $this->_nombreCampo = $arr_parametro[nombre_campo];
        $this->_tipoDato = $arr_parametro[tipo_dato];
        $this->_longitud = $arr_parametro[longitud];
        $this->_descripcion = $arr_parametro[descripcion];
        $this->_campoObligatorio = $arr_parametro[campo_obligatorio];
    }
    
    public function getPosicion()
    {
        return $this->_posicion;
    }
    public function getNombreCampo()
    {
        return $this->_nombreCampo;
    }
    public function getTipoDato()
    {
        return $this->_tipoDato;
    }
    public function getLongitudCampo()
    {
        return $this->_longitud;
    }
    
    public function getCampoObligatorio(){
        return $this->_campoObligatorio;
    }
}

?>