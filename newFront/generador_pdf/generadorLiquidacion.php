<?php                                                                                                                                                                            
//ALMACENAMIENTO                                                                                                                                                                 
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';                                                                        
require_once 'php-barcode.php';                                                                                                                                                      
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/includes/FuncionesComunes.php'; 
require_once('barcode.inc.php'); 

$objFuncionesComunes =  new FuncionesComunes();
$db = NEW frenteAlmacenamiento();
$db->addSelect("to_char(liquidacion_fecha,'DD-MM-YYYY') AS liquidacion_fecha");
$db->addSelect("to_char(fecha_desde,'DD-MM-YYYY') AS fecha_desde");
$db->addSelect("to_char(fecha_hasta,'DD-MM-YYYY') AS fecha_hasta");
$db->addSelect("vendedor_nombre");
$db->addSelect("factura_nro");
$db->addSelect("to_char(factura_fecha,'DD-MM-YYYY') AS factura_fecha");
$db->addSelect("cliente_calle");
$db->addSelect("cliente_direccion");
$db->addSelect("localidad_nombre");
$db->addSelect("dl.liquidacion_id AS nro_liquidacion");
$db->addSelect("liquidacion_importe AS liquidacion_importe");
$db->addSelect("importe_detalle AS importe_detalle");
$db->addFrom('datos_liquidaciones dl');
$db->addFrom('INNER JOIN detalle_liquidaciones del USING(liquidacion_id)');
$db->addFrom('INNER JOIN datos_facturas df USING(factura_id)');
$db->addFrom('INNER JOIN datos_clientes dc ON (df.cliente_id = dc.cliente_id)');
$db->addFrom('INNER JOIN datos_vendedores ON dl.vendedor_id = datos_vendedores.vendedor_id');
$db->addFrom('LEFT JOIN datos_localidades dloc ON (dloc.localidad_id=dc.cliente_loca_id)');                             
$db->addFrom('LEFT JOIN datos_provincias dprov ON (dloc.localidad_prov_id = dprov.provincia_id)');
//$db->addGroup('dc.cliente_nombre,dc.cliente_calle,dc.cliente_altura,dc.cliente_piso, dc.cliente_depto,dc.cliente_cuit,CASE WHEN dc.cliente_resp_insc THEN \'RESPONSABLE INSCRIPTO\' ELSE \'Exento\' END, dloc.localidad_nombre,dprov.provincia_nombre,df.factura_id,df.factura_nro,to_char(df.factura_fecha::date,\'DD/MM/YYYY\'),df.factura_total_fact,df.factura_total_fact,df.factura_total_iva,dp.producto_nombre || \' \' || dp.producto_presentacion || \' - \' || dm.marca_nombre,def.importe_unitario,def.producto_id,def.importe_iva,df.de_factura_id, df2.factura_nro,df.descuento,dp,producto_nombre,df.factura_fecha');                   
$db->addOrderby('factura_fecha');                   

if($_GET['crIdLiquidacion']!=null){
    $db->addWhere('dl.liquidacion_id = \'' . $_GET['crIdLiquidacion']. '\'');
}else{
    echo "No llego ID de factura";exit;
}
$db->generarSelect();                                                                                                
//echo $db->getQry();exit;
$arrCabecera =  $db->ejecutar();
//var_dump($p); exit;
//echo $db->getQry();exit;                                                                                           
 if(count($arrCabecera) ==0){                                                                                        
    echo "<h2>Sin registros para {$_GET['crIdPedido']} ";                                                            
    exit;                                                                                                            
}                                                                                                                    

$pdf = new FPDF ('P','mm',array(210,297));
$pdf->AddPage();                          

//CABECERA
$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 52;  // barcode center                  
$y        = 280;  // barcode center                   
$height   = 12;   // barcode height in 1D ; module size in 2D
$width    = 0.3;    // barcode height in 1D ; not use in 2D  
$angle    = 0;   // rotation in degrees                      
$black    = '000000'; // color in hexa                       
$type     = 'code128';                                                                          

//DATOS PROPIOS
$pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpg');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,34, utf8_decode("Razón Social: "));
$pdf->Text(5,38, utf8_decode("Domicilio Comercial: ")); 
$pdf->SetFont('Arial','',10);
$pdf->Text(30,34, utf8_decode("Limon S.R.L"));
$pdf->Text(40,38, utf8_decode("Av. Roca 739 - "));
$pdf->Text(5,42, utf8_decode("Hurlingham, Buenos Aires "));


//FACTURA LETRA
//BORDER INFERIOR

//BORDER IZQUIERDO

//BORDER DERECHO

//BORDER QUE LIMITA
$pdf->Line(98.5, 5 ,98.5, 45);

$pdf->SetFont('Arial','B',24);
//$pdf->Text(115,17, utf8_decode("FACTURA"));

$pdf->Text(115,17, utf8_decode("Liquidación"));


$pdf->SetFont('Arial','B',10);
$pdf->Text(115,23, utf8_decode("Nro: " . $arrCabecera[0]['nro_liquidacion']));


//FECHA
$pdf->Text(115,28, utf8_decode("Fecha de Emisión: ") . $arrCabecera[0]['factura_fecha']) ;
$pdf->Text(115,33, utf8_decode("Periodo: ") . $arrCabecera[0]['fecha_desde'] . " - " . $arrCabecera[0]['fecha_hasta']) ;
$pdf->Text(115,38, utf8_decode("Vendedor: ") . $arrCabecera[0]['vendedor_nombre']) ;
//DATOS FISCALES

