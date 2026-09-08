<?php
require_once "Email.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/newFront/libreria/error_2.0/FrenteError.php";
/**
 * Description of FrenteEmails
 *
 * @author Guillo
 */

class FrenteEmails {
    
    private $_email;

    public function enviarEmail($arrEmail){
        $this->_email = new Email();
        return $this->_email->enviarEmail($arrEmail);
    }
    
    public function clearAddresses(){
        $this->_email->clearAddresses();
    }
}

