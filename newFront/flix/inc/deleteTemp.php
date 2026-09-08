<?php

echo '<pre>';
print_r($_POST);
echo '</pre>';

echo getcwd();
if(unlink('../tmp/'.$_POST['file'])){
    echo 'se borro';
}else{
    echo 'no se borro';
}