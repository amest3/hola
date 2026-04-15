-- Datos de prueba (10 registros por categoría principal)
-- Requiere que el esquema de `esquema_evaluacion_360.sql` ya exista.

START TRANSACTION;

-- =============================
-- Roles base (idempotente)
-- =============================
INSERT IGNORE INTO roles (nombre) VALUES
  ('estudiante'),
  ('docente'),
  ('jefe_area'),
  ('vicerrector'),
  ('admin');

-- =============================
-- 10 periodos_academicos
-- =============================
INSERT INTO periodos_academicos (id, nombre, fecha_inicio, fecha_fin, activo) VALUES
  (1, '2022-A', '2022-01-10', '2022-06-30', FALSE),
  (2, '2022-B', '2022-07-11', '2022-12-20', FALSE),
  (3, '2023-A', '2023-01-09', '2023-06-30', FALSE),
  (4, '2023-B', '2023-07-10', '2023-12-20', FALSE),
  (5, '2024-A', '2024-01-08', '2024-06-30', FALSE),
  (6, '2024-B', '2024-07-08', '2024-12-20', FALSE),
  (7, '2025-A', '2025-01-06', '2025-06-30', TRUE),
  (8, '2025-B', '2025-07-07', '2025-12-20', FALSE),
  (9, '2026-A', '2026-01-05', '2026-06-30', FALSE),
  (10, '2026-B', '2026-07-06', '2026-12-20', FALSE);

-- =============================
-- 10 areas
-- =============================
INSERT INTO areas (id, nombre) VALUES
  (1, 'Informatica'),
  (2, 'Ciencias Experimentales'),
  (3, 'Mecatronica'),
  (4, 'Electricidad'),
  (5, 'Matematica'),
  (6, 'Lengua y Literatura'),
  (7, 'Ciencias Sociales'),
  (8, 'Ingles'),
  (9, 'Emprendimiento'),
  (10, 'Educacion Fisica');

-- =============================
-- 10 cursos (especialidades solicitadas incluidas)
-- =============================
INSERT INTO cursos (id, curso, paralelo, nombre, especialidad) VALUES
  (1, '1', 'A', 'Primero A', 'informatica'),
  (2, '1', 'B', 'Primero B', 'ciencias experimentales'),
  (3, '2', 'A', 'Segundo A', 'mecatronica'),
  (4, '2', 'B', 'Segundo B', 'electricidad'),
  (5, '3', 'A', 'Tercero A', 'informatica'),
  (6, '3', 'B', 'Tercero B', 'ciencias experimentales'),
  (7, '4', 'A', 'Cuarto A', 'mecatronica'),
  (8, '4', 'B', 'Cuarto B', 'electricidad'),
  (9, '5', 'A', 'Quinto A', 'informatica'),
  (10, '5', 'B', 'Quinto B', 'mecatronica');

-- =============================
-- 10 materias (inventadas)
-- =============================
INSERT INTO materias (id, nombre, area_id) VALUES
  (1, 'Programacion Web I', 1),
  (2, 'Base de Datos I', 1),
  (3, 'Fisica Aplicada', 2),
  (4, 'Quimica General', 2),
  (5, 'Robotica Basica', 3),
  (6, 'Automatizacion Industrial', 3),
  (7, 'Circuitos Electricos', 4),
  (8, 'Instalaciones Electricas', 4),
  (9, 'Algoritmos y Estructuras', 1),
  (10, 'Sistemas Embebidos', 3);

