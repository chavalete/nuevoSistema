<?php
$_SERVER['DOCUMENT_ROOT']='/var/www/html';
require_once '/var/www/html/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once '/var/www/html/newFront/mail/FrenteEmails.php';


class MailAlertaVencimientosProductos {
    
    private $_db_dM;
    
    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento();
    }
    
    public function enviar(){
        
        $this->_db_dM->addSelect('sc.cantidad');
        $this->_db_dM->addSelect('dp.producto_nombre');
        $this->_db_dM->addSelect('dp.producto_presentacion');
        $this->_db_dM->addSelect('dp.codigo_referencia');
        $this->_db_dM->addSelect('dl.lote');
        $this->_db_dM->addSelect('to_char(dl.lote_vencimiento,\'dd-mm-yyyy\') lote_vencimiento ');
        $this->_db_dM->addSelect('de.estanteria_nombre');
        //$this->_db_dM->addSelect('');
        $this->_db_dM->addFrom('stock_completo sc');
        $this->_db_dM->addFrom('INNER JOIN datos_lotes dl USING (lote_id)');
        $this->_db_dM->addFrom('INNER JOIN datos_productos dp ON (sc.producto_id=dp.producto_id)');
        $this->_db_dM->addFrom('INNER JOIN datos_estanterias de USING (estanteria_id)');
        //$this->_db_dM->addFrom('');
        $this->_db_dM->addWhere('sc.cantidad > 0');
        $this->_db_dM->addWhere('dl.lote_vencimiento <= now()::date + 120');
        
        //$this->_db_dM->addGroup('');
        
        $this->_db_dM->addOrderBy('dl.lote_vencimiento ASC');
        
        $this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();exit;
        
        $rsVencimiento= $this->_db_dM->ejecutar();
        if(count($rsVencimiento) > 0 ){
            
            $vencimiento = $rsVencimiento[0];            
            $body= "<font  color=red ><h3> ALERTA! " . utf8_decode('próximos') . " vencimientos de productos. </h3></font>";
            $body.= "<table border=2><tr><th>Cant.</th><th> Producto</th><th>Lote</th><th>Venc</th><th>Estanteria</th><tr>";
            
            foreach ($rsVencimiento as $productos){
                $body.="<tr><td>" . $productos['cantidad'] . "</td>";
                $body.="<td>" . utf8_decode($productos['producto_nombre']) . "  " . utf8_decode($productos['producto_nombre']) ."(" . utf8_decode($productos['codigo_referencia'] . ")</td>");
                $body.="<td>" . $productos['lote'] . "</td>";
                $body.="<td>" . $productos['lote_vencimiento'] . "</td>";
                $body.="<td>" . $productos['estanteria_nombre'] . "</td></tr>";
            }
            $body.="</text>"; 
            $bodyHTML= $body;
            $vencimiento['body'] = $bodyHTML;
            $vencimiento['host'] = 'smtp.gmail.com';
            $vencimiento['port'] = 587;
            $vencimiento['smtpsecure'] = 'tls';
            $vencimiento['username'] = 'vencimientosortodontia@gmail.com';
            $vencimiento['password'] = 'ortodontiaVence';
            $vencimiento['from'] = "vencimientosortodontia@gmail.com";
            $vencimiento['fromname'] = "ORTODONTIA Vencimiento " . utf8_decode('Alerta Venc.'); 
            $vencimiento['addreplyto'][0]= array('address'=> 'vencimientosortodontia@gmail.com', 'name'=>'FIXAMO Envios');
            //destinatarios
            $vencimiento['addaddress'][] = array('address' => 'brandi.marce@gmail.com', 'name' => 'Marce');   
            //$vencimiento['addaddress'][] = array('address' => 'naverbuj@dmelmac.com.ar', 'name' => 'Guillo');   
            $vencimiento['addaddress'][] = array('address' => 'apannia@hotmail.com', 'name' => 'Andres');
	    //$vencimiento['addaddress'][] = array('address' => 'belen@shospitalarias.com.ar', 'name' => 'Belen');
	    //$vencimiento['addaddress'][] = array('address' => 'mariana@shospitalarias.com.ar', 'name' => 'Mariana');	    
	            
            $vencimiento['subject'] = " Alerta para vencimientos -MINI";
            
            $email = new FrenteEmails();
            echo $email->enviarEmail($vencimiento);
            
            $email->clearAddresses();
            
        }
    }
}

$mail = new MailAlertaVencimientosProductos();
$mail->enviar();
