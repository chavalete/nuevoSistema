<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/distribucion/DatoHojaDeRuta.php';
require_once 'DetalleHojaDeRutaExtendido.php';

class DatoHojaDeRutaExtendido extends DatoHojaDeRuta
{
    private $_db;
    private $_totalHoja;

    public function setTotalHdr($total){
        $this->_totalHoja = $total;
    }
    public function getTotalHdr(){
        return $this->_totalHoja;
    }
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }

    public function getDataDB($id){

	$this->setHojaId($id);

	$this->_db->addSelect('hoja_id');
        $this->_db->addSelect('hoja_fecha');
        $this->_db->addSelect('distribuidor_id');
	$this->_db->addSelect('hoja_obs');
	$this->_db->addSelect('hoja_usuario_id');
	$this->_db->addSelect('hoja_anulada');
	$this->_db->addSelect('hoja_anulada_fecha_hora');
	$this->_db->addSelect('hoja_anulada_usuario_id');
	$this->_db->addSelect('acompanante');
	$this->_db->addSelect('vehiculo_id');
	$this->_db->addSelect('carga');

	$this->_db->addFrom('datos_hojas_de_ruta');


	$this->_db->addWhere('hoja_id = \'' . $this->getHojaId() . '\'');

	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	$resultado = $this->_db->ejecutar();

	//verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $errorDetalle = 'La hoja que intenta cargar no existe';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        return $resultado[0];
    }
    public function cargarme($datos){
	if(is_numeric($datos)){

	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	$this->setHojaId($resultado['hoja_id']);
	$this->setHojaFechaHora($resultado['hoja_fecha']);
	$this->setHojaUsuarioId($resultado['hoja_usuario_id']);
	$this->setHojaAnulada($resultado['hoja_anulada']);
	$this->setHojaAnuladaFechaHora($resultado['hoja_anulada_fecha_hora']);
	$this->setHojaAnuladaUsuarioId($resultado['hoja_anulada_usuario_id']);
	$this->setAcompanante($resultado['acompanante']);
	$this->setVehiculo($resultado['vehiculo']);
	$this->setCarga($resultado['carga']);
	$this->setTotalHdr($resultado['total']);
	$this->setHojaObs($resultado['hoja_obs']);

    }

    public function salvarme($arrParametros){

	$this->_db->addCamposTabla('hoja_id');
        $this->_db->addCamposTabla('distribuidor_id');
        $this->_db->addCamposTabla('hoja_usuario_id');
        $this->_db->addCamposTabla('vehiculo_id');
        $this->_db->addCamposTabla('carga');
        $this->_db->addCamposValue($arrParametros['hojaId']);
        $this->_db->addCamposValue($arrParametros['distribuidorId']);
        $this->_db->addCamposValue($arrParametros['hojaUsuarioId']);
        $this->_db->addCamposValue("'" . $arrParametros['vehiculo'] . "'");
        $this->_db->addCamposValue("'" . $arrParametros['carga'] . "'");
        if($arrParametros['acompanante']!=null){
            $this->_db->addCamposTabla('acompanante');
            $this->_db->addCamposValue("'" . $arrParametros['acompanante'] . "'");
        }
        if($arrParametros['observaciones']!=null){
            $this->_db->addCamposTabla('hoja_obs');
            $this->_db->addCamposValue("'" . $arrParametros['observaciones'] . "'");
        }


        $this->_db->addFrom('datos_hojas_de_ruta');

        $this->_db->generarInsert();

        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();


    }
    public function actualizarMe($arrParametros){


    }
}
