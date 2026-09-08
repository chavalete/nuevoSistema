<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'php-barcode.php';
ob_end_clean();
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/includes/FuncionesComunes.php';
require_once('barcode.inc.php');

$db = NEW frenteAlmacenamiento();

$objFuncionesComunes = new FuncionesComunes();

$db->addSelect('dhr.hoja_id');
$db->addSelect('to_char(dhr.hoja_fecha, \'dd-mm-yyyy\') as hoja_fecha ');
$db->addSelect('dd.distribuidor_nombre');
$db->addSelect('dc.cliente_nombre');
$db->addSelect('dc.cliente_direccion');
$db->addSelect('dloca.localidad_nombre');
$db->addSelect('dprov.provincia_nombre');
$db->addSelect('dhr.hoja_obs');
$db->addSelect('dm.obs');
$db->addSelect('dm.remito_nro');
$db->addSelect('dm.movimiento_total_fact');
$db->addSelect('dehr.importe_cobrado');
$db->addSelect('acompanante');
$db->addSelect('vehiculo_nombre AS vehiculo');
$db->addSelect('carga');
$db->addSelect('hoja_estado_id');
$db->addSelect('horario_entrega');
$db->addFrom('datos_hojas_de_ruta dhr');
$db->addFrom('INNER JOIN detalle_hojas_de_ruta dehr USING (hoja_id)');
$db->addFrom('INNER JOIN datos_distribuidores dd USING (distribuidor_id)');
$db->addFrom('INNER JOIN datos_vehiculos  USING (vehiculo_id)');
$db->addFrom('INNER JOIN datos_movimientos dm USING (factura_id)');
$db->addFrom('INNER JOIN datos_clientes dc ON (dm.cliente_id=dc.cliente_id)');
$db->addFrom('INNER JOIN datos_localidades dloca ON (dloca.localidad_id = dc.cliente_loca_id)');
$db->addFrom('INNER JOIN datos_provincias dprov ON (dloca.localidad_prov_id = dprov.provincia_id)');
$db->addWhere('hoja_id = ' . $_GET['crHojaId']);
$db->addOrderBy('hoja_detalle_id');
$db->generarSelect();
$arrCabecera =  $db->ejecutar();
//echo $db->getQry();exit;
if(count($arrCabecera) ==0){
   echo "<h2>Sin registros para {$_GET['crHojaId']} ";
   exit;
}

//var_dump($arrCabecera); exit;
$pdf = new FPDF ('L','mm',array(210,297));
$pdf->AddPage();


//CABECERA
$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 23;  // barcode center
$y        = 70;  // barcode center
$height   = 9;   // barcode height in 1D ; module size in 2D
$width    = 0.3;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees
$black    = '000000'; // color in hexa
$type     = 'code128';

//$code = $arrCabecera[0]['id_movimiento'];
//$data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);




$pdf->Line(10, 20 ,290 , 20);

$pdf->SetFont('Arial','B',12);
$pdf->Text(10,8, "Hoja de ruta Nro.: " . $arrCabecera[0]['hoja_id'] );
$pdf->Text(10,13, "Fecha : " . $arrCabecera[0]['hoja_fecha'] );
$pdf->Text(10,18, "Chofer: ");
$pdf->Text(35,18, "" . utf8_decode($arrCabecera[0]['distribuidor_nombre']) );
$pdf->Text(70,8, utf8_decode("Acompañante : "));
$pdf->Text(105,8, "" . utf8_decode($arrCabecera[0]['acompanante']) );
$pdf->Text(70,13, utf8_decode("Vehiculo : "));
$pdf->Text(100,13, "" . utf8_decode($arrCabecera[0]['vehiculo']) );
$pdf->Text(70,18, utf8_decode("Carga : "));
$pdf->Text(90,18, "" . utf8_decode($arrCabecera[0]['carga']) );
$pdf->Text(120,18, utf8_decode("Control : "));


