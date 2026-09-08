<?php
require_once 'StandarDato.php';
/*
 * Datos correspondientes a una transaccion de medicamentos
 * @author chava
 */
class medicamentosDTO 
{
    private $f_evento;
    private $h_evento;
    private $gln_origen;
    private $cuit_origen;
    private $gln_destino;
    private $cuit_destino;
    private $n_remito;
    private $n_factura;
    private $vencimiento;
    private $gtin;
    private $lote;
    private $numero_serial;
    private $id_evento;
    private $apellido;
    private $nombres;
    private $n_documento;
    private $sexo;
    private $Tipo_documento;
    private $direccion;
    private $localidad;
    private $numero;
    private $piso;
    private $dpto;
    private $n_postal;
    private $telefono;
    
    
    public function __construct($linea,$objEstandar)
    {
        $this->setDatos($linea,$objEstandar);          
    }
    //setea los datos para ser enviados a la funcion sendmedicamtos
    public function setDatos($linea,$objEstandar)
    {   
        //var_dump($linea);exit;
        foreach($objEstandar->getColStandares() as $key => $parametro)
        {
            $nombreAtributo = $parametro->getNombreCampo();
            if($parametro->getTipoDato() == 'date') 
            {
                $this->$nombreAtributo = $linea[$nombreAtributo];
            }
            if($parametro->getTipoDato() == 'string' || $parametro->getTipoDato() == 'numerico')
            {
                /*
                $this->$nombreAtributo = str_pad($linea[$nombreAtributo],  
                                                   $parametro->getLongitudCampo(),  
                                                   " ",  
                                                   STR_PAD_LEFT);
                */
                $this->$nombreAtributo = $linea[$nombreAtributo];
                
            }
        }
    }
}
?>