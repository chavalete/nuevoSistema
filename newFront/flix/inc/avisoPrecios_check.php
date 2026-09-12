<?php
// Endpoint aislado: devuelve el HTML actualizado del aviso de precios.
// js/avisoPrecios.js lo llama por AJAX despues de cada cambio de
// sección (cada vez que loadFlix() pega contra tpl/flix/flix.php), asi
// el aviso se actualiza sin depender de una recarga completa de
// página. Reusa tpl/avisoPrecios.php tal cual (misma consulta) en vez
// de duplicar la lógica.
session_start();
// Este endpoint puede llamarse muchas veces por minuto (cada cambio de
// seccion, o cada 1 segundo mientras se prueba). El cache de sesion de
// tpl/avisoPrecios.php quedaba pegado en resultados viejos entre estas
// llamadas, asi que lo forzamos a chequear fresco siempre aca. La carga
// completa de pagina (header.php) sigue usando su propio cache normal.
unset($_SESSION['avisoPreciosUltimoCheck']);
include '/var/www/html/limonLocal/flix/tpl/avisoPrecios.php';
