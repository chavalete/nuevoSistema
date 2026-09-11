<?php
// Endpoint aislado: devuelve el HTML actualizado del aviso de precios.
// js/avisoPrecios.js lo llama por AJAX despues de cada cambio de
// sección (cada vez que loadFlix() pega contra tpl/flix/flix.php), asi
// el aviso se actualiza sin depender de una recarga completa de
// página. Reusa tpl/avisoPrecios.php tal cual (misma consulta, mismo
// cache de 1 hora) en vez de duplicar la lógica.
session_start();
include __DIR__ . '/../tpl/avisoPrecios.php';
