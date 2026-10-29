<?php
session_start();
require_once __DIR__ . '/inc/cambioSucursal.php';
require_once __DIR__ . '/../usuarios/FrenteUsuarios.php';

if(!$_SESSION['usuarioValidado']){
    header('Location: login.php');
    exit();
}

$usuarioId  = (int)$_SESSION['usuarioId'];
$sucursalId = isset($_POST['sucursalId']) ? (int)$_POST['sucursalId'] : 0;

//solo los usuarios habilitados y a una sucursal valida
if(!puedeCambiarSucursal() || !isset($SUCURSALES[$sucursalId])){
    header('Location: index.php');
    exit();
}

//el usuario tiene que estar dado de alta en esa sucursal (igual que en el login)
$db = new FrenteAlmacenamiento();
$db->addSelect('usuario_id');
$db->addFrom('usuarios_sucursales');
$db->addWhere('usuario_id = ?');
$db->addWhere('sucursal_id = ?');
$db->generarSelect();
$resultado = $db->ejecutar(Array(Array($usuarioId, $sucursalId)));
if(empty($resultado)){
    header('Location: index.php');
    exit();
}

//recargo los permisos de la nueva sucursal (igual que en el login de inc/process.php)
$frenteUsuarios = NEW FrenteUsuarios();
$frenteUsuarios->cargarUsuario($usuarioId, $sucursalId);

$_SESSION['sucursalId']    = $sucursalId;
$_SESSION['sistemaNombre'] = $SUCURSALES[$sucursalId];
unset($_SESSION['pestana_activa']);

$colObjSeccion = $frenteUsuarios->getColSecciones();
$_SESSION['secciones'] = $colObjSeccion[$usuarioId]->getArrSecciones();
$colObjSubseccion = $frenteUsuarios->getColSubsecciones();
$_SESSION['subsecciones'] = $colObjSubseccion[$usuarioId]->getArrSubsecciones();
$colObjBotonera = $frenteUsuarios->getColBotonera();
$_SESSION['botonera'] = $colObjBotonera[$usuarioId]->getArrBotonera();

header('Location: index.php');
exit();
