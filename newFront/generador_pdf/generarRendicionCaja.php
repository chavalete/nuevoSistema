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
$db->addSelect("to_char(fecha_cobro,'DD-MM-YYYY') AS fecha_cobro");
$db->addSelect("sum(importe_pago) AS importe_total");
$db->addSelect("forma_desc");
$db->addSelect("forma_pago_id");
$db->addSelect("usuario_nombre_completo");
$db->addSelect("factura_nro");
$db->addFrom('datos_cobros');
$db->addFrom('INNER JOIN detalle_cobros  USING(cobro_id)');
$db->addFrom('INNER JOIN datos_formas_pagos USING(forma_pago_id)');
$db->addFrom('INNER JOIN datos_cobros_facturas USING(cobro_id)');
$db->addFrom('INNER JOIN datos_facturas USING(factura_id)');
$db->addFrom('INNER JOIN datos_usuarios ON datos_cobros.cobro_usuario_id = datos_usuarios.usuario_id');
$db->addGroup("fecha_cobro,usuario_nombre_completo, forma_desc, cobro_cancelado, factura_nro, forma_pago_id");
$db->addOrderby('datos_cobros.fecha_cobro, forma_desc');

$fecha= $objFuncionesComunes->fechaFormatoDb($_GET['fechaDesde']);;
if($_GET['fechaDesde']!=null){
    $db->addWhere('fecha_cobro= \'' . $fecha. '\'');
    $db->addWhere('cobro_usuario_id= \'' . $_GET['idUsuario']. '\'');
}
//$db->addWhere("sucursal_id={$_SESSION['sucursalId']}");
$db->addWhere('cobro_cancelado=false');
$db->generarSelect();                                                                                                
//echo $db->getQry();exit;
$arrCabecera =  $db->ejecutar();
//var_dump($p); exit;
//echo $db->getQry();exit;                                                                                           

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
$pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpeg');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,34, utf8_decode("Razón Social: "));
$pdf->Text(5,38, utf8_decode("Domicilio Comercial: ")); 
$pdf->SetFont('Arial','',10);
$pdf->Text(30,34, utf8_decode("Limon Autoservicio Mayorista"));
$pdf->Text(40,38, utf8_decode(" Av. Gaona 901 - "));
$pdf->Text(5,42, utf8_decode("Moreno, Buenos Aires "));


//FACTURA LETRA
//BORDER INFERIOR

//BORDER IZQUIERDO

//BORDER DERECHO

//BORDER QUE LIMITA
$pdf->Line(98.5, 5 ,98.5, 45);

$pdf->SetFont('Arial','B',24);
//$pdf->Text(115,17, utf8_decode("FACTURA"));

$pdf->Text(115,17, utf8_decode("Rendición de caja"));


$pdf->SetFont('Arial','B',10);
//FECHA
$pdf->Text(115,28, utf8_decode("Fecha: ") . $_GET['fechaDesde']) ;
$pdf->Text(115,32, utf8_decode("Usuario: ") . utf8_decode($arrCabecera[0]['usuario_nombre_completo'])) ;
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

//FIN CABECERA
$pdf->SetFont('Arial','B',15);
$pdf->Text(20,55, "Boleta" );
$pdf->Text(55,55, "Forma de pago" );
$pdf->Text(100,55, "Importe" );


