SELECT 
  mt.id_movimiento AS id_movimiento_sc, 
  mt.tipo_movimiento_id AS id_tipo_movimiento_sc, 
  hoja_validada_fecha_hora AS fecha_hora_movimiento, 
  hoja_validada_fecha_hora::date  AS f_evento ,
  to_char(hoja_validada_fecha_hora,'HH24:MI') AS h_evento,
  fn_get_codigo_trazabilidad_new(trazabilidad_codigo) AS numero_serial,
  dl.lote_nro AS lote,
  dl.lote_vencimiento AS vencimiento,
  lpad(dp_tt.producto_gtin,14,'0') AS gtin ,
  ts.almacen_id AS id_sucursal,
  'f' AS es_agrupador,
  hfa.pedido_nro
FROM 
  lotes_trazabilidad.movimientos_trazabilidad mt  
  INNER JOIN lotes_trazabilidad.datos_trazabilidad dt USING(trazabilidad_id)  
  INNER JOIN datos_lotes dl USING(lote_id)  
  INNER JOIN datos_productos_a_tt dp_tt ON (dt.producto_id=dp_tt.producto_id) 
  INNER JOIN tablas_stock ts ON (dt.stock_id = ts.tabla_id) 
  INNER JOIN hojas_facturas_agrupadas hfa ON(hfa.factura_id = mt.id_movimiento) 
  INNER JOIN detalle_hojas_de_ruta dehdr ON ( hfa.en_factura_id= dehdr.factura_id) 
  INNER JOIN datos_hojas_de_ruta USING(hoja_id) 
WHERE 
  hfa.en_factura_id = 6276748 
  AND hfa.factura_id NOT IN (6276748) 
GROUP BY 
  mt.id_movimiento,
  mt.tipo_movimiento_id,
  hoja_validada_fecha_hora,
  trazabilidad_codigo,
  dl.lote_nro,
  dl.lote_vencimiento,
  dp_tt.producto_gtin,
  ts.almacen_id,
  hfa.pedido_nro
ORDER BY 
  hoja_validada_fecha_hora ASC;


$this->_db_dM->addSelect('id_movimiento_sc');
        $this->_db_dM->addFrom('mensajeria_anmat');
        $this->_db_dM->addWhere('regristo_finalizado =\'t\'');
        $this->_db_dM->addWhere('regristo_traspasado =\'f\'');
        $this->_db_dM->addWhere('es_agrupador =\'t\'');
        $this->_db_dM->addWhere('id_tipo_movimiento_sc = 12 GROUP BY id_movimiento_sc');
        $this->_db_dM->addOrderBy('id_movimiento_sc');
