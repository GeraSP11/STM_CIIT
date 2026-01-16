-- version_1_5

CREATE DATABASE ciit_tms;

CREATE TABLE localidades (
    id_localidad SERIAL PRIMARY KEY,
    nombre_centro_trabajo VARCHAR(100),
    clave_centro_trabajo VARCHAR(100),
    ubicacion_georeferenciada VARCHAR(200),
    poblacion VARCHAR(100),
    localidad VARCHAR(100),
    estado VARCHAR(100),
    tipo_instalacion VARCHAR(50) CHECK (tipo_instalacion IN 
        ('Centro Productivo', 'Centro de Distribucion', 'PODEBI', 'Almacen'))
);

CREATE TABLE tipo_recepcion(
    id_recepcion SERIAL PRIMARY KEY,
    localidad INT REFERENCES localidades(id_localidad) 
        ON DELETE CASCADE ON UPDATE CASCADE, 
    modalidad_vehiculo VARCHAR(50) CHECK (modalidad_vehiculo IN 
        ('Carretero', 'Ferroviario', 'Marítimo', 'Aéreo')),
    capacidad_simultanea INT
);


CREATE TABLE personal (
    id_personal SERIAL PRIMARY KEY,
    nombre_personal VARCHAR(100),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    numero_empleado INT,
    afiliacion_laboral INT REFERENCES localidades(id_localidad) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    cargo VARCHAR(50) CHECK (cargo IN 
        ('Autoridad', 'Administrador del TMS', 'Operador Logístico', 'Cliente', 'Jefe de Almacén', 'Chófer')),
    curp VARCHAR(18)
);

CREATE TABLE usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nombre_usuario VARCHAR(100),
    contrasena VARCHAR(100),
    correo_electronico VARCHAR(150),
    identificador_de_rh INT REFERENCES personal(id_personal)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE productos (
    id_producto SERIAL PRIMARY KEY,
    nombre_producto VARCHAR(100),
    ubicacion_producto INT REFERENCES localidades(id_localidad) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    peso FLOAT,
    altura FLOAT,
    largo FLOAT,
    ancho FLOAT,
    cajas_por_cama INT,
    camas_por_pallet INT,
    peso_soportado FLOAT,
    peso_volumetrico FLOAT,
    unidades_existencia FLOAT,
    tipo_de_embalaje VARCHAR(100) CHECK (tipo_de_embalaje IN (
        'Embalaje primario','Embalaje secundario','Embalaje terciario','Caja de cartón corrugado',
        'Caja rígida','Caja plegadiza','Bolsa plástica','Bolsa de polietileno','Saco','Fardo',
        'Tambo de acero','Tambo plástico','Bidón','Granel sólido','Granel líquido','Pallet de madera',
        'Pallet plástico','Pallet metálico','Emplaye / stretch film','Retráctil (shrink wrap)',
        'Contenedor marítimo','Contenedor refrigerado','Contenedor a granel',
        'IBC (Intermediate Bulk Container)','Cráte','Huacal','Estiba','Super sack (big bag)',
        'Envase de vidrio','Envase metálico','Envase plástico',
        'Tarimas especiales NOM-SCT para mercancías peligrosas'
    )),
    tipo_de_mercancia VARCHAR(100) CHECK (tipo_de_mercancia IN (
        'Mercancías peligrosas','Perecederas','Cadena de frío','Electrónicas / electromecánicas',
        'Frágiles','Industriales','Sustancias químicas','Alimentos procesados','Textiles y moda',
        'Culturales / editoriales','Vehículos y autopartes','Muebles y enseres',
        'Materiales de construcción','Agropecuarias','Bienes de alto valor'
    )),
    observaciones VARCHAR(200)
);

