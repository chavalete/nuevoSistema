--PHPs usados:
--DatoModelo.php
--DetalleModelo.php
--DatoModeloExtendido
--DetalleModeloExtendido.php
--frenteDeFrentes.php
--FrenteModelo.php
--FrenteModeloAdmin.php

----Dato
-----acordarse de resetear la secuencia
CREATE SEQUENCE seq_datos_modelo_modelo_id START 40;
    
ALTER TABLE frente.datos_modelo ADD COLUMN activo boolean default true;
ALTER TABLE frente.datos_modelo ADD observaciones VARCHAR;
ALTER TABLE frente.datos_modelo ADD usuario_id integer;

CREATE UNIQUE INDEX idx_datos_modelo_modelo_id ON frente.datos_modelo(modelo_id);
CREATE INDEX  idx_datos_modelo_usuario_id ON frente.datos_modelo(usuario_id);
CREATE INDEX  idx_datos_modelo_fecha_alta ON frente.datos_modelo(fecha_alta); 
CREATE INDEX  idx_datos_modelo_modelo_nombre ON frente.datos_modelo(modelo_nombre);

--Detalle
--acordarse de recordar la secuencia
ALTER SEQUENCE frente.seq_detalle_modelo_id RESTART;

UPDATE frente.detalle_modelo SET detalle_modelo_id = nextval('frente.seq_detalle_modelo_id');


ALTER TABLE frente.detalle_modelo ALTER COLUMN sortable SET DEFAULT false ;
ALTER TABLE frente.detalle_modelo ALTER COLUMN required SET DEFAULT false ;
ALTER TABLE frente.detalle_modelo ALTER COLUMN editable SET DEFAULT false ;
ALTER TABLE frente.detalle_modelo ALTER align SET DEFAULT 'center';
CREATE INDEX idx_detalle_modelo_modelo_id ON frente.detalle_modelo(modelo_id );
CREATE INDEX idx_detalle_modelo_orden_nro ON frente.detalle_modelo(orden_nro);
CREATE INDEX idx_detalle_modelo_sortable ON frente.detalle_modelo(sortable);
CREATE INDEX idx_detalle_modelo_requires ON frente.detalle_modelo(required);
CREATE INDEX idx_detalle_modelo_editable ON frente.detalle_modelo(editable);
CREATE UNIQUE INDEX  idx_detalle_modelo_detalle_modelo_id ON frente.detalle_modelo(detalle_modelo_id);  



                  ---------------- Lista de precios--------------------
                  
ALTER TABLE lista_precios_1 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_2 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_5 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_6 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_7 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_8 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_9 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_10 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_11 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_12 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_13 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_14 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_15 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_16 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_17 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_18 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_19 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_20 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_21 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_22 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_23 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_24 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_25 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_26 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_27 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_28 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_29 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_30 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_31 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_32 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_33 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_34 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_35 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_38 ADD COLUMN descuento real default 0;
ALTER TABLE lista_precios_40 ADD COLUMN descuento real default 0;

ALTER TABLE detalle_movimientos ADD COLUMN descuento real default 0;








