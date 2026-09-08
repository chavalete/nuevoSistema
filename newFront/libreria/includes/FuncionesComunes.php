<?php

class  FuncionesComunes
{

    public function __construct(){
           setlocale(LC_MONETARY, 'es_AR');
    }


    public function parsearAutocompletar($campo){

	/**viene campo id primero luego pipe**/
	/**ejemplo 1|Cliente Lamborguini**/    

	$arrCampos = explode (")",$campo);
	
	return substr($arrCampos[0], 1);
    }
    public function convertirFormatoFechaAnmat($fecha){

	$ano= substr($fecha,0,4);
	$mes = substr($fecha,5,2);
	$dia = substr($fecha,8,2);
	$fecha = $dia ."/".$mes."/".$ano;
	return $fecha;
    }
    public function fechaFormatoDb($fecha){
        $ano= substr($fecha,6,4);
        $mes = substr($fecha,3,2);
        $dia = substr($fecha,0,2);
        $fecha= $ano."/".$mes ."/".$dia;
        return $fecha;
    }
    public function formatoMoneda($importe){
	    //return money_format('%(#2n', $importe);
	    //return number_format($importe, 2, ',', '.');
	    $importe= number_format($importe, 2, ',', '.');
        return "$".$importe;
	    /*$fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
        $n= $fmt->formatCurrency($importe,'usd');
        $numero=  "$" . substr($n,0,-3);
        return  $numero;*/
    }
    public function formatoMonedaExtranjera($importe){
        setlocale(LC_MONETARY, 'en_US');
        return money_format('%i', $importe);
    }

    public function parsearCodigoTrazabilidadEstandar($codigoEstandar){
	    unset($arrTraza);
        	
 	    if(substr($codigoEstandar, 0,3) == '+d2' || substr($codigoEstandar, 0,3) == ']d2' ){
                $arrTraza['tipo'] = 'datamatrixECC200'; 
                $cadena =  substr($codigoEstandar, 3);
            }elseif(substr($codigoEstandar, 0,3) == '+c0' || substr($codigoEstandar, 0,3) == ']c0' ){
                $arrTraza['tipo'] = 'code128'; 
                $cadena =  substr($codigoEstandar, 3);
            }else{
                $arrTraza['tipo'] = 'AA'; 
                $cadena = $codigoEstandar;
            }

            $cadenaAnterior = $cadena;
            while(strlen($cadena) > 0){
            
                if(substr($cadena, 0,3) == 414){
                    $arrTraza['gln'] = substr($cadena, 3,14); 
                    $cadena =  substr($cadena, 17);
                }
                
                if(substr($cadena, 0,2) == 01){
                    $arrTraza['gtin'] = substr($cadena, 2,14); 
                    $cadena =  substr($cadena, 16);
                }
                //RECORRER HASTA ENCONTRAR UN @ O FIN
                if(substr($cadena, 0,2) == 21){
                    $arrTraza['serie'] = substr($cadena, 2,7); 
                    $cadena =  substr($cadena, 9);
                }
                
                if(substr($cadena, 0,2) == 17){
                    $arrTraza['venc'] = substr($cadena, 2,6); 
                    $cadena =  substr($cadena, 8);
                }
                //RECORRER HASTA ENCONTRAR UN @ O FIN
                if(substr($cadena, 0,2) == 10){
                        $arrTraza['lote'] = substr($cadena, 2);
                        $cadena =  substr($cadena, (2 + strlen(substr($cadena, 2))));
                }
                
                if($cadena == $cadenaAnterior){
                    $cadena = NULL;
                }else{
                    $cadenaAnterior = $cadena;
                }
            }
            return $arrTraza;
    }
}
