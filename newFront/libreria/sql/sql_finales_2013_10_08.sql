ALTER TABLE datos_trazabilidad DROP COLUMN origen_desc ;
ALTER TABLE detalle_movimientos DROP COLUMN pm;
ALTER TABLE datos_lotes ALTER lote_vencimiento DROP NOT NULL ;
ALTER TABLE datos_movimientos ADD COLUMN fecha_inicio_alquiler date;