# Mapa del código fuente de SIGA

Este documento explica cómo está organizado el código fuente propio de SIGA, qué responsabilidad tiene cada componente y cómo fluye una solicitud. No reproduce literalmente los archivos PHP, HTML, CSS o JavaScript: el código fuente actualizado se encuentra en las rutas del repositorio descritas aquí.

## Vista general

```text
SIGA/
├── app/
│   ├── Controllers/
│   ├── Helpers/
│   ├── Models/
│   └── Views/
├── config/
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── uploads/
├── sql/
├── infinityfree_upload/
├── composer.json
├── create-admin.php
└── .env.example
```

La carpeta `app/`, `config/`, `public/`, `sql/` y los archivos de raíz son el proyecto fuente. `infinityfree_upload/` contiene una copia preparada para alojamientos compartidos; sus archivos de aplicación, vistas y recursos web deben mantenerse alineados con el código raíz. `vendor/`, las plantillas compiladas de `tmp/` y los archivos cargados por usuarios no son código fuente propio.

## Punto de entrada y dependencias

| Archivo | Responsabilidad |
| --- | --- |
| `public/index.php` | Punto de entrada web. Inicia la sesión, carga Composer y la configuración, prepara Fat-Free y registra las rutas de la aplicación. |
| `composer.json` | Declara PHP y las dependencias del proyecto: Fat-Free Framework, PHP dotenv y FPDF. Configura el autoload PSR-4 `App\` → `app/`. |
| `composer.lock` | Fija las versiones exactas de las dependencias para instalaciones reproducibles. |
| `create-admin.php` | Utilidad CLI para crear el primer administrador; rechaza la ejecución vía web y almacena la contraseña con `password_hash()`. |
| `.env.example` | Plantilla de variables de entorno. Cópiala a `.env` y configura credenciales privadas; no publiques `.env`. |

### Rutas principales

`public/index.php` relaciona las URLs y métodos HTTP con las acciones de los controladores. Entre otras, registra:

- Autenticación: `/login`, `/login/authenticate`, `/logout`.
- Alumnos: `/alumnos`, `/alumnos/buscar`, formularios, detalle y operaciones de administración.
- Notas: `/notas`, búsqueda, historial, alta y edición.
- Catálogos: carreras, cursos, semestres y secciones.
- Preferencias y cuentas: `/ajustes` y las operaciones protegidas de usuarios.
- Reportes: alumnos por carrera, notas, historial individual y exportaciones.

Fat-Free despacha cada solicitud a la acción asociada. El archivo finaliza ejecutando `$f3->run()`.

## Controladores

Los controladores reciben solicitudes HTTP, aplican autorización y validación, llaman a modelos/helpers y preparan la vista o descarga.

| Archivo | Función |
| --- | --- |
| `app/Controllers/LoginController.php` | Inicio y cierre de sesión, autenticación y manejo de credenciales. |
| `app/Controllers/DashboardController.php` | Estadísticas y página principal según el rol de la cuenta. |
| `app/Controllers/AlumnoController.php` | Listado, búsqueda con filtros, alta, edición, detalle, fotografías y eliminación de alumnos. |
| `app/Controllers/NotaController.php` | Registro, validación, edición, búsqueda e historial de calificaciones. |
| `app/Controllers/CatalogoController.php` | Administración de carreras, cursos, semestres y secciones. |
| `app/Controllers/ReporteController.php` | Reportes de alumnos/notas, filtros y exportaciones CSV y DOC. |
| `app/Controllers/AjustesController.php` | Preferencias y administración de cuentas de usuario. |

La copia `infinityfree_upload/app/Controllers/` contiene las versiones destinadas al paquete de producción compartido.

## Modelos

Los modelos encapsulan las consultas PDO a las tablas académicas. Las consultas con entradas variables deben usar parámetros preparados.

| Archivo | Tabla o datos |
| --- | --- |
| `app/Models/Alumno.php` | Alumnos, búsqueda por nombres/apellidos y filtros académicos asociados. |
| `app/Models/Nota.php` | Notas, consulta de historial, reportes y estadísticas de aprobación. |
| `app/Models/Carrera.php` | Carreras. |
| `app/Models/Curso.php` | Cursos. |
| `app/Models/Semestre.php` | Semestres. |
| `app/Models/Seccion.php` | Secciones. |
| `app/Models/Usuario.php` | Cuentas, roles, hashes y estados de usuario. |

## Helpers

| Archivo | Responsabilidad |
| --- | --- |
| `app/Helpers/Database.php` | Conexión PDO a MySQL y ejecución de consultas. |
| `app/Helpers/Access.php` | Reglas para requerir sesión iniciada o rol de administrador. |
| `app/Helpers/Csrf.php` | Generación y validación de tokens CSRF. |
| `app/Helpers/Validation.php` | Validaciones compartidas de campos y valores. |
| `app/Helpers/File.php` | Validación, almacenamiento y eliminación de fotografías. |
| `app/Helpers/DemoReportData.php` | Fachada que reúne datos reales de modelos para reportes y calcula estados de aprobación. El nombre histórico no significa que use datos ficticios. |

## Vistas

Las plantillas Fat-Free están bajo `app/Views/`. Los controladores pasan valores con `$f3->set(...)`; las plantillas los muestran con `{{ @valor }}` y usan `<repeat>` y `<check>` para listas y condiciones.

| Directorio/archivo | Contenido |
| --- | --- |
| `app/Views/layouts/main.html` | Estructura común de pantallas autenticadas y navegación. |
| `app/Views/layouts/auth.html` | Estructura de las páginas de acceso. |
| `app/Views/login/index.html` | Formulario de inicio de sesión. |
| `app/Views/dashboard/index.html` | Dashboard académico. |
| `app/Views/alumnos/index.html` | Listado paginado de alumnos. |
| `app/Views/alumnos/buscar.html` | Búsqueda por nombre/apellido y filtros académicos. |
| `app/Views/alumnos/form.html` | Alta y edición de alumnos. |
| `app/Views/alumnos/ver.html` | Detalle de alumno. |
| `app/Views/notas/index.html` | Listado de notas recientes. |
| `app/Views/notas/buscar.html` | Búsqueda de alumno para operaciones de notas. |
| `app/Views/notas/form.html` | Registro y edición de notas. |
| `app/Views/notas/historial.html` | Historial y estadísticas de notas del alumno. |
| `app/Views/catalogos/carreras/index.html`, `form.html` | Listado y formulario de carreras. |
| `app/Views/catalogos/cursos/index.html`, `form.html` | Listado y formulario de cursos. |
| `app/Views/catalogos/semestres/index.html`, `form.html` | Listado y formulario de semestres. |
| `app/Views/catalogos/secciones/index.html`, `form.html` | Listado y formulario de secciones. |
| `app/Views/reportes/alumnos.html` | Reporte de alumnos por carrera. |
| `app/Views/reportes/notas.html` | Reporte general filtrable de notas. |
| `app/Views/reportes/alumno.html` | Reporte e historial individual del alumno. |
| `app/Views/ajustes/index.html` | Preferencias de idioma/apariencia y administración de cuentas. |

La copia `infinityfree_upload/app/Views/` contiene las plantillas incluidas en el paquete de publicación.

## Recursos de interfaz

| Ruta | Responsabilidad |
| --- | --- |
| `public/css/theme.css` | Tema visual, componentes de interfaz y variables de color para modo claro y oscuro. |
| `public/js/settings.js` | Preferencia de idioma español/inglés, tema claro/oscuro y comportamiento de pestañas de Ajustes. |
| `public/uploads/` | Directorio público de fotografías cargadas; contiene datos de usuario y requiere respaldo, no es caché. |
| `app/Views/layouts/` | Carga los recursos visuales y aplica la preferencia de tema almacenada en el navegador. |

Las versiones de `public/css/` y `public/js/` en `infinityfree_upload/` se copian al paquete de producción.

## Configuración y base de datos

| Ruta | Responsabilidad |
| --- | --- |
| `config/app.php` | Carga variables de entorno para nombre, entorno, URL, umbral de aprobación, límite de nota y archivos. |
| `config/database.php` | Construye la conexión PDO MySQL con las variables de `.env`. |
| `sql/sistema_academico_final.sql` | Esquema y datos iniciales para una instalación nueva. **Es destructivo: elimina y recrea la base.** |
| `sql/migrate_users.sql` | Migración de cuentas para una base existente. |
| `sql/habilitar_eliminacion.sql` | Permisos `DELETE` requeridos para eliminaciones permanentes. |
| `sql/limpiar_datos.sql` | Elimina **todos** los registros de las tablas académicas y de usuarios; deja las tablas vacías y reinicia sus contadores. Es destructivo y no debe usarse si se quieren conservar datos. |

La escala de notas es de 0 a 100. La nota mínima aprobatoria es 61 por defecto y se configura con `NOTA_MINIMA_APROBACION`; no se debe confundir ese umbral con el mínimo permitido para ingresar una nota, que es 0.

## Paquete de producción compartida

`infinityfree_upload/` es una distribución de la aplicación para subir a un hosting que publique desde una carpeta web:

- Su `.htaccess` de raíz bloquea rutas privadas y reescribe hacia `public/`.
- `public/.htaccess` dirige rutas no existentes a `public/index.php`.
- Incluye sus propios `app/`, `config/`, `public/` y `vendor/`.
- Se sube el **contenido** de esa carpeta, preservando su estructura. `.env` se crea por separado con credenciales del proveedor y no se almacena en el repositorio.

Después de modificar un archivo de aplicación raíz que también exista en `infinityfree_upload/`, confirma que ambas copias queden sincronizadas antes de preparar producción.

## Flujo de una solicitud

1. El navegador solicita una ruta registrada en `public/index.php`.
2. Fat-Free resuelve el patrón y llama al controlador correspondiente.
3. El controlador valida sesión, rol, parámetros y token CSRF en las operaciones que modifican datos.
4. Los modelos consultan MySQL por medio de PDO y los helpers proporcionan acceso, validación, CSRF o manejo de archivos.
5. El controlador carga datos en Fat-Free y renderiza una plantilla de `app/Views/`, o genera una descarga/redirect.
6. El navegador presenta la respuesta; el JavaScript conserva preferencias de idioma y tema en el almacenamiento local del navegador.

## Cómo añadir una funcionalidad

1. Registra la ruta y el método HTTP en `public/index.php`.
2. Implementa la acción en el controlador responsable, aplicando autorización y validación de entradas.
3. Añade o reutiliza la operación de acceso a datos en el modelo correcto. Usa parámetros PDO.
4. Crea o modifica la plantilla de `app/Views/` y prepara sus variables en el controlador.
5. Si cambias el esquema, escribe una migración segura e idempotente para bases existentes; no uses el script destructivo de instalación como actualización.
6. Si el archivo forma parte del paquete compartido, sincroniza la versión correspondiente en `infinityfree_upload/`.
7. Verifica sintaxis PHP, flujo autorizado/no autorizado, validación de datos y renderizado.

Para requisitos, publicación, seguridad, rutas y permisos, consulta [documentacion.md](documentacion.md) y [PUBLICACION_PLATAFORMAS.md](PUBLICACION_PLATAFORMAS.md).
