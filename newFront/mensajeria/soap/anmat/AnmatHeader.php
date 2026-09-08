<?php

class anmatHeader extends SoapHeader
{
    private $_usuarioTransporte = 'testwservice';
    private $_passTransporte = 'testwservicepsw';
    private $_autoriHeader;
    private $_autoriVars;
    
    public function __construct() {
        
        $this->_autoriHeader = sprintf( file_get_contents('/var/www/html/melmac/soap/anmat/archivo_wsse.wsse'), 
                                        htmlspecialchars($this->_usuarioTransporte), 
                                        htmlspecialchars($this->_passTransporte)
                                      );
        $this->_autoriVars = new SoapVar($this->_autoriHeader ,XSD_ANYXML);

        SoapHeader::SoapHeader( 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
                                "Security",
                                $this->_autoriVars
                              );
    }
    
}

?>
