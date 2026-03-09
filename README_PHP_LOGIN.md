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

## Nota de contraseñas
Con el seed actual, la contraseña de cada usuario es exactamente el valor de su `password_hash` (por ejemplo: `hash_demo_1`, `hash_demo_2`, etc.).


## Dashboard por rol
Al iniciar sesión, el panel cambia según el rol principal detectado:
- `estudiante`: muestra curso, paralelo, especialidad y periodo.
- `docente` / `companero_docente`: muestra asignaciones de materias y cursos.
- `jefe_area`: muestra áreas asignadas por periodo.
- `vicerrector`: muestra métricas globales.
- `admin`: muestra métricas de catálogos del sistema.