//$pdf->SetFont('Arial','B',12);
$pdf->Text(175,10, "CERVEZA AZUL: " );
$pdf->Text(175,18, "CERVEZA GRIS: " );
$pdf->Text(225,10, "COCA RETORNABLE 2L: " );
$pdf->Text(225,18, "COCA RETORNABLE 1L: " );
//PARA CAJONES
$pdf->Line(173, 5 ,290 , 5);
$pdf->Line(173, 12 ,290 , 12);
$pdf->Line(173, 5,173 , 20);
$pdf->Line(223, 5,223 , 20);
$pdf->Line(290, 5,290 , 20);

//$alto = 50;
//linea arriba
$pdf->Line(10, 25,250 , 25);
//linea ABAJO
$pdf->Line(10, 34,250 , 34);
//izquierda
$pdf->Line(10, 25,10 , 45);
//derecha
$pdf->Line(250, 25,250 , 45);
$pdf->Line(10, 45,250 , 45);

$pdf->SetFont('Arial','B',12);
$pdf->Text(15,30, "GASTOS: " );
$pdf->Text(15,40, "ROTURAS: " );



$pdf->Line(2, 50,295 , 50);


$pdf->SetFont('Arial','',10);
$pdf->Text(7,55, "TOTAL" );
$pdf->Text(45,55, "DIRECCION" );
$pdf->Text(120,55, "HORARIO" );
$pdf->Text(162,55, "EFECTIVO" );
$pdf->Text(185,55, "DEUDA" );
$pdf->Text(210,55, "RECHAZO" );

$pdf->Line(2, 58,295 , 58);

$alto = 63;
$i=1;
$importeTotal=0;
foreach ($arrCabecera as $detalle){

    $pdf->SetFont('Arial','',8);

    $pdf->Text(5.5,$alto-2,"Rto :". substr($detalle['remito_nro'],-8) );
    $pdf->Text(6,$alto+2, $objFuncionesComunes->formatoMoneda($detalle['importe_cobrado']));
    if($i==1){
        $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$detalle['remito_nro']), $width, $height);
    }else{
        $data = Barcode::fpdf($pdf, $black, $x, $alto+7, $angle, $type, array('code'=>$detalle['remito_nro']), $width, $height);
    }

    $direccionCompleta = utf8_decode($detalle['cliente_direccion'])  ;
    if($detalle['cliente_piso'] != null ){
        $direccionCompleta .= " Piso: " . $detalle['cliente_piso'];
    }
    if($detalle['cliente_depto'] != null ){
        $direccionCompleta .= " Depto: " . $detalle['cliente_depto'];
    }


    $pdf->Text(45,$alto-1, utf8_decode($direccionCompleta));
    $pdf->Text(45,$alto+3, "Localidad: " . utf8_decode($detalle['localidad_nombre']));
    $pdf->SetFont('Arial','',8);
    $pdf->Text(120,$alto+3, $detalle['horario_entrega'] );

    //$pdf->Text(190,$alto, $i );

    $alto = $alto +12;
    $pdf->Line(2, $alto ,295 , $alto);
    $alto = $alto +5;
    $i++;
    if($detalle['hoja_estado_id'] ==25 || $detalle['hoja_estado_id'] ==29){
        $importeTotal = $importeTotal + $detalle['importe_cobrado'];
    }
    $pdf->Line(2, 50 ,2  , $alto-5);
    $pdf->Line(44, 50,44  , $alto-5);
    $pdf->Line(118, 50 ,118  , $alto-5);
    $pdf->Line(160, 50 ,160  , $alto-5);
    $pdf->Line(182, 50 ,182  , $alto-5);
    $pdf->Line(200, 50 ,200  , $alto-5);
    $pdf->Line(295, 50 ,295  , $alto-5);
    if($i==9){

        $pdf->AddPage();
        $pdf->SetFont('Arial','B',12);
        $pdf->Text(10,8, "Hoja de ruta Nro.: " . $arrCabecera[0]['hoja_id'] );
        $pdf->Text(10,13, "Fecha : " . $arrCabecera[0]['hoja_fecha'] );
        $pdf->Text(10,18, "Chofer : ");
        $pdf->Text(35,18, "" . utf8_decode($arrCabecera[0]['distribuidor_nombre']) );
        $pdf->Text(70,8, utf8_decode("Acompañante : "));
        $pdf->Text(105,8, "" . utf8_decode($arrCabecera[0]['acompanante']) );
        $pdf->Text(70,13, utf8_decode("Vehiculo : "));
        $pdf->Text(100,13, "" . utf8_decode($arrCabecera[0]['vehiculo']) );
        $pdf->Text(70,18, utf8_decode("Carga : "));
        $pdf->Text(90,18, "" . utf8_decode($arrCabecera[0]['carga']) );
        $pdf->Text(120,18, utf8_decode("Control : "));
        $pdf->Line(2, 50,295 , 50);

        $pdf->SetFont('Arial','',10);
        $pdf->Text(7,55, "TOTAL" );
        $pdf->Text(45,55, "DIRECCION" );
        $pdf->Text(120,55, "HORARIO" );
        $pdf->Text(162,55, "EFECTIVO" );
        $pdf->Text(185,55, "DEUDA" );
        $pdf->Text(210,55, "RECHAZO" );

        $pdf->Line(2, 58,295 , 58);

        $alto = 63;
        $i=1;
        //$importeTotal=0;

    }
}

    //PARA EL TOTAL DE BULTOS
    //$pdf->Line(218, $alto+5 ,238  , $alto+5);


