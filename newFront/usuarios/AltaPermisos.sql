-----------------------------Secciones----------------------------
ALTER TABLE usuarios_secciones ADD movimientos boolean default false;
ALTER TABLE usuarios_secciones ADD laboratorio boolean default false;
ALTER TABLE usuarios_secciones ADD producción boolean default false;
ALTER TABLE usuarios_secciones ADD stock boolean default false;
ALTER TABLE usuarios_secciones ADD abms boolean default false;
ALTER TABLE usuarios_secciones ADD utilidades boolean default false;
ALTER TABLE usuarios_secciones ADD mensajeria boolean default false;
ALTER TABLE usuarios_secciones ADD pedidos boolean default false;
ALTER TABLE usuarios_secciones ADD distribución boolean default false;


-----------------------------Subsecciones----------------------------
        
--Movimientos
ALTER TABLE usuarios_subsecciones ADD entradas boolean default false;
ALTER TABLE usuarios_subsecciones ADD salidas boolean default false;
ALTER TABLE usuarios_subsecciones ADD ingresos boolean default false;
--Administración
--ALTER TABLE usuarios_subsecciones ADD listaDePrecios boolean default false;

--Stock
ALTER TABLE usuarios_subsecciones ADD consultaStock boolean default false;
ALTER TABLE usuarios_subsecciones ADD controlStock boolean default false;
ALTER TABLE usuarios_subsecciones ADD auditorStock boolean default false;
ALTER TABLE usuarios_subsecciones ADD stockEnCustodia boolean default false;
--ABMS
ALTER TABLE usuarios_subsecciones ADD categorias boolean default false;
ALTER TABLE usuarios_subsecciones ADD clientes boolean default false;
ALTER TABLE usuarios_subsecciones ADD localidades boolean default false;
ALTER TABLE usuarios_subsecciones ADD marcas boolean default false;
ALTER TABLE usuarios_subsecciones ADD productos boolean default false;
ALTER TABLE usuarios_subsecciones ADD proveedores boolean default false;
ALTER TABLE usuarios_subsecciones ADD vendedores boolean default false;

--utilidades
ALTER TABLE usuarios_subsecciones ADD actualizacionDePrecios boolean default false;

ALTER TABLE usuarios_subsecciones ADD ajustes boolean default false;
ALTER TABLE usuarios_subsecciones ADD entradasCodigos boolean default false;
ALTER TABLE usuarios_subsecciones ADD médicos boolean default false;
ALTER TABLE usuarios_subsecciones ADD almacenes boolean default false;
ALTER TABLE usuarios_subsecciones ADD estanterias boolean default false;
ALTER TABLE usuarios_subsecciones ADD obrasSociales boolean default false;
ALTER TABLE usuarios_subsecciones ADD sucursales boolean default false;
ALTER TABLE usuarios_subsecciones ADD bloqueador boolean default false;
ALTER TABLE usuarios_subsecciones ADD transaccionesAgrupadas boolean default false;
ALTER TABLE usuarios_subsecciones ADD pendientesDeConfirmacion boolean default false;
ALTER TABLE usuarios_subsecciones ADD listadoDePendientes boolean default false;
ALTER TABLE usuarios_subsecciones ADD estados boolean default false;
ALTER TABLE usuarios_subsecciones ADD pedidos boolean default false;
ALTER TABLE usuarios_subsecciones ADD provincias boolean default false;
ALTER TABLE usuarios_subsecciones ADD hojaDeRuta boolean default false;
ALTER TABLE usuarios_subsecciones ADD seguimientoHistorico boolean default false;
ALTER TABLE usuarios_subsecciones ADD custodia boolean default false;
ALTER TABLE usuarios_subsecciones ADD presentaciones boolean default false;
ALTER TABLE usuarios_subsecciones ADD ordenDeTrabajo boolean default false;
ALTER TABLE usuarios_subsecciones ADD autorizaciones boolean default false;
ALTER TABLE usuarios_subsecciones ADD interfaz boolean default false;
ALTER TABLE usuarios_subsecciones ADD glns boolean default false;
ALTER TABLE usuarios_subsecciones ADD transportistas boolean default false;
ALTER TABLE usuarios_subsecciones ADD recepcionesCierre boolean default false;
ALTER TABLE usuarios_subsecciones ADD ordenCompra boolean default false;
ALTER TABLE usuarios_subsecciones ADD importacion boolean default false;
ALTER TABLE usuarios_subsecciones ADD entregas boolean default false;
ALTER TABLE usuarios_subsecciones ADD entregasCierre boolean default false;
ALTER TABLE usuarios_subsecciones ADD modelos boolean default false;
ALTER TABLE usuarios_subsecciones ADD usuarios boolean default false;
ALTER TABLE usuarios_subsecciones ADD pacientes boolean default false;
ALTER TABLE usuarios_subsecciones ADD usuariosSecciones boolean default false;
ALTER TABLE usuarios_subsecciones ADD usuariosBotonera boolean default false;
ALTER TABLE usuarios_subsecciones ADD laboratorios boolean default false;
ALTER TABLE usuarios_subsecciones ADD municipios boolean default false;
ALTER TABLE usuarios_subsecciones ADD departamentos boolean default false;
ALTER TABLE usuarios_subsecciones ADD distribuidores boolean default false;
ALTER TABLE usuarios_subsecciones ADD busquedaPorCodigo boolean default false;
ALTER TABLE usuarios_subsecciones ADD transacciones boolean default false;
ALTER TABLE usuarios_subsecciones ADD vehiculos boolean default false;
ALTER TABLE usuarios_subsecciones ADD seguimiento boolean default false;

