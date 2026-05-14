-- ============================================================
-- ESQUEMA BASE DE DATOS - RAPTORLEARN
-- Portal Educativo Interactivo sobre Rapaces Ibéricas
-- ============================================================

-- ============================================================
-- MÓDULO: USUARIOS Y AUTENTICACIÓN
-- ============================================================

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100),
    fecha_nacimiento DATE,
    tipo_usuario ENUM('estudiante', 'educador', 'admin') DEFAULT 'estudiante',
    avatar VARCHAR(255),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO: ENCICLOPEDIA DE RAPACES
-- ============================================================

CREATE TABLE especies (
    id_especie INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cientifico VARCHAR(100) UNIQUE NOT NULL,
    nombre_comun VARCHAR(100) NOT NULL,
    nombre_ingles VARCHAR(100),
    familia VARCHAR(50),
    orden VARCHAR(50) DEFAULT 'Accipitriformes',
    descripcion TEXT,
    caracteristicas_fisicas TEXT,
    envergadura_min DECIMAL(4,2), -- en metros
    envergadura_max DECIMAL(4,2),
    peso_min DECIMAL(5,2), -- en gramos
    peso_max DECIMAL(5,2),
    longitud_min DECIMAL(4,2), -- en cm
    longitud_max DECIMAL(4,2),
    dimorfismo_sexual TEXT,
    habitat TEXT,
    distribucion_geografica TEXT,
    altitud_min INT,
    altitud_max INT,
    dieta TEXT,
    comportamiento_caza TEXT,
    reproduccion TEXT,
    epoca_cria VARCHAR(50),
    numero_huevos VARCHAR(20),
    estado_conservacion ENUM('LC', 'NT', 'VU', 'EN', 'CR', 'EW', 'EX'),
    poblacion_iberica TEXT,
    amenazas TEXT,
    medidas_conservacion TEXT,
    curiosidades TEXT,
    dificultad_identificacion ENUM('fácil', 'medio', 'difícil') DEFAULT 'medio',
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre_comun (nombre_comun),
    INDEX idx_familia (familia),
    INDEX idx_conservacion (estado_conservacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE imagenes_especies (
    id_imagen INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    ruta_imagen VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255),
    tipo ENUM('foto', 'silueta', 'vuelo', 'juvenil', 'habitat') DEFAULT 'foto',
    es_principal BOOLEAN DEFAULT FALSE,
    orden_visualizacion INT DEFAULT 0,
    creditos VARCHAR(200),
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    INDEX idx_especie (id_especie),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audios_especies (
    id_audio INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    ruta_audio VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255),
    tipo_canto ENUM('llamada', 'alarma', 'cortejo', 'territorial') DEFAULT 'llamada',
    duracion_segundos INT,
    creditos VARCHAR(200),
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    INDEX idx_especie (id_especie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mapas_distribucion (
    id_mapa INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    tipo_mapa ENUM('reproduccion', 'invernada', 'migración', 'residente') NOT NULL,
    datos_geojson TEXT, -- Coordenadas en formato GeoJSON
    ruta_imagen_mapa VARCHAR(255),
    descripcion TEXT,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    INDEX idx_especie (id_especie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE especies_similares (
    id_comparacion INT AUTO_INCREMENT PRIMARY KEY,
    id_especie_1 INT NOT NULL,
    id_especie_2 INT NOT NULL,
    diferencias_clave TEXT,
    FOREIGN KEY (id_especie_1) REFERENCES especies(id_especie) ON DELETE CASCADE,
    FOREIGN KEY (id_especie_2) REFERENCES especies(id_especie) ON DELETE CASCADE,
    INDEX idx_especie1 (id_especie_1),
    INDEX idx_especie2 (id_especie_2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO: GAMIFICACIÓN
-- ============================================================

CREATE TABLE niveles (
    id_nivel INT AUTO_INCREMENT PRIMARY KEY,
    numero_nivel INT UNIQUE NOT NULL,
    nombre_nivel VARCHAR(50) NOT NULL,
    puntos_necesarios INT NOT NULL,
    recompensa_descripcion TEXT,
    icono VARCHAR(255),
    INDEX idx_numero (numero_nivel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE progreso_usuarios (
    id_progreso INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_nivel_actual INT NOT NULL,
    puntos_totales INT DEFAULT 0,
    puntos_nivel_actual INT DEFAULT 0,
    especies_descubiertas INT DEFAULT 0,
    fecha_ultima_actividad DATETIME DEFAULT CURRENT_TIMESTAMP,
    racha_dias INT DEFAULT 0,
    ultima_fecha_racha DATE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_nivel_actual) REFERENCES niveles(id_nivel),
    UNIQUE KEY unique_usuario_progreso (id_usuario),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE insignias (
    id_insignia INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(255),
    categoria ENUM('especies', 'conocimiento', 'conservación', 'social', 'especial'),
    condicion_desbloqueo TEXT,
    puntos_recompensa INT DEFAULT 0,
    rareza ENUM('común', 'rara', 'épica', 'legendaria') DEFAULT 'común',
    activa BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE insignias_usuarios (
    id_insignia_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_insignia INT NOT NULL,
    fecha_obtencion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_insignia) REFERENCES insignias(id_insignia) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_insignia (id_usuario, id_insignia),
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_obtencion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE misiones (
    id_mision INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo_mision ENUM('diaria', 'semanal', 'permanente', 'evento') DEFAULT 'permanente',
    categoria VARCHAR(50),
    objetivo_cantidad INT DEFAULT 1,
    puntos_recompensa INT NOT NULL,
    id_insignia_recompensa INT,
    requisito_nivel_minimo INT DEFAULT 1,
    fecha_inicio DATE,
    fecha_fin DATE,
    activa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_insignia_recompensa) REFERENCES insignias(id_insignia) ON DELETE SET NULL,
    INDEX idx_tipo (tipo_mision),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE misiones_usuarios (
    id_mision_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_mision INT NOT NULL,
    progreso_actual INT DEFAULT 0,
    completada BOOLEAN DEFAULT FALSE,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_completada DATETIME,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_mision) REFERENCES misiones(id_mision) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_mision (id_usuario, id_mision),
    INDEX idx_usuario (id_usuario),
    INDEX idx_completada (completada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE especies_descubiertas (
    id_descubrimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_especie INT NOT NULL,
    fecha_descubrimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
    puntos_obtenidos INT DEFAULT 10,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_especie (id_usuario, id_especie),
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_descubrimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO: MINI-JUEGOS EDUCATIVOS
-- ============================================================

CREATE TABLE juegos (
    id_juego INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo_juego ENUM('cuestionario', 'siluetas', 'puzzle', 'memoria', 'sonidos') NOT NULL,
    descripcion TEXT,
    dificultad ENUM('fácil', 'medio', 'difícil') DEFAULT 'medio',
    puntos_base INT DEFAULT 10,
    tiempo_limite INT, -- en segundos, NULL si no tiene límite
    intentos_maximos INT,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_tipo (tipo_juego)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cuestionarios (
    id_cuestionario INT AUTO_INCREMENT PRIMARY KEY,
    id_juego INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50),
    numero_preguntas INT NOT NULL,
    tiempo_total INT, -- en segundos
    puntuacion_minima_aprobar INT,
    FOREIGN KEY (id_juego) REFERENCES juegos(id_juego) ON DELETE CASCADE,
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE preguntas (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    id_cuestionario INT NOT NULL,
    enunciado TEXT NOT NULL,
    tipo_pregunta ENUM('multiple', 'verdadero_falso', 'relacionar') DEFAULT 'multiple',
    imagen_asociada VARCHAR(255),
    audio_asociado VARCHAR(255),
    puntos INT DEFAULT 10,
    explicacion TEXT,
    orden_presentacion INT DEFAULT 0,
    FOREIGN KEY (id_cuestionario) REFERENCES cuestionarios(id_cuestionario) ON DELETE CASCADE,
    INDEX idx_cuestionario (id_cuestionario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE respuestas (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_pregunta INT NOT NULL,
    texto_respuesta TEXT NOT NULL,
    es_correcta BOOLEAN DEFAULT FALSE,
    orden_presentacion INT DEFAULT 0,
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta) ON DELETE CASCADE,
    INDEX idx_pregunta (id_pregunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE partidas (
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_juego INT NOT NULL,
    puntuacion INT DEFAULT 0,
    tiempo_empleado INT, -- en segundos
    completada BOOLEAN DEFAULT FALSE,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_juego) REFERENCES juegos(id_juego) ON DELETE CASCADE,
    INDEX idx_usuario (id_usuario),
    INDEX idx_juego (id_juego),
    INDEX idx_fecha (fecha_inicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE respuestas_usuarios (
    id_respuesta_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_partida INT NOT NULL,
    id_pregunta INT NOT NULL,
    id_respuesta_seleccionada INT,
    es_correcta BOOLEAN,
    tiempo_respuesta INT, -- en segundos
    fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_partida) REFERENCES partidas(id_partida) ON DELETE CASCADE,
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta) ON DELETE CASCADE,
    FOREIGN KEY (id_respuesta_seleccionada) REFERENCES respuestas(id_respuesta) ON DELETE SET NULL,
    INDEX idx_partida (id_partida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO: CONTENIDOS EDUCATIVOS Y CURIOSIDADES
-- ============================================================

CREATE TABLE articulos (
    id_articulo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    categoria ENUM('curiosidad', 'mito', 'conservación', 'noticia', 'investigación') NOT NULL,
    autor VARCHAR(100),
    imagen_destacada VARCHAR(255),
    resumen TEXT,
    fuente VARCHAR(255),
    url_externa VARCHAR(500),
    visitas INT DEFAULT 0,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    destacado BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_categoria (categoria),
    INDEX idx_fecha (fecha_publicacion),
    INDEX idx_destacado (destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE articulos_especies (
    id_articulo_especie INT AUTO_INCREMENT PRIMARY KEY,
    id_articulo INT NOT NULL,
    id_especie INT NOT NULL,
    FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo) ON DELETE CASCADE,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    INDEX idx_articulo (id_articulo),
    INDEX idx_especie (id_especie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO: ÁREA DE EDUCADORES
-- ============================================================

CREATE TABLE recursos_educativos (
    id_recurso INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    tipo_recurso ENUM('ficha_pdf', 'actividad', 'guia', 'presentacion', 'evaluacion') NOT NULL,
    nivel_educativo VARCHAR(50), -- Primaria, ESO, Bachillerato
    asignaturas TEXT, -- Ciencias Naturales, Biología, etc.
    archivo_ruta VARCHAR(255),
    imagen_previa VARCHAR(255),
    descargas INT DEFAULT 0,
    valoracion_media DECIMAL(3,2),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_tipo (tipo_recurso),
    INDEX idx_nivel (nivel_educativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recursos_especies (
    id_recurso_especie INT AUTO_INCREMENT PRIMARY KEY,
    id_recurso INT NOT NULL,
    id_especie INT NOT NULL,
    FOREIGN KEY (id_recurso) REFERENCES recursos_educativos(id_recurso) ON DELETE CASCADE,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    INDEX idx_recurso (id_recurso),
    INDEX idx_especie (id_especie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE valoraciones_recursos (
    id_valoracion INT AUTO_INCREMENT PRIMARY KEY,
    id_recurso INT NOT NULL,
    id_usuario INT NOT NULL,
    puntuacion INT CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_valoracion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_recurso) REFERENCES recursos_educativos(id_recurso) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_recurso (id_usuario, id_recurso),
    INDEX idx_recurso (id_recurso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE certificados (
    id_certificado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo_certificado VARCHAR(100) NOT NULL,
    descripcion TEXT,
    codigo_verificacion VARCHAR(50) UNIQUE NOT NULL,
    archivo_pdf VARCHAR(255),
    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_usuario (id_usuario),
    INDEX idx_codigo (codigo_verificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO: SISTEMA DE ACTIVIDAD Y ESTADÍSTICAS
-- ============================================================

CREATE TABLE historial_actividad (
    id_actividad INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo_actividad ENUM('login', 'especie_vista', 'juego_jugado', 'insignia_obtenida', 
                        'mision_completada', 'recurso_descargado', 'articulo_leido') NOT NULL,
    descripcion VARCHAR(255),
    puntos_obtenidos INT DEFAULT 0,
    fecha_actividad DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_usuario (id_usuario),
    INDEX idx_tipo (tipo_actividad),
    INDEX idx_fecha (fecha_actividad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE estadisticas_globales (
    id_estadistica INT AUTO_INCREMENT PRIMARY KEY,
    total_usuarios INT DEFAULT 0,
    total_partidas_jugadas INT DEFAULT 0,
    total_especies_descubiertas INT DEFAULT 0,
    total_recursos_descargados INT DEFAULT 0,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO: CONFIGURACIÓN Y ADMINISTRACIÓN
-- ============================================================

CREATE TABLE configuracion_sistema (
    id_config INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion TEXT,
    tipo_dato ENUM('string', 'int', 'boolean', 'json') DEFAULT 'string',
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLAS AUXILIARES Y DE SOPORTE
-- ============================================================

-- Familias de rapaces (catálogo)
CREATE TABLE familias_rapaces (
    id_familia INT AUTO_INCREMENT PRIMARY KEY,
    nombre_familia VARCHAR(50) UNIQUE NOT NULL,
    nombre_cientifico VARCHAR(100),
    descripcion TEXT,
    caracteristicas_generales TEXT,
    numero_especies_ibericas INT DEFAULT 0,
    imagen_representativa VARCHAR(255),
    INDEX idx_nombre (nombre_familia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estados de conservación (catálogo UICN)
CREATE TABLE estados_conservacion (
    codigo VARCHAR(2) PRIMARY KEY,
    nombre_espanol VARCHAR(50) NOT NULL,
    nombre_ingles VARCHAR(50) NOT NULL,
    descripcion TEXT,
    color_representativo VARCHAR(7), -- código hexadecimal
    nivel_amenaza INT, -- 1 (menor) a 7 (mayor)
    INDEX idx_nivel (nivel_amenaza)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comunidades Autónomas (para distribución geográfica)
CREATE TABLE comunidades_autonomas (
    id_comunidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    codigo VARCHAR(3) UNIQUE NOT NULL, -- AND, CAT, GAL, etc.
    area_km2 DECIMAL(10,2),
    coordenadas_centro VARCHAR(100), -- lat,lng
    INDEX idx_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación especies-comunidades autónomas
CREATE TABLE especies_comunidades (
    id_especie_comunidad INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    id_comunidad INT NOT NULL,
    presencia ENUM('residente', 'reproductor', 'invernante', 'migrante', 'ocasional') NOT NULL,
    poblacion_estimada VARCHAR(100),
    notas TEXT,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    FOREIGN KEY (id_comunidad) REFERENCES comunidades_autonomas(id_comunidad) ON DELETE CASCADE,
    UNIQUE KEY unique_especie_comunidad (id_especie, id_comunidad),
    INDEX idx_especie (id_especie),
    INDEX idx_comunidad (id_comunidad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hábitats (catálogo)
CREATE TABLE habitats (
    id_habitat INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT,
    icono VARCHAR(255),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación especies-hábitats (muchos a muchos)
CREATE TABLE especies_habitats (
    id_especie_habitat INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    id_habitat INT NOT NULL,
    preferencia ENUM('alta', 'media', 'baja') DEFAULT 'media',
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    FOREIGN KEY (id_habitat) REFERENCES habitats(id_habitat) ON DELETE CASCADE,
    UNIQUE KEY unique_especie_habitat (id_especie, id_habitat),
    INDEX idx_especie (id_especie),
    INDEX idx_habitat (id_habitat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de alimentación (catálogo)
CREATE TABLE tipos_alimentacion (
    id_tipo_alimentacion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    icono VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación especies-alimentación
CREATE TABLE especies_alimentacion (
    id_especie_alimentacion INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    id_tipo_alimentacion INT NOT NULL,
    porcentaje_dieta INT CHECK (porcentaje_dieta BETWEEN 0 AND 100),
    notas TEXT,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    FOREIGN KEY (id_tipo_alimentacion) REFERENCES tipos_alimentacion(id_tipo_alimentacion) ON DELETE CASCADE,
    UNIQUE KEY unique_especie_alimentacion (id_especie, id_tipo_alimentacion),
    INDEX idx_especie (id_especie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Amenazas (catálogo)
CREATE TABLE amenazas (
    id_amenaza INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT,
    tipo_amenaza ENUM('directa', 'indirecta', 'natural') DEFAULT 'directa',
    gravedad ENUM('baja', 'media', 'alta', 'crítica') DEFAULT 'media'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación especies-amenazas
CREATE TABLE especies_amenazas (
    id_especie_amenaza INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    id_amenaza INT NOT NULL,
    impacto ENUM('bajo', 'moderado', 'alto', 'crítico') DEFAULT 'moderado',
    notas TEXT,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    FOREIGN KEY (id_amenaza) REFERENCES amenazas(id_amenaza) ON DELETE CASCADE,
    UNIQUE KEY unique_especie_amenaza (id_especie, id_amenaza),
    INDEX idx_especie (id_especie),
    INDEX idx_amenaza (id_amenaza)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Etiquetas/Tags para contenidos
CREATE TABLE etiquetas (
    id_etiqueta INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    tipo ENUM('especie', 'comportamiento', 'conservación', 'educativo', 'general') DEFAULT 'general',
    contador_uso INT DEFAULT 0,
    INDEX idx_nombre (nombre),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación artículos-etiquetas
CREATE TABLE articulos_etiquetas (
    id_articulo_etiqueta INT AUTO_INCREMENT PRIMARY KEY,
    id_articulo INT NOT NULL,
    id_etiqueta INT NOT NULL,
    FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo) ON DELETE CASCADE,
    FOREIGN KEY (id_etiqueta) REFERENCES etiquetas(id_etiqueta) ON DELETE CASCADE,
    UNIQUE KEY unique_articulo_etiqueta (id_articulo, id_etiqueta),
    INDEX idx_articulo (id_articulo),
    INDEX idx_etiqueta (id_etiqueta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación recursos-etiquetas
CREATE TABLE recursos_etiquetas (
    id_recurso_etiqueta INT AUTO_INCREMENT PRIMARY KEY,
    id_recurso INT NOT NULL,
    id_etiqueta INT NOT NULL,
    FOREIGN KEY (id_recurso) REFERENCES recursos_educativos(id_recurso) ON DELETE CASCADE,
    FOREIGN KEY (id_etiqueta) REFERENCES etiquetas(id_etiqueta) ON DELETE CASCADE,
    UNIQUE KEY unique_recurso_etiqueta (id_recurso, id_etiqueta),
    INDEX idx_recurso (id_recurso),
    INDEX idx_etiqueta (id_etiqueta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Glosario de términos ornitológicos
CREATE TABLE glosario (
    id_termino INT AUTO_INCREMENT PRIMARY KEY,
    termino VARCHAR(100) UNIQUE NOT NULL,
    definicion TEXT NOT NULL,
    categoria VARCHAR(50),
    termino_ingles VARCHAR(100),
    imagen_ilustrativa VARCHAR(255),
    visitas INT DEFAULT 0,
    INDEX idx_termino (termino),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Favoritos de usuarios
CREATE TABLE favoritos_especies (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_especie INT NOT NULL,
    fecha_marcado DATETIME DEFAULT CURRENT_TIMESTAMP,
    notas_personales TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_favorito (id_usuario, id_especie),
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_marcado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Marcadores de artículos
CREATE TABLE marcadores_articulos (
    id_marcador INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_articulo INT NOT NULL,
    fecha_marcado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_articulo (id_usuario, id_articulo),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comentarios en artículos
CREATE TABLE comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_articulo INT NOT NULL,
    id_comentario_padre INT, -- Para respuestas anidadas
    contenido TEXT NOT NULL,
    fecha_comentario DATETIME DEFAULT CURRENT_TIMESTAMP,
    editado BOOLEAN DEFAULT FALSE,
    fecha_edicion DATETIME,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo) ON DELETE CASCADE,
    FOREIGN KEY (id_comentario_padre) REFERENCES comentarios(id_comentario) ON DELETE CASCADE,
    INDEX idx_articulo (id_articulo),
    INDEX idx_usuario (id_usuario),
    INDEX idx_padre (id_comentario_padre),
    INDEX idx_fecha (fecha_comentario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rankings globales (tabla desnormalizada para performance)
CREATE TABLE ranking_usuarios (
    id_ranking INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo_ranking ENUM('puntos_totales', 'especies_descubiertas', 'insignias_obtenidas', 'partidas_ganadas') NOT NULL,
    posicion INT NOT NULL,
    valor INT NOT NULL,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_tipo (id_usuario, tipo_ranking),
    INDEX idx_tipo_posicion (tipo_ranking, posicion),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Logs de administración
CREATE TABLE logs_admin (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    id_registro_afectado INT,
    detalles TEXT,
    ip_origen VARCHAR(45),
    fecha_accion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_accion),
    INDEX idx_tabla (tabla_afectada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reportes de usuarios (contenido inapropiado)
CREATE TABLE reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario_reporta INT NOT NULL,
    tipo_contenido ENUM('comentario', 'articulo', 'usuario', 'recurso') NOT NULL,
    id_contenido INT NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('pendiente', 'revisado', 'resuelto', 'descartado') DEFAULT 'pendiente',
    id_moderador INT,
    notas_moderador TEXT,
    fecha_reporte DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion DATETIME,
    FOREIGN KEY (id_usuario_reporta) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_moderador) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_reporte),
    INDEX idx_tipo (tipo_contenido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notificaciones de usuarios
CREATE TABLE notificaciones (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo_notificacion ENUM('insignia', 'nivel', 'mision', 'comentario', 'sistema', 'logro') NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    url_destino VARCHAR(255),
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_lectura DATETIME,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_usuario_leida (id_usuario, leida),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sesiones activas (para gestión de login)
CREATE TABLE sesiones (
    id_sesion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token_sesion VARCHAR(255) UNIQUE NOT NULL,
    ip_conexion VARCHAR(45),
    user_agent TEXT,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_token (token_sesion),
    INDEX idx_usuario (id_usuario),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Insertar niveles básicos
INSERT INTO niveles (numero_nivel, nombre_nivel, puntos_necesarios, recompensa_descripcion) VALUES
(1, 'Observador Novato', 0, 'Bienvenido a RaptorLearn'),
(2, 'Aprendiz de Rapaces', 100, 'Desbloqueo de primer cuestionario avanzado'),
(3, 'Explorador Alado', 250, 'Avatar especial de águila'),
(4, 'Conocedor de Aves', 500, 'Acceso a contenido exclusivo'),
(5, 'Experto Ornitólogo', 1000, 'Insignia de Maestro'),
(6, 'Guardián de Rapaces', 2000, 'Certificado digital descargable'),
(7, 'Maestro de las Aves', 4000, 'Reconocimiento especial');

-- Insertar estados de conservación UICN
INSERT INTO estados_conservacion (codigo, nombre_espanol, nombre_ingles, descripcion, color_representativo, nivel_amenaza) VALUES
('LC', 'Preocupación Menor', 'Least Concern', 'Especie abundante y extendida', '#60C659', 1),
('NT', 'Casi Amenazada', 'Near Threatened', 'Especie cerca de calificar como amenazada', '#CCE226', 2),
('VU', 'Vulnerable', 'Vulnerable', 'Alto riesgo de extinción en estado silvestre', '#F9E814', 3),
('EN', 'En Peligro', 'Endangered', 'Muy alto riesgo de extinción', '#FC7F3F', 4),
('CR', 'En Peligro Crítico', 'Critically Endangered', 'Riesgo extremadamente alto de extinción', '#D81E05', 5),
('EW', 'Extinta en Estado Silvestre', 'Extinct in the Wild', 'Solo sobrevive en cautividad', '#542344', 6),
('EX', 'Extinta', 'Extinct', 'No quedan individuos vivos', '#000000', 7);

-- Insertar familias de rapaces ibéricas
INSERT INTO familias_rapaces (nombre_familia, nombre_cientifico, descripcion, numero_especies_ibericas) VALUES
('Accipitridae', 'Accipitridae', 'Familia que incluye águilas, milanos, buitres y aguiluchos', 18),
('Falconidae', 'Falconidae', 'Familia de halcones y cernícalos', 5),
('Strigidae', 'Strigidae', 'Búhos y mochuelos', 8),
('Tytonidae', 'Tytonidae', 'Lechuzas', 1),
('Pandionidae', 'Pandionidae', 'Águila pescadora', 1);

-- Insertar hábitats
INSERT INTO habitats (nombre, descripcion) VALUES
('Bosques mediterráneos', 'Bosques de encinas, alcornoques y pinos'),
('Montaña', 'Zonas de alta montaña y cortados rocosos'),
('Dehesas', 'Pastizales arbolados con encinas y alcornoques'),
('Humedales', 'Marismas, lagunas y zonas húmedas'),
('Estepas', 'Llanuras áridas y campos de cultivo'),
('Zonas costeras', 'Acantilados y áreas marítimas'),
('Bosques caducifolios', 'Hayedos y robledales del norte'),
('Zonas urbanas', 'Ciudades y pueblos');

-- Insertar tipos de alimentación
INSERT INTO tipos_alimentacion (nombre, descripcion) VALUES
('Micromamíferos', 'Ratones, topillos y pequeños roedores'),
('Aves pequeñas', 'Paseriformes y otras aves menores'),
('Carroña', 'Animales muertos'),
('Reptiles', 'Serpientes, lagartos y lagartijas'),
('Insectos', 'Insectos grandes y pequeños'),
('Peces', 'Peces de agua dulce y marina'),
('Mamíferos medianos', 'Conejos, liebres y similares'),
('Anfibios', 'Ranas, sapos y tritones');

-- Insertar amenazas comunes
INSERT INTO amenazas (nombre, descripcion, tipo_amenaza, gravedad) VALUES
('Electrocución en tendidos eléctricos', 'Muerte por descarga eléctrica al posarse en torres', 'directa', 'crítica'),
('Venenos', 'Uso ilegal de cebos envenenados', 'directa', 'crítica'),
('Colisión con aerogeneradores', 'Impacto con molinos eólicos', 'directa', 'alta'),
('Pérdida de hábitat', 'Destrucción o degradación del hábitat natural', 'indirecta', 'alta'),
('Persecución directa', 'Caza ilegal y destrucción de nidos', 'directa', 'alta'),
('Contaminación', 'Pesticidas y otros contaminantes', 'indirecta', 'media'),
('Cambio climático', 'Alteración de hábitats y recursos', 'indirecta', 'media'),
('Molestias humanas', 'Turismo no controlado y actividades recreativas', 'directa', 'media');

-- Insertar Comunidades Autónomas españolas
INSERT INTO comunidades_autonomas (nombre, codigo, area_km2, coordenadas_centro) VALUES
('Andalucía', 'AND', 87268.00, '37.5443,-4.7278'),
('Aragón', 'ARA', 47719.00, '41.5988,-0.9066'),
('Asturias', 'AST', 10604.00, '43.3619,-5.8593'),
('Baleares', 'BAL', 4992.00, '39.6953,3.0176'),
('Canarias', 'CAN', 7447.00, '28.2916,-16.6291'),
('Cantabria', 'CNT', 5321.00, '43.1828,-3.9878'),
('Castilla-La Mancha', 'CLM', 79463.00, '39.2797,-3.0977'),
('Castilla-León', 'CYL', 94223.00, '41.8357,-4.3976'),
('Cataluña', 'CAT', 32113.00, '41.5912,1.5209'),
('Valencia', 'VAL', 23255.00, '39.4840,-0.7533'),
('Extremadura', 'EXT', 41634.00, '39.4937,-6.0679'),
('Galicia', 'GAL', 29574.00, '42.8782,-8.0447'),
('Madrid', 'MAD', 8028.00, '40.4168,-3.7038'),
('Región de Murcia', 'MUR', 11313.00, '37.9922,-1.1307'),
('Navarra', 'NAV', 10391.00, '42.6954,-1.6761'),
('País Vasco', 'PVA', 7234.00, '42.9896,-2.6189'),
('La Rioja', 'RIO', 5045.00, '42.2871,-2.5396'),
('Ciudad de Ceuta', 'CEU', 19.00, '35.8894,-5.3213'),
('Ciudad de Melilla', 'MEL', 12.00, '35.2923,-2.9381');

-- Insertar términos del glosario ornitológico
INSERT INTO glosario (termino, definicion, categoria, termino_ingles) VALUES
-- Anatomía y Morfología
('Envergadura', 'Distancia entre las puntas de las alas completamente extendidas. Es una medida fundamental para identificar rapaces en vuelo.', 'Anatomía', 'Wingspan'),
('Tarsos', 'Parte de las patas de las aves situada entre los dedos y la articulación tibiotarsiana. En rapaces suelen estar cubiertos de plumas o escamas.', 'Anatomía', 'Tarsus'),
('Cera', 'Membrana carnosa situada en la base del pico de las rapaces, donde se encuentran las narinas (orificios nasales).', 'Anatomía', 'Cere'),
('Garra', 'Uña curva y afilada de las rapaces, fundamental para capturar y matar presas. También llamada "uña".', 'Anatomía', 'Talon'),
('Dimorfismo sexual', 'Diferencias físicas entre machos y hembras de una misma especie. En rapaces, normalmente las hembras son más grandes.', 'Anatomía', 'Sexual dimorphism'),
('Plumaje juvenil', 'Conjunto de plumas que presentan las aves jóvenes, generalmente diferente al de los adultos.', 'Anatomía', 'Juvenile plumage'),
('Remeras', 'Plumas grandes de las alas, fundamentales para el vuelo. Se dividen en primarias y secundarias.', 'Anatomía', 'Flight feathers'),
('Rectrices', 'Plumas de la cola, importantes para la dirección y estabilidad durante el vuelo.', 'Anatomía', 'Tail feathers'),
('Disco facial', 'Conjunto de plumas que rodean la cara de los búhos y lechuzas, ayudando a dirigir el sonido hacia los oídos.', 'Anatomía', 'Facial disk'),

-- Comportamiento y Ecología
('Rapaz', 'Ave de presa que caza activamente otros animales para alimentarse. Incluye tanto rapaces diurnas como nocturnas.', 'Comportamiento', 'Raptor'),
('Vuelo en círculos', 'Técnica de vuelo utilizada por muchas rapaces para ganar altura aprovechando las corrientes térmicas ascendentes.', 'Comportamiento', 'Soaring'),
('Cernido', 'Tipo de vuelo estacionario en el que el ave permanece suspendida en el aire batiendo las alas rápidamente, característico del cernícalo.', 'Comportamiento', 'Hovering'),
('Picado', 'Descenso en vertical a gran velocidad para capturar una presa, típico de halcones.', 'Comportamiento', 'Stoop'),
('Territorial', 'Comportamiento de defensa de un área específica frente a otros individuos de la misma especie.', 'Comportamiento', 'Territorial'),
('Migración', 'Desplazamiento estacional de aves entre áreas de reproducción e invernada.', 'Comportamiento', 'Migration'),
('Sedentaria', 'Ave que permanece en la misma área geográfica durante todo el año.', 'Comportamiento', 'Sedentary'),
('Planeador', 'Tipo de vuelo sin batir las alas, aprovechando corrientes de aire. Típico de buitres y águilas.', 'Comportamiento', 'Gliding'),
('Carroñero', 'Animal que se alimenta principalmente de cadáveres de otros animales.', 'Comportamiento', 'Scavenger'),
('Pellet', 'Bola de materia no digerible (huesos, pelo, plumas) que las rapaces regurgitan tras la digestión.', 'Comportamiento', 'Pellet'),

-- Reproducción
('Nidificación', 'Proceso de construcción del nido y cría de los polluelos.', 'Reproducción', 'Nesting'),
('Puesta', 'Conjunto de huevos depositados en un nido durante un período reproductivo.', 'Reproducción', 'Clutch'),
('Incubación', 'Proceso de mantener los huevos a temperatura adecuada para el desarrollo del embrión.', 'Reproducción', 'Incubation'),
('Polluelo', 'Cría de ave que aún no puede volar y depende de sus progenitores.', 'Reproducción', 'Chick'),
('Volandero', 'Pollo que ha abandonado el nido pero aún depende parcialmente de los padres.', 'Reproducción', 'Fledgling'),
('Cainismo', 'Comportamiento en el que el polluelo mayor mata a sus hermanos menores, común en águilas.', 'Reproducción', 'Cainism'),
('Nido de palitos', 'Estructura construida con ramas y ramitas en árboles o acantilados, típica de muchas rapaces.', 'Reproducción', 'Stick nest'),

-- Conservación
('Especie amenazada', 'Especie en riesgo de extinción según categorías de la UICN.', 'Conservación', 'Threatened species'),
('Reintroducción', 'Liberación de individuos en áreas donde la especie se había extinguido localmente.', 'Conservación', 'Reintroduction'),
('Censo', 'Conteo sistemático de individuos de una población para conocer su tamaño y tendencia.', 'Conservación', 'Census'),
('Hábitat', 'Lugar o tipo de ambiente en el que vive naturalmente una especie.', 'Conservación', 'Habitat'),
('Electrocución', 'Muerte por descarga eléctrica, principal causa de mortalidad no natural en grandes rapaces.', 'Conservación', 'Electrocution'),
('Veneno', 'Sustancia tóxica utilizada ilegalmente que causa mortalidad en rapaces, especialmente carroñeras.', 'Conservación', 'Poison'),
('ZEPA', 'Zona de Especial Protección para las Aves, figura de protección europea.', 'Conservación', 'SPA'),

-- Taxonomía
('Orden', 'Categoría taxonómica que agrupa familias con características comunes.', 'Taxonomía', 'Order'),
('Familia', 'Grupo taxonómico que agrupa géneros relacionados evolutivamente.', 'Taxonomía', 'Family'),
('Género', 'Categoría taxonómica que agrupa especies estrechamente relacionadas.', 'Taxonomía', 'Genus'),
('Especie', 'Unidad básica de clasificación biológica, conjunto de individuos que pueden reproducirse entre sí.', 'Taxonomía', 'Species'),
('Nombre científico', 'Denominación en latín compuesta por género y especie, única para cada organismo.', 'Taxonomía', 'Scientific name'),
('Accipitriformes', 'Orden de aves rapaces diurnas que incluye águilas, buitres, milanos y aguiluchos.', 'Taxonomía', 'Accipitriformes'),
('Falconiformes', 'Orden de aves rapaces que incluye halcones y cernícalos.', 'Taxonomía', 'Falconiformes'),
('Strigiformes', 'Orden de aves rapaces nocturnas, incluye búhos, lechuzas y mochuelos.', 'Taxonomía', 'Strigiformes'),

-- Identificación
('Silueta', 'Forma general del ave en vuelo, útil para identificación a distancia.', 'Identificación', 'Silhouette'),
('Plumaje nupcial', 'Coloración que adquieren algunas aves durante la época de reproducción.', 'Identificación', 'Breeding plumage'),
('Banda alar', 'Franja de color diferente en las alas, visible en vuelo, útil para identificación.', 'Identificación', 'Wing bar'),
('Obispillo', 'Parte inferior del dorso, justo por encima de la base de la cola.', 'Identificación', 'Rump'),
('Bigotera', 'Marca oscura que baja desde el ojo hacia los lados del cuello, típica de halcones.', 'Identificación', 'Moustachial stripe'),

-- Técnicas de observación
('Avistamiento', 'Acto de observar y registrar la presencia de un ave en un lugar determinado.', 'Observación', 'Sighting'),
('Migración activa', 'Observación de aves en pleno desplazamiento migratorio.', 'Observación', 'Active migration'),
('Punto de observación', 'Lugar estratégico desde donde se observan aves, especialmente durante la migración.', 'Observación', 'Watchpoint'),
('Prismáticos', 'Instrumento óptico de aumento utilizado para la observación de aves a distancia.', 'Observación', 'Binoculars'),
('Telescopio terrestre', 'Instrumento óptico de gran aumento utilizado para observación de aves a larga distancia.', 'Observación', 'Spotting scope');

-- Insertar configuración básica del sistema
INSERT INTO configuracion_sistema (clave, valor, descripcion, tipo_dato) VALUES
('puntos_por_especie_descubierta', '10', 'Puntos otorgados al descubrir una nueva especie', 'int'),
('puntos_por_login_diario', '5', 'Puntos por mantener racha diaria', 'int'),
('max_intentos_cuestionario', '3', 'Número máximo de intentos en cuestionarios', 'int'),
('duracion_sesion_minutos', '60', 'Duración de sesión de usuario en minutos', 'int'),
('especies_minimas_certificado', '15', 'Especies mínimas descubiertas para certificado', 'int'),
('modo_mantenimiento', 'false', 'Activar modo mantenimiento del sitio', 'boolean'),
('email_contacto', 'info@raptorlearn.es', 'Email de contacto principal', 'string'),
('max_subida_imagen_mb', '5', 'Tamaño máximo de imagen en MB', 'int');

-- ============================================================
-- INSERTAR ESPECIES DE RAPACES IBÉRICAS
-- ============================================================

INSERT INTO especies (
    nombre_cientifico, nombre_comun, nombre_ingles, familia, orden,
    descripcion, caracteristicas_fisicas, 
    envergadura_min, envergadura_max, peso_min, peso_max, longitud_min, longitud_max,
    dimorfismo_sexual, habitat, distribucion_geografica,
    altitud_min, altitud_max, dieta, comportamiento_caza, reproduccion,
    epoca_cria, numero_huevos, estado_conservacion, poblacion_iberica,
    amenazas, medidas_conservacion, curiosidades, dificultad_identificacion
) VALUES 
(
    'Aquila chrysaetos',
    'Águila Real',
    'Golden Eagle',
    'Accipitridae',
    'Accipitriformes',
    'Majestuosa rapaz de gran tamaño, considerada el rey de las aves en muchas culturas. Es una de las águilas más grandes y poderosas de Europa, capaz de cazar presas de considerable tamaño.',
    'Plumaje pardo oscuro con tonos dorados en cabeza y nuca. Pico robusto de color gris oscuro con cera amarilla. Tarsos completamente emplumados hasta los dedos. Los juveniles presentan manchas blancas en alas y cola que desaparecen con la edad.',
    1.90, 2.27, 3000.00, 6700.00, 75.00, 95.00,
    'Las hembras son notablemente más grandes que los machos, pudiendo superar en hasta un 25% su peso. Esta diferencia es especialmente visible durante la época de cría.',
    'Prefiere zonas montañosas con cortados rocosos, aunque también habita en zonas de media montaña con alternancia de bosques y espacios abiertos. Necesita territorios amplios con abundancia de presas.',
    'Presente en todas las cordilleras peninsulares: Pirineos, Cordillera Cantábrica, Sistema Ibérico, Sistema Central y sierras béticas. También en zonas montañosas de Portugal.',
    0, 2500,
    'Dieta muy variada que incluye mamíferos medianos (conejos, liebres, marmotas), aves (perdices, córvidos), reptiles y ocasionalmente carroña. Puede cazar presas de hasta 15 kg.',
    'Caza principalmente al acecho desde posaderos elevados o mediante vuelos de prospección a baja altura. Captura a sus presas con potentes picados que pueden superar los 240 km/h.',
    'Especie monógama con vínculos de pareja de larga duración. Construyen nidos voluminosos en cortados rocosos o grandes árboles, que reutilizan y amplían año tras año.',
    'Enero-Julio',
    '1-3 (normalmente 2)',
    'LC',
    'Población estimada en 1.500-1.900 parejas reproductoras. Tendencia estable o ligeramente positiva en las últimas décadas.',
    'Electrocución en tendidos eléctricos, colisión con aerogeneradores, pérdida de hábitat por cambios en usos del suelo, molestias en época de cría, uso ilegal de venenos.',
    'Corrección de tendidos eléctricos peligrosos, creación de reservas en áreas de nidificación, programas de vigilancia y seguimiento, planes de gestión cinegética compatibles.',
    'Puede vivir más de 30 años en libertad. Sus nidos pueden alcanzar 2 metros de diámetro y pesar más de 200 kg tras años de uso. En algunas culturas ha sido utilizada en cetrería para cazar lobos.',
    'medio'
),
(
    'Buteo buteo',
    'Busardo Ratonero',
    'Common Buzzard',
    'Accipitridae',
    'Accipitriformes',
    'Rapaz de tamaño mediano muy común y adaptable. Es una de las aves de presa más abundantes en la península ibérica y de las más fáciles de observar.',
    'Plumaje muy variable, desde casi completamente oscuro a formas claras con pecho blanquecino. Generalmente pardo con barrado en pecho y vientre. Cola redondeada con bandas oscuras. Alas anchas y redondeadas.',
    1.13, 1.28, 427.00, 1364.00, 51.00, 57.00,
    'Las hembras son ligeramente más grandes que los machos, diferencia menos marcada que en otras rapaces. El dimorfismo es principalmente de tamaño.',
    'Muy versátil: bosques, campiñas, dehesas, zonas de matorral y bordes de cultivos. Evita únicamente áreas muy áridas y zonas de alta montaña.',
    'Presente en toda la península ibérica excepto en zonas esteparias del sureste. Más abundante en la mitad norte. Población residente complementada con invernantes del norte de Europa.',
    0, 1800,
    'Principalmente micromamíferos (topillos, ratones), también lombrices, insectos grandes, reptiles, pequeñas aves y ocasionalmente carroña. Muy oportunista en su alimentación.',
    'Caza desde posaderos o en vuelo estacionario. Frecuentemente se le observa en vuelos circulares a baja altura sobre campos y praderas. También camina por el suelo buscando invertebrados.',
    'Nidifica en árboles, construyendo un nido de ramas y raíces. Muy territorial durante la reproducción. Realiza vuelos nupciales espectaculares con picados y ascensos.',
    'Marzo-Julio',
    '2-4 (normalmente 3)',
    'LC',
    'Población muy abundante estimada en 50.000-100.000 parejas reproductoras. Tendencia estable.',
    'Colisión con vehículos (frecuentemente se posa en señales de tráfico), electrocución ocasional, uso de rodenticidas que reduce sus presas.',
    'Especie común sin medidas específicas necesarias. Se beneficia de la agricultura extensiva y zonas de mosaico agropecuario.',
    'Es el ave rapaz diurna más abundante de Europa. Su nombre viene de su predilección por cazar ratones. Emite un característico maullido lastimero que se escucha frecuentemente.',
    'fácil'
),
(
    'Bubo bubo',
    'Búho Real',
    'Eurasian Eagle-Owl',
    'Strigidae',
    'Strigiformes',
    'El búho de mayor tamaño de Europa y una de las aves rapaces nocturnas más poderosas del mundo. Su presencia es indicador de ecosistemas bien conservados.',
    'Plumaje pardo con moteado oscuro y estrías verticales. Grandes penachos auriculares (orejas) muy característicos. Ojos naranja intenso. Disco facial poco marcado. Patas y dedos completamente emplumados.',
    1.60, 1.88, 1500.00, 4200.00, 60.00, 75.00,
    'Las hembras son significativamente más grandes y pesadas que los machos, pudiendo superar en un 30% su peso. Esta diferencia es característica de las rapaces nocturnas.',
    'Muy versátil: zonas rocosas, bosques abiertos, barrancos, cortados, canteras abandonadas. Evita bosques muy densos. Puede vivir cerca de zonas habitadas si no es molestado.',
    'Presente en toda la península ibérica, más abundante en zonas montañosas y premontañosas. También en Baleares. Ausente de Canarias.',
    0, 2000,
    'Depredador generalista: mamíferos (conejos, liebres, ratas, erizos), aves (palomas, córvidos, otras rapaces), reptiles, anfibios e incluso peces. Puede cazar presas de hasta 2 kg.',
    'Caza principalmente al crepúsculo y durante la noche. Vuela silenciosamente gracias a las plumas especializadas de sus alas. Localiza presas por vista y oído excepcional.',
    'Especie monógama muy territorial. No construye nido, pone los huevos directamente sobre el suelo en repisas rocosas, cuevas o huecos de árboles grandes.',
    'Diciembre-Abril',
    '2-4 (normalmente 2-3)',
    'LC',
    'Población estimada en 3.000-4.000 parejas. Tendencia ligeramente positiva tras décadas de persecución.',
    'Electrocución en tendidos eléctricos (principal causa de mortalidad no natural), colisión con vallados cinegéticos, atropellos, molestias en zonas de cría, persecución ilegal.',
    'Corrección de tendidos peligrosos, protección de áreas de nidificación, campañas de concienciación, vigilancia contra expolio de nidos.',
    'Su canto territorial es un potente "UHU" que puede oírse a más de 2 km. Puede vivir más de 20 años en libertad. Es capaz de matar y comer zorros adultos.',
    'medio'
),
(
    'Gyps fulvus',
    'Buitre Leonado',
    'Griffon Vulture',
    'Accipitridae',
    'Accipitriformes',
    'Ave carroñera de gran tamaño y vuelo majestuoso. Forma parte de un gremio de necrófagos esencial para el equilibrio ecológico, actuando como "limpiadores" del ecosistema.',
    'Plumaje pardo-leonado en cuerpo y coberteras alares. Cuello largo cubierto de plumón blanco. Cabeza y cuello desnudos de color blanquecino. Collar de plumas blancas en la base del cuello. Cola corta y oscura.',
    2.34, 2.65, 6000.00, 11300.00, 95.00, 110.00,
    'Las hembras son ligeramente más grandes que los machos, aunque la diferencia es poco apreciable en campo. Ambos sexos prácticamente idénticos en plumaje.',
    'Zonas de montaña con cortados rocosos para nidificar y áreas abiertas de pastos para alimentarse. Requiere corrientes térmicas para sus largos vuelos de prospección.',
    'Presente en todas las grandes cordilleras peninsulares y sistemas montañosos. Muy abundante en Pirineos, Cordillera Cantábrica, Sistema Ibérico y sierras del sur.',
    100, 2500,
    'Exclusivamente carroñera. Se alimenta de cadáveres de ungulados domésticos y silvestres (ovejas, cabras, vacas, ciervos, jabalíes). Consume principalmente partes blandas y vísceras.',
    'Localiza carroña mediante vuelos de prospección a gran altura en grupos. Utiliza corrientes térmicas para planear largas distancias con mínimo gasto energético. Alimentación social en grupos.',
    'Especie colonial que nidifica en cortados rocosos formando colonias que pueden alcanzar centenares de parejas. Construyen nidos de ramas y materiales vegetales en repisas.',
    'Enero-Julio',
    '1 (raramente 2)',
    'LC',
    'Población muy abundante: 25.000-30.000 parejas reproductoras. España alberga más del 90% de la población europea. Tendencia positiva.',
    'Envenenamiento por cebos tóxicos ilegales (principal amenaza), colisión con aerogeneradores, electrocución, falta de alimento por eliminación de muladares, molestias en colonias.',
    'Red de muladares autorizados, vigilancia contra uso de venenos, protección de colonias de cría, programas de alimentación suplementaria, eliminación de tendidos eléctricos peligrosos.',
    'Puede vivir más de 40 años. Es capaz de localizar carroña desde alturas superiores a 1.000 metros. Su sistema digestivo tolera bacterias que serían mortales para otros animales.',
    'fácil'
),
(
    'Tyto alba',
    'Lechuza Común',
    'Barn Owl',
    'Tytonidae',
    'Strigiformes',
    'Rapaz nocturna de tamaño mediano con aspecto fantasmagórico. Es una de las aves más cosmopolitas del mundo y excelente controladora de poblaciones de roedores.',
    'Plumaje característico: dorso dorado con motas grises, vientre blanco o crema. Disco facial en forma de corazón de color blanco. Ojos oscuros (no amarillos como otros búhos). Patas largas y cubiertas de plumas blancas.',
    0.85, 0.93, 187.00, 700.00, 33.00, 39.00,
    'Las hembras son ligeramente más grandes y tienen el plumaje ventral más moteado que los machos. Los machos suelen ser más blancos en el vientre.',
    'Muy ligada a zonas agrícolas y ambientes humanizados: cortijos, iglesias, graneros, masías, ruinas. También en zonas de cultivo con presencia de edificaciones. Evita bosques densos.',
    'Presente en toda la península ibérica y Baleares, desde el nivel del mar hasta media montaña. Especialmente abundante en zonas agrícolas tradicionales.',
    0, 1200,
    'Altamente especializada en micromamíferos, principalmente topillos, ratones de campo y musarañas. Ocasionalmente captura pequeñas aves, murciélagos e insectos grandes.',
    'Caza en vuelo silencioso a baja altura sobre campos y praderas. Localiza presas principalmente por el oído, capaz de detectar un ratón bajo la hierba en total oscuridad.',
    'No construye nido. Utiliza huecos en edificios, árboles, cortados rocosos o cajas nido. Puede criar dos veces al año si hay abundancia de alimento.',
    'Febrero-Noviembre',
    '4-7 (hasta 11 en años excepcionales)',
    'LC',
    'Población estimada en 50.000-100.000 parejas. Tendencia decreciente por pérdida de hábitat agrícola tradicional y uso de rodenticidas.',
    'Uso de rodenticidas (veneno secundario y reducción de presas), pérdida de edificios agrícolas tradicionales, colisión con vehículos, electrocución en tendidos.',
    'Instalación de cajas nido en edificios agrícolas, fomento de agricultura ecológica, protección de edificios rurales abandonados, campañas de concienciación sobre rodenticidas.',
    'Puede tragar presas enteras de hasta 100 gramos. Regurgita egagrópilas que son muy útiles para estudios científicos. Su vuelo es completamente silencioso gracias a plumas especializadas.',
    'fácil'
),
(
    'Milvus migrans',
    'Milano Negro',
    'Black Kite',
    'Accipitridae',
    'Accipitriformes',
    'Rapaz de tamaño medio-grande, migratoria, que forma grandes concentraciones durante los pasos migratorios. Es una de las rapaces más abundantes del mundo.',
    'Plumaje pardo oscuro uniforme, más negruzco que el Milano Real. Cola ahorquillada poco profunda. Cabeza grisácea con estrías oscuras. Cera y patas amarillas. En vuelo presenta silueta característica con alas anguladas.',
    1.35, 1.55, 630.00, 1100.00, 55.00, 60.00,
    'Las hembras son ligeramente mayores que los machos, aunque la diferencia es poco apreciable. Prácticamente no hay diferencias en el plumaje entre sexos.',
    'Muy versátil: proximidades de humedales, embalses, ríos, dehesas, zonas agrícolas. Muestra preferencia por ambientes húmedos. Evita zonas muy boscosas.',
    'Presente en toda la península durante la época reproductora (marzo-septiembre). Principalmente migratoria, inverna en África subsahariana. Pequeña población invernante en el sur peninsular.',
    0, 1500,
    'Muy oportunista y carroñero: peces muertos, basuras, pequeños vertebrados, insectos, carroña. A menudo se alimenta en vertederos. Captura presas vivas ocasionalmente.',
    'Caza volando bajo sobre el agua o el suelo. Muy oportunista, aprovecha cualquier fuente de alimento disponible. Frecuenta vertederos y zonas con actividad humana.',
    'Nidifica en árboles, frecuentemente en colonias laxas. Construye nidos con ramas incorporando plásticos y otros materiales artificiales. Muy gregario.',
    'Abril-Julio',
    '2-3',
    'LC',
    'Población reproductora estimada en 30.000-50.000 parejas. Tendencia estable. España es uno de los principales bastiones europeos de la especie.',
    'Envenenamiento secundario por rodenticidas, colisión con tendidos eléctricos y aerogeneradores, electrocución, molestias en colonias, cambios en gestión de vertederos.',
    'Control del uso de venenos, protección de colonias de cría, gestión adecuada de muladares y vertederos, corrección de tendidos eléctricos.',
    'Uno de los primeros migrantes en llegar en primavera. Realiza migraciones de más de 5.000 km. En algunas zonas urbanas se ha adaptado a comer desperdicios y puede verse en parques.',
    'medio'
),
(
    'Falco peregrinus',
    'Halcón Peregrino',
    'Peregrine Falcon',
    'Falconidae',
    'Falconiformes',
    'El ave más rápida del mundo en picado, capaz de superar los 300 km/h. Símbolo de la velocidad y precisión en el reino animal.',
    'Dorso gris pizarra, vientre blanquecino con fino barrado oscuro. Capirote negro que contrasta con mejillas blancas. Evidente bigotera negra. Cera y anillo ocular amarillo. Juveniles pardos con estriado vertical.',
    0.95, 1.15, 600.00, 1300.00, 36.00, 49.00,
    'Las hembras son notablemente más grandes que los machos (hasta 50% más pesadas). Esta diferencia permite cazar presas de diferente tamaño, reduciendo competencia intraespecífica.',
    'Cortados rocosos, acantilados marinos, zonas montañosas. Cada vez más frecuente en ciudades, nidificando en edificios altos (catedrales, rascacielos). Necesita espacios abiertos para cazar.',
    'Presente en toda la península ibérica y Baleares, desde costa hasta montaña. Población residente reforzada en invierno por ejemplares del norte de Europa.',
    0, 2500,
    'Casi exclusivamente aves capturadas en vuelo: palomas, estorninos, gorriones, aves acuáticas, limícolas. Ocasionalmente murciélagos. Raramente captura presas terrestres.',
    'Caza mediante espectaculares picados desde gran altura, golpeando a sus presas en pleno vuelo con las garras. También persecución directa en vuelo horizontal.',
    'Nidifica en repisas de cortados rocosos, acantilados marinos o edificios altos. No construye nido, pone los huevos directamente sobre el sustrato. Muy territorial.',
    'Febrero-Junio',
    '3-4',
    'LC',
    'Población en recuperación: 2.500-3.000 parejas. Sufrió un drástico declive en los años 60-70 por uso de DDT. Actualmente en franca recuperación.',
    'Molestias en lugares de cría (escalada, fotografía), expolio de nidos, electrocución, colisión con tendidos, contaminación por metales pesados.',
    'Protección estricta de zonas de cría, regulación de escalada en época reproductora, lucha contra el expolio, vigilancia de nidos, proyectos de reintroducción urbana.',
    'Ave más rápida del mundo en picado (390 km/h registrados). Fue muy utilizado en cetrería. Se ha adaptado a vivir en grandes ciudades donde caza palomas.',
    'medio'
);

-- ============================================================
-- RELACIONES ESPECIES-COMUNIDADES AUTÓNOMAS
-- ============================================================

-- Águila Real (id_especie: 1)
INSERT INTO especies_comunidades (id_especie, id_comunidad, presencia, poblacion_estimada) VALUES
(1, 1, 'residente', '200-250 parejas'),
(1, 2, 'residente', '180-200 parejas'),
(1, 3, 'residente', '80-100 parejas'),
(1, 8, 'residente', '350-400 parejas'),
(1, 9, 'residente', '120-140 parejas'),
(1, 11, 'residente', '100-120 parejas'),
(1, 13, 'residente', '70-90 parejas'),
(1, 15, 'residente', '60-80 parejas');

-- Busardo Ratonero (id_especie: 2) - Presente en todas las CC.AA.
INSERT INTO especies_comunidades (id_especie, id_comunidad, presencia, poblacion_estimada) VALUES
(2, 1, 'residente', '8.000-12.000 parejas'),
(2, 2, 'residente', '6.000-8.000 parejas'),
(2, 3, 'residente', '5.000-7.000 parejas'),
(2, 6, 'residente', '3.000-4.000 parejas'),
(2, 8, 'residente', '15.000-20.000 parejas'),
(2, 9, 'residente', '5.000-7.000 parejas'),
(2, 11, 'residente', '4.000-6.000 parejas'),
(2, 12, 'residente', '6.000-8.000 parejas');

-- Búho Real (id_especie: 3)
INSERT INTO especies_comunidades (id_especie, id_comunidad, presencia, poblacion_estimada) VALUES
(3, 1, 'residente', '400-500 parejas'),
(3, 2, 'residente', '300-400 parejas'),
(3, 8, 'residente', '600-700 parejas'),
(3, 9, 'residente', '250-300 parejas'),
(3, 10, 'residente', '200-250 parejas'),
(3, 11, 'residente', '300-350 parejas'),
(3, 13, 'residente', '150-200 parejas');

-- Buitre Leonado (id_especie: 4)
INSERT INTO especies_comunidades (id_especie, id_comunidad, presencia, poblacion_estimada) VALUES
(4, 1, 'residente', '3.000-4.000 parejas'),
(4, 2, 'residente', '6.000-7.000 parejas'),
(4, 3, 'residente', '2.000-2.500 parejas'),
(4, 7, 'residente', '4.000-5.000 parejas'),
(4, 8, 'residente', '8.000-10.000 parejas'),
(4, 9, 'residente', '1.500-2.000 parejas'),
(4, 11, 'residente', '3.000-3.500 parejas'),
(4, 15, 'residente', '800-1.000 parejas');

-- Lechuza Común (id_especie: 5)
INSERT INTO especies_comunidades (id_especie, id_comunidad, presencia, poblacion_estimada) VALUES
(5, 1, 'residente', '10.000-15.000 parejas'),
(5, 2, 'residente', '5.000-7.000 parejas'),
(5, 7, 'residente', '8.000-10.000 parejas'),
(5, 8, 'residente', '12.000-15.000 parejas'),
(5, 10, 'residente', '4.000-6.000 parejas'),
(5, 11, 'residente', '6.000-8.000 parejas');

-- Milano Negro (id_especie: 6) - Principalmente migratoria
INSERT INTO especies_comunidades (id_especie, id_comunidad, presencia, poblacion_estimada) VALUES
(6, 1, 'reproductor', '5.000-7.000 parejas'),
(6, 2, 'reproductor', '3.000-4.000 parejas'),
(6, 8, 'reproductor', '8.000-10.000 parejas'),
(6, 9, 'reproductor', '2.000-3.000 parejas'),
(6, 11, 'reproductor', '6.000-8.000 parejas'),
(6, 13, 'reproductor', '1.500-2.000 parejas');

-- Halcón Peregrino (id_especie: 7)
INSERT INTO especies_comunidades (id_especie, id_comunidad, presencia, poblacion_estimada) VALUES
(7, 1, 'residente', '300-400 parejas'),
(7, 2, 'residente', '200-250 parejas'),
(7, 4, 'residente', '80-100 parejas'),
(7, 8, 'residente', '400-500 parejas'),
(7, 9, 'residente', '150-200 parejas'),
(7, 10, 'residente', '100-120 parejas'),
(7, 12, 'residente', '200-250 parejas');

-- ============================================================
-- RELACIONES ESPECIES-HÁBITATS
-- ============================================================

INSERT INTO especies_habitats (id_especie, id_habitat, preferencia) VALUES
-- Águila Real
(1, 2, 'alta'),      -- Montaña
(1, 1, 'media'),     -- Bosques mediterráneos
(1, 3, 'media'),     -- Dehesas

-- Busardo Ratonero
(2, 1, 'alta'),      -- Bosques mediterráneos
(2, 3, 'alta'),      -- Dehesas
(2, 7, 'alta'),      -- Bosques caducifolios
(2, 5, 'media'),     -- Estepas

-- Búho Real
(3, 2, 'alta'),      -- Montaña
(3, 1, 'media'),     -- Bosques mediterráneos
(3, 6, 'media'),     -- Zonas costeras

-- Buitre Leonado
(4, 2, 'alta'),      -- Montaña
(4, 3, 'alta'),      -- Dehesas
(4, 5, 'media'),     -- Estepas

-- Lechuza Común
(5, 5, 'alta'),      -- Estepas (zonas agrícolas)
(5, 3, 'alta'),      -- Dehesas
(5, 8, 'alta'),      -- Zonas urbanas

-- Milano Negro
(6, 4, 'alta'),      -- Humedales
(6, 3, 'media'),     -- Dehesas
(6, 1, 'media'),     -- Bosques mediterráneos

-- Halcón Peregrino
(7, 2, 'alta'),      -- Montaña
(7, 6, 'alta'),      -- Zonas costeras
(7, 8, 'media');     -- Zonas urbanas

-- ============================================================
-- RELACIONES ESPECIES-ALIMENTACIÓN
-- ============================================================

INSERT INTO especies_alimentacion (id_especie, id_tipo_alimentacion, porcentaje_dieta) VALUES
-- Águila Real
(1, 7, 60),  -- Mamíferos medianos
(1, 2, 30),  -- Aves pequeñas
(1, 4, 10),  -- Reptiles

-- Busardo Ratonero
(2, 1, 70),  -- Micromamíferos
(2, 5, 15),  -- Insectos
(2, 4, 10),  -- Reptiles
(2, 8, 5),   -- Anfibios

-- Búho Real
(3, 7, 40),  -- Mamíferos medianos
(3, 1, 30),  -- Micromamíferos
(3, 2, 20),  -- Aves pequeñas
(3, 4, 10),  -- Reptiles

-- Buitre Leonado
(4, 3, 100), -- Carroña

-- Lechuza Común
(5, 1, 95),  -- Micromamíferos
(5, 2, 3),   -- Aves pequeñas
(5, 5, 2),   -- Insectos

-- Milano Negro
(6, 3, 40),  -- Carroña
(6, 6, 30),  -- Peces
(6, 1, 15),  -- Micromamíferos
(6, 5, 15),  -- Insectos

-- Halcón Peregrino
(7, 2, 100); -- Aves pequeñas

-- ============================================================
-- RELACIONES ESPECIES-AMENAZAS
-- ============================================================

INSERT INTO especies_amenazas (id_especie, id_amenaza, impacto) VALUES
-- Águila Real
(1, 1, 'crítico'),   -- Electrocución
(1, 3, 'alto'),      -- Colisión aerogeneradores
(1, 5, 'moderado'),  -- Persecución directa

-- Busardo Ratonero
(2, 6, 'moderado'),  -- Contaminación (rodenticidas)

-- Búho Real
(3, 1, 'crítico'),   -- Electrocución
(3, 8, 'moderado'),  -- Molestias humanas

-- Buitre Leonado
(4, 2, 'crítico'),   -- Venenos
(4, 3, 'alto'),      -- Colisión aerogeneradores
(4, 1, 'moderado'),  -- Electrocución

-- Lechuza Común
(5, 6, 'alto'),      -- Contaminación (rodenticidas)
(5, 4, 'alto'),      -- Pérdida de hábitat

-- Milano Negro
(6, 6, 'alto'),      -- Contaminación
(6, 1, 'moderado'),  -- Electrocución

-- Halcón Peregrino
(7, 8, 'moderado'),  -- Molestias humanas
(7, 5, 'bajo');      -- Persecución (históricamente alta)

-- ============================================================
-- ESPECIES SIMILARES (Comparaciones)
-- ============================================================

INSERT INTO especies_similares (id_especie_1, id_especie_2, diferencias_clave) VALUES
(1, 4, 'El Águila Real tiene plumaje uniformemente oscuro y cola redondeada; el Buitre Leonado es más grande, de color leonado y tiene cuello largo desnudo con collar de plumas blancas.');

-- ============================================================
-- INSERTAR JUEGO CUESTIONARIO
-- ============================================================
INSERT INTO juegos (nombre, tipo_juego, descripcion, dificultad, puntos_base, tiempo_limite) VALUES
('Cuestionario de Rapaces', 'cuestionario', 'Pon a prueba tus conocimientos sobre rapaces ibéricas', 'medio', 10, 300);

-- Insertar cuestionario
INSERT INTO cuestionarios (id_juego, titulo, descripcion, categoria, numero_preguntas, tiempo_total, puntuacion_minima_aprobar) VALUES
(1, 'Rapaces Ibéricas - Nivel Básico', 'Preguntas básicas sobre las rapaces de la península ibérica', 'general', 5, 300, 60);

-- Insertar preguntas
INSERT INTO preguntas (id_cuestionario, enunciado, tipo_pregunta, puntos, explicacion, orden_presentacion) VALUES
(1, '¿Cuál es el ave rapaz diurna más grande de Europa?', 'multiple', 10, 'El Buitre Leonado es el ave rapaz diurna más grande de Europa, con una envergadura de hasta 2,65 metros.', 1),
(1, '¿A qué velocidad puede llegar el Halcón Peregrino en picado?', 'multiple', 10, 'El Halcón Peregrino puede superar los 300 km/h en picado, siendo el animal más rápido del mundo.', 2),
(1, '¿Qué rapaz nocturna tiene el disco facial en forma de corazón?', 'multiple', 10, 'La Lechuza Común tiene un disco facial característico en forma de corazón de color blanco.', 3),
(1, '¿Cuál de estas rapaces es exclusivamente carroñera?', 'multiple', 10, 'El Buitre Leonado se alimenta exclusivamente de carroña, siendo un elemento clave del ecosistema.', 4),
(1, '¿Qué significa LC en la clasificación de la UICN?', 'multiple', 10, 'LC significa Least Concern (Preocupación Menor), la categoría de menor riesgo en la clasificación UICN.', 5);

-- Insertar respuestas pregunta 1
INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta, orden_presentacion) VALUES
(1, 'Águila Real', FALSE, 1),
(1, 'Buitre Leonado', TRUE, 2),
(1, 'Búho Real', FALSE, 3),
(1, 'Milano Negro', FALSE, 4);

-- Insertar respuestas pregunta 2
INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta, orden_presentacion) VALUES
(2, 'Más de 100 km/h', FALSE, 1),
(2, 'Más de 200 km/h', FALSE, 2),
(2, 'Más de 300 km/h', TRUE, 3),
(2, 'Más de 400 km/h', FALSE, 4);

-- Insertar respuestas pregunta 3
INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta, orden_presentacion) VALUES
(3, 'Búho Real', FALSE, 1),
(3, 'Cárabo Común', FALSE, 2),
(3, 'Lechuza Común', TRUE, 3),
(3, 'Mochuelo Europeo', FALSE, 4);

-- Insertar respuestas pregunta 4
INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta, orden_presentacion) VALUES
(4, 'Águila Real', FALSE, 1),
(4, 'Halcón Peregrino', FALSE, 2),
(4, 'Busardo Ratonero', FALSE, 3),
(4, 'Buitre Leonado', TRUE, 4);

-- Insertar respuestas pregunta 5
INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta, orden_presentacion) VALUES
(5, 'En Peligro', FALSE, 1),
(5, 'Preocupación Menor', TRUE, 2),
(5, 'Vulnerable', FALSE, 3),
(5, 'Casi Amenazada', FALSE, 4);

-- ============================================================
-- INSERTAR USUARIOS DE PRUEBA
-- ============================================================
INSERT INTO usuarios (
    email, password_hash, nombre, apellidos, tipo_usuario, 
    activo, email_verificado, fecha_registro
) VALUES 
(
    'usuario@raptorlearn.es',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uSfalW4ru',
    'Carlos',
    'Martínez López',
    'estudiante',
    1, 1, NOW()
),
(
    'educador@raptorlearn.es',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uSfalW4ru',
    'María',
    'García Fernández',
    'educador',
    1, 1, NOW()
);

-- ============================================================
-- RASTREAR AUTORIA
-- ============================================================
-- Rastrear autor de especies
ALTER TABLE especies 
    ADD COLUMN id_autor INT NULL AFTER fecha_actualizacion,
    ADD FOREIGN KEY (id_autor) REFERENCES usuarios(id_usuario) ON DELETE SET NULL;

-- Rastrear autor de cuestionarios
ALTER TABLE cuestionarios
    ADD COLUMN id_autor INT NULL AFTER puntuacion_minima_aprobar,
    ADD FOREIGN KEY (id_autor) REFERENCES usuarios(id_usuario) ON DELETE SET NULL;