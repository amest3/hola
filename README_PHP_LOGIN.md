# Mini sitio PHP (login + rol + datos de usuario)

## 1) Requisitos
- PHP 8+
- MySQL 8+ o MariaDB 10.4+
- Haber ejecutado:
  - `esquema_evaluacion_360.sql`
  - `datos_prueba_evaluacion_360.sql`


## Compatibilidad de collation
Si te aparece el error `#1273 - Collation desconocida: 'utf8mb4_0900_ai_ci'`, usa `utf8mb4_unicode_ci` (ya está configurado así en este proyecto).

## 2) Variables de entorno
Configura estas variables antes de levantar PHP:

```bash
export DB_HOST=127.0.0.1
export DB_PORT=33066
export DB_NAME=uets360
export DB_USER=root
export DB_PASS=tu_password
```

## 3) Levantar servidor
```bash
php -S 0.0.0.0:8000 -t public
```


## XAMPP / subcarpeta
Si lo abres como `http://localhost/tu_carpeta/public/`, este proyecto usa rutas relativas para evitar redirecciones al root de `localhost`.

## 4) Flujo
- `/index.php` -> iniciar sesión
- `/dashboard.php` -> muestra tipo de usuario (rol) y datos del usuario autenticado
- `/usuarios.php` -> lista de usuarios con sus roles
- `/logout.php` -> cerrar sesión
- `/resultado_total.php` -> nota total por docente (solo roles autorizados)

## Nota de contraseñas
Con el seed actual, la contraseña de cada usuario es exactamente el valor de su `password_hash` (por ejemplo: `hash_demo_1`, `hash_demo_2`, etc.).


## Dashboard por rol
Al iniciar sesión, el panel cambia según el rol principal detectado:
- `estudiante`: muestra curso, paralelo, especialidad y periodo.
- `docente`: muestra asignaciones de materias y cursos.
- `jefe_area`: muestra áreas asignadas por periodo.
- `vicerrector`: muestra métricas globales.
- `admin`: muestra métricas de catálogos del sistema.


## Encuestas por rol y resultados
Se agregaron páginas de encuesta separadas para cada rol (20 preguntas por rol):
- `encuesta_estudiante.php`
- `encuesta_docente.php`
- `encuesta_jefe_area.php`
- `encuesta_vicerrector.php`
- `encuesta_admin.php`

`docente` ahora sirve tanto para autoevaluación como para evaluación de otros docentes (pares).

Además, `resultados.php` muestra los resultados enviados por el usuario autenticado (promedio y detalle por pregunta).
En cada encuesta ahora debes elegir en una lista desplegable el docente a evaluar; si eliges tu propio usuario docente, el registro queda como `autoevaluacion`.
Para rol `docente`, las preguntas cambian según el docente seleccionado: si te eliges a ti mismo se carga banco de autoevaluación; si eliges a otro docente se carga banco de evaluación a pares.
Si el usuario logueado tiene rol `docente`, el selector inicia automáticamente en sí mismo (autoevaluación). Al cambiar el docente en el desplegable, la página se recarga automáticamente para cargar el banco correcto de preguntas.





## Flujo estudiante
Al iniciar sesión como estudiante, primero se solicita contexto académico en `seleccionar_contexto.php` (curso, paralelo y materia).
Ese contexto se usa para filtrar docentes a evaluar en las encuestas.


## Datos seed extra
Se añadió un usuario administrador de prueba: `Carla Admin` (`carla.admin@colegio.edu`).
