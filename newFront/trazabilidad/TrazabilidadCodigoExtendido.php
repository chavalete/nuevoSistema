<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/TrazabilidadCodigo.php';
require_once 'IndiceExtendido.php';

/**
 * Description of CodigoTrazabilidadExtendido
 *
 * @author root
 */
class TrazabilidadCodigoExtendido extends TrazabilidadCodigo
{
    
    private $_db;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->setTotalColumnas(7);
        $this->setObjIndice(new Indice());
    }
    
    public function generarCodigo(){
        $this->_db->addSelect('seq_trazabilidad_codigos_codigo_id');
        $this->_db->generarProximo();
        $resultado = $this->_db->ejecutar();
        
        $this->setCodigoId($resultado[0]['nextval']);
        
        $id = $this->getCodigoId();
        $i = 0;
        
        while($i < $this->getTotalColumnas()){
            if($id < $this->getObjIndice()->getTotalPosiciones()){
                $this->setTrazabilidadCodigo($this->getObjIndice()->getPosicion($id) . $this->getTrazabilidadCodigo());
                $id = 0; // entra por primera unica vez si se cumple la condicion de una o por ultima vez en el ciclo.
            }else{
                $this->setTrazabilidadCodigo($this->getObjIndice()->getPosicion($id % $this->getObjIndice()->getTotalPosiciones()) .                $this->getTrazabilidadCodigo());
                $id = floor($id/$this->getObjIndice()->getTotalPosiciones()); // sigo descomponiendo el nro
            }
            $i++;
        }
        return $this->salvarme();
    }

    public function cargarme($id){
        
    }
    public function salvarme(){
        
        //echo "id " . $this->getCodigoId() . " " . $this->getTrazabilidadCodigo() . " -> ";
        
        $this->_db->addFrom('trazabilidad_codigos');
        $this->_db->addCamposTabla('codigo_id');
        $this->_db->addCamposTabla('trazabilidad_codigo');
        $this->_db->addCamposValue($this->getCodigoId());
        $this->_db->addCamposValue('\'' . $this->getTrazabilidadCodigo() . '\'');
        
        $this->_db->generarInsert();
        
        return $this->_db->getQry();
    }
    public function actualizarme(){

    }

    
}
