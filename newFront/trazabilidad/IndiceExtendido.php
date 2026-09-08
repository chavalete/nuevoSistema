<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/Indice.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndiceExtendido
 *
 * @author root
 */
class IndiceExtendido extends Indice
{
    public function __construct($ninusc = 0, $mayusc = 1) {
        parent::__construct($ninusc, $mayusc);
    }
}

?>
