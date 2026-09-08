CREATE OR REPLACE FUNCTION fn_movimiento_stock(lote_automatico integer, id_pedido integer, id_detalle_pedido integer ,cantidad_pedida integer, id_producto integer, id_lote integer) RETURNS INTEGER AS $$
DECLARE                       
reg_lotes_disponibles RECORD;
reg_datos_vw_stock RECORD;
int_pendiente  integer := $4 ;
int_cantidad integer := 0;
txt_error text;
BEGIN
SELECT INTO  reg_datos_vw_stock cantidad_total, producto_nombre FROM vw_stock_completo_agrupado WHERE producto_id = $5;
IF reg_datos_vw_stock.cantidad_total < (int_pendiente*-1) THEN
    txt_error := 'La cantidad solicitada (' || $4 || ') es mayor a la cantidad en stock ('|| reg_datos_vw_stock.cantidad_total || ') disponible del producto ' || reg_datos_vw_stock.producto ;
    RAISE EXCEPTION 'Atenti!   " % " ' , txt_error ;
END IF;
--segun tipo de movimiento
IF $1 = 1 THEN --solo para el movimiento de "Baja por pedido tomado" con lote_automatioco en 1.
    FOR reg_lotes_disponibles IN SELECT * FROM stock_completo INNER JOIN datos_estanterias USING (estanteria_id) INNER JOIN datos_lotes USING (lote_id) WHERE stock_completo.producto_id =$5  AND cantidad > 0 ORDER BY lote_vencimiento, lote_id ASC LOOP
        --int_pendiente:= reg_lotes_disponibles.cantidad + int_cantidad;
         --Actualizar stock_completo 
        IF (int_pendiente * -1) < reg_lotes_disponibles.cantidad THEN
            int_cantidad := int_pendiente;
            int_pendiente := 0;
        ELSE
            int_cantidad := reg_lotes_disponibles.cantidad * -1;
            int_pendiente := reg_lotes_disponibles.cantidad + int_pendiente;
        END IF;
        PERFORM fn_actualizar_stock_completo(int_cantidad::integer, reg_lotes_disponibles.sucursal_id::integer, reg_lotes_disponibles.almacen_id::integer, reg_lotes_disponibles.estanteria_id::integer, reg_lotes_disponibles.lote_id::integer, $5::integer);
        --Insert en la tabla que relaciona el pedido con el lote a utilizar y su ubicacion dentro de la sucursal
        INSERT INTO pedidos_lotes(pedido_id, cantidad, lote_id, estanteria_id, pedido_detalle_id, producto_id) VALUES ($2, int_cantidad, reg_lotes_disponibles.lote_id, reg_lotes_disponibles.estanteria_id,$3,$5);
        int_cantidad:=int_pendiente;
        IF int_pendiente = 0 THEN 
            EXIT;--corta solo el for
        END IF;
    END LOOP;
ELSE --solo para el movimiento de "Baja por pedido tomado" con lote_automatioco en 0.
        SELECT INTO reg_lotes_disponibles * FROM stock_completo INNER JOIN datos_productos USING (producto_id) WHERE lote_id = $6 AND producto_id = $5 AND cantidad > 0;
        IF (int_pendiente * -1) > reg_lotes_disponibles.cantidad THEN
            txt_error := 'La cantidad solicitada (' || int_pendiente * -1 || ') es mayor a la cantidad en stock ('|| reg_lotes_disponibles.cantidad  || ') disponible del producto ' || reg_lotes_disponibles.producto_nombre || ' ' || reg_lotes_disponibles.producto_presentacion  ;
            RAISE EXCEPTION 'Atenti!   " % " ' , txt_error ;
        END IF;
        --Actualizar stock_completo 
        PERFORM fn_actualizar_stock_completo(int_pendiente::integer, reg_lotes_disponibles.sucursal_id::integer, reg_lotes_disponibles.almacen_id::integer, reg_lotes_disponibles.estanteria_id::integer, reg_lotes_disponibles.lote_id::integer, $5::integer);
        --Insert en la tabla que relaciona el pedido con el lote a utilizar y su ubicacion dentro de la sucursal
        INSERT INTO pedidos_lotes(pedido_id, cantidad, lote_id, estanteria_id, pedido_detalle_id,producto_id) VALUES ($2, int_pendiente, $6, reg_lotes_disponibles.estanteria_id, $3,$5);
        int_cantidad := int_pendiente;
END IF;
RETURN 1;
END;
$$ LANGUAGE plpgsql;