$alto = 63;
$i=1;
$subtotal=0;
$antFormaPago=$arrCabecera[0]['forma_pago_id'];
foreach ($arrCabecera as $detalle){
    if($antFormaPago==$detalle['forma_pago_id']){
        $pdf->SetFont('Arial','',12); 
        
        $pdf->Text(20,$alto, utf8_decode($detalle['factura_nro'])) ;
        $pdf->Text(55,$alto, utf8_decode($detalle['forma_desc'])) ;
        $pdf->Text(100,$alto, $detalle['importe_total'] );
        
        
        $subtotal=$subtotal + $detalle['importe_total'];
        $alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $alto = $alto + 4;
        $i++;
        if($i==25 || $i==51 || $i==72 || $i==94 || $i==122 || $i==150 || $i==178 || $i==206 || $i==234){
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(155,270, utf8_decode("Subtotal: ". $objFuncionesComunes->formatoMoneda($subtotal)));
            //BORDER SUPERIOR
            $pdf->Line(3, 255 ,205 , 255);
            //BORDER INFERIOR
            $pdf->Line(3, 290 ,205 , 290);


            
            $pdf->AddPage();
            //DATOS PROPIOS
            $pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpeg');
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(5,34, utf8_decode("Razón Social: "));
            $pdf->Text(5,38, utf8_decode("Domicilio Comercial: ")); 
            $pdf->SetFont('Arial','',10);
            $pdf->Text(30,34, utf8_decode("Limon Autoservicio Mayorista"));
            $pdf->Text(40,38, utf8_decode(" Av. Gaona 901 - "));
            $pdf->Text(5,42, utf8_decode("Moreno, Buenos Aires "));

            //FACTURA LETRA

            $pdf->SetFont('Arial','B',24);
            $pdf->Text(115,17, utf8_decode("Rendición de caja"));
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(115,28, utf8_decode("Fecha: ") . $_GET['fechaDesde']) ;
            $pdf->Text(115,32, utf8_decode("Usuario: ") . utf8_decode($arrCabecera[0]['usuario_nombre_completo'])) ;



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
            $pdf->SetFont('Arial','B',15);
            $pdf->Text(20,55, "Boleta" );
            $pdf->Text(55,55, "Forma de pago" );
            $pdf->Text(100,55, "Importe" );

            $alto = 60;

        }

    }else{
        $pdf->SetFont('Arial','B',10);

        //$pdf->Text(164,275, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
        $pdf->Text(155,280, utf8_decode("Importe Total:  $ " . $subtotal));

        //BORDER SUPERIOR
        $pdf->Line(3, 270 ,205 , 270);
        //BORDER INFERIOR
        $pdf->Line(3, 290 ,205 , 290);
        $pdf->AddPage();
        //DATOS PROPIOS
        $pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpeg');
        $pdf->SetFont('Arial','B',10);
        $pdf->Text(5,34, utf8_decode("Razón Social: "));
        $pdf->Text(5,38, utf8_decode("Domicilio Comercial: "));
        $pdf->SetFont('Arial','',10);
        $pdf->Text(30,34, utf8_decode("Limon Autoservicio Mayorista"));
        $pdf->Text(40,38, utf8_decode(" Av. Gaona 901 - "));
        $pdf->Text(5,42, utf8_decode("Moreno, Buenos Aires "));

        $pdf->Line(98.5, 5 ,98.5, 45);

        $pdf->SetFont('Arial','B',24);
        //$pdf->Text(115,17, utf8_decode("FACTURA"));

        $pdf->Text(115,17, utf8_decode("Rendición de caja"));


        $pdf->SetFont('Arial','B',10);
        //FECHA
        $pdf->Text(115,28, utf8_decode("Fecha: ") . $_GET['fechaDesde']) ;
        $pdf->Text(115,32, utf8_decode("Usuario: ") . utf8_decode($arrCabecera[0]['usuario_nombre_completo'])) ;
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

        //FIN CABECERA
        $pdf->SetFont('Arial','B',15);
        $pdf->Text(20,55, "Boleta" );
        $pdf->Text(55,55, "Forma de pago" );
        $pdf->Text(100,55, "Importe" );


        $subtotal=0;
        $i=1;
        $alto = 63;
        $pdf->SetFont('Arial','',12);

        $pdf->Text(20,$alto, utf8_decode($detalle['factura_nro'])) ;
        $pdf->Text(55,$alto, utf8_decode($detalle['forma_desc'])) ;
        $pdf->Text(100,$alto, $detalle['importe_total'] );


        $subtotal=$subtotal + $detalle['importe_total'];
        $alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $alto = $alto + 4;
        $i++;
    }
    $antFormaPago=$detalle['forma_pago_id'];
}
$pdf->SetFont('Arial','B',10);

//$pdf->Text(164,275, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
$pdf->Text(155,280, utf8_decode("Importe Total:  $ " . $subtotal));

//BORDER SUPERIOR
$pdf->Line(3, 270 ,205 , 270);
//BORDER INFERIOR
$pdf->Line(3, 290 ,205 , 290);


