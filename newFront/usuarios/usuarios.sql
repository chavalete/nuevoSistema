--ALTA DEL FRENTE

INSERT INTO frente.datos_modelo(modelo_id, modelo_nombre) Values (nextval('seq_datos_modelo_modelo_id'),'usuarios');
    
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Herramientas','herramientas','7');

INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro, class,editable) Values (currval('seq_datos_modelo_modelo_id'),'Observaciones','observaciones','6','null','true');

INSERT INTO frente.detalle_modelo (modelo_id, display, name, orden_nro,editable) VALUES (currval('seq_datos_modelo_modelo_id'), 'Subseccion inicio','subseccion_inicio','5','true');

INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro,class,editable) Values (currval('seq_datos_modelo_modelo_id'),'Activo','usuario_activo','4','select','true');

INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro,editable) Values (currval('seq_datos_modelo_modelo_id'),'Nombre completo','usuario_nombre_completo','3','true');

INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro,editable) Values (currval('seq_datos_modelo_modelo_id'),'Nombre','usuario_nombre','2','true');

INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Id','usuario_id','1');

--------PHP---------
abms_usuarios_crear_form.php
FrenteUsuarios.php
UsuariosExtendido.php
Usuario.php
usuarios.sql
--------------------

//VERIFAR LOS IDS ANTES DE TIRAR

ALTER TABLE datos_usuarios ADD COLUMN subseccion_inicio varchar(150);

/*INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values ('49','Usuario bas','usuario_bas','4');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values ('46','Subseccion Ini','subseccion_inicio','6');
UPDATE frente.detalle_modelo SET display = 'Herramientas',name = 'herramientas',orden_nro = '7',sortable = false,required = 'false',editable = 'false',modelo_activo = 'true' WHERE detalle_modelo_id = '358';
UPDATE frente.detalle_modelo SET display = 'Activo',name = 'usuario_activo',orden_nro = '5',sortable = false,required = 'false',editable = 'false',modelo_activo = 'true' WHERE detalle_modelo_id = '359';

*/

-- MODIFICACION PERMISOS
--flix/inc/process.php -- linea 208
--flix/index.php -- linea 16 

--libreria/usuario.php
--FrenteDeFrentes.php
--FrenteModeloAdmin.php

---PARA QUE SEA EDITABLE------

----------------------------------

/*INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values ('47','Usuario bas','usuario_bas','4');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values ('47','Subseccion Ini','subseccion_inicio','6');
*/
/*
UPDATE frente.detalle_modelo SET display = 'Herramientas',name = 'herramientas',orden_nro = '7',sortable = false,required = 'false',editable = 'false',modelo_activo = 'true' WHERE detalle_modelo_id = '358';
UPDATE frente.detalle_modelo SET display = 'Activo',name = 'usuario_activo',orden_nro = '5',sortable = false,required = 'false',editable = 'false',modelo_activo = 'true' WHERE detalle_modelo_id = '359';

UPDATE frente.detalle_modelo set class = 'select' WHERE detalle_modelo_id = 359;
*/
