<?php
$_SERVER['DOCUMENT_ROOT']='/var/www/html';
require_once '/var/www/html/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once '/var/www/html/newFront/mail/FrenteEmails.php';


class MailAlertaStockAlerta{
    
    private $_db_dM;
    
    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento();
    }
    
    public function enviar(){
        
        $this->_db_dM->addSelect('sc.cantidad');
        $this->_db_dM->addSelect('dp.stock_minimo');
        $this->_db_dM->addSelect('dp.stock_alerta');
        $this->_db_dM->addSelect('dp.producto_nombre');
        $this->_db_dM->addSelect('dp.producto_presentacion');
        $this->_db_dM->addSelect('dp.codigo_referencia');
        $this->_db_dM->addSelect('dl.lote');
        $this->_db_dM->addSelect('to_char(dl.lote_vencimiento,\'dd-mm-yyyy\') lote_vencimiento ');
        $this->_db_dM->addSelect('de.estanteria_nombre');
        //$this->_db_dM->addSelect('stock_minimo');
        $this->_db_dM->addFrom('stock_completo sc');
        $this->_db_dM->addFrom('INNER JOIN datos_lotes dl USING (lote_id)');
        $this->_db_dM->addFrom('INNER JOIN datos_productos dp ON (sc.producto_id=dp.producto_id)');
        $this->_db_dM->addFrom('INNER JOIN datos_estanterias de USING (estanteria_id)');
        //$this->_db_dM->addFrom('');
        //$this->_db_dM->addWhere('sc.cantidad > 0');
        $this->_db_dM->addWhere('sc.cantidad<= dp.stock_alerta');
        
        //$this->_db_dM->addGroup('');
        
        $this->_db_dM->addOrderBy('dp.producto_nombre ASC');
        
        $this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();exit;
        
        $rsVencimiento= $this->_db_dM->ejecutar();
        if(count($rsVencimiento) > 0 ){
            
            $vencimiento = $rsVencimiento[0];            
            $body= "<font  color=red ><h3> Limoneta ALERTA!  stock de productos. </h3></font>";
            $body.= "<table border=2><tr><th>Cant.</th><th> Stock Alerta</th><th> Producto</th><tr>";
            
            foreach ($rsVencimiento as $productos){
                $body.="<tr><td>" . $productos['cantidad'] . "</td>";
                $body.="<td>" . $productos['stock_alerta'] . "</td>";
                $body.="<td>" . utf8_decode($productos['producto_nombre']) . "  " . utf8_decode($productos['producto_presentacion']) ."</tr>";
                
            }
            $body.="</text>"; 
            $bodyHTML= $body;
            $vencimiento['body'] = $bodyHTML;
            $vencimiento['host'] = 'smtp.gmail.com';
            $vencimiento['port'] = 587;
            $vencimiento['smtpsecure'] = 'tls';
            $vencimiento['username'] = 'alertaslimonsj@gmail.com';
            $vencimiento['password'] = 'dkzwpgnrcxfckdsi';
            $vencimiento['from'] = "alertaslimonsj@gmail.com";
            $vencimiento['fromname'] = "Limon Reparto Stock Alerta"; 
            $vencimiento['addreplyto'][0]= array('address'=> 'alertaslimonsj@gmail.com', 'name'=>'LIMONETA Envios');
            //destinatarios
            $vencimiento['addaddress'][] = array('address' => 'LUCAS.ARGANARAS@HOTMAIL.COM', 'name' => 'Lucas Hotmail');
            $vencimiento['addaddress'][] = array('address' => 'nicoaverbuj@gmail.com', 'name' => 'Chava');
            $vencimiento['addaddress'][] = array('address' => 'LUCAS.ARGANARAS.LIMON@GMAIL.COM', 'name' => 'Lucas Gmail');
            $vencimiento['addaddress'][] = array('address' => 'jeronimodietz.limon@gmail.com', 'name' => 'Jeronimo Dietz');
	    //$vencimiento['addaddress'][] = array('address' => 'mariana@shospitalarias.com.ar', 'name' => 'Mariana');	    
	            
            $vencimiento['subject'] = " Stock Alerta -LIMONETA";
            
            
            $email = new FrenteEmails();
            echo $email->enviarEmail($vencimiento);
            
            $email->clearAddresses();
            
        }
    }
}

$mail = new MailAlertaStockAlerta();
$mail->enviar();
