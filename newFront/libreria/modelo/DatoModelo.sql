CREATE SEQUENCE seq_datos_modelo_modelo_id;

UPDATE frente.datos_modelo SET modelo_id =  nextval('seq_datos_modelo_modelo_id');


ALTER TABLE frente.datos_modelo ADD COLUMN activo boolean default true;
ALTER TABLE frente.datos_modelo ADD observaciones VARCHAR;
ALTER TABLE frente.datos_modelo ADD usuario_id integer;

CREATE UNIQUE INDEX idx_datos_modelo_modelo_id ON frente.datos_modelo(modelo_id);
CREATE INDEX  idx_datos_modelo_usuario_id ON frente.datos_modelo(usuario_id);
CREATE INDEX  idx_datos_modelo_fecha_alta ON frente.datos_modelo(fecha_alta); 
CREATE INDEX  idx_datos_modelo_modelo_nombre ON frente.datos_modelo(modelo_nombre);

