<?php $menu = ParseINI::getConfig('menu'); ?>
<ul class="menu">
    <?php
    foreach($menu as $key => $value){
        if($value[0] == true){
            if(count($value) > 1){
                //var_dump($_SESSION['secciones']);//exit;
                //var_dump($key);//exit;
                if($_SESSION['secciones'][strtolower($key)]){
                ?>
                <li class="seccion">
                    <a href="#">
                        <span class="nav-ico"><?=mb_strtoupper(mb_substr($key, 0, 2))?></span>
                        <?=$key?>
                        <span class="arrow"></span>
                    </a>

                    <?php
                    // Ajusta el ancho del menu si el texto supera los 15 caracteres
                    $reSize = false;
                    $newSize = 0;
                    for($i=1; $i<count($value); $i++){
                        $size = strlen($value[$i]);
                        if($size > 15 && $size > $newSize){
                            $reSize = true;
                            $newSize = 150 + ($size - 15) * 7;
                            $style = 'width: '.$newSize.'px;';
                        }
                    }
                    }
                    ?>
                    <ul style="z-index: 9999;">
                        <?php
                        for($i = 1; $i < count($value); $i++){
                            $splited = explode(' ', $value[$i]);
                            for($e=0; $e<count($splited); $e++){
                                if($e == 0){
                                    $params = strtolower($splited[0]);
                                }else{
                                    $params .= ucfirst($splited[$e]);
                                }
                            }
                            //var_dump($params);//exit;
                            //var_dump($_SESSION['subsecciones']);//exit;
                            if($_SESSION['subsecciones'][strtolower($params)]){
                            ?>

                            <li class="menu-option" params="<?=$params;?>" style="<?=$reSize ? $style : '';?>">
                                <?=$value[$i];?>
                            </li>
                            <?php
                                }
                            } ?>
                    </ul>
                </li>
                <?php
                }
            }else{
                ?>
                <li class="seccion-menu-option" params="<?=strtolower($key);?>">
                    <a href="#">
                        <span class="nav-ico"><?=mb_strtoupper(mb_substr($key, 0, 2))?></span>
                        <?=$key?>
                    </a>
                </li>
                <?php
            }
        }

    ?>
</ul>
