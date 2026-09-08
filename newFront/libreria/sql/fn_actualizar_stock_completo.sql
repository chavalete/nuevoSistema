--PARA DROPEAR LA FN Y TRIGGER ANTERIOR
DROP TRIGGER trigger_actualizar_stock_completo ON detalle_movimientos;
DROP FUNCTION fn_actualizar_stock_completo();


CREATE OR REPLACE FUNCTION fn_actualizar_stock_completo(cantidad_pedida integer , id_sucursal integer, id_almacen integer, id_estanteria integer, id_lote integer, id_producto integer) RETURNS integer AS $$
DECLARE
    reg_stock record;
    reg_produ record;
    importe_stock numeric(14,2);
    importe_costo numeric(14,2);
BEGIN
--chequea la existencia en la tabla
SELECT into reg_stock * FROM stock_completo
WHERE sucursal_id = $2 AND almacen_id = $3 AND estanteria_id = $4 AND lote_id = $5 AND producto_id = $6;
SELECT into reg_produ producto_pventa, producto_pcosto FROM datos_productos WHERE producto_id = $3;
IF FOUND THEN
    UPDATE stock_completo SET cantidad = cantidad + $1, valor_stock = valor_stock + reg_produ.producto_pventa * $1, costo_stock = costo_stock + reg_produ.producto_pcosto * $1 WHERE sucursal_id = $2 AND almacen_id = $3 AND estanteria_id = $4  
    AND lote_id = $5  AND producto_id = $6;
ELSE
    INSERT INTO stock_completo(sucursal_id, almacen_id, estanteria_id, lote_id, producto_id, cantidad, valor_stock, costo_stock)
    VALUES ($2, $3, $4, $5, $6, $1, importe_stock, importe_costo);
END IF;
RETURN 1;
END;
$$ LANGUAGE plpgsql;




CREATE OR REPLACE FUNCTION fn_actualizar_stock_completo(cantidad_pedido integer, id_sucursal integer, id_almacen integer, id_estanteria integer, id_lote integer, id_producto integer) RETURNS integer AS $$

DECLARE

    reg_stock record;
    reg_produ record;
    importe_stock numeric(14,2);
    importe_costo numeric(14,2);

BEGIN
--chequea la existencia en la tabla
SELECT into reg_produ producto_pventa, producto_pcosto FROM datos_productos WHERE producto_id = $3;
SELECT into reg_stock * FROM stock_completo WHERE lote_id = $2 AND producto_id = $3;

IF FOUND THEN

    UPDATE stock_completo SET cantidad = cantidad + $1, valor_stock = valor_stock + reg_produ.producto_pventa * $1, costo_stock = costo_stock + reg_produ.producto_pcosto * $1 WHERE lote_id = $2  AND producto_id = $3;
ELSE
    importe_stock:= reg_produ.producto_pventa * $1;
    importe_costo:= reg_produ.producto_pcosto * $1;
    INSERT INTO stock_completo(lote_id, producto_id, cantidad, valor_stock, costo_stock) VALUES ($2, $3, $1,importe_stock, importe_costo);
END IF;
RETURN 1;
END;
$$ LANGUAGE plpgsql;