-----------------------------Botonera----------------------------

--Movimientos
ALTER TABLE usuarios_botonera ADD entradas_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD entradas_alta_de_stock boolean default true;
ALTER TABLE usuarios_botonera ADD entradas_alta_stock_por_codigo boolean default true;
ALTER TABLE usuarios_botonera ADD entradas_reimprimir_codigo_de_trazabilidad boolean default true;
ALTER TABLE usuarios_botonera ADD salidas_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD salidas_salida_de_stock boolean default true;
ALTER TABLE usuarios_botonera ADD salidas_exportar_salidas boolean default true;
ALTER TABLE usuarios_botonera ADD salidas_preparar_pedido boolean default true;
ALTER TABLE usuarios_botonera ADD ingresos_busqueda_avanzada boolean default true;

--Produccion
ALTER TABLE usuarios_botonera ADD ordenDeTrabajo_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD ordenDeTrabajo_crear_orden boolean default true;
ALTER TABLE usuarios_botonera ADD ordenDeTrabajo_preparar_orden boolean default true;

--Stock
ALTER TABLE usuarios_botonera ADD consultaStock_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD consultaStock_exportar_stock boolean default true;
ALTER TABLE usuarios_botonera ADD stockEnCustodia_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD auditorStock_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD auditorStock_exportar_auditor boolean default true;
ALTER TABLE usuarios_botonera ADD controlStock_control boolean default true;

--ABMS
ALTER TABLE usuarios_botonera ADD clientes_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD clientes_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD categorias_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD categorias_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD productos_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD productos_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD presentaciones_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD presentaciones_crear_nuevo boolean default true;

---ORIGINAL: productos-presentaciones_busqueda_avanzada Y productos-presentaciones_crear_nuevo 
--se guardan asi porque el guion medio tira error
ALTER TABLE usuarios_botonera ADD productos_presentaciones_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD productos_presentaciones_crear_nuevo boolean default true;

ALTER TABLE usuarios_botonera ADD marcas_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD marcas_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD custodia_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD custodia_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD medicos_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD medicos_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD pacientes_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD pacientes_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD proveedores_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD proveedores_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD almacenes_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD almacenes_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD estanterias_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD estanterias_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD sucursales_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD sucursales_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD obrasSociales_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD obrasSociales_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD vendedores_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD vendedores_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD distribuidores_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD distribuidores_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD estados_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD estados_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD provincias_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD provincias_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD localidades_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD localidades_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD laboratorios_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD laboratorios_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD municipios_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD municipios_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD departamentos_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD departamentos_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD usuarios_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD usuarios_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD usuariosSecciones_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD usuariosBotonera_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD usuariosSecciones_alta_de_seccion_subseccion boolean default true;
ALTER TABLE usuarios_botonera ADD usuariosBotonera_alta_de_botones boolean default true;
ALTER TABLE usuarios_botonera ADD usuariosHerramientas_alta_de_herramientas boolean default true;
ALTER TABLE usuarios_botonera ADD vehiculos_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD vehiculos_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD modelos_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD modelos_busqueda_avanzada boolean default true;

--Mensajeria
ALTER TABLE usuarios_botonera ADD transacciones_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD transacciones_reproceso boolean default true;
ALTER TABLE usuarios_botonera ADD transaccionesAgrupadas_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD transaccionesAgrupadas_exportador_movimientos_sap boolean default true;
ALTER TABLE usuarios_botonera ADD bloqueador_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD pendientesDeConfirmacion_busqueda_avanzada boolean default true;

--Pedidos
ALTER TABLE usuarios_botonera ADD listadoDePendientes_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD pedidos_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD pedidos_alta_pedidos boolean default true;
ALTER TABLE usuarios_botonera ADD seguimientoHistorico_busqueda_avanzada boolean default true;

--Distribución
ALTER TABLE usuarios_botonera ADD hojaDeRuta_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD hojaDeRuta_alta_hdr boolean default true;
ALTER TABLE usuarios_botonera ADD seguimiento_busqueda_avanzada boolean default true;

--utilidades
ALTER TABLE usuarios_botonera ADD actualizacionDePrecios_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD actualizacionDePrecios_actualizar boolean default true;

--Administración
ALTER TABLE usuarios_botonera ADD listaDePrecios_busqueda_avanzada boolean default true;
ALTER TABLE usuarios_botonera ADD listaDePrecios_crear_nuevo boolean default true;
ALTER TABLE usuarios_botonera ADD listaDePrecios_exportar_listas boolean default true;



---Para poder verlo de una
