--PHPs usados:
--DatoModelo.php
--DetalleModelo.php
--DatoModeloExtendido
--DetalleModeloExtendido.php
--frenteDeFrentes.php
--FrenteModelo.php
--FrenteModeloAdmin.php
--detalles.php
--process.php

--ALTA:
UPDATE frente.datos_modelo SET modelo_id = 39 WHERE oid = 776851;

CREATE SEQUENCE seq_datos_modelo_modelo_id START 40;

ALTER TABLE frente.datos_modelo ADD COLUMN activo boolean default true;
ALTER TABLE frente.datos_modelo ADD observaciones VARCHAR;
ALTER TABLE frente.datos_modelo ADD usuario_id integer;

CREATE UNIQUE INDEX idx_datos_modelo_modelo_id ON frente.datos_modelo(modelo_id);
CREATE INDEX  idx_datos_modelo_usuario_id ON frente.datos_modelo(usuario_id);
CREATE INDEX  idx_datos_modelo_fecha_alta ON frente.datos_modelo(fecha_alta); 
CREATE INDEX  idx_datos_modelo_modelo_nombre ON frente.datos_modelo(modelo_nombre);

INSERT INTO frente.datos_modelo(modelo_id, modelo_nombre) Values (nextval('seq_datos_modelo_modelo_id'),'modelos');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Herramientas','herramientas','5');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro,editable) Values (currval('seq_datos_modelo_modelo_id'),'Observaciones','observaciones','4','true');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro,class,editable) Values (currval('seq_datos_modelo_modelo_id'),'Activo','modelo_activo','3','select','true');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro,editable) Values (currval('seq_datos_modelo_modelo_id'),'Nombre','modelo_nombre','2',true);
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Id','modelo_id','1');


ALTER SEQUENCE frente.seq_detalle_modelo_id START 1;
UPDATE frente.detalle_modelo SET detalle_modelo_id = nextval('frente.seq_detalle_modelo_id');

ALTER TABLE frente.detalle_modelo ALTER COLUMN sortable SET DEFAULT false ;
ALTER TABLE frente.detalle_modelo ALTER COLUMN required SET DEFAULT false ;
ALTER TABLE frente.detalle_modelo ALTER COLUMN editable SET DEFAULT false ;
ALTER TABLE frente.detalle_modelo ALTER align SET DEFAULT 'center';
CREATE UNIQUE INDEX  idx_detalle_modelo_detalle_modelo_id ON frente.detalle_modelo(detalle_modelo_id);  
CREATE INDEX idx_detalle_modelo_modelo_id ON frente.detalle_modelo(modelo_id );
CREATE INDEX idx_detalle_modelo_orden_nro ON frente.detalle_modelo(orden_nro);
CREATE INDEX idx_detalle_modelo_sortable ON frente.detalle_modelo(sortable);
CREATE INDEX idx_detalle_modelo_requires ON frente.detalle_modelo(required);
CREATE INDEX idx_detalle_modelo_editable ON frente.detalle_modelo(editable);


--phps
--DatoModelo.php
--DetalleModelo.php
--DatoModeloExtendido
--DetalleModeloExtendido.php
--frenteDeFrentes.php
--FrenteModelo.php
--FrenteModeloAdmin.php