$pdf->Line(45, $alto,170 , $alto);
//linea ABAJO

$pdf->SetFont('Arial','B',12);
$pdf->Text(50,$alto+5, "TOTAL: " );
if($importeTotal>0){
    $pdf->Text(70,$alto+5, $objFuncionesComunes->formatoMoneda($importeTotal) );
}
$pdf->Text(110,$alto+5, "EFECTIVO: " );

//$pdf = new FPDF ('P','mm',array(210,297));
$pdf->AddPage('P','A4');

$db->addSelect('dhr.hoja_id');
$db->addSelect('to_char(dhr.hoja_fecha, \'dd-mm-yyyy\') as hoja_fecha ');
$db->addSelect('dd.distribuidor_nombre');
$db->addSelect('sum(abs(de.cantidad)) AS bultos');
$db->addSelect('dp.producto_nombre');
$db->addSelect('dp.producto_presentacion');
$db->addSelect('acompanante');
$db->addSelect('vehiculo_nombre AS vehiculo');
$db->addFrom('datos_hojas_de_ruta dhr');
$db->addFrom('INNER JOIN detalle_hojas_de_ruta dehr USING (hoja_id)');
$db->addFrom('INNER JOIN detalle_movimientos de USING (id_movimiento)');
$db->addFrom('INNER JOIN datos_movimientos dm USING (id_movimiento)');
$db->addFrom('INNER JOIN datos_distribuidores dd ON dhr.distribuidor_id = dd.distribuidor_id');
$db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
$db->addFrom('INNER JOIN datos_vehiculos USING(vehiculo_id)');
$db->addWhere('hoja_estado_id != 29');
$db->addWhere('soy_deuda = false');
$db->addWhere('hoja_id = ' . $_GET['crHojaId']);
$db->addGroup('dhr.hoja_id,to_char(dhr.hoja_fecha, \'dd-mm-yyyy\'), dd.distribuidor_nombre,dp.producto_nombre,dp.producto_presentacion,acompanante, vehiculo');
$db->addOrderBy('producto_nombre');
$db->generarSelect();
$arrBultos =  $db->ejecutar();
//echo $db->getQry();exit;
if(count($arrCabecera) ==0){
   echo "<h2>Sin registros para {$_GET['crHojaId']} ";
   exit;
}

$pdf->Line(10, 35 ,200 , 35);

