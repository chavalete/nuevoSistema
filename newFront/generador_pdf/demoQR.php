<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/phpqrcode/qrlib.php';

class codigoQR {

    public function generarQR($arrParametros){
        $arr=array();
        $arr['ver']=1;
        $arr['fecha']=$arrParametros['0']['factura_fecha_qr'];
        $arr['cuit']=$arrParametros['0']['cuit_ortodontia'];
        $arr['ptoVta']=(int)$arrParametros['0']['factura_puesto_venta'];
        $arr['tipoCmp']=$arrParametros['0']['factura_tipo'];
        $arr['nroCmp']=$arrParametros['0']['factura_nro_afip'];
        $arr['importe']=(double)$arrParametros['0']['total_factura'];
        $arr['moneda']='PES';
        $arr['ctz']=1;
        $arr['tipoDocRec']=80;
        $arr['nroDocRec']=(int)str_replace("-","",$arrParametros[0]['cliente_cuit']);
        $arr['tipoCodAut']='E';
        $arr['codAut']=(int)$arrParametros['0']['factura_cae'];
        $codigo = base64_encode(json_encode($arr));
            
        $codigo = rtrim(strtr($codigo, '+/', '-_'), '=');
        $codigo="https://www.arca.gob.ar/fe/qr/?p=".$codigo;
        
	QRcode::png($codigo, "/var/www/html/newFront/generador_pdf/codigoQR/".$arr['codAut']. ".png", "H", 2, 1);
	//QRcode::png($codigo, "/tmp/".$arr['codAut']. ".png", "H", 2, 1);
    }

}
