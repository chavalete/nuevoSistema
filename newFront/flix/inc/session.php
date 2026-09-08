<?php
session_start();

if(!$_SESSION['usuarioValidado']){
    header('Location: login.php');
}