$pdf->SetFont('Arial','B',12);
$pdf->Text(10,8, "Hoja de ruta Nro.: " . $arrCabecera[0]['hoja_id'] );
$pdf->Text(10,13, "Fecha : " . $arrCabecera[0]['hoja_fecha'] );
$pdf->Text(10,18, "Chofer : ");
$pdf->Text(35,18, "" . utf8_decode($arrCabecera[0]['distribuidor_nombre']) );
$pdf->Text(10,23, utf8_decode("Acompañante : "));
$pdf->Text(42,23, "" . utf8_decode($arrCabecera[0]['acompanante']) );
$pdf->Text(10,28, utf8_decode("Vehiculo : "));
$pdf->Text(35,28, "" . utf8_decode($arrCabecera[0]['vehiculo']) );
$pdf->Text(10,33, utf8_decode("Carga : "));
$pdf->Text(30,33, "" . utf8_decode($arrCabecera[0]['carga']) );

$pdf->SetFont('Arial','',10);
$pdf->Text(20,50, "PRODUCTO" );
$pdf->Text(110,50, "BULTOS" );
$pdf->Text(150,50, "SEPARADO" );

$alto = 53;
$pdf->Line(15, 45,180 , 45);
$totalBultos=0;
$i=0;
foreach($arrBultos AS $detalle){

        $pdf->Line(15, $alto ,180 , $alto);
        $alto = $alto + 4;
        $pdf->Text(18,$alto, utf8_decode($detalle['producto_nombre']) . " - " .  $detalle['producto_presentacion']);
        $pdf->Text(115,$alto, $detalle['bultos'] );
        $alto =  $alto+4;
        $i++;
        $totalBultos= $totalBultos + $detalle['bultos'];
        $pdf->Line(15, 45 ,15  , $alto);
        $pdf->Line(105, 45 ,105  , $alto);
        $pdf->Line(140, 45 ,140  , $alto);
        $pdf->Line(180, 45 ,180  , $alto);
        $pdf->Line(15, $alto,180 , $alto);
        if($i==29){
            $pdf->AddPage('P','A4');
            $pdf->Line(10, 35 ,200 , 35);
            $pdf->SetFont('Arial','B',12);
            $pdf->Text(10,8, "Hoja de ruta Nro.: " . $arrCabecera[0]['hoja_id'] );
            $pdf->Text(10,13, "Fecha : " . $arrCabecera[0]['hoja_fecha'] );
            $pdf->Text(10,18, "Chofer : ");
            $pdf->Text(35,18, "" . utf8_decode($arrCabecera[0]['distribuidor_nombre']) );
            $pdf->Text(10,23, utf8_decode("Acompañante : "));
            $pdf->Text(42,23, "" . utf8_decode($arrCabecera[0]['acompanante']) );
            $pdf->Text(10,28, utf8_decode("Vehiculo : "));
            $pdf->Text(35,28, "" . utf8_decode($arrCabecera[0]['vehiculo']) );
            $pdf->Text(10,33, utf8_decode("Carga : "));
            $pdf->Text(30,33, "" . utf8_decode($arrCabecera[0]['carga']) );

            $pdf->SetFont('Arial','',10);
            $pdf->Text(20,50, "PRODUCTO" );
            $pdf->Text(110,50, "BULTOS" );
            $pdf->Text(150,50, "SEPARADO" );

            $alto = 53;
            $pdf->Line(15, 45,180 , 45);
            //$totalBultos=0;
            $i=0;
        }

}
    $pdf->Line(15, 45 ,15  , $alto);
    $pdf->Line(105, 45 ,105  , $alto);
    $pdf->Line(140, 45 ,140  , $alto);
    $pdf->Line(180, 45 ,180  , $alto);
    //PARA EL TOTAL DE BULTOS
    //$pdf->Line(218, $alto+5 ,238  , $alto+5);


$pdf->Line(15, $alto,180 , $alto);
$pdf->Text(105,$alto+5, "Total: " .$totalBultos );



$pdf->Output();
