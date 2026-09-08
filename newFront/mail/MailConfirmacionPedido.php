<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mail/FrenteEmails.php';


class MailConfirmacionPedido {
    
    private $_db_dM;
    
    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento();
    }
    
    public function enviar($idMovimiento){
        
        $this->_db_dM->addSelect('dm.remito_nro');
        $this->_db_dM->addSelect('dv.vendedor_id');
        $this->_db_dM->addSelect('dv.vendedor_mail');
        $this->_db_dM->addSelect('dv.vendedor_nombre');
        $this->_db_dM->addSelect('dd.distribuidor_id');
        $this->_db_dM->addSelect('dd.distribuidor_mail');
        $this->_db_dM->addSelect('dd.distribuidor_nombre');
        $this->_db_dM->addSelect('dc.cliente_nombre');
        $this->_db_dM->addSelect('dp.producto_nombre || \' \' || dp.producto_presentacion as producto_nombre');
        //$this->_db_dM->addSelect('trazabilidad_codigo');
        $this->_db_dM->addSelect('to_char(fecha_vencimiento_alquiler, \'dd/mm/yyyy\') as fecha_vencimiento');
        $this->_db_dM->addSelect('abs(cantidad) AS cantidad');
        $this->_db_dM->addSelect('paciente_nombre');
        $this->_db_dM->addSelect('medico_nombre');
        $this->_db_dM->addSelect('datos_obras_sociales.obra_social_nombre');
        $this->_db_dM->addSelect('fecha_vencimiento_alquiler as vencimiento');
        $this->_db_dM->addSelect('domicilio_entrega');
        $this->_db_dM->addSelect('dem.lote_id');
        
        $this->_db_dM->addFrom('datos_movimientos dm');
        $this->_db_dM->addFrom('LEFT JOIN datos_vendedores dv USING (vendedor_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_distribuidores dd USING (distribuidor_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_pacientes USING (paciente_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_medicos USING (medico_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_obras_sociales ON (datos_obras_sociales.obra_social_id = datos_pacientes.obra_social_id )');
        //$this->_db_dM->addFrom('INNER JOIN movimientos_trazabilidad mt USING (id_movimiento)');
        //$this->_db_dM->addFrom('INNER JOIN datos_trazabilidad dt ON (mt.trazabilidad_id=dt.trazabilidad_id)');
        $this->_db_dM->addFrom('INNER JOIN detalle_movimientos dem USING (id_movimiento)');
        $this->_db_dM->addFrom('INNER JOIN datos_productos dp ON (dp.producto_id=dem.producto_id)');
        $this->_db_dM->addFrom('INNER JOIN datos_clientes dc ON (dc.cliente_id=dm.cliente_id)');
       	
        $this->_db_dM->addWhere('dm.id_movimiento = ' . $idMovimiento);
        $this->_db_dM->addWhere('dm.movimiento_finalizado = true ');
	
        $this->_db_dM->addGroup(' dm.remito_nro');
        $this->_db_dM->addGroup(', dv.vendedor_id');
        $this->_db_dM->addGroup(', dv.vendedor_mail');
        $this->_db_dM->addGroup(', dv.vendedor_nombre');
        $this->_db_dM->addGroup(', dd.distribuidor_id');
        $this->_db_dM->addGroup(', dd.distribuidor_mail');
        $this->_db_dM->addGroup(', dd.distribuidor_nombre');
        $this->_db_dM->addGroup(', dc.cliente_nombre');
        $this->_db_dM->addGroup(', dp.producto_nombre || \' \' || dp.producto_presentacion');
        $this->_db_dM->addGroup(', cantidad');
        //$this->_db_dM->addGroup(', trazabilidad_codigo');
        $this->_db_dM->addGroup(', fecha_vencimiento_alquiler ');
        $this->_db_dM->addGroup(', paciente_nombre');
        $this->_db_dM->addGroup(', medico_nombre');
        $this->_db_dM->addGroup(', datos_obras_sociales.obra_social_nombre');
        $this->_db_dM->addGroup(', domicilio_entrega');
        $this->_db_dM->addGroup(', dem.lote_id');

        $this->_db_dM->addOrderBy('fecha_vencimiento_alquiler ASC');
        
        $this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();exit;
        $rsVencimiento= $this->_db_dM->ejecutar();
        if(count($rsVencimiento) > 0 ){
            
            $vencimiento = $rsVencimiento[0];            
            $body= "<font color = blue><h3> " . utf8_decode('Confirmación') . " de carga </h3></font>";
            $body.= utf8_decode("Institución :") . utf8_decode($vencimiento['cliente_nombre']) . "<br>";
            $body.="Medico : " . $vencimiento['medico_nombre'] . " - Paciente :  " . utf8_decode($vencimiento['paciente_nombre']) . " - Obra Social : " . $vencimiento['obra_social_nombre'] . "<br>";
            $body.="Vendedor : " . $vencimiento['vendedor_nombre'] . "<br>"; 
            $body.="Distribuidor : " . $vencimiento['distribuidor_nombre'] .  "<br>";
            $body.="Remito : " . $vencimiento['remito_nro'] . "<br>";
            $body.="Vencimiento : " . $vencimiento['fecha_vencimiento'] ."<br><br>";
            
            foreach ($rsVencimiento as $productos){
                $body.="Producto : " . utf8_decode($productos['producto_nombre']) . " - Cantidad : " . $productos['cantidad']  . "<br>";
            }
            
            $bodyHTML= $body;
            $vencimiento['body'] = $bodyHTML;
            $vencimiento['host'] = 'smtp.gmail.com';
            $vencimiento['port'] = 587;
            $vencimiento['smtpsecure'] = 'tls';
            $vencimiento['username'] = 'shenvios@gmail.com';
            $vencimiento['password'] = 'diana2013';
            $vencimiento['from'] = "shenvios@gmail.com";
            $vencimiento['fromname'] = "Soluciones Hospitalarias " . utf8_decode('Confirmación'); 
            $vencimiento['addreplyto'][0]= array('address'=> 'shenvios@gmail.com', 'name'=>'SH Envios');
            $vencimiento['addaddress'][] = array('address' => 'egalimberti@shospitalarias.com.ar', 'name' => 'Ezequiel G.');   
            
            if($vencimiento['vendedor_mail']){
                $vencimiento['addaddress'][] = array('address' => $vencimiento['vendedor_mail'] ,'name' => $vencimiento['vendedor_nombre']); 
            }
            if($vencimiento['distribuidor_mail']){
                $vencimiento['addaddress'][] = array('address' => $vencimiento['distribuidor_mail'], 'name' => $vencimiento['distribuidor_nombre']);   
            }
            
            $vencimiento['subject'] =  utf8_decode('Confirmación') . " de envio";
            
            $email = new FrenteEmails();
            $email->enviarEmail($vencimiento);
            
            $email->clearAddresses();
            
        }
    }
}
/*
$mail = new MailConfirmacionPedido();
$mail->enviar(20);
*/
