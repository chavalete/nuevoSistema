<div id='header'>
    <div class="arriba">
        <div id="logo" class='flotar-izquierda'>
    <?php
        $esLimoneta = (isset($_SESSION['sistemaNombre']) && strtolower(trim($_SESSION['sistemaNombre'])) === 'limoneta');
        $logo   = $esLimoneta ? 'img/limoneta2.png' : 'img/limon.jpeg';
        $width  = $esLimoneta ? '140' : '100'; // Ajustá el tamaño a gusto
        $height = $esLimoneta ? '40'  : '35';
    ?>
    <img src="<?php echo $logo; ?>" alt="Dmelmac" title="Dmelmac" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />

    <?php if (!$esLimoneta): ?>
        <span><?php echo $_SESSION['sistemaNombre']; ?></span>
    <?php endif; ?>
        </div>
    </div>
    <div class='abajo'>
        <div id="contenedor-botonera">
            <?php include('header-menu.php'); ?>
        </div>
    </div>
</div>
<div id="content-col">
<div id="topbar">
    <button type="button" id="sidebarToggle" class="hamburger" title="Contraer menú" aria-label="Contraer menú"><span></span></button>
    <div id="topbar-title"></div>
    <button type="button" id="themeToggle" class="theme-toggle" title="Cambiar a modo oscuro" aria-label="Cambiar a modo oscuro"></button>
    <div class="logout-contenedor">
        <p class="logout-usuario">
            <?php echo $_SESSION['usuarioNombre']; ?>
        </p>
        <a href="login.php?crDestruir=1" title="Logout" alt="Logout" class="logout">
            <img src="img/logout-btn.png" width="28" height="27" alt="Logout" title="Logout" border="0" />
        </a>
    </div>
</div>