-- =============================
-- 10 usuarios
-- =============================
INSERT INTO usuarios (id, nombres, apellidos, email, password_hash, activo, creado_en) VALUES
  (1, 'Fabian', 'Munoz', 'fabian.munoz@colegio.edu', 'hash_demo_1', TRUE, NOW()),
  (2, 'Josue', 'Abad', 'josue.abad@colegio.edu', 'hash_demo_2', TRUE, NOW()),
  (3, 'Pablo', 'Durazno', 'pablo.durazno@colegio.edu', 'hash_demo_3', TRUE, NOW()),
  (4, 'Wilson', 'Cedillo', 'wilson.cedillo@colegio.edu', 'hash_demo_4', TRUE, NOW()),
  (5, 'Aaron', 'Machuca', 'aaron.machuca@colegio.edu', 'hash_demo_5', TRUE, NOW()),
  (6, 'Maria', 'Lopez', 'maria.lopez@colegio.edu', 'hash_demo_6', TRUE, NOW()),
  (7, 'Kevin', 'Paredes', 'kevin.paredes@colegio.edu', 'hash_demo_7', TRUE, NOW()),
  (8, 'Lucia', 'Vera', 'lucia.vera@colegio.edu', 'hash_demo_8', TRUE, NOW()),
  (9, 'Diana', 'Reyes', 'diana.reyes@colegio.edu', 'hash_demo_9', TRUE, NOW()),
  (10, 'Jorge', 'Mora', 'jorge.mora@colegio.edu', 'hash_demo_10', TRUE, NOW()),
  (11, 'Carla', 'Admin', 'carla.admin@colegio.edu', 'hash_demo_11', TRUE, NOW());

-- =============================
-- 10 usuario_roles
-- =============================
INSERT INTO usuario_roles (id, usuario_id, rol_id, estado, asignado_en) VALUES
  (1, 1, (SELECT id FROM roles WHERE nombre = 'vicerrector' LIMIT 1), TRUE, NOW()),
  (2, 2, (SELECT id FROM roles WHERE nombre = 'jefe_area' LIMIT 1), TRUE, NOW()),
  (3, 3, (SELECT id FROM roles WHERE nombre = 'docente' LIMIT 1), TRUE, NOW()),
  (4, 4, (SELECT id FROM roles WHERE nombre = 'docente' LIMIT 1), TRUE, NOW()),
  (5, 5, (SELECT id FROM roles WHERE nombre = 'estudiante' LIMIT 1), TRUE, NOW()),
  (6, 6, (SELECT id FROM roles WHERE nombre = 'docente' LIMIT 1), TRUE, NOW()),
  (7, 7, (SELECT id FROM roles WHERE nombre = 'estudiante' LIMIT 1), TRUE, NOW()),
  (8, 8, (SELECT id FROM roles WHERE nombre = 'estudiante' LIMIT 1), TRUE, NOW()),
  (9, 9, (SELECT id FROM roles WHERE nombre = 'docente' LIMIT 1), TRUE, NOW()),
  (10, 10, (SELECT id FROM roles WHERE nombre = 'jefe_area' LIMIT 1), TRUE, NOW()),
  (11, 11, (SELECT id FROM roles WHERE nombre = 'admin' LIMIT 1), TRUE, NOW());

-- =============================
-- 10 asignaciones_docente
-- =============================
INSERT INTO asignaciones_docente (id, docente_id, materia_id, curso_id, periodo_id) VALUES
  (1, 3, 1, 1, 7),
  (2, 3, 2, 5, 7),
  (3, 6, 3, 2, 7),
  (4, 6, 4, 6, 7),
  (5, 9, 5, 3, 7),
  (6, 9, 6, 7, 7),
  (7, 3, 9, 9, 7),
  (8, 6, 7, 4, 7),
  (9, 9, 8, 8, 7),
  (10, 3, 10, 10, 7);

-- =============================
-- 10 matriculas_estudiante
-- =============================
INSERT INTO matriculas_estudiante (id, estudiante_id, curso_id, periodo_id) VALUES
  (1, 5, 1, 7),
  (2, 7, 2, 7),
  (3, 8, 3, 7),
  (4, 5, 5, 8),
  (5, 7, 6, 8),
  (6, 8, 7, 8),
  (7, 5, 9, 9),
  (8, 7, 10, 9),
  (9, 8, 1, 10),
  (10, 5, 2, 10);

-- =============================
-- 10 jefaturas_area
-- =============================
INSERT INTO jefaturas_area (id, usuario_id, area_id, periodo_id) VALUES
  (1, 2, 1, 7),
  (2, 2, 2, 7),
  (3, 2, 3, 7),
  (4, 2, 4, 7),
  (5, 2, 1, 8),
  (6, 2, 2, 8),
  (7, 2, 3, 8),
  (8, 2, 4, 8),
  (9, 2, 1, 9),
  (10, 2, 2, 9);

