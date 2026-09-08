CREATE TABLE datos_identificadores_aplicacion_gs1(id_ident_aplic integer, identificador_nro varchar, identificador_nombre varchar, 
longitud integer, longitud_fija boolean);

CREATE INDEX idx_datos_identificadores_aplicacion_gs1_id_ident_aplic ON datos_identificadores_aplicacion_gs1(id_ident_aplic);
CREATE INDEX idx_datos_identificadores_aplicacion_gs1_identificador_nro ON datos_identificadores_aplicacion_gs1(identificador_nro);


INSERT INTO datos_identificadores_aplicacion_gs1 VALUES (1,'01','gln',14,true);
INSERT INTO datos_identificadores_aplicacion_gs1 VALUES (2,'10','lote',20,false);
INSERT INTO datos_identificadores_aplicacion_gs1 VALUES (3,'17','Vto',6,true);
INSERT INTO datos_identificadores_aplicacion_gs1 VALUES (4,'21','traza',20,false);

