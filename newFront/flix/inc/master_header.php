<?php

include ('autoload.php');
include ('session.php');

$seccion = empty($_POST['parametros']['tipo']) ? ParseINI::getConfig('defaults_js', 'type') : $_POST['parametros']['tipo'];
