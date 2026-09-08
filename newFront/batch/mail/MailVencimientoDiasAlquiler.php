<?php
$_SERVER['DOCUMENT_ROOT']='/var/www/html';
date_default_timezone_set('America/Buenos_Aires');
require_once '/var/www/html/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once  '/var/www/html/newFront/mail/FrenteEmails.php';

/**
 * Description of mailRecepcionesNoPencientes
 *
 * @author root
 */

class MailVencimientoDiasAlquiler {
    
    private $_db_dM;
    
    public function __construct() {
        $this->_db_dM=new FrenteAlmacenamiento();
    }
    
    public function comenzar(){
        
        $this->_db_dM->addSelect('dm.remito_nro');
        $this->_db_dM->addSelect('dv.vendedor_id');
        $this->_db_dM->addSelect('dv.vendedor_mail');
        $this->_db_dM->addSelect('dv.vendedor_nombre');
        $this->_db_dM->addSelect('dd.distribuidor_id');
        $this->_db_dM->addSelect('dd.distribuidor_mail');
        $this->_db_dM->addSelect('dd.distribuidor_nombre');
        $this->_db_dM->addSelect('dc.cliente_nombre');
        $this->_db_dM->addSelect('dp.producto_nombre || \' \' || dp.producto_presentacion as producto_nombre');
        $this->_db_dM->addSelect('trazabilidad_codigo');
        $this->_db_dM->addSelect('to_char(fecha_vencimiento_alquiler, \'dd/mm/yyyy\') as fecha_vencimiento');
        $this->_db_dM->addSelect('paciente_nombre');
        $this->_db_dM->addSelect('medico_nombre');
        $this->_db_dM->addSelect('datos_obras_sociales.obra_social_nombre');
        $this->_db_dM->addSelect('fecha_vencimiento_alquiler as vencimiento');
        $this->_db_dM->addSelect('domicilio_entrega');
        $this->_db_dM->addFrom('datos_movimientos dm');
        $this->_db_dM->addFrom('LEFT JOIN datos_vendedores dv USING (vendedor_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_distribuidores dd USING (distribuidor_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_pacientes USING (paciente_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_medicos USING (medico_id)');
        $this->_db_dM->addFrom('LEFT JOIN datos_obras_sociales ON (datos_obras_sociales.obra_social_id = datos_pacientes.obra_social_id )');
        $this->_db_dM->addFrom('INNER JOIN movimientos_trazabilidad mt USING (id_movimiento)');
        $this->_db_dM->addFrom('INNER JOIN datos_trazabilidad dt ON (mt.trazabilidad_id=dt.trazabilidad_id)');
        $this->_db_dM->addFrom('INNER JOIN datos_productos dp ON (dp.producto_id=dt.producto_id)');
        $this->_db_dM->addFrom('INNER JOIN datos_clientes dc ON (dc.cliente_id=dm.cliente_id)');
       	$this->_db_dM->addWhere('fecha_vencimiento_alquiler <= now()::date + 10');
        $this->_db_dM->addWhere('dp.alquilable=true ');
        $this->_db_dM->addWhere('mt.finalizado=false');
        $this->_db_dM->addWhere('dm.movimiento_finalizado = true ');
	//$this->_db_dM->addWhere('');
        $this->_db_dM->addGroup(' dm.remito_nro');
        $this->_db_dM->addGroup(', dv.vendedor_id');
        $this->_db_dM->addGroup(', dv.vendedor_mail');
        $this->_db_dM->addGroup(', dv.vendedor_nombre');
        $this->_db_dM->addGroup(', dd.distribuidor_id');
        $this->_db_dM->addGroup(', dd.distribuidor_mail');
        $this->_db_dM->addGroup(', dd.distribuidor_nombre');
        $this->_db_dM->addGroup(', dc.cliente_nombre');
        $this->_db_dM->addGroup(', dp.producto_nombre || \' \' || dp.producto_presentacion');
        $this->_db_dM->addGroup(', trazabilidad_codigo');
        $this->_db_dM->addGroup(', fecha_vencimiento_alquiler ');
        $this->_db_dM->addGroup(', paciente_nombre');
        $this->_db_dM->addGroup(', medico_nombre');
        $this->_db_dM->addGroup(', domicilio_entrega');
        $this->_db_dM->addGroup(', datos_obras_sociales.obra_social_nombre');
        $this->_db_dM->addOrderBy('fecha_vencimiento_alquiler ASC');
        $this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();exit;
        $rsVencimiento= $this->_db_dM->ejecutar();
        if(count($rsVencimiento)> 0 ){
            //para mandar el mail interno
            $bodyHTML=NULL;
            $titulo ='<h1>Bombas ya vencidas</h1>';
            $fontColor="<font color=red>";
            $entro = 0;

            foreach ($rsVencimiento AS $vencimiento){
                if(date('Y-m-d') < date($vencimiento['vencimiento']) && !$entro ){
                    $titulo ='<h1>Bombas por vencer </h1>';
                    $fontColor="<font color=blue>";
                    $entro = 1;
                }
                $body= $titulo;
                $body.=$fontColor . "<h3>" . $vencimiento['fecha_vencimiento'] . "</h3></font>";
                $body.= utf8_decode("Cliente :") . $vencimiento['cliente_nombre'] . "<br>";
                $body.="Medico : " . $vencimiento['medico_nombre'] . " - Paciente :  " . $vencimiento['paciente_nombre'] . " - Obra Social : " . $vencimiento['obra_social_nombre'] . "<br>";
                $body.="Vendedor : " . $vencimiento['vendedor_nombre']  . "  -  Distribuidor : " . $vencimiento['distribuidor_nombre'] .  "<br>";
                $body.="Producto : " . utf8_decode($vencimiento['producto_nombre']) . " - Traza : " . $vencimiento['trazabilidad_codigo']  . "<br>";
                $body.="Remito : " . $vencimiento['remito_nro'] . "<br>";
                $body.="Dir. Entrega : " . $vencimiento['domicilio_entrega'];
                $bodyHTML.= $body;
                
		if($vencimiento['vendedor_mail']){
                    $vencimientosXVendedor[$vencimiento['vendedor_id']][]=$vencimiento; 
                }
                if($vencimiento['distribuidor_mail']){
                    $vencimientosXDistribuidor[$vencimiento['distribuidor_id']][]=$vencimiento;                     
                }
                
                $body=NULL;
                $titulo = NULL;
                
            }
            /*
            echo "<pre>";
            print_r($vencimientosXVendedor);
            echo "</pre>";
            exit;
             * 
             */
            $vencimiento['body'] = $bodyHTML;
            $vencimiento['host'] = 'smtp.gmail.com';
            $vencimiento['port'] = 587;
            $vencimiento['smtpsecure'] = 'tls';
            $vencimiento['username'] = 'shbombavencimiento@gmail.com';
            $vencimiento['password'] = 'diana2013';
            $vencimiento['from'] = "shbombavencimiento@gmail.com";
            $vencimiento['fromname'] = "Soluciones Hospitalarias"; 
            $vencimiento['addreplyto'][]= array('address'=> 'shbombasvencimiento@gmail.com', 'name'=>'SH Bombas Venc');
            $vencimiento['addaddress'][] = array('address' => 'belen@shospitalarias.com.ar', 'name' => 'Belen');   
            $vencimiento['addaddress'][] = array('address' => 'irene@shospitalarias.com.ar' ,'name' => 'Irene'); 
            $vencimiento['addaddress'][] = array('address' => 'mariana@shospitalarias.com.ar', 'name' => 'Mariana');   
            $vencimiento['addaddress'][] = array('address' => 'egalimberti@shospitalarias.com.ar', 'name' => 'Ezequiel');   
            //$vencimiento['addaddress'][] = array('address' => 'glacanna@dmelmac.com.ar', 'name' => 'Guillo');   
            $vencimiento['subject'] = "Reporte Vencimiento Bombas";
            
            $email = new FrenteEmails();
            $email->enviarEmail($vencimiento);
            
            $email->clearAddresses();
            $vencimiento['addaddress']=array();
            
            //para los vendedores
            if(count($vencimientosXVendedor) > 0){
                foreach($vencimientosXVendedor AS $key => $xVendedor){
                    $bodyHTML=NULL;
                    $titulo ='<h1>Bombas ya vencidas</h1>';
                    $entro = 0;

                    foreach ($xVendedor AS $vendedor){
                        if(date('Y-m-d') < date($vendedor['vencimiento']) && !$entro ){
                            $titulo ='<h1>Bombas por vencer </h1>';
                            $fontColor="<font color=blue>";
                            $entro = 1;
                        }
                        $body= $titulo;
                        $body.= $fontColor  . "<h3>" . $vendedor['fecha_vencimiento'] . "</h3></font> <br> ";
                        $body.="Cliente :" . $vendedor['cliente_nombre'] . " - Paciente :  " . $vendedor['paciente_nombre'] . "<br>";
                        $body.="Medico : " . $vendedor['medico_nombre'] . " - Obra Social : " . $vendedor['obra_social_nombre'] . "<br>";
                        $body.="Distribuidor : " . $vendedor['distribuidor_nombre'] . " - Vendedor : " . $vendedor['vendedor_nombre'] . "<br>";
                        $body.="Dir. entrega : " . $vendedor['domicilio_entrega'] . "<br>"; 
                        $body.="Producto : " . utf8_decode($vendedor['producto_nombre']) . " - Traza : " . $vendedor['trazabilidad_codigo']  . "<br><br>";

                        $bodyHTML.= $body;

                        $body=NULL;
                        $titulo = NULL;
                    }

                    $vencimiento['body'] = $bodyHTML;
                    $vencimiento['addaddress'][] = array('address' => $xVendedor[0]['vendedor_mail'],
                                                         'name' => $xVendedor[0]['vendedor_nombre']);
                    if($key == 4 || $key == 7 || $key == 8 || $key == 6 || $key == 3 || $key == 2 || $key == 1 || $key == 10 ){ // 4,7,8,6,3,2,1,10 ->solicitado por mail
                        $vencimiento['addaddress'][] = array('address' => 'irene@shospitalarias.com.ar' ,'name' => 'Irene');   
			$vencimiento['addaddress'][] = array('address' => 'mariana@shospitalarias.com.ar' ,'name' => 'Mariana');
			$vencimiento['addaddress'][] = array('address' => 'ramiro@shospitalarias.com.ar' ,'name' => 'Ramiro');

                    }
                    //$vencimiento['addaddress'][] = array('address' => 'guillolacanna@gmail.com', 'name' => 'Guillo');   
                    $email->enviarEmail($vencimiento);
                    $vencimiento['addaddress']=array();

                }
            }
            $email->clearAddresses();
            $vencimiento['addaddress']=array();
            
            //para los distribuidores
            
            if(count($vencimientosXDistribuidor) > 0){
                foreach($vencimientosXDistribuidor AS $key => $xDistribuidor){
                    $bodyHTML=NULL;
                    $titulo ='<h1>Bombas ya vencidas</h1>';
                    $fontColor="<font color=red>";
                    $entro = 0;

                    foreach ($xDistribuidor AS $distribuidor){
                        if(date('Y-m-d') < date($distribuidor['vencimiento']) && !$entro ){
                            $titulo ='<h1>Bombas por vencer </h1>';

                            $entro = 1;
                        }
                        $body= $titulo;
                        $body.="<h2>" . $distribuidor['fecha_vencimiento'] . "</h2> <br> ";
                        $body.="Cliente :" . $distribuidor['cliente_nombre'] . " - Paciente :  " . $distribuidor['paciente_nombre'] . "<br>";
                        $body.="Medico : " . $distribuidor['medico_nombre'] . " - Obra Social : " . $distribuidor['obra_social_nombre'] . "<br>";
                        $body.="Distribuidor : " . $distribuidor['distribuidor_nombre'] . " - Vendedor : " . $distribuidor['vendedor_nombre'] . "<br>";
                        $body.="Dir. entrega : " . $distribuidor['domicilio_entrega'] . "<br><br>"; 
                        $body.="Producto : " . utf8_decode($distribuidor['producto_nombre']) . " - Traza : " . $distribuidor['trazabilidad_codigo']  . "<br>";

                        $bodyHTML.= $body;

                        $body=NULL;
                        $titulo = NULL;
                    }

                    $vencimiento['body'] = $bodyHTML;
                    $vencimiento['addaddress'][] = array('address' => $xDistribuidor[0]['distribuidor_mail'],
                                                         'name' => $xDistribuidor[0]['distribuidor_nombre']);
		    if($key == 8 || $key == 9){ // 9,8 ->solicitado por mail
                        $vencimiento['addaddress'][] = array('address' => 'irene@shospitalarias.com.ar' ,'name' => 'Irene');
			$vencimiento['addaddress'][] = array('address' => 'mariana@shospitalarias.com.ar' ,'name' => 'Mariana');
                        $vencimiento['addaddress'][] = array('address' => 'ramiro@shospitalarias.com.ar' ,'name' => 'Ramiro');
                    }
                    //$vencimiento['addaddress'][] = array('address' => 'guillolacanna@gmail.com', 'name' => 'Guillo');
		    echo $email->enviarEmail($vencimiento);
                    $vencimiento['addaddress']=array();
                }
            }    
            return;
        }else{
            //echo "Sin avisos de vencimiento que enviar. \n ";
            return;
        }
    }        
}

$mail = new MailVencimientoDiasAlquiler();
$mail->comenzar();
