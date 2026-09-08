CREATE TABLE emails (
email_id integer,
emisor varchar,
email_emisor varchar,
email_receptor varchar,
asunto text,
cuerpo text,
cabecera text
) WITH OIDS;

CREATE INDEX idx_emails_email_id ON emails(email_id);

CREATE SEQUENCE seq_emails_email_id START 1;


CREATE SEQUENCE seq_log_email_envios_envio_id START 1;

CREATE TABLE log_email_envios (
envio_id integer DEFAULT nextval('seq_log_email_envios_envio_id'),
ultima_transaccion_id integer,
fecha_hora_envio timestamp
)with oids; 

CREATE UNIQUE INDEX idx_log_email_envios ON log_email_envios (envio_id);
CREATE UNIQUE INDEX idx_log_email_envios_ultima_transacc_id ON log_email_envios (ultima_transaccion_id);

ALTER TABLE datos_sucursales_usuarios_anmat ADD email varchar;
