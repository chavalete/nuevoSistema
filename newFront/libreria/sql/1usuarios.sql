CREATE SEQUENCE seq_datos_usuarios_id;

CREATE TABLE datos_usuarios 
    (
    usuario_id smallint NOT NULL PRIMARY KEY,
    usuario_nombre character varying(100) NOT NULL,
    usuario_password character varying(200) NOT NULL,
    usuario_nombre_completo character varying(100) NOT NULL,
    usuario_fecha_alta date NOT NULL,
    usuario_activo boolean DEFAULT true,
    usuario_fecha_inactivo date,
    usuario_ultimo_login timestamp,
    usuario_administrador boolean
    ) WITH OIDS;

CREATE UNIQUE INDEX da_usuarios_usuario_idx ON datos_usuarios (usuario_id);
CREATE INDEX da_usuarios_usuario_activo_true ON datos_usuarios (usuario_id) WHERE (usuario_activo=true);
CREATE INDEX da_usuarios_usuario_activo_false ON datos_usuarios (usuario_id) WHERE (usuario_activo=false);