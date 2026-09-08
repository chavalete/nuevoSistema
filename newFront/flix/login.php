<?php
session_start();
if(!empty($_GET['crDestruir'])){
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>LIMON</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link type="text/css" rel="stylesheet" href="css/login.css" />
        <link type="text/css" rel="stylesheet" href="css/msgBoxLight.css" />
        <link type="text/css" rel="stylesheet" href="css/theme.css" />
        <script src="js/jQuery.min-1.8.3.js" type="text/javascript"></script>
        <script src="js/jquery.msgBox.js" type="text/javascript"></script>
        <script src="js/logIn.js" type="text/javascript"></script>
    </head>
    <body>
       <div class="login-cuadro">
            <div class="login-contenedor">
                <img class="login-logo" src="img/dmelmac-logo-transparente.png" alt="Dmelmac" />
                <h1 class="login-title">Iniciar sesión</h1>
                <form class="form-login">
                    <label for="user">Usuario</label>
                    <input id="user" type="text" name="usuario" value="" autocomplete="username">

                    <label for="pass">Contraseña</label>
                    <input id="pass" type="password" name="psw" value="" autocomplete="current-password">

                    <label for="sucursal">Sucursal</label>
                    <select name="sucursal" id="sucursal">
                        <option value="2">Moreno</option>
                        <option value="3">Rosas</option>
                        <option value="4">Limoneta</option>
                    </select>

                    <input type="button" name="Ingresar" value="INGRESAR" class="login-btn" />
                </form>
            </div>
       </div>
    </body>
</html>
