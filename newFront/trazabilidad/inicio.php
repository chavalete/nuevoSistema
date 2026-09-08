<?php
//var_dump($_SERVER);exit;
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/trazabilidad/FrenteMovimiento.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of inicio
 *
 * @author dMELMAC
 */
class inicio
{
    public function iniciar(){
        $movimiento = new FrenteMovimiento();
        //ALTA
/*
        $arrDetalle = Array(
                            Array(
                                'sucursal_id' => 1,
                                'almacen_id' => 2,
                                'estanteria_id' => 2,
                                'producto_id' => 3,
                                'lote_id' => 1,
                                'cantidad' => 2 
                            ),
                        Array(
                                'sucursal_id' => 1,
                                'almacen_id' => 1,
                                'estanteria_id' => 1,
                                'producto_id' => 4,
                                'lote_id' => 1,
                                'cantidad' => 4
                            )
                        );
        $arrDato[] =
                    Array(
                        'prove_id' => 1 ,
                        'tipo_movimiento_id' => 1 ,
                        'despacho_nro' => 987 ,
                        'pm' => 'Prueba' ,
                        'factura_nro' => 666,
                        'remito_nro' => 777,
                        'arr_parametros_detalle' => $arrDetalle,
                        'movimiento_usuario_id' => 1
                   );
*/

        
        $arrDetalle = Array(Array(
                                'sucursal_id' => 1,
                                'almacen_id' => 1,
                                'estanteria_id' => 1,
                                'producto_id' => 1,
                                'lote_id' => 1,
                                'arr_codigos' => Array(2223,3334,4445)
                            ),
                            Array(
                                'sucursal_id' => 1, // si 
                                'almacen_id' => 1, // si 
                                'estanteria_id' => 1, // si 
                                'producto_id' => 2, //  
                                'lote_id' => 1,
                                'arr_codigos' => Array(7777,8888)
                            )
                        );
        $arrDato[] = Array(
                         'prove_id' => 1,
                         'tipo_movimiento_id' => 1,
                         'movimiento_usuario_id' => 1,
                         'factura_nro' => 44,
                         'arr_parametros_detalle' => $arrDetalle,
                        
                                    );
/*
        $arrDetalle = Array(Array(
                             'arr_codigos' => Array('00000000vS')
                            )
                        );

        $arrDato[] =
                    Array(
                        'tipo_movimiento_id' => 2 ,
                        'cliente_id' => 1 ,
                        'paciente_id' => 1 ,
                        'medico_id' => 1 ,
                        'obra_social' => 1 ,
                        'movimiento_usuario_id' => 1,
                        'factura_nro' => 45,
                        'arr_parametros_detalle' => $arrDetalle
                        
                      );
  
*/
        $movimiento->generarMovimiento($arrDato);
            
        if(count($movimiento->getArrError()) > 0){
            $arrError = $movimiento->getArrError();
            echo "<pre>";
            print_r($arrError);
            echo "</pre>";
        }
    }
}

inicio::iniciar();

