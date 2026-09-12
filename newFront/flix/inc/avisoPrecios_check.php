<?php
// Endpoint aislado: devuelve el HTML actualizado del aviso de precios.
// js/avisoPrecios.js lo llama por AJAX cada 5 minutos (setInterval), asi
// el aviso se actualiza sin depender de una recarga completa de
// página. Reusa tpl/avisoPrecios.php tal cual (misma consulta, sin
// cache) en vez de duplicar la lógica.
session_start();
include '/var/www/html/limonLocal/flix/tpl/avisoPrecios.php';
