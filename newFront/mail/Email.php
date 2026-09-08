<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/PHPMailer/class.phpmailer.php';
/**
 * Description of eMail
 *
 * @author Guillo
 */
class Email {
    
    private $_db_dM;
    private $_emailId;
    private $_arrError = array();
    private $_mail;


    public function setArrError($arrError){
        $this->_arrError[] = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError;
    }

    public function setEmailId($id){
        $this->_emailId = $id;
    }
    public function getEmailId(){
        return $this->_emailId;
    }
       
    public function __construct() {
        $this->_db_dM= new FrenteAlmacenamiento();
        $this->_mail = new PHPMailer();
        $this->_mail->isSMTP();
        $this->_mail->SMTPDebug = 0;
        $this->_mail->Debugoutput = 'html';
        $this->_mail->SMTPAuth = true;

    }

    public function enviarEmail($arrEmail){
        //print_r($arrEmail);exit;
        $arrError = array();
        if(!$arrEmail['host']){
            $arrError['detalle'] = 'Se necesita un host para el smtp(host)';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            $this->_mail->Host = $arrEmail['host'];  
        }
        
        if(!$arrEmail['username']){
            $arrError['detalle'] = 'Se necesita saber el nombre de usuario para el smtp(username)';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            $this->_mail->Username = $arrEmail['username'];  
        }
        
        if(!$arrEmail['password']){
            $arrError['detalle'] = 'Se necesita la pass del usuario para el smtp(password)';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            $this->_mail->Password = $arrEmail['password'];  
        }
        
        if(!$arrEmail['smtpsecure']){
            $arrError['detalle'] = 'Se necesita saber si usa smtpsecure el smtp(smtpsecure)';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            $this->_mail->SMTPSecure = $arrEmail['smtpsecure'];  
        }
        
        if(!$arrEmail['subject']){
            $arrError['detalle'] = 'Se necesita saber el asunto del email (subjet)';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            $this->_mail->Subject = $arrEmail['subject'];
        }
        
        if(!$arrEmail['from']){
            $arrError['detalle'] = 'Se necesita saber quien lo envia (from)';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            $this->_mail->From = $arrEmail['from'];  
        }
        $this->_mail->FromName = $arrEmail['fromname'];  
        
        if(count($arrEmail['addaddress']) == 0 ){
            $arrError['detalle'] = 'Se necesita saber a quien va dirigido ( addAddress[0](address,name) )';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            foreach($arrEmail['addaddress'] as $arrTo){
                $this->_mail->addAddress($arrTo['address'], $arrTo['name']);  
            }
        }
        
        if(count($arrEmail['addreplyto']) == 0 ){
            $arrError['detalle'] = 'Se necesita saber a quien va dirigida la respuesta ( addreplyto[0](address,name) )';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            foreach($arrEmail['addaddress'] as $arrTo){
                $this->_mail->addReplyTo($arrTo['address'], $arrTo['name']);
            }
        }
        
        if(!$arrEmail['body']){
            $arrError['detalle'] = 'Se necesita saber el cuerpo del email (body)';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }else{
            $this->_mail->msgHTML($arrEmail['body']);
        }
        
        if(count($this->getArrError()) > 0 ){
            var_dump($this->getArrError());
            return;
        }
        
        if(!$this->_mail->send()) {
            echo ' Error: ' . $this->_mail->ErrorInfo;
            $arrEmail['enviado'] = 'false';
            $arrEmail['mensaje_error'] = $this->_mail->ErrorInfo;
            //exit;
        }else {
            //echo "enviado!!" ;// exit;
            $arrEmail['enviado']= 'true';
            $arrEmail['mensaje_error'] = 'null';
            
        }
               
        $this->salvarMe($arrEmail);
    }
    
    private function salvarMe($arrEmail){
        $this->_db_dM->addSelect('seq_emails_email_id');
        $this->_db_dM->generarProximo();
        $resultado = $this->_db_dM->ejecutar();
        $this->setEmailId($resultado[0]['nextval']);
        
        foreach($arrEmail['addaddress'] as $To){
            $arrTo[] = $To['address'];            
        }
        
        
        $this->_db_dM->addCamposTabla('email_id');
        $this->_db_dM->addCamposTabla('emisor');
        $this->_db_dM->addCamposTabla('email_emisor');
        $this->_db_dM->addCamposTabla('email_receptor');
        $this->_db_dM->addCamposTabla('asunto');
        $this->_db_dM->addCamposTabla('cuerpo');
        $this->_db_dM->addCamposTabla('cabecera');
        $this->_db_dM->addCamposTabla('enviado');
        $this->_db_dM->addCamposTabla('mensaje_error');
        
        $this->_db_dM->addCamposValue($this->getEmailId());
        $this->_db_dM->addCamposValue("'". $this->_mail->FromName . "'");
        $this->_db_dM->addCamposValue("'" . $this->_mail->From. "'");
        $this->_db_dM->addCamposValue("'" . implode(",", $arrTo) . "'");
        $this->_db_dM->addCamposValue("'" . $this->_mail->Subject . "'");
        $this->_db_dM->addCamposValue("'" . $this->_mail->Body . "'");
        $this->_db_dM->addCamposValue("null");
        $this->_db_dM->addCamposValue($arrEmail['enviado']);
        $this->_db_dM->addCamposValue($arrEmail['mensaje_error']);
        
        $this->_db_dM->addFrom('emails');
        
        $this->_db_dM->generarInsert();
        
	//echo $this->_db_dM->getQry();exit;
        $this->_db_dM->ejecutar();
     
    }
    
    private function cargarMe(){
        //x codificar
    }
    
    public function clearAddresses(){
        $this->_mail->clearAddresses();
    }
}   