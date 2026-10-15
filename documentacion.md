# Documentación del Sistema Académico

## 1. Descripción

Aplicación web para administrar alumnos, carreras, cursos, semestres, secciones y notas. Incluye historial académico, reportes filtrables, exportación de datos y cuentas con roles de administrador y usuario normal.

## 2. Tecnologías y requisitos

- PHP 8.1 o superior.
- MySQL 8.0.16 o superior.
- Fat-Free Framework 3.x y Composer.
- Extensiones PHP: `pdo_mysql`, `mbstring` y `fileinfo`.
- Servidor PHP integrado, Apache o Nginx.
- Bootstrap 5, HTML, CSS y JavaScript para la interfaz.

Instala las dependencias del proyecto con `composer install` desde la raíz. El autoload PSR-4 registra el espacio de nombres `App\` en `app/`.

## 3. Organización del código

| Ruta | Responsabilidad |
| --- | --- |
| `public/index.php` | Inicializa Fat-Free, sesión, rutas y manejo de errores. |
| `app/Controllers/` | Atiende solicitudes HTTP y prepara los datos de cada vista. |
| `app/Models/` | Ejecuta operaciones SQL sobre las tablas del sistema. |
| `app/Helpers/Database.php` | Conexión PDO y consultas preparadas. |
| `app/Helpers/Csrf.php` | Generación y validación de tokens CSRF. |
| `app/Helpers/Access.php` | Reglas comunes para exigir inicio de sesión o rol de administrador. |
| `app/Helpers/File.php` | Validación y manejo de fotografías. |
| `app/Views/` | Plantillas Fat-Free para las pantallas. |
| `config/` | Configuración de aplicación y conexión MySQL desde `.env`. |
| `public/css/`, `public/js/` | Estilos, idioma y comportamiento de interfaz. |
| `public/uploads/` | Fotografías cargadas de alumnos. |
| `sql/` | Esquema de instalación, datos iniciales y migraciones. |
| `create-admin.php` | Crea el primer administrador desde la terminal. |

## 4. Módulos

- **Dashboard:** estadísticas generales; los registros recientes solo se muestran al administrador.
- **Alumnos:** registro, edición, búsqueda, fotografía, detalle y eliminación permanente.
- **Notas:** registro y edición de calificaciones, historial y eliminación permanente.
- **Catálogos:** mantenimiento de carreras, cursos, semestres y secciones.
- **Reportes:** alumnos por carrera, notas con filtros e historial individual para usuarios autenticados.
- **Exportaciones:** CSV y documento HTML compatible con Word (`.doc`).
- **Usuarios:** inicio de sesión y administración de cuentas solo para administradores.
- **Preferencias:** idioma español/inglés guardado en el navegador.

Los reportes consultan las tablas MySQL mediante `DemoReportData`, una fachada de consultas y transformación (no un conjunto de datos ficticios).

## 5. Base de datos

La base se llama `sistema_academico` y usa `utf8mb4`. Las tablas principales son:

| Tabla | Contenido |
| --- | --- |
| `carreras` | Orientaciones académicas; estado activo/inactivo. |
| `cursos` | Código y nombre de cada curso. El código es único. |
| `semestres` | Semestres ordenados en la aplicación por `id_semestre`. |
| `secciones` | Secciones académicas. |
| `alumnos` | Datos personales, fotografía opcional y carrera asociada. |
| `notas` | Alumno, curso, semestre, sección, calificación y estado. |
| `usuarios` | Nombre, correo único, hash de contraseña, rol y estado. |

Las relaciones de alumnos y notas usan claves foráneas con `ON DELETE RESTRICT`. Al eliminar desde la aplicación, el administrador puede bloquear el borrado si existen dependencias o confirmar su eliminación en cascada. Las notas se borran antes que el alumno o catálogo dentro de una transacción; en una carrera, el borrado en cascada también elimina sus alumnos y sus notas. La aplicación conserva además acciones para desactivar registros. La base limita las notas al rango de 0 a 100.

El script `sql/sistema_academico_final.sql` incluye datos iniciales y crea o actualiza el usuario MySQL `sistema_app`. **Este script elimina y vuelve a crear la base de datos**; úsalo solo para una instalación nueva o cuando aceptes perder los datos actuales.

Para una base ya existente, aplica `sql/migrate_users.sql`. Crea `usuarios` si no existe y sincroniza el usuario MySQL `sistema_app` con los permisos de la aplicación. También puedes aplicar `sql/habilitar_eliminacion.sql` para actualizar únicamente los permisos `DELETE`. Si cambias la contraseña de `sistema_app`, actualízala en el SQL que ejecutes y en `.env`.

## 6. Instalación

### Instalación nueva

1. Instala PHP, MySQL, Composer y las extensiones PHP indicadas arriba.
2. Desde la raíz del proyecto, instala dependencias y crea la configuración local:

```powershell
composer install
Copy-Item .env.example .env
```

3. En `sql/sistema_academico_final.sql`, cambia la contraseña de ejemplo de `sistema_app`. Configura la misma contraseña en `.env` mediante `DB_PASS`. No uses la contraseña de ejemplo en un entorno compartido.
4. Importa el esquema y los datos. En PowerShell:

```powershell
Get-Content .\sql\sistema_academico_final.sql | mysql -u root -p
```

En Linux con MySQL configurado para autenticar `root` por socket:

```bash
sudo mysql < sql/sistema_academico_final.sql
```

Si `root` utiliza contraseña, usa `mysql -u root -p < sql/sistema_academico_final.sql`.

5. Desde la raíz del proyecto, crea el primer administrador:

```powershell
php .\create-admin.php
```

El comando solicita nombre, correo y contraseña. La contraseña debe tener al menos 12 caracteres y se almacena usando `password_hash()`.

### Base existente

No vuelvas a importar el script final si necesitas conservar la información. En PowerShell, ejecuta la migración:

```powershell
Get-Content .\sql\migrate_users.sql | mysql -u root -p sistema_academico
php .\create-admin.php
```

En Linux con autenticación por socket de `root`:

```bash
sudo mysql sistema_academico < sql/migrate_users.sql
php create-admin.php
```

Si la cuenta MySQL de la aplicación todavía no tiene permisos de borrado, actualízalos con:

```bash
sudo mysql < sql/habilitar_eliminacion.sql
```

Si `root` utiliza contraseña, sustituye `sudo mysql` por `mysql -u root -p`. Los permisos se conceden con una cuenta administrativa de MySQL; SIGA no puede otorgarlos desde la interfaz.

### Variables de entorno

`.env.example` documenta la configuración disponible:

| Variable | Uso |
| --- | --- |
| `DB_HOST`, `DB_PORT` | Host y puerto MySQL. |
| `DB_NAME`, `DB_USER`, `DB_PASS` | Base y credenciales de la aplicación. |
| `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL` | Nombre, entorno, depuración y URL pública. |
| `NOTA_MINIMA_APROBACION` | Calificación mínima para aprobar; por defecto `60`. |
| `NOTA_MAXIMA` | Límite superior de la calificación; por defecto `100`. |
| `MAX_FILE_SIZE` | Tamaño máximo de fotografía en bytes; por defecto 2 MiB. |
| `ALLOWED_IMAGE_TYPES` | Tipos MIME permitidos para fotografías. |
| `PAGINATION_LIMIT` | Tamaño de página configurado; por defecto `20`. |

No publiques `.env` ni guardes contraseñas reales en el repositorio.

## 7. Ejecución

Configura `public/` como raíz del sitio web. Para desarrollo, desde PowerShell:

```powershell
Set-Location public
php -S localhost:8000
```

Abre `http://localhost:8000`. En Linux puede iniciarse con `cd public && php -S localhost:8000`.

