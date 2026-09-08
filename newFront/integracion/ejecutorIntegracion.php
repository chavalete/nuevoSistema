<?php
require_once 'FrenteIntegracionSucursales.php';


//$arrBases = array('reparto', 'dmelmac');
//POR AHORA SOLO BUSCA DE REPARTO A MORENO
$arrBases = array('reparto');
foreach ($arrBases AS $base){
    $frente = new FrenteIntegracionSucursales();
    $frente->buscarSalidas($base);
}
