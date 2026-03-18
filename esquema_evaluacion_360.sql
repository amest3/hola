-- Esquema SQL para sistema de evaluación 360 docente
-- Compatible con MySQL 8+ y MariaDB (collation segura)

CREATE DATABASE IF NOT EXISTS `uets360`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `uets360`;

START TRANSACTION;

-- =============================
-- Catálogos base
-- =============================

CREATE TABLE roles (
  id            BIGINT PRIMARY KEY AUTO_INCREMENT,
  nombre        VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE usuarios (
  id             BIGINT PRIMARY KEY AUTO_INCREMENT,
  nombres        VARCHAR(100) NOT NULL,
  apellidos      VARCHAR(100) NOT NULL,
  email          VARCHAR(150) NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  activo         BOOLEAN NOT NULL DEFAULT TRUE,
  creado_en      TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE usuario_roles (
  id          BIGINT PRIMARY KEY AUTO_INCREMENT,
  usuario_id  BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
  rol_id      BIGINT NOT NULL REFERENCES roles(id) ON DELETE RESTRICT,
  estado      BOOLEAN NOT NULL DEFAULT TRUE,
  asignado_en TIMESTAMP NOT NULL DEFAULT NOW(),
  UNIQUE (usuario_id, rol_id)
);

CREATE INDEX idx_usuario_roles_usuario_id ON usuario_roles(usuario_id);
CREATE INDEX idx_usuario_roles_rol_id ON usuario_roles(rol_id);

CREATE TABLE periodos_academicos (
  id            BIGINT PRIMARY KEY AUTO_INCREMENT,
  nombre        VARCHAR(100) NOT NULL UNIQUE,
  fecha_inicio  DATE NOT NULL,
  fecha_fin     DATE NOT NULL,
  activo        BOOLEAN NOT NULL DEFAULT FALSE,
  CONSTRAINT ck_periodo_fechas CHECK (fecha_fin >= fecha_inicio)
);

CREATE TABLE areas (
  id      BIGINT PRIMARY KEY AUTO_INCREMENT,
  nombre  VARCHAR(120) NOT NULL UNIQUE
);

CREATE TABLE cursos (
  id            BIGINT PRIMARY KEY AUTO_INCREMENT,
  curso         VARCHAR(20) NOT NULL,
  paralelo      VARCHAR(20) NOT NULL,
  nombre        VARCHAR(120) NOT NULL,
  especialidad  VARCHAR(120) NOT NULL,
  UNIQUE (curso, paralelo)
);

CREATE TABLE materias (
  id       BIGINT PRIMARY KEY AUTO_INCREMENT,
  nombre   VARCHAR(120) NOT NULL,
  area_id  BIGINT NULL REFERENCES areas(id) ON DELETE SET NULL,
  UNIQUE (nombre, area_id)
);

-- =============================
-- Relación académica
-- =============================

CREATE TABLE asignaciones_docente (
  id          BIGINT PRIMARY KEY AUTO_INCREMENT,
  docente_id  BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
  materia_id  BIGINT NOT NULL REFERENCES materias(id) ON DELETE RESTRICT,
  curso_id    BIGINT NOT NULL REFERENCES cursos(id) ON DELETE RESTRICT,
  periodo_id  BIGINT NOT NULL REFERENCES periodos_academicos(id) ON DELETE RESTRICT,
  UNIQUE (docente_id, materia_id, curso_id, periodo_id)
);

CREATE INDEX idx_asig_doc_docente ON asignaciones_docente(docente_id);
CREATE INDEX idx_asig_doc_periodo ON asignaciones_docente(periodo_id);

CREATE TABLE matriculas_estudiante (
  id             BIGINT PRIMARY KEY AUTO_INCREMENT,
  estudiante_id  BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
  curso_id       BIGINT NOT NULL REFERENCES cursos(id) ON DELETE RESTRICT,
  periodo_id     BIGINT NOT NULL REFERENCES periodos_academicos(id) ON DELETE RESTRICT,
  UNIQUE (estudiante_id, curso_id, periodo_id)
);

CREATE INDEX idx_matriculas_estudiante_id ON matriculas_estudiante(estudiante_id);
CREATE INDEX idx_matriculas_periodo_id ON matriculas_estudiante(periodo_id);

CREATE TABLE jefaturas_area (
  id          BIGINT PRIMARY KEY AUTO_INCREMENT,
  usuario_id  BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
  area_id     BIGINT NOT NULL REFERENCES areas(id) ON DELETE RESTRICT,
  periodo_id  BIGINT NOT NULL REFERENCES periodos_academicos(id) ON DELETE RESTRICT,
  UNIQUE (usuario_id, area_id, periodo_id)
);

-- =============================
-- Evaluación 360
-- =============================

CREATE TABLE evaluaciones_360 (
  id              BIGINT PRIMARY KEY AUTO_INCREMENT,
  docente_id      BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
  evaluador_id    BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
  tipo_evaluador  VARCHAR(20) NOT NULL,
  periodo_id      BIGINT NOT NULL REFERENCES periodos_academicos(id) ON DELETE RESTRICT,
  materia_id      BIGINT NULL REFERENCES materias(id) ON DELETE SET NULL,
  curso_id        BIGINT NULL REFERENCES cursos(id) ON DELETE SET NULL,
  estado          VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  fecha           DATE NOT NULL,
  creado_en       TIMESTAMP NOT NULL DEFAULT NOW(),
  CONSTRAINT ck_tipo_evaluador
    CHECK (tipo_evaluador IN ('estudiante', 'par_docente', 'jefe_area', 'vicerrector', 'autoevaluacion')),
  CONSTRAINT ck_estado_evaluacion
    CHECK (estado IN ('pendiente', 'en_progreso', 'enviada', 'cerrada')),
  CONSTRAINT ck_evaluador_distinto_docente
    CHECK (
      (tipo_evaluador = 'autoevaluacion' AND evaluador_id = docente_id)
      OR
      (tipo_evaluador <> 'autoevaluacion' AND evaluador_id <> docente_id)
    )
);

CREATE INDEX idx_eval_docente ON evaluaciones_360(docente_id);
CREATE INDEX idx_eval_evaluador ON evaluaciones_360(evaluador_id);
CREATE INDEX idx_eval_periodo ON evaluaciones_360(periodo_id);

-- =============================
-- Banco de preguntas y respuestas
-- =============================

CREATE TABLE preguntas (
  id              BIGINT PRIMARY KEY AUTO_INCREMENT,
  texto           TEXT NOT NULL,
  tipo_pregunta   VARCHAR(20) NOT NULL DEFAULT 'likert',
  activo          BOOLEAN NOT NULL DEFAULT TRUE,
  orden           INTEGER NOT NULL DEFAULT 0,
  creado_en       TIMESTAMP NOT NULL DEFAULT NOW(),
  CONSTRAINT ck_tipo_pregunta
    CHECK (tipo_pregunta IN ('likert', 'si_no', 'opcion_multiple'))
);

CREATE TABLE respuestas (
  id               BIGINT PRIMARY KEY AUTO_INCREMENT,
  evaluacion_id    BIGINT NOT NULL REFERENCES evaluaciones_360(id) ON DELETE CASCADE,
  completada_en    TIMESTAMP NULL,
  observaciones    TEXT NULL,
  UNIQUE (evaluacion_id)
);

CREATE TABLE detalle_respuestas (
  id              BIGINT PRIMARY KEY AUTO_INCREMENT,
  respuesta_id    BIGINT NOT NULL REFERENCES respuestas(id) ON DELETE CASCADE,
  pregunta_id     BIGINT NOT NULL REFERENCES preguntas(id) ON DELETE RESTRICT,
  valor_numerico  NUMERIC(5,2) NULL,
  valor_texto     TEXT NULL,
  creado_en       TIMESTAMP NOT NULL DEFAULT NOW(),
  CONSTRAINT ck_valor_informado
    CHECK (valor_numerico IS NOT NULL OR valor_texto IS NOT NULL),
  UNIQUE (respuesta_id, pregunta_id)
);

CREATE INDEX idx_detalle_respuesta_id ON detalle_respuestas(respuesta_id);
CREATE INDEX idx_detalle_pregunta_id ON detalle_respuestas(pregunta_id);

-- Roles sugeridos
INSERT IGNORE INTO roles (nombre) VALUES
  ('estudiante'),
  ('docente'),
  ('jefe_area'),
  ('vicerrector'),
  ('admin')
;

COMMIT;
