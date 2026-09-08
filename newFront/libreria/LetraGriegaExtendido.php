<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/libreria/LetraGriega.php'

/*
CLASE DE LETRA GRIEGA
*/
class LetraGriegaExtendido extends LetraGriega
{
    public function cargarMe($id);
    
    public function salvarMe();
    
    public function actualizarMe();
}