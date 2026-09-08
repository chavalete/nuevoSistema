/*
cofing.ini OK
modal.js OK
inc.Frentes.php OK
FrenteAlmacenamiento.php OK
frenteDeFrentes.php  OK
FrenteModeloAdmin.php  -- SIN CAMBIOS
FrenteUsuarios.php  OK
FrentePermisos.php -- COPIADO
usuariosSecciones.php   -- 
usuariosSubsecciones.php
abms_usuariosSecciones_busquedaAvanzada.php
abms_usuariosSecciones_crear.php
abms_usuariosSecciones_crear_form.php
FrenteBotones.php
UsuariosBotonera.php
abms_usuariosBotonera_busquedaAvanzada.php
abms_usuariosBotonera_crear.php
abms_usuariosBotonera_crear_form.php
FrenteHerramientas.php
UsuariosHerramientas.php
flix/inc/process.php
header-menu.php
botonera.php
*/
CREATE TABLE usuarios_secciones (
relacion_seccion_id integer NOT NULL,
usuario_id integer NOT NULL
)WITH oids;

CREATE UNIQUE INDEX idx_usuarios_secciones_relacion_seccion_id ON usuarios_secciones (relacion_seccion_id);
CREATE UNIQUE INDEX idx_usuarios_secciones_usuario_id  ON usuarios_secciones (usuario_id);

INSERT INTO usuarios_secciones(relacion_seccion_id, usuario_id) SELECT usuario_id, usuario_id FROM datos_usuarios;

CREATE SEQUENCE seq_usuarios_secciones_relacion_seccion_id START 1;

CREATE TABLE usuarios_subsecciones(
relacion_subseccion_id integer NOT NULL,
usuario_id integer NOT NULL
)WITH oids; 

CREATE UNIQUE INDEX idx_usuarios_subsecciones_relacion_subseccion_id ON usuarios_subsecciones (relacion_subseccion_id);
CREATE UNIQUE INDEX idx_usuarios_subsecciones_subseccion_id_usuario_id ON usuarios_subsecciones (relacion_subseccion_id, usuario_id);

INSERT INTO usuarios_subsecciones(relacion_subseccion_id, usuario_id) SELECT usuario_id, usuario_id FROM datos_usuarios;

CREATE SEQUENCE seq_usuarios_subseccion_relacion_seccion_id START 1;

CREATE TABLE usuarios_botonera (
relacion_boton_id integer NOT NULL,
usuario_id integer NOT NULL
) WITH oids;

CREATE UNIQUE INDEX idx_usuarios_botonera_relacion_boton_id ON usuarios_botonera (relacion_boton_id);
CREATE UNIQUE INDEX idx_usuarios_botonera_relacion_boton_id_usuario_id ON usuarios_botonera (relacion_boton_id,
usuario_id);

INSERT INTO usuarios_botonera(relacion_boton_id, usuario_id) SELECT usuario_id, usuario_id FROM datos_usuarios;

CREATE SEQUENCE seq_usuarios_botonera_relacion_boton_id START 1;

CREATE TABLE usuarios_herramientas(
relacion_seccion_id integer NOT NULL,
herramienta_relacion_id integer not null,
usuario_id integer not null
)WITH oids;

CREATE UNIQUE INDEX idx_usuarios_herramientas_herramienta_relacion_id ON usuarios_herramientas(herramienta_relacion_id); 
CREATE UNIQUE INDEX idx_usuarios_herramientas_usuario_id ON usuarios_herramientas(usuario_id);

CREATE SEQUENCE seq_usuarios_herramientas_herramientas_relacion_id START 1;

-- no va  -- INSERT INTO usuarios_herramientas(herramienta_relacion_id, usuario_id)VALUES(1,1);
          ------------ALTA DEL FRENTE DE SECCIONES---------------

INSERT INTO frente.datos_modelo(modelo_id, modelo_nombre, usuario_id) Values (nextval('seq_datos_modelo_modelo_id'),'usuariosSecciones','1');

--INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Herramientas','herramientas','2');

INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Nombre Completo','usuario_nombre_completo','1');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Herramientas','herramientas','2');


INSERT INTO frente.datos_modelo(modelo_id, modelo_nombre, usuario_id) Values (nextval('seq_datos_modelo_modelo_id'),'usuariosSubsecciones','1');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Herramientas','herramientas','2');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Nombre usuario','usuario_id','1');

----------------------ALTA DEL FRENTE DE Botones------------------------------------
INSERT INTO frente.datos_modelo(modelo_id, modelo_nombre, usuario_id) Values (nextval('seq_datos_modelo_modelo_id'),'usuariosBotonera','1');

INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Herramientas','herramientas','2');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Nombre completo','usuario_nombre_completo','1');


    -- parece que no va UPDATE frente.detalle_modelo SET display = 'Herramientas',name = 'herramientas',orden_nro = '4',sortable = false,required = 'false',editable = 'false',modelo_activo = 'false' WHERE detalle_modelo_id = '453';

------------------------------------------------------------
INSERT INTO frente.datos_modelo(modelo_id, modelo_nombre, usuario_id) Values (nextval('seq_datos_modelo_modelo_id'),'usuariosHerramientas','1');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Herramientas','herramientas','2');
INSERT INTO frente.detalle_modelo(modelo_id, display, name, orden_nro) Values (currval('seq_datos_modelo_modelo_id'),'Usuario nombre','usuario_id','1');



SELECT 
c.column_name AS monbre_columna,
FROM information_schema.columns c
WHERE UPPER(c.table_name) = upper( 'usuarios_secciones' )
ORDER BY c.ordinal_position;

--/var/www/html/trazabilidad/flix/js/modal.js
--/var/www/html/trazabilidad/flix/cfg/config.ini
--/var/www/html/trazabilidad/flix/inc/process.php
--/var/www/html/trazabilidad/flix/tpl/flix/botonera.php
--/var/www/html/trazabilidad/flix/tpl/header-menu.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosHerramientas_crear.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosSecciones_crear.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosBotonera_crear_form.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosHerramientas_crear_form.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosSecciones_crear_form.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosBotonera_busquedaAvanzada.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosBotonera_crear.php
--/var/www/html/trazabilidad/flix/tpl/modal/abms_usuariosSecciones_busquedaAvanzada.php

--/var/www/html/trazabilidad/batch/integracion/integracionEjecutor.sh

--/var/www/html/trazabilidad/modelo/frenteDeFrentes.php                                                                                                                                           

--/var/www/html/trazabilidad/libreria/almacenamiento/FrenteAlmacenamiento.php
--/var/www/html/trazabilidad/libreria/includes/inc.Frentes.php
--/var/www/html/trazabilidad/libreria/usuarios/Botonera.php
--/var/www/html/trazabilidad/libreria/usuarios/Herramienta.php
--/var/www/html/trazabilidad/libreria/usuarios/Seccion.php
--/var/www/html/trazabilidad/libreria/usuarios/Subseccion.php

--/var/www/html/trazabilidad/usuarios/SubseccionExtendido.php
--/var/www/html/trazabilidad/usuarios/SeccionExtendido.php
--/var/www/html/trazabilidad/usuarios/FrentePermisos.php
--/var/www/html/trazabilidad/usuarios/UsuariosBotonera.php
--/var/www/html/trazabilidad/usuarios/FrenteUsuarios.php
--/var/www/html/trazabilidad/usuarios/UsuariosSecciones.sql
--/var/www/html/trazabilidad/usuarios/BotoneraExtendido.php
--/var/www/html/trazabilidad/usuarios/UsuariosSecciones.php
--/var/www/html/trazabilidad/usuarios/FrenteHerramientas.php
--/var/www/html/trazabilidad/usuarios/UsuariosSubsecciones.php
--/var/www/html/trazabilidad/usuarios/FrenteBotones.php
--/var/www/html/trazabilidad/usuarios/UsuariosHerramientas.php




-- **** VER SQL DE USUARIOS
