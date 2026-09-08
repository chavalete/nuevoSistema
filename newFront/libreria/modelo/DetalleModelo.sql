ALTER SEQUENCE frente.seq_detalle_modelo_id RESTART ;
UPDATE frente.detalle_modelo SET detalle_modelo_id = nextval('frente.seq_detalle_modelo_id');

ALTER TABLE frente.detalle_modelo ALTER COLUMN sortable SET DEFAULT TRUE ;
ALTER TABLE frente.detalle_modelo ALTER COLUMN required SET DEFAULT TRUE ;
ALTER TABLE frente.detalle_modelo ALTER COLUMN editable SET DEFAULT TRUE ;
ALTER TABLE frente.detalle_modelo ALTER align SET DEFAULT 'center';

CREATE INDEX idx_detalle_modelo_modelo_id ON frente.detalle_modelo(modelo_id );
CREATE INDEX idx_detalle_modelo_orden_nro ON frente.detalle_modelo(orden_nro);
CREATE INDEX idx_detalle_modelo_sortable ON frente.detalle_modelo(sortable);
CREATE INDEX idx_detalle_modelo_requires ON frente.detalle_modelo(required);
CREATE INDEX idx_detalle_modelo_editable ON frente.detalle_modelo(editable);
CREATE UNIQUE INDEX  idx_detalle_modelo_detalle_modelo_id ON frente.detalle_modelo(detalle_modelo_id);  



