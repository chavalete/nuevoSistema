<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/**
 * Description of Importador
 *
 * @author root
 */
class ImportadorRemesa
{
    private $_db;
    private $_archivo = NULL;
    private $_archivoNombre = NULL;
    
    private $_arrLineas = Array();
    private $_arrError = Array();
    
    public function __construct($parametros) {
    
        $this->_db = new FrenteAlmacenamiento();
	$this->_archivo = $_SERVER['DOCUMENT_ROOT']. '/newFront/flix/tmp/' .$parametros['archivo'];
	//echo $this->_archivo;exit;
        $this->_archivoNombre = $archivo;
        
    }
    
    public function setArrLineas($linea){
        $this->_arrLineas[] = $linea;
    }
    
    public function getArrLineas(){
        return $this->_arrLineas;
    }
    
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError;
    }

    public function cargarRemesa(){
        if(($this->_archivo = fopen($this->_archivo, "r"))  !== FALSE){
	    
            while(($linea = fgetcsv($this->_archivo, 1080, ";")) !== FALSE){
                $this->setArrLineas($linea);
            }
        }else{
            $arrError['detalle'] = 'Error al Abrir el archivo';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
            $arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"No se pudo abrir el archivo"
		    );
	    echo json_encode($arrDevolver);exit;
            
        }
    }
    
    public function salvarRemesa(){
        //echo count($this->getArrLineas());
        if(count($this->getArrLineas()) == 0){
            $arrError['detalle'] = 'Error: No se encontraron datos para guardar';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
            $arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"No se encontraron datos para guardar"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        $arrLlaves = array_shift($this->_arrLineas);//saco la primera posicion de array que contiene los encabezados
        //renombro las llaves del array con la cabecera
        $remesa_nro= $this->_arrLineas[0][0];
        for($i=0;$i<count($this->_arrLineas);$i++){
            $this->_arrLineas[$i] = array_combine($arrLlaves, $this->_arrLineas[$i]);
            unset ($this->_arrLineas[$i]['ZW_REMESS']);
            unset ($this->_arrLineas[$i]['ZW_CLIENTE']);
            unset ($this->_arrLineas[$i]['ZW_LOJACLI']);
            unset ($this->_arrLineas[$i]['ZW_VOLUME']);
            unset ($this->_arrLineas[$i]['C6_XXWOF1']);
            unset ($this->_arrLineas[$i]['C6_PEDCLIF1']);
        }
        
        $this->_db->addCamposTabla('seq_datos_remesa_remesa_id');
        $this->_db->generarProximo();
        $resultado= $this->_db->ejecutar();
        $remesa_id= $resultado[0]['nextval']; 

        //echo $remesa_id .  " = " . $remesa_nro;
        //exit;
        
        $this->_db->addFrom('detalle_remesas');
        
        $this->_db->addCamposTabla('remesa_id');
        $this->_db->addCamposTabla('producto');
        $this->_db->addCamposTabla('numero_serial');
        $this->_db->addCamposTabla('lote');
        $this->_db->addCamposTabla('vencimiento');
        
        $this->_db->addCamposValue($remesa_id);
        $this->_db->addCamposValue(':ZW_PRODUTO');
        $this->_db->addCamposValue(':ZW_NUMSERI');
        $this->_db->addCamposValue(':ZW_LOTECTL');
        $this->_db->addCamposValue(':B8_DTVALID');
        $this->_db->generarInsert();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar($this->_arrLineas);
        if(count($this->_db->getArrError()) > 0 ){
        //echo "aca";exit;
            $this->setArrError($this->_db->getArrError());
            $arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"Error al guardar los detalles, valide q la remesa no haya sido cargada con anterioridad"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        $this->_db->addFrom('datos_remesas');
        
        $this->_db->addCamposTabla('remesa_id');
        $this->_db->addCamposTabla('remesa_nombre');
        $this->_db->addCamposTabla('remesa_nro');
        $this->_db->addCamposTabla('remesa_fecha_hora_importacion');
        
        $this->_db->addCamposValue($remesa_id);
        $this->_db->addCamposValue("'" . $this->_archivoNombre . "'");
        $this->_db->addCamposValue($remesa_nro);
        $this->_db->addCamposValue('now()');
        $this->_db->generarInsert();
        $resultado = $this->_db->ejecutar();
        
        if(count($this->_db->getArrError()) > 0 ){
        
            $this->setArrError($this->_db->getArrError());
            $arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"Error al guardar los datos de cabecera de remesa"
		    );
	    echo json_encode($arrDevolver);exit;
        }else{
	    $arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 1,
		    "mensaje" =>"Remesa Cargada con exito"
		    );
	    echo json_encode($arrDevolver);exit;
        
        }    
                
    }
    
    
}
/*
$imp = new ImportadorRemesa('remesa_test.csv');
$imp->cargarRemesa();
if(count($imp->getArrError()) > 0 ){
    echo "<pre>";
    print_r($imp->getArrError());
    echo "</pre>";
    exit;
}
$imp->salvarRemesa();

if(count($imp->getArrError()) > 0 ){
    echo "<pre>";
    print_r($imp->getArrError());
    echo "</pre>";
    exit;
}
echo "<pre>";
print_r($imp->getArrLineas());
echo "</pre>";
*/

