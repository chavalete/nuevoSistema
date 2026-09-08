<?php
require_once '/var/www/html/trazabilidadCorvision/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once '/var/www/html/trazabilidadCorvision/afipPropia/soap/FrenteAfip.php';


class ncElectronica{

    private $_db;
    private $objAfip;

    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
        $this->FrenteAfip =  NEW FrenteAfip();
    
    }
    public function enviarNc($facturaId){
    
    $qry="SELECT
            datos_facturas.factura_id,
            datos_facturas.factura_total_fact AS total_factura,
            datos_facturas.factura_total_iva AS total_iva,
            datos_facturas.factura_total_fact-datos_facturas.factura_total_iva  AS total_neto,
            datos_facturas.factura_puesto_venta::integer,
            datos_facturas.factura_tipo,
            to_char(datos_facturas.factura_fecha,'YYYYMMDD') AS factura_fecha,cliente_nombre,
            cliente_calle || ' ' ||  cliente_altura || ' ' || localidad_nombre AS direccion,
            cliente_cuit,
            datos_facturas.observaciones,
            condicion_iva_id,
            df2.factura_nro_afip,
            df2.factura_tipo AS factura_tipo_origen,
            df2.factura_puesto_venta AS factura_puesto_venta_origen,
            CASE WHEN condicion_iva_id = 5 THEN '99' ELSE '80' END AS tipo_doc
        FROM
            datos_facturas
            INNER JOIN datos_factura_electronica USING(factura_id)
            INNER JOIN datos_clientes USING(cliente_id)
            INNER JOIN datos_localidades ON datos_clientes.cliente_loca_id = localidad_id
            INNER JOIN datos_facturas df2 ON datos_facturas.de_factura_id = df2.factura_id
        WHERE
	    factura_aprobada=false
	    AND datos_facturas.factura_id = $facturaId
	    AND datos_facturas.factura_tipo IN (3,8) ORDER BY factura_id LIMIT 1;";
    
    //echo $qry;exit;
    $this->_db->setQry($qry);
    $resultado=$this->_db->ejecutar();
    if(count($resultado)==0){
        echo "sin registros";exit;
    }
    $this->FrenteAfip->cargarCredenciales();
    foreach($resultado AS $detalle){
            $arrIVA = $this->calcularAlicuotas($detalle['factura_id']);
            $detalle['factura_nro']=$this->FrenteAfip->traerUltimoComprobante($detalle);
       
            $factura = array(
                'FeCAEReq'=>
                array(
                    'CantReg' => 1,                     // Cantidad de detalles
                    'PtoVta' => $detalle['factura_puesto_venta'],
                    'CbteTipo' => $detalle['factura_tipo'],              // 1: FCA, 2: NDA, 3:NCA, 6: FCB, 11: FCC
                ),
                'FECAEDetRequest'=> array(
                    'Concepto' => 1,               // 1: Productos, 2: Servicios, 3/4: Ambos
                    'DocTipo' => $detalle['tipo_doc'],
                    //'DocTipo' => 80,              // 96: DNI, 80: CUIT, 99: Consumidor Final
                    'DocNro' => str_replace("-","",$detalle['cliente_cuit']),    // Nro. de CUIT o DNI
                    'CbteDesde' 	=> $detalle['factura_nro'],  // Nro de facura a enviar
                    'CbteHasta' 	=> $detalle['factura_nro'],  // Nro de facura a enviar
                    'CbteFch' => $detalle['factura_fecha'],   // Formato AAAAMMDD
                    // importes subtotales generales:
                    'ImpNeto' => $detalle['total_neto'],            // neto gravado
                    'ImpOpEx' => 0,             // operacioens exentas
                    'ImpTotConc' => 0,          // no gravado
                    'ImpIVA' => $detalle['total_iva'],              // IVA liquidado
                    'ImpTrib' => 0,              // otros tributos
                    'ImpTotal' => $detalle['total_factura'],// total de la factura
                    'MonCotiz' => 1,   // 1 para pesos
                    'MonId' => 'PES',  // 'PES': pesos, 'DOL': dolares (solo exportacion)
                    'CondicionIVAReceptorId'=>$detalle['condicion_iva_id'],
                    'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados
                    array(
                        'Tipo' 		=> $detalle['factura_tipo_origen'], // Tipo de comprobante (ver tipos disponibles)
                        'PtoVta' 	=> $detalle['factura_puesto_venta_origen'], // Punto de venta
                        'Nro' 		=> $detalle['factura_nro_afip'], // Numero de comprobante
                        'Cuit' 		=> str_replace("-","",$detalle['cliente_cuit']) // (Opcional) Cuit del emisor del comprobante
                        )
                    ),
                    'Iva' =>
                        $arrIVA
                )
            );
        //print_r($factura);exit;
        $this->FrenteAfip->solicitarCAE($factura);
        //var_dump($this->FrenteAfip->getUltimaRespuesta());exit;
        foreach ($this->FrenteAfip->getUltimaRespuesta() AS $respuesta){
            //var_dump($respuesta);exit;
            if(is_object($respuesta->Errors)){
                foreach($respuesta->Errors AS $error){
                    /*echo $error->Code;
                    echo $error->Msg;*/
                    $this->_db->addCamposUpdate('factura_enviada=  true ');
                    $this->_db->addCamposUpdate('factura_enviada_fecha_hora= now()');
                    $this->_db->addCamposUpdate('observaciones= \'' . $error->Code .'\'');
                    $this->_db->addCamposUpdate('error_desc = \'' . $error->Msg .'\'');
                    $this->_db->addFrom('datos_factura_electronica');
                    $this->_db->addWhere('factura_id = \'' . $detalle['factura_id'] .'\'');
                    $this->_db->generarUpdate();
                    //$qry=$this->_db->getQry();
                    //echo $this->_db->getQry($qry);
                    //echo $qry;exit;
                    $this->_db->ejecutar();

                    if(count($this->_db->getArrError()) > 0){
                        echo "Error al salvar los datos";
                    }else{
                        echo "Error en envio";
                    }
                }
            }
            foreach($respuesta->FeDetResp AS $data){
                if($data->Resultado=='A'){
                    /*echo "\n CAE: ";
                    echo $data->CAE;
		    echo "\n CAEVto: ";*/
                    //echo $data->CAEFchVto;
                    $this->_db->addCamposUpdate('factura_cae= \'' . $data->CAE .'\'');
                    $this->_db->addCamposUpdate('vencimiento_cae= \'' . $data->CAEFchVto .'\'');
                    if($detalle['condicion_iva_id']==1 || $detalle['condicion_iva_id']==6){
                        $facturaNro ='A0005-' . str_pad($detalle['factura_nro'], 8, "0", STR_PAD_LEFT);
                    }else{
                        $facturaNro ='B0005-' . str_pad($detalle['factura_nro'], 8, "0", STR_PAD_LEFT);
                    }
                    $this->_db->addCamposUpdate('factura_nro = \'' . $facturaNro .'\'');
                    $this->_db->addCamposUpdate('factura_enviada= \'' . true .'\'');
                    $this->_db->addCamposUpdate('factura_nro_afip= \'' . $detalle['factura_nro'] .'\'');
                    $this->_db->addFrom('datos_facturas');
                    $this->_db->addWhere('factura_id = \'' . $detalle['factura_id'] .'\'');
                    $this->_db->generarUpdate();
                    $qry=$this->_db->getQry();
            //echo $qry;exit;

                    $this->_db->addCamposUpdate('factura_aprobada= true');
                    $this->_db->addCamposUpdate('factura_enviada=  true');
                    $this->_db->addCamposUpdate('factura_enviada_fecha_hora= now()');
            //$this->_db->addCamposUpdate('observaciones= \'' . $facturas[0]['motivos_obs'] .'\'');
                    $this->_db->addCamposUpdate('observaciones= NULL');
                    $this->_db->addCamposUpdate('error_desc = NULL');
                    $this->_db->addFrom('datos_factura_electronica');
                    $this->_db->addWhere('factura_id = \'' . $detalle['factura_id'] .'\'');
                    $this->_db->generarUpdate();
                    $qry.=$this->_db->getQry();
                    $this->_db->setQry($qry);
                    $this->_db->ejecutarTransaccion();
                    //echo $qry;
                    if(count($this->_db->getArrError()) > 0){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    foreach ($data->Observaciones AS $error){
                        $this->_db->addCamposUpdate('factura_enviada=  true ');
                        $this->_db->addCamposUpdate('factura_enviada_fecha_hora= now()');
                        $this->_db->addCamposUpdate('observaciones= \'' . $error->Code .'\'');
                        $this->_db->addCamposUpdate('error_desc = \'' . $error->Msg .'\'');
                        $this->_db->addFrom('datos_factura_electronica');
                        $this->_db->addWhere('factura_id = \'' . $detalle['factura_id'] .'\'');
                        $this->_db->generarUpdate();
                        //$qry=$this->_db->getQry();
                        //echo $this->_db->getQry($qry);
                        //echo $qry;exit;
                        $this->_db->ejecutar();

                        if(count($this->_db->getArrError()) > 0){
                            return false;
                        }else{
                            return false;
                        }
                    }
                }
            }
        }
        //echo "frena";exit;
        }
    }
    public function calcularAlicuotas($facturaId){
        $qryIVA="SELECT sum(importe_iva) AS iva_detalle, sum(importe_detalle - importe_iva) AS base_imp, CASE WHEN producto_iva = 21 THEN '5' ELSE '4' END AS alicuota_id FROM detalle_facturas INNER JOIN datos_productos USING(producto_id) WHERE factura_id =$facturaId GROUP BY CASE WHEN producto_iva = 21 THEN '5' ELSE '4' END;";
            //echo $qryIVA;
        $this->_db->setQry($qryIVA);
        $resultado = $this->_db->ejecutar();

        foreach ($resultado AS $detalle){
            $arr[] = array(
                    'Id' => $detalle['alicuota_id'],
                    'BaseImp' => $detalle['base_imp'],
                    'Importe' => $detalle['iva_detalle']
                );
        }
        return $arr;
    }
}
/*$f= new ncElectronica();
$f->enviarNc(184);*/
?>