//BORDER SUPERIOR
$pdf->Line(3, 5 ,205 , 5);
//BORDER INFERIOR
$pdf->Line(3, 45 ,205 , 45);
//BORDER IZQUIERDO
$pdf->Line(3, 5 ,3, 290);
//BORDER DERECHO
$pdf->Line(205, 5 ,205, 290);
//FIN DATOS PROPIOS Y CUADRO







if($arrCabecera[0]['de_factura_id']>0){
    $pdf->Text(22,65, $arrCabecera[0][factura_origen]) ;
}else{
    $remitos=substr($remitos, 0, -2);
    $pdf->Text(20,65, $remitos) ;
}
//$pdf->Line(3, 70 ,205 , 70);
$pedidos=substr($pedidos, 0, -1);

//FIN CABECERA
$pdf->SetFont('Arial','B',9);
$pdf->Text(5,55, "Item" );
$pdf->Text(20,55, "Fecha" );
$pdf->Text(40,55, "Nro. Remito" );
$pdf->Text(90,55, "Direccion" );
$pdf->Text(178,55, "Subtotal" );


$alto = 60;
$i=1;

foreach ($arrCabecera as $detalle){
    
        $pdf->SetFont('Arial','',8); 
        
        $pdf->Text(6,$alto, $i) ;
        $pdf->Text(18,$alto, utf8_decode($detalle['factura_fecha'])) ;
        $pdf->Text(40,$alto, $detalle['factura_nro'] );
        $pdf->Text(70,$alto, utf8_decode($detalle['cliente_direccion']));
        $pdf->Text(70,$alto+3, utf8_decode($detalle['localidad_nombre']));
        $pdf->Text(180,$alto, $detalle['importe_detalle']);
        
        $subtotal=$subtotal + $detalle['importe_detalle'];
        $alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $alto = $alto + 4;
        $i++;
        if($i==25 || $i==51 || $i==72 || $i==94 || $i==122 || $i==150 || $i==178 || $i==206){
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(155,270, utf8_decode("Subtotal: ". $objFuncionesComunes->formatoMoneda($subtotal)));
            //BORDER SUPERIOR
            $pdf->Line(3, 255 ,205 , 255);
            //BORDER INFERIOR
            $pdf->Line(3, 290 ,205 , 290);


            
            $pdf->AddPage();
            //DATOS PROPIOS
            $pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpg');
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(5,34, utf8_decode("Razón Social: "));
            $pdf->Text(5,38, utf8_decode("Domicilio Comercial: ")); 
            $pdf->SetFont('Arial','',10);
            $pdf->Text(30,34, utf8_decode("Limon S.R.L"));
            $pdf->Text(40,38, utf8_decode("Juan Manuel de Rosas 2667 - "));
            $pdf->Text(5,42, utf8_decode("San Justo, Buenos Aires "));
            //FACTURA LETRA

            $pdf->SetFont('Arial','B',24);
            //$pdf->Text(115,17, utf8_decode("FACTURA"));

            $pdf->Text(115,17, utf8_decode("Liquidación"));


            $pdf->SetFont('Arial','B',10);
            $pdf->Text(115,23, utf8_decode("Nro: " . $arrCabecera[0]['nro_liquidacion']));


            //FECHA
            $pdf->Text(115,28, utf8_decode("Fecha de Emisión: ") . $arrCabecera[0]['factura_fecha']) ;
            $pdf->Text(115,33, utf8_decode("Periodo: ") . $arrCabecera[0]['fecha_desde'] . " - " . $arrCabecera[0]['fecha_hasta']) ;
            $pdf->Text(115,38, utf8_decode("Vendedor: ") . $arrCabecera[0]['vendedor_nombre']) ;


            //BORDER SUPERIOR
            $pdf->Line(3, 5 ,205 , 5);
            //BORDER INFERIOR
            $pdf->Line(3, 45 ,205 , 45);
            //BORDER IZQUIERDO
            $pdf->Line(3, 5 ,3, 290);
            //BORDER DERECHO
            $pdf->Line(205, 5 ,205, 290);
            //FIN DATOS PROPIOS Y CUADRO


            //$pdf->Line(3, 70 ,205 , 70);



            //FIN CABECERA
            $pdf->SetFont('Arial','B',9);
            $pdf->Text(160,50, utf8_decode("Transporte: $ " . $objFuncionesComunes->formatoMoneda($subtotal)));
            $pdf->SetFont('Arial','B',9);
            $pdf->Text(5,55, "Item" );
            $pdf->Text(20,55, "Fecha" );
            $pdf->Text(40,55, "Nro. Remito" );
            $pdf->Text(90,55, "Direccion" );
            $pdf->Text(178,55, "Subtotal" );

            $alto = 60;
            
        }
        //exit;
}

$pdf->SetFont('Arial','B',10);

//$pdf->Text(164,275, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
$pdf->Text(155,280, utf8_decode("Importe Total:  $ " . $arrCabecera[0]['liquidacion_importe']));

//BORDER SUPERIOR
$pdf->Line(3, 270 ,205 , 270);
//BORDER INFERIOR
$pdf->Line(3, 290 ,205 , 290);
/*

*/

//$pdf->Line(98.5, 255 ,98.5, 290);
//$pdf->Line(98, 273 ,205 , 273);



$pdf->Output();

    