La carpeta `public/uploads/` debe permitir escritura al proceso PHP. En Linux, ajusta propietario/permisos para el usuario del servidor; evita permisos globales de escritura.

## 8. Cuentas y permisos

- El acceso se realiza con correo y contraseña de una cuenta activa.
- Las contraseñas no se guardan en texto plano; se verifican con `password_verify()`.
- El primer administrador se crea con `create-admin.php`; no existe una contraseña inicial fija en SQL.
- Solo los administradores pueden acceder a las pantallas y acciones de gestión de alumnos, notas, catálogos y usuarios. El servidor aplica la restricción, además de ocultar los enlaces de navegación.
- Los usuarios normales pueden consultar el dashboard estadístico y los reportes/exportaciones; no pueden consultar las pantallas de gestión.
- Un administrador puede crear, activar, desactivar y eliminar otras cuentas desde Ajustes.
- No se permite desactivar ni eliminar la cuenta propia ni desactivar o eliminar al último administrador activo.
- Las cuentas desactivadas no pueden volver a iniciar sesión y una sesión existente se invalida en su siguiente solicitud.
- Los formularios de autenticación y de administración de cuentas usan tokens CSRF.

Actualmente no hay una pantalla para restablecer contraseñas. Un administrador puede crear una cuenta nueva; el restablecimiento de una existente requiere una operación administrativa planificada.

