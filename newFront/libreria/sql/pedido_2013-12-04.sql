CREATE OR REPLACE FUNCTION fn_actualizar_pedido_preparado(id_pedido integer, id_usuario integer) RETURNS INTEGER AS $$
BEGIN
    UPDATE datos_pedidos SET pedido_preparado= TRUE , pedido_preparado_usuario_id= $2 ,pedido_preparado_fecha= now() WHERE pedido_id = $1;
    RETURN 1;
END;
$$ LANGUAGE plpgsql;
