<?php
require_once '/var/www/html/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class FrenteActualizadorLista{
    
    public function actualizar($arrParametros){
    $db = new FrenteAlmacenamiento();
    $arrLista = explode ("||",$arrParametros[listas]);
    array_pop($arrLista);
    $cantListas=0;
    $qry=null;
    foreach($arrLista AS $lista){
        list( $productoId, $precio,$costo, $unidades, $precioPromo) = explode("#",$lista);
        
            if($costo>$precio){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "El precio de unidad  del producto " . $resultado[0][producto] . " no puede menor al costo"
                        );
                    echo json_encode($arrDevolver);exit;
            }
            if($costo>$precioPromo && $unidades>0){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "El precio promo  del producto " . $resultado[0][producto] . " no puede menor al costo"
                        );
                    echo json_encode($arrDevolver);exit;
            }
        
            $qry.="UPDATE lista_precios_10 SET producto_pventa = ".$precio.", precio_promocion=".$precioPromo.",cantidad_promocion=".$unidades."  WHERE producto_id = $productoId;";
            $qry.="UPDATE lista_precios_11 SET producto_pventa = ".$precio*1.07.", precio_promocion=".$precioPromo*1.07.",cantidad_promocion=".$unidades."  WHERE producto_id = $productoId;";
            $qry.="UPDATE datos_productos SET actualizar_costo=false, producto_pcosto= ".$costo." WHERE producto_id = $productoId;";
            $cantListas++;
        }
        //echo $qry;exit;
        $db->setQry($qry);
            
                $db->ejecutarTransaccion();
                if(count($db->getArrError()) > 0){
                    $error =true;
                }else{
                    $error =false;
                }
                
            
            
            if($error){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "Error al actualizar!!!!"
                        );
                    echo json_encode($arrDevolver);exit;
        
            }else{
                    $arrDevolver = array(
                        "soyError" => false,
                        "nivel" => 1,
                        "mensaje" => "Lista la magia!!!!"
                        );
                    echo json_encode($arrDevolver);exit;
            }
        }
    public function actualizarSubfamilias($arrParametros){
    
    
    
    $db = new FrenteAlmacenamiento();
    $arrLista = explode ("||",$arrParametros[listas]);
    array_pop($arrLista);
    $cantListas=0;
    $qry=null;
    foreach($arrLista AS $lista){
        list( $subfamiliaId, $precio,$costo, $unidades, $precioPromo) = explode("#",$lista);
        
        
            if($costo>$precio){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "El precio de la unidad subfamilia " . $resultado[0][subfamilia_nombre] . " no puede menor al costo"
                        );
                    echo json_encode($arrDevolver);exit;
            }
            if($costo>$precioPromo && $unidades>0){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "El precio de la promox subfamilia " . $resultado[0][subfamilia_nombre] . " no puede menor al costo"
                        );
                    echo json_encode($arrDevolver);exit;
            }
        
            $qry.= "UPDATE lista_precios_10 ls SET producto_pventa = ".$precio.",precio_promocion=".$precioPromo.",cantidad_promocion=".$unidades."   FROM relaciones_familias_subfamilias dp WHERE  dp.subfamilia_id =".$subfamiliaId." AND ls.producto_id = dp.producto_id;";

            $qry.= "UPDATE lista_precios_11 ls SET producto_pventa = ".$precio*1.07.",precio_promocion=".$precioPromo*1.07.",cantidad_promocion=".$unidades."   FROM relaciones_familias_subfamilias dp WHERE  dp.subfamilia_id =".$subfamiliaId." AND ls.producto_id = dp.producto_id;";
            
            $qry.= "UPDATE datos_productos dp SET actualizar_costo=false, producto_pcosto=".$costo."   FROM relaciones_familias_subfamilias rfs WHERE  rfs.subfamilia_id =".$subfamiliaId." AND dp.producto_id = rfs.producto_id;";
            
            $cantListas++;
        }
        //echo $qry;exit;
        $db->setQry($qry);
            
            
                $db->ejecutarTransaccion();
                if(count($db->getArrError()) > 0){
                    $error =true;
                }
            
                
            
            
            if($error){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "Error al actualizar!!!!"
                        );
                    echo json_encode($arrDevolver);exit;
        
            }else{
                    $arrDevolver = array(
                        "soyError" => false,
                        "nivel" => 1,
                        "mensaje" => "Lista la magia!!!!"
                        );
                    echo json_encode($arrDevolver);exit;
            }
        }
 }