CREATE TABLE productos_localidades (
    id_pl SERIAL PRIMARY KEY,
    id_producto INT REFERENCES productos(id_producto) ON DELETE CASCADE ON UPDATE CASCADE,
    id_localidad INT REFERENCES localidades(id_localidad) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE contenedores_teu(
    id_contenedor SERIAL PRIMARY KEY,
    etiqueta VARCHAR(50),
    largo FLOAT,
    ancho FLOAT,
    altura FLOAT,
    descripcion VARCHAR (100)
);

CREATE TABLE transacciones_productos (
    id_transaccion SERIAL PRIMARY KEY,
    numero_transaccion VARCHAR(50),
    localidad INT REFERENCES localidades(id_localidad) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    tipo_transaccion VARCHAR(50) CHECK (tipo_transaccion IN 
        ('Ingreso de producto', 'Salida de producto', 'Solicitud de Pedido')),
    fecha_inicio_transaccion DATE,
    fecha_finalizacion_transaccion DATE,
    usuario INT REFERENCES usuarios(id_usuario) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE vehiculos (
    id_vehiculo SERIAL PRIMARY KEY,
    clave_vehiculo VARCHAR(50),
    modalidad_vehiculo VARCHAR(50) CHECK (modalidad_vehiculo IN 
        ('Carretero', 'Ferroviario', 'Marítimo', 'Aéreo')),
    descripcion_vehiculo VARCHAR(100),
    chofer_asignado INT REFERENCES personal(id_personal) 
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    clase VARCHAR(2),
    nomenclatura VARCHAR(8),
    numero_de_ejes INT,
    numero_de_llantas INT,
    peso_bruto_vehicular FLOAT
);

CREATE TABLE carrocerias ( 
    id_carroceria SERIAL PRIMARY KEY,
    matricula VARCHAR(50),
    localidad_pertenece INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    responsable_carroceria INT REFERENCES personal(id_personal)
        ON DELETE SET NULL ON UPDATE CASCADE,
    numero_contenedores INT,
    peso_vehicular FLOAT,
    numero_ejes_vehiculares INT,
    tipo_carroceria varchar (30) CHECK (tipo_carroceria IN 
        ('Unidad de arrastre', 'Unidad de carga', 'Mixta')),
    estatus_carroceria varchar(15) CHECK (estatus_carroceria IN 
        ('Disponible', 'Ensamblada', 'En mantenimiento', 'En reparación')),
    modalidad_carroceria VARCHAR (30) CHECK (modalidad_carroceria IN
        ('Carretero', 'Ferroviario', 'Marítimo', 'Aéreo'))
);

CREATE TABLE vehiculos_carrocerias (
    id_vehiculo_carroceria SERIAL PRIMARY KEY,
    id_vehiculo INT REFERENCES vehiculos(id_vehiculo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    id_carroceria INT REFERENCES carrocerias(id_carroceria)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE carrocerias_detalle (
    id_carrocerias_detalle SERIAL PRIMARY KEY,
    identificador_carroceria INT REFERENCES carrocerias(id_carroceria)
        ON DELETE CASCADE ON UPDATE CASCADE,
    numero_contenedor INT,
    longitud FLOAT,
    anchura FLOAT,
    altura FLOAT    
);

CREATE TABLE maniobras (
    id_maniobra SERIAL PRIMARY KEY,
    localidad INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    vehiculo INT REFERENCES vehiculos(id_vehiculo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    tiempo_carga INT,
    tiempo_descarga INT,
    tiempo_almacenaje INT

);

CREATE TABLE mantenimientos_vehiculos (
    id_mantenimiento SERIAL PRIMARY KEY,
    id_transporte INT REFERENCES vehiculos(id_vehiculo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    fecha_ingreso DATE,
    fecha_salida DATE,
    descripcion_servicios VARCHAR(200)
);

CREATE TABLE rutas (
    id_ruta SERIAL PRIMARY KEY,
    localidad_origen INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    localidad_destino INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    modalidad_ruta VARCHAR(50) CHECK (modalidad_ruta IN 
        ('Carretera', 'Ferroviaria', 'Marítima', 'Aérea')),
    tipo_ruta varchar(3),
    distancia FLOAT,
    peso_soportado FLOAT,
    descripcion VARCHAR(200)
);

CREATE TABLE pedidos (
    id_pedido SERIAL PRIMARY KEY,
    clave_pedido VARCHAR(50) NOT NULL,
    localidad_origen INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    localidad_destino INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    estatus_pedido VARCHAR(50) CHECK (estatus_pedido  IN 
        ('En captura', 'En preparación', 'En recolección', 'Enviado', 'En tránsito', 'En reparto', 'Entregado')) ,
    fecha_solicitud DATE,
    fecha_entrega DATE,
    observaciones varchar(200)
);

CREATE TABLE pedidos_detalles (
    id_pedido_detalles SERIAL PRIMARY KEY,
    pedido INT REFERENCES pedidos(id_pedido)
        ON DELETE CASCADE ON UPDATE CASCADE,
    identificador_producto INT REFERENCES productos(id_producto)
        ON DELETE CASCADE ON UPDATE CASCADE,
    cantidad_producto INT,
    observaciones VARCHAR(200)
);

CREATE TABLE fleteros (
    id_fletero SERIAL PRIMARY KEY,
    localidad_origen INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    localidad_destino INT REFERENCES localidades(id_localidad)
        ON DELETE CASCADE ON UPDATE CASCADE,
    identificador_vehiculo INT REFERENCES vehiculos(id_vehiculo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    fecha_y_hora_llegada TIMESTAMP,
    fecha_y_hora_salida TIMESTAMP
);

CREATE TABLE fleteros_detalle (
    id_fletero_detalle SERIAL PRIMARY KEY,
    identificador_flete INT REFERENCES fleteros(id_fletero)
        ON DELETE CASCADE ON UPDATE CASCADE,
    identificador_producto INT REFERENCES productos(id_producto)
        ON DELETE CASCADE ON UPDATE CASCADE,
    numero_unidades INT,
    numero_contenedor INT,
    observaciones VARCHAR(200)
);

CREATE TABLE envios (
    id_envio SERIAL PRIMARY KEY,
    identificador_flete INT REFERENCES fleteros(id_fletero)
        ON DELETE CASCADE ON UPDATE CASCADE,
    identificador_pedido INT REFERENCES pedidos(id_pedido)
        ON DELETE CASCADE ON UPDATE CASCADE,
    punto_verificacion INT REFERENCES localidades(id_localidad),
    personal_asignado INT REFERENCES personal(id_personal)
        ON DELETE CASCADE ON UPDATE CASCADE,
    personal_verifica INT REFERENCES personal(id_personal)
        ON DELETE CASCADE ON UPDATE CASCADE,
    fecha_verificacion DATE,
    geolocalizacion VARCHAR(200)
);