-- =============================
-- 10 evaluaciones_360
-- =============================
INSERT INTO evaluaciones_360 (id, docente_id, evaluador_id, tipo_evaluador, periodo_id, materia_id, curso_id, estado, fecha, creado_en) VALUES
  (1, 3, 5, 'estudiante', 7, 1, 1, 'enviada', '2025-03-10', NOW()),
  (2, 3, 7, 'estudiante', 7, 2, 5, 'enviada', '2025-03-11', NOW()),
  (3, 6, 8, 'estudiante', 7, 3, 2, 'enviada', '2025-03-12', NOW()),
  (4, 6, 9, 'par_docente', 7, 4, 6, 'en_progreso', '2025-03-13', NOW()),
  (5, 9, 3, 'par_docente', 7, 5, 3, 'enviada', '2025-03-14', NOW()),
  (6, 3, 2, 'jefe_area', 7, 1, NULL, 'enviada', '2025-03-15', NOW()),
  (7, 6, 2, 'jefe_area', 7, 3, NULL, 'pendiente', '2025-03-16', NOW()),
  (8, 9, 1, 'vicerrector', 7, NULL, NULL, 'enviada', '2025-03-17', NOW()),
  (9, 3, 3, 'autoevaluacion', 7, NULL, NULL, 'enviada', '2025-03-18', NOW()),
  (10, 6, 6, 'autoevaluacion', 7, NULL, NULL, 'cerrada', '2025-03-19', NOW());

-- =============================
-- 10 preguntas (sin abiertas)
-- =============================
INSERT INTO preguntas (id, texto, tipo_pregunta, activo, orden, creado_en) VALUES
  (1, 'El docente explica con claridad los temas.', 'likert', TRUE, 1, NOW()),
  (2, 'El docente fomenta la participacion en clase.', 'likert', TRUE, 2, NOW()),
  (3, 'El docente usa recursos didacticos adecuados.', 'likert', TRUE, 3, NOW()),
  (4, 'El docente cumple puntualmente con sus clases.', 'si_no', TRUE, 4, NOW()),
  (5, 'El docente evalua de manera justa.', 'likert', TRUE, 5, NOW()),
  (6, 'El docente resuelve dudas oportunamente.', 'likert', TRUE, 6, NOW()),
  (7, 'El docente promueve trabajo colaborativo.', 'likert', TRUE, 7, NOW()),
  (8, 'El docente domina su asignatura.', 'likert', TRUE, 8, NOW()),
  (9, 'El docente retroalimenta tareas y proyectos.', 'likert', TRUE, 9, NOW()),
  (10, 'Desempeno global del docente.', 'opcion_multiple', TRUE, 10, NOW());

-- =============================
-- 10 respuestas (1 por evaluacion)
-- =============================
INSERT INTO respuestas (id, evaluacion_id, completada_en, observaciones) VALUES
  (1, 1, NOW(), NULL),
  (2, 2, NOW(), NULL),
  (3, 3, NOW(), NULL),
  (4, 4, NOW(), NULL),
  (5, 5, NOW(), NULL),
  (6, 6, NOW(), NULL),
  (7, 7, NOW(), NULL),
  (8, 8, NOW(), NULL),
  (9, 9, NOW(), NULL),
  (10, 10, NOW(), NULL);

-- =============================
-- 10 detalle_respuestas
-- =============================
INSERT INTO detalle_respuestas (id, respuesta_id, pregunta_id, valor_numerico, valor_texto, creado_en) VALUES
  (1, 1, 1, 4.00, NULL, NOW()),
  (2, 2, 2, 5.00, NULL, NOW()),
  (3, 3, 3, 4.00, NULL, NOW()),
  (4, 4, 4, 1.00, NULL, NOW()),
  (5, 5, 5, 3.00, NULL, NOW()),
  (6, 6, 6, 4.00, NULL, NOW()),
  (7, 7, 7, 5.00, NULL, NOW()),
  (8, 8, 8, 5.00, NULL, NOW()),
  (9, 9, 9, 4.00, NULL, NOW()),
  (10, 10, 10, NULL, 'Excelente', NOW());

COMMIT;