//AHORA VAMOS POR HDR
$db = NEW frenteAlmacenamiento();
$db->addSelect("to_char(hoja_estado_fecha,'DD-MM-YYYY') AS fecha_cobro");
$db->addSelect("sum(importe_cobrado) AS importe_total");
$db->addSelect("forma_desc");
$db->addSelect("forma_pago_id");
$db->addSelect("usuario_nombre_completo");
$db->addSelect("factura_nro");
$db->addFrom('datos_hojas_de_ruta dhr');
$db->addFrom('INNER JOIN detalle_hojas_de_ruta dehrd  USING(hoja_id)');
$db->addFrom('INNER JOIN datos_formas_pagos USING(forma_pago_id)');
$db->addFrom('INNER JOIN datos_facturas USING(factura_id)');
$db->addFrom('INNER JOIN datos_usuarios ON dehrd.hoja_estado_usuario_id = datos_usuarios.usuario_id');
$db->addGroup("fecha_cobro,usuario_nombre_completo,hoja_estado_fecha, forma_desc, hoja_anulada, factura_nro, forma_pago_id");
$db->addOrderby('hoja_estado_fecha, forma_desc');

$fecha= $objFuncionesComunes->fechaFormatoDb($_GET['fechaDesde']);;
if($_GET['fechaDesde']!=null){
    $db->addWhere('hoja_estado_fecha::date= \'' . $fecha. '\'');
    $db->addWhere('hoja_estado_usuario_id= \'' . $_GET['idUsuario']. '\'');
}else{
    echo "No llego ID de usuario";exit;
}
$db->addWhere('importe_cobrado>0');
$db->addWhere('hoja_anulada=false');
$db->generarSelect();
//echo $db->getQry();exit;
$arrCabecera =  $db->ejecutar();
if(count($arrCabecera)>0){
    $pdf->AddPage();
    //DATOS PROPIOS
$pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpeg');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,34, utf8_decode("Razón Social: "));
$pdf->Text(5,38, utf8_decode("Domicilio Comercial: "));
$pdf->SetFont('Arial','',10);
$pdf->Text(30,34, utf8_decode("Limon Autoservicio Mayorista"));
$pdf->Text(40,38, utf8_decode(" Av. Gaona 901 - "));
$pdf->Text(5,42, utf8_decode("Moreno, Buenos Aires "));


//FACTURA LETRA
//BORDER INFERIOR

//BORDER IZQUIERDO

//BORDER DERECHO

//BORDER QUE LIMITA
$pdf->Line(98.5, 5 ,98.5, 45);

$pdf->SetFont('Arial','B',24);
//$pdf->Text(115,17, utf8_decode("FACTURA"));

$pdf->Text(115,17, utf8_decode("Rendición de caja Reparto"));


$pdf->SetFont('Arial','B',10);
//FECHA
$pdf->Text(115,28, utf8_decode("Fecha: ") . $_GET['fechaDesde']) ;
$pdf->Text(115,32, utf8_decode("Usuario: ") . utf8_decode($arrCabecera[0]['usuario_nombre_completo'])) ;
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

//FIN CABECERA
$pdf->SetFont('Arial','B',15);
$pdf->Text(20,55, "Boleta" );
$pdf->Text(55,55, "Forma de pago" );
$pdf->Text(100,55, "Importe" );


$alto = 63;
$i=1;
$subtotal=0;
$antFormaPago=$arrCabecera[0]['forma_pago_id'];
foreach ($arrCabecera as $detalle){
    if($antFormaPago==$detalle['forma_pago_id']){
        $pdf->SetFont('Arial','',12);

        $pdf->Text(20,$alto, utf8_decode($detalle['factura_nro'])) ;
        $pdf->Text(55,$alto, utf8_decode($detalle['forma_desc'])) ;
        $pdf->Text(100,$alto, $detalle['importe_total'] );


        $subtotal=$subtotal + $detalle['importe_total'];
        $alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $alto = $alto + 4;
        $i++;
        if($i==25 || $i==51 || $i==72 || $i==94 || $i==122 || $i==150 || $i==178 || $i==206 || $i==234){
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(155,270, utf8_decode("Subtotal: ". $objFuncionesComunes->formatoMoneda($subtotal)));
            //BORDER SUPERIOR
            $pdf->Line(3, 255 ,205 , 255);
            //BORDER INFERIOR
            $pdf->Line(3, 290 ,205 , 290);



            $pdf->AddPage();
            //DATOS PROPIOS
            $pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpeg');
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(5,34, utf8_decode("Razón Social: "));
            $pdf->Text(5,38, utf8_decode("Domicilio Comercial: "));
            $pdf->SetFont('Arial','',10);
            $pdf->Text(30,34, utf8_decode("Limon Autoservicio Mayorista"));
            $pdf->Text(40,38, utf8_decode(" Av. Gaona 901 - "));
            $pdf->Text(5,42, utf8_decode("Moreno, Buenos Aires "));

            //FACTURA LETRA

            $pdf->SetFont('Arial','B',24);
            $pdf->Text(115,17, utf8_decode("Rendición de caja Reparto"));
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(115,28, utf8_decode("Fecha: ") . $_GET['fechaDesde']) ;
            $pdf->Text(115,32, utf8_decode("Usuario: ") . utf8_decode($arrCabecera[0]['usuario_nombre_completo'])) ;



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
            $pdf->SetFont('Arial','B',15);
            $pdf->Text(20,55, "Boleta" );
            $pdf->Text(55,55, "Forma de pago" );
            $pdf->Text(100,55, "Importe" );

            $alto = 60;

        }

    }else{
        $pdf->SetFont('Arial','B',10);

        //$pdf->Text(164,275, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
        $pdf->Text(155,280, utf8_decode("Importe Total:  $ " . $subtotal));

        //BORDER SUPERIOR
        $pdf->Line(3, 270 ,205 , 270);
        //BORDER INFERIOR
        $pdf->Line(3, 290 ,205 , 290);
        $pdf->AddPage();
        //DATOS PROPIOS
        $pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpeg');
        $pdf->SetFont('Arial','B',10);
        $pdf->Text(5,34, utf8_decode("Razón Social: "));
        $pdf->Text(5,38, utf8_decode("Domicilio Comercial: "));
        $pdf->SetFont('Arial','',10);
        $pdf->Text(30,34, utf8_decode("Limon Autoservicio Mayorista"));
        $pdf->Text(40,38, utf8_decode(" Av. Gaona 901 - "));
        $pdf->Text(5,42, utf8_decode("Moreno, Buenos Aires "));

        $pdf->Line(98.5, 5 ,98.5, 45);

        $pdf->SetFont('Arial','B',24);
        //$pdf->Text(115,17, utf8_decode("FACTURA"));

        $pdf->Text(115,17, utf8_decode("Rendición de caja Reparto"));


        $pdf->SetFont('Arial','B',10);
        //FECHA
        $pdf->Text(115,28, utf8_decode("Fecha: ") . $_GET['fechaDesde']) ;
        $pdf->Text(115,32, utf8_decode("Usuario: ") . utf8_decode($arrCabecera[0]['usuario_nombre_completo'])) ;
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

        //FIN CABECERA
        $pdf->SetFont('Arial','B',15);
        $pdf->Text(20,55, "Boleta" );
        $pdf->Text(55,55, "Forma de pago" );
        $pdf->Text(100,55, "Importe" );


        $subtotal=0;
        $i=1;
        $alto = 63;
        $pdf->SetFont('Arial','',12);

        $pdf->Text(20,$alto, utf8_decode($detalle['factura_nro'])) ;
        $pdf->Text(55,$alto, utf8_decode($detalle['forma_desc'])) ;
        $pdf->Text(100,$alto, $detalle['importe_total'] );


        $subtotal=$subtotal + $detalle['importe_total'];
        $alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $alto = $alto + 4;
        $i++;
    }
    $antFormaPago=$detalle['forma_pago_id'];
}
$pdf->SetFont('Arial','B',10);

//$pdf->Text(164,275, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
$pdf->Text(155,280, utf8_decode("Importe Total:  $ " . $subtotal));

//BORDER SUPERIOR
$pdf->Line(3, 270 ,205 , 270);
//BORDER INFERIOR
$pdf->Line(3, 290 ,205 , 290);

}



$pdf->Output();

    