## 9. Rutas principales

### Acceso y ajustes

| Método y ruta | Función |
| --- | --- |
| `GET /login` | Formulario de acceso. |
| `POST /login/authenticate` | Validar correo y contraseña. |
| `GET /logout` | Cerrar sesión. |
| `GET /ajustes` | Preferencias de idioma; administración de cuentas para admin. |
| `POST /ajustes/usuarios` | Crear cuenta. Solo admin. |
| `POST /ajustes/usuarios/{id}/estado/{activar\|desactivar}` | Cambiar estado de una cuenta. Solo admin. |
| `POST /ajustes/usuarios/{id}/eliminar` | Eliminar una cuenta permanentemente. Solo admin. |

### Operación académica

| Método y ruta | Función |
| --- | --- |
| `GET /` | Dashboard. |
| `GET /alumnos` | Listado paginado de alumnos. |
| `GET /alumnos/buscar?q=...` | Buscar alumnos. |
| `GET /alumnos/nuevo`, `POST /alumnos/guardar` | Crear alumno. |
| `GET /alumnos/{id}`, `GET /alumnos/{id}/editar`, `POST /alumnos/{id}/actualizar` | Consultar o editar alumno. |
| `GET /alumnos/{id}/desactivar`, `GET /alumnos/{id}/activar` | Cambiar estado del alumno. |
| `POST /alumnos/{id}/eliminar` | Eliminar al alumno, con opción de bloquear si tiene notas o borrarlas también. Solo admin. |
| `GET /notas` | Últimas notas. |
| `GET /notas/buscar?q=...`, `GET /notas/historial/{id}` | Buscar alumno e historial. |
| `GET /notas/nuevo?id_alumno={id}`, `POST /notas/guardar` | Registrar nota. |
| `GET /notas/{id}/editar`, `POST /notas/{id}/actualizar` | Editar nota. |
| `GET /notas/{id}/desactivar` | Desactivar nota. |
| `POST /notas/{id}/eliminar` | Eliminar una nota permanentemente. Solo admin. |

Carreras, cursos, semestres y secciones usan el mismo patrón: `GET /{catalogo}`, `GET /{catalogo}/nuevo`, `POST /{catalogo}/guardar`, `GET /{catalogo}/{id}/editar`, `POST /{catalogo}/{id}/actualizar` y `GET /{catalogo}/{id}/activar`. Cada catálogo también tiene `POST /{catalogo}/{id}/eliminar`; el administrador elige bloquear si tiene datos relacionados o borrarlos también.

### Reportes

- `GET /reportes/alumnos`: alumnos filtrables por carrera.
- `GET /reportes/notas`: notas filtrables por carrera, curso, sección y semestre.
- `GET /reportes/alumno/{id}`: reporte individual e historial.
- Agrega `/exportar-csv` o `/exportar-doc` a las rutas de reporte para descargar sus datos.

## 10. Seguridad y limitaciones

- Las consultas del modelo usan parámetros PDO.
- Se aplican validaciones de servidor, límites de notas y validación de imágenes.
- Usa `APP_DEBUG=false` fuera del desarrollo y sirve el sistema por HTTPS.
- Las acciones existentes de activación/desactivación académica todavía usan `GET`; deben migrarse a `POST` con CSRF antes de exponer el sistema a Internet.
- Las eliminaciones permanentes usan `POST` y protección CSRF; las eliminaciones en cascada se ejecutan dentro de transacciones.
- La preferencia de idioma se guarda en el navegador, no en el perfil del usuario.
- No hay pruebas automatizadas configuradas en Composer; valida los cambios en una instancia MySQL de desarrollo antes de actualizar producción.

## 11. Exportaciones y notas

Las notas se registran entre 0 y 100; una nota menor que `NOTA_MINIMA_APROBACION` (60 por defecto) se considera reprobada. En el reporte individual se puede filtrar el historial en pantalla y descargar el historial completo o solo las notas aprobadas o reprobadas en CSV y DOC. Los CSV se generan con UTF-8 y BOM para facilitar su apertura en hojas de cálculo. Los archivos `.doc` contienen HTML compatible con Word; no son archivos `.docx` ni PDF.

La escala permitida es de 0 a `NOTA_MAXIMA` en la aplicación, y la base limita `nota` a 0–100. Si se modifica la escala, conserva ambas reglas compatibles.