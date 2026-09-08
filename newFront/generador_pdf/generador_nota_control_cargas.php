<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'Barcode.php';
ob_end_clean();
require_once 'fpdf.php';


$db = NEW frenteAlmacenamiento();

$db->addSelect('cliente_nombre');
$db->addSelect('cliente_direccion');
$db->addSelect('cliente_cuit');
$db->addSelect('cliente_telefono');
$db->addSelect('cant_dias_alquiler');
$db->addSelect('dm.obs');
$db->addSelect('CASE WHEN cliente_resp_insc THEN \'RESPONSABLE INSCRIPTO\' ELSE \'Exento\' END AS condicion_iva');
$db->addSelect('domicilio_entrega AS direccion_entrega');
$db->addSelect('medico_nombre');
$db->addSelect('paciente_nombre');
//$db->addSelect('CASE WHEN factura_nro IS NULL THEN \'-------\' ELSE factura_nro END AS factura_nro');
//$db->addSelect('CASE WHEN remito_nro IS NULL THEN \'-------\' ELSE remito_nro END AS remito_nro');
$db->addSelect('to_char(movimiento_fecha_hora::date,\'DD / MM / YYYY\') AS movimiento_fecha');
$db->addSelect('condicion_venta_nombre');
$db->addSelect('cliente_forma_pago');
$db->addFrom('datos_movimientos dm');
$db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
$db->addFrom('LEFT JOIN datos_medicos USING(medico_id)');
$db->addFrom('LEFT JOIN datos_pacientes USING(paciente_id)');
$db->addFrom('LEFT JOIN datos_condicion_venta USING( condicion_venta_id)');
$db->addWhere('id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
$db->generarSelect();
$arrCabecera =  $db->ejecutar();
//echo $db->getQry();exit;
if(count($arrCabecera) ==0){
    echo "<h2>Sin registros para {$_GET['crIdMovimiento']} ";
    exit;
}

//var_dump($arrCabecera); exit;
$pdf = new FPDF ('P','mm',array(210,297));
$pdf->AddPage();

//CABECERA

//$pdf->SetFont('Arial','B',14);
//$pdf->Text(120,15, " - " . str_pad($arrCabecera[0]['remito_nro'] , 8, "0", STR_PAD_LEFT));

$pdf->SetFont('Arial','',10);
$pdf->Text(158,40, " " . $arrCabecera[0]['movimiento_fecha'] );

$pdf->SetFont('Arial','B',10);
$pdf->Text(30,47, " " . utf8_decode($arrCabecera[0]['cliente_nombre']) );
$pdf->SetFont('Arial','',10);
$pdf->Text(30,51, " " . utf8_decode($arrCabecera[0]['cliente_direccion']) );
$pdf->Text(30,56, " " . utf8_decode($arrCabecera[0]['cliente_telefono']) );

$pdf->Text(30,80, " " . $arrCabecera[0]['condicion_iva'] );
$pdf->Text(145,80, " " . $arrCabecera[0]['cliente_cuit'] );

$pdf->Text(60,90, " " . utf8_decode($arrCabecera[0]['cliente_forma_pago'] . " " . $arrCabecera[0]['condicion_venta_nombre']) );


//DETALLES

$db->addSelect('producto_nombre || \' \' || producto_presentacion AS producto_nombre');
$db->addSelect('abs(cantidad) AS cantidad');
$db->addSelect('lote');
$db->addSelect('datos_productos.codigo_referencia  AS nro_lista');
$db->addFrom('detalle_movimientos');
$db->addFrom('INNER JOIN datos_productos USING(producto_id)');
$db->addFrom('INNER JOIN datos_lotes USING(lote_id)');
$db->addWhere('id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
$db->addOrderBy('producto_nombre, producto_presentacion');
$db->generarSelect();

//echo $db->getQry();exit;

$arrDetalles =  $db->ejecutar();

$pdf->SetFont('Arial','',10);
$pdf->Text(15,110, "ITEM" );
$pdf->Text(30,110, "DESCRIPCION" );
$pdf->Text(140,110, "CANT" );
$pdf->Text(160,110, "LISTA" );
$pdf->Text(180,110, "LOTE" );

$alto = 120;
$i=1;
foreach ($arrDetalles as $detalle){
    
    $pdf->SetFont('Arial','',10);
    $pdf->Text(15,$alto, $i);
        
    $pdf->SetFont('Arial','',10);
    $pdf->Text(30,$alto, utf8_decode($detalle['producto_nombre']) );

    $pdf->SetFont('Arial','',10);
    $pdf->Text(145,$alto, $detalle['cantidad'] );

    $pdf->SetFont('Arial','',8);
    $pdf->Text(160,$alto, $detalle['nro_lista'] );
    $pdf->SetFont('Arial','',8);
    $pdf->Text(180,$alto, $detalle['lote'] );

    $i++;

    $alto = $alto + 5;
}
$pdf->SetFont('Arial','B',10);
$pdf->Text(20,$alto, utf8_decode("Alquiler por : " . $arrCabecera[0]['cant_dias_alquiler'] . " días") );

//DETALLES

$db->addSelect('despacho_nro');
$db->addFrom('movimientos_trazabilidad');
$db->addFrom('INNER JOIN vw_despacho_trazas USING(trazabilidad_id)');
$db->addWhere('id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
$db->addGroup('despacho_nro');
$db->generarSelect();

//echo $db->getQry();exit;

$arrDespacho =  $db->ejecutar();

$alto = $alto + 10;
$pdf->Text(15,$alto, "Despachos" );
$alto = $alto + 5;
foreach($arrDespacho AS $despacho){
      $pdf->SetFont('Arial','',10);
      $pdf->Text(15,$alto , $despacho['despacho_nro'] );
      $alto = $alto + 5;
}

if($arrCabecera[0][obs]!=NULL){
    $alto = $alto + 5;
    $pdf->Text(15,$alto, "Observaciones" );
    $alto = $alto + 5;
    $pdf->Text(15,$alto, " " . utf8_decode($arrCabecera[0]['obs']) );
}

//EXTRAS
$pdf->SetFont('Arial','',10);

if(strlen($arrCabecera[0]['direccion_entrega']) > 1){
    $pdf->Text(30,250, utf8_decode("Dirección :" . $arrCabecera[0]['direccion_entrega']));
}
if(strlen($arrCabecera[0]['paciente_nombre']) > 1){
    $pdf->Text(30,255, utf8_decode("Paciente : " . $arrCabecera[0]['paciente_nombre']) );                    
}
if(strlen($arrCabecera[0]['medico_nombre']) > 1 ){
    $pdf->Text(30,260, utf8_decode("Médico : " . $arrCabecera[0]['medico_nombre']) );
}

$pdf->Output();
