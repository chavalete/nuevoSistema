<?php
#==============================================================================
define ("WSDL", "/var/www/html/newFront/afipPropia/wsaa.wsdl");     # The WSDL corresponding to WSAA
define ("CERT", "limonProduccion.crt");       # The X.509 certificate in PEM format
define ("PRIVATEKEY", "limon.key"); # The private key correspoding to CERT (PEM)
//define ("URL", "https://wsaahomo.afip.gov.ar/ws/services/LoginCms");
define ("URL", "https://wsaa.afip.gov.ar/ws/services/LoginCms");
#------------------------------------------------------------------------------
# You shouldn't have to change anything below this line!!!
#==============================================================================
function CreateTRA($SERVICE)

{
  //var_dump($SERVICE);exit;
  $TRA = new SimpleXMLElement(
    '<?xml version="1.0" encoding="UTF-8"?>' .
    '<loginTicketRequest version="1.0">'.
    '</loginTicketRequest>');
  $TRA->addChild('header');
  $TRA->header->addChild('uniqueId',date('U'));
  $TRA->header->addChild('generationTime',date('c',date('U')-60));
  $TRA->header->addChild('expirationTime',date('c',date('U')+120));
  $TRA->addChild('service',$SERVICE);
  $TRA->asXML('TRA.xml');
}
#==============================================================================
# This functions makes the PKCS#7 signature using TRA as input file, CERT and
# PRIVATEKEY to sign. Generates an intermediate file and finally trims the 
# MIME heading leaving the final CMS required by WSAA.
function SignTRA()
{
  $STATUS=openssl_pkcs7_sign("TRA.xml", "TRA.tmp", "file://".CERT,
    array("file://".PRIVATEKEY, PASSPHRASE),
    array(),
    !PKCS7_DETACHED
    );
  if (!$STATUS) {exit("ERROR generating PKCS#7 signature\n");}
  $inf=fopen("TRA.tmp", "r");
  $i=0;
  $CMS="";
  while (!feof($inf)) 
    { 
      $buffer=fgets($inf);
      if ( $i++ >= 4 ) {$CMS.=$buffer;}
    }
  fclose($inf);
#  unlink("TRA.xml");
  unlink("TRA.tmp");
  return $CMS;
}
#==============================================================================
function CallWSAA($CMS)
{
  $client=new SoapClient(WSDL, array(
          'soap_version'   => SOAP_1_2,
          'location'       => URL,
          'trace'          => 1,
          'exceptions'     => 0
          )); 
  $results=$client->loginCms(array('in0'=>$CMS));
  file_put_contents("request-loginCms.xml",$client->__getLastRequest());
  file_put_contents("response-loginCms.xml",$client->__getLastResponse());
  if (is_soap_fault($results)){
    var_dump($results);exit;
    exit("SOAP Fault: ".$results->faultcode."\n".$results->faultstring."\n");
  }else{
    /*echo "<pre>";
    print_r($results);
    echo  "\n Credenciales generadas";
    echo "</pre>";*/
  }
  return $results->loginCmsReturn;
}
#==============================================================================
function ShowUsage($MyPath)
{
  printf("Uso  : %s Arg#1 Arg#2\n", $MyPath);
  printf("donde: Arg#1 debe ser el service name del WS de negocio.\n");
  printf("  Ej.: %s wsfe\n", $MyPath);
}
#==============================================================================
ini_set("soap.wsdl_cache_enabled", "0");
if (!file_exists(CERT)) {exit("Failed to open ".CERT."\n");}
if (!file_exists(PRIVATEKEY)) {exit("Failed to open ".PRIVATEKEY."\n");}
if (!file_exists(WSDL)) {exit("Failed to open ".WSDL."\n");}
if ( $argv < 2 ) {ShowUsage($argv[0]); exit();}
$argv[1]="wsfe";
$SERVICE=$argv[1];
//echo $SERVICE;exit;
CreateTRA($SERVICE);
$CMS=SignTRA();
$TA=CallWSAA($CMS);
//var_dump($TA);exit;
if (!file_put_contents("/var/www/html/trazabilidadCorvision/afipPropia/TA.xml", $TA)) {exit();}
?>
