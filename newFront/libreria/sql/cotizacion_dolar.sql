CREATE TABLE datos_cotizacion_dolar(
    cotizacion_id bigint, 
    cotizacion numeric(4,2),
    observaciones text,
    cotizacion_fecha_hora timestamp default now(), 
    cotizacion_usuario_id smallint,
    cotizacion_procesada boolean default 'f',
    cotizacion_procesada_fecha_hora timestamp,
    cotizacion_procesada_usuario_id smallint
    ) WITH OIDS;
    
CREATE SEQUENCE seq_datos_cotizacion_idx;

CREATE UNIQUE INDEX da_cotizacion_dolar_idx ON datos_cotizacion_dolar (cotizacion_id);

INSERT INTO frente.datos_modelo VALUES (38,'actualizacionDePrecios');

INSERT INTO frente.detalle_modelo SELECT 38,display,name,align,orden_nro,nextval('frente.seq_detalle_modelo_id'),sortable, class, required, editable, modelo_activo FROM frente.detalle_modelo WHERE modelo_id = 6;
UPDATE frente.detalle_modelo SET display ='Cotización',name='cotizacion' WHERE detalle_modelo_id = 269;
informacion_ortodontia_mini=# UPDATE frente.detalle_modelo SET display ='Fecha',name='cotizacion_fecha_hora', orden_nro = 2  WHERE detalle_modelo_id = 267;
informacion_ortodontia_mini=# UPDATE frente.detalle_modelo SET display ='Observaciones',name='observaciones', orden_nro = 3  WHERE detalle_modelo_id = 268;


ALTER TABLE datos_productos ADD COLUMN producto_pventa numeric(14,2);
ALTER TABLE datos_productos ADD COLUMN producto_pcosto numeric(14,2);
ALTER TABLE datos_productos ADD COLUMN producto_pventa_dolar numeric(14,2);
ALTER TABLE datos_productos ADD COLUMN producto_pcosto_dolar numeric(14,2);


NOTICE:  drop cascades to 3 other objects
DETAIL:  drop cascades to view vw_stock_completo_agrupado
drop cascades to constraint datos_trazabilidad_producto_id_fkey on table datos_trazabilidad
drop cascades to constraint detalle_movimientos_producto_id_fkey on table detalle_movimientos