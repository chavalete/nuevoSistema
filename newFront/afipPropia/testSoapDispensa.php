<?php
//require_once '/var/www/html/afipPropia/FacturaElectronica.php';
require_once '/var/www/html/trazabilidadCorvision/afipPropia/soap/FrenteAfip.php';

//var_dump($parametros);exit;
$parametros['factura_puesto_venta'] = 3;
$parametros['factura_tipo'] = 1;
$objSoap = NEW FrenteAfip();
$objSoap->cargarCredenciales();
$objSoap->traerUltimoComprobante($parametros);
var_dump($respuesta);exit;
foreach ($objSoap->getUltimaRespuesta() AS $respuesta){
	//echo $respuesta->FECompUltimoAutorizadoResult['CbteNro'];exit;
	$nroComprobante =  $respuesta->CbteNro + 1;
	//var_dump($respuesta);exit;
}
$data = array(
	'Auth'=>$parametros['Auth'],
	'FeCAEReq'=>array(
			'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
			'PtoVta' 	=> 5,  // Punto de venta
			'CbteTipo' 	=> 6  // Tipo de comprobante (ver tipos disponibles)
	),
	'FECAEDetRequest'=> array(
	'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
	'DocTipo' 	=> 99, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
	'DocNro' 	=> 0,  // Número de documento del comprador (0 consumidor final)
	'CbteDesde' 	=> $nroComprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
	'CbteHasta' 	=> $nroComprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
	'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
	'ImpTotal' 	=> 121, // Importe total del comprobante
	'ImpTotConc' 	=> 0,   // Importe neto no gravado
	'ImpNeto' 	=> 100, // Importe neto gravado
	'ImpOpEx' 	=> 0,   // Importe exento de IVA
	'ImpIVA' 	=> 21,  //Importe total de IVA
	'ImpTrib' 	=> 0,   //Importe total de tributos
	'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
	'MonCotiz' 	=> 1,
    'CondicionIVAReceptorId'=>4,// Cotización de la moneda usada (1 para pesos argentinos)
	'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
			array(
				'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles)
				'BaseImp' 	=> 100, // Base imponible
				'Importe' 	=> 21 // Importe
			)
		),
	)
);
//var_dump($data);exit;
$objSoap->solicitarCAE($data);
foreach ($objSoap->getUltimaRespuesta() AS $respuesta){
	foreach($respuesta->FeDetResp AS $data){
		if($data->Resultado=='A'){
			echo "\n CAE: ";
			echo $data->CAE;
			echo "\n CAEVto: ";
			echo $data->CAEFchVto;
		}else{
			foreach ($data->Observaciones AS $error){
				echo $error->Code;
				echo $error->Msg;
			}
		}
	}
}
