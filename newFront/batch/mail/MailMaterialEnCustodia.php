<?php
$_SERVER['DOCUMENT_ROOT']='/var/www/html';
require_once '/var/www/html/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once '/var/www/html/newFront/mail/FrenteEmails.php';


class MailMaterialEnCustodia{
    
    private $_db_dM;
    
    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento();
    }
    
    public function enviar(){
        //Codigo del Material // Producto // Fecha de asignación de custodia // Personal de custodia que lo tiene//
        $this->_db_dM->addSelect('codigo_referencia');
        $this->_db_dM->addSelect('producto_nombre');
        $this->_db_dM->addSelect('producto_presentacion');
        $this->_db_dM->addSelect('trazabilidad_codigo');
        $this->_db_dM->addSelect('custodiante_id');
        $this->_db_dM->addSelect('custodiante_nombre_completo');
        
        $this->_db_dM->addFrom('movimientos_trazabilidad');
        $this->_db_dM->addFrom('INNER JOIN datos_trazabilidad USING(trazabilidad_id)');
	$this->_db_dM->addFrom('INNER JOIN datos_movimientos USING(id_movimiento)');
	$this->_db_dM->addFrom('INNER JOIN datos_custodiantes USING(custodiante_id)');
	$this->_db_dM->addFrom('INNER JOIN datos_productos USING(producto_id)');
        $this->_db_dM->addWhere('finalizado = false ');
        $this->_db_dM->addWhere('datos_movimientos.tipo_movimiento_id  = 8 ');
        
        $this->_db_dM->generarSelect();
        
        //echo $this->_db_dM->getQry();exit;
        
        $rsCustodia= $this->_db_dM->ejecutar();
        if(count($rsCustodia) > 0 ){
            
            $vencimiento = $rsCustodia[0];            
            $body= "<font  color=red ><h3> Materiales en custodia. </h3></font>";
            $body.= "<table border=2><tr><th>Nombre(codigo)</th><th>Traza/Serie</th><th>Custodio Nombre</th><th></th><tr>";
            
            foreach ($rsCustodia as $productos){
                $body.="<td>" . utf8_decode($productos['producto_nombre']) . "  " . utf8_decode($productos['producto_presentacion']) ."(" . utf8_decode($productos['codigo_referencia'] . ")</td>");
                //$body.="<td>" . $productos[''] . "</td>";
                $body.="<td>" . $productos['trazabilidad_codigo'] . "</td>";
                $body.="<td>" . $productos['custodiante_nombre_completo'] . "</td></tr>";
            }
            $body.="</text>"; 
            $bodyHTML= $body;
            $vencimiento['body'] = $bodyHTML;
            $vencimiento['host'] = 'smtp.gmail.com';
            $vencimiento['port'] = 587;
            $vencimiento['smtpsecure'] = 'tls';
            $vencimiento['username'] = 'shenvios@gmail.com';
            $vencimiento['password'] = 'diana2013';
            $vencimiento['from'] = "shenvios@gmail.com";
            $vencimiento['fromname'] = "Soluciones Hospitalarias " . utf8_decode('Material an custodia'); 
            $vencimiento['addreplyto'][0]= array('address'=> 'shenvios@gmail.com', 'name'=>'SH Envios');
            //destinatarios
            //$vencimiento['addaddress'][] = array('address' => 'glacanna@dmelmac.com.ar', 'name' => 'Guillo');   
            
            $vencimiento['addaddress'][] = array('address' => 'irene@shospitalarias.com.ar', 'name' => 'Irene');   
            $vencimiento['addaddress'][] = array('address' => 'egalimberti@shospitalarias.com.ar', 'name' => 'Ezequiel');
	    
	    $vencimiento['addaddress'][] = array('address' => 'belen@shospitalarias.com.ar', 'name' => 'Belen');
	    $vencimiento['addaddress'][] = array('address' => 'mariana@shospitalarias.com.ar', 'name' => 'Mariana');	    
	    $vencimiento['addaddress'][] = array('address' => 'ramiro@shospitalarias.com.ar', 'name' => 'Ramiro');
	    /*
	    $vencimiento['addaddress'][] = array('address' => '@shospitalarias.com.ar', 'name' => 'Guillermo');
	    $vencimiento['addaddress'][] = array('address' => '@shospitalarias.com.ar', 'name' => 'Juan');	    
	    $vencimiento['addaddress'][] = array('address' => '@shospitalarias.com.ar', 'name' => 'Diana');
	    */
            
            $vencimiento['subject'] = "Material en custodia";
            
            $email = new FrenteEmails();
            echo $email->enviarEmail($vencimiento);
            
            $email->clearAddresses();
            
        }
    }
}

$mail = new MailMaterialEnCustodia();
$mail->enviar();

