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
| `public/css/`, `public/js/` | Estilos, idioma, tema y comportamiento de interfaz. |
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
- **Preferencias:** idioma español/inglés y tema claro/oscuro guardados en el navegador.

La búsqueda de alumnos acepta nombre o apellido y filtros combinables por carrera, sección y curso. Los reportes consultan las tablas MySQL mediante `DemoReportData`, una fachada de consultas y transformación (no un conjunto de datos ficticios).

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
| `NOTA_MINIMA_APROBACION` | Calificación mínima para aprobar; por defecto `61`. |
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
| `GET /ajustes` | Preferencias de idioma y apariencia; administración de cuentas para admin. |
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
- `GET /alumnos/buscar?q=...&id_carrera=...&id_seccion=...&id_curso=...`: búsqueda por nombre o apellido, combinable con carrera, sección y curso.
- `GET /reportes/notas`: notas filtrables por carrera, curso, sección, semestre y estado (aprobados, reprobados o ambos).
- `GET /reportes/alumno/{id}?estado=todos|aprobados|reprobados`: reporte individual; filtra el historial y conserva estadísticas generales.
- Agrega `/exportar-csv` o `/exportar-doc` a las rutas de reporte para descargar sus datos.

## 10. Seguridad y limitaciones

- Las consultas del modelo usan parámetros PDO.
- Se aplican validaciones de servidor, límites de notas y validación de imágenes.
- Usa `APP_DEBUG=false` fuera del desarrollo y sirve el sistema por HTTPS.
- Las acciones existentes de activación/desactivación académica todavía usan `GET`; deben migrarse a `POST` con CSRF antes de exponer el sistema a Internet.
- Las eliminaciones permanentes usan `POST` y protección CSRF; las eliminaciones en cascada se ejecutan dentro de transacciones.
- Las preferencias de idioma y tema se guardan en el navegador, no en el perfil del usuario.
- No hay pruebas automatizadas configuradas en Composer; valida los cambios en una instancia MySQL de desarrollo antes de actualizar producción.

## 11. Exportaciones y notas

Los reportes de notas marcan aprobación desde `NOTA_MINIMA_APROBACION` (61 por defecto) y permiten filtrar aprobados, reprobados o ambos; las exportaciones CSV y DOC conservan el filtro aplicado. En el reporte individual, el filtro se aplica al historial y las estadísticas siguen considerando todos los cursos del alumno. La escala de notas es de 0 a 100. Los CSV se generan con UTF-8 y BOM para facilitar su apertura en hojas de cálculo. Los archivos `.doc` contienen HTML compatible con Word; no son archivos `.docx` ni PDF.

La escala permitida es de 0 a `NOTA_MAXIMA` en la aplicación, y la base limita `nota` a 0–100. Si se modifica la escala, conserva ambas reglas compatibles.

## 12. Manual técnico de publicación

Las instrucciones específicas para Ubuntu/Debian, Apache, Nginx, cPanel, InfinityFree, Windows/XAMPP e IIS están en [PUBLICACION_PLATAFORMAS.md](PUBLICACION_PLATAFORMAS.md).

### 12.1. Software, prerrequisitos e instalación

Antes de instalar o publicar SIGA, confirma que el servidor cumpla estos requisitos:

- PHP 8.1 o posterior con `pdo_mysql`, `mbstring` y `fileinfo`.
- MySQL 8.0.16 o posterior y una base de datos con soporte InnoDB.
- Composer para preparar las dependencias. No se necesita Composer en el servidor si se publica el directorio `vendor/` ya instalado.
- Apache con `mod_rewrite` y permiso para leer archivos `.htaccess`, o un servidor equivalente con reescritura de rutas configurada.
- HTTPS para cualquier instalación accesible desde Internet.

Comprueba los requisitos desde una terminal:

```bash
php -v
php -m | grep -E 'fileinfo|mbstring|pdo_mysql'
mysql --version
composer --version
```

En una instalación nueva de Ubuntu puedes instalar las herramientas con:

```bash
sudo apt update
sudo apt install php-cli php-mysql php-mbstring php-xml php-curl php-zip mysql-server composer
```

Clona o copia el proyecto al servidor y prepara las dependencias desde su raíz:

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
```

Edita `.env` en el servidor. Configura como mínimo `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_ENV`, `APP_DEBUG` y `APP_URL`. Usa los datos MySQL que haya creado el administrador del servidor o el panel del proveedor; no reutilices contraseñas de ejemplo. Para producción define `APP_ENV=production`, `APP_DEBUG=false` y la URL HTTPS pública.

#### Publicación en un servidor propio

1. Configura el dominio y el certificado HTTPS en Apache o Nginx.
2. Define `public/` como raíz web (`DocumentRoot`). No expongas la raíz completa del repositorio: allí están `app/`, `config/`, `.env`, `sql/` y otros archivos que no deben servirse directamente.
3. Habilita la reescritura de rutas. En Apache, el archivo `public/.htaccess` dirige las solicitudes que no corresponden a un archivo existente a `public/index.php`. En Nginx, configura una regla equivalente para que las rutas de la aplicación usen ese punto de entrada.
4. Crea la base y el usuario MySQL de la aplicación con permisos mínimos. Para una instalación nueva en un MySQL administrado por el propio servidor, el SQL de `sql/sistema_academico_final.sql` crea y carga la base; **elimina y vuelve a crear `sistema_academico` y no debe ejecutarse sobre una base con datos que se quieran conservar**. En una base existente aplica la migración documentada en la sección 6.
5. Ajusta `.env` con las credenciales de la base y la URL del sitio. No guardes este archivo en Git ni lo incluyas en copias públicas.
6. Asegura que PHP pueda escribir únicamente en `public/uploads/` y `tmp/`. Concede permisos al usuario del servicio web; no uses permisos globales como `777`.
7. Crea el primer administrador con `php create-admin.php` desde la raíz del proyecto y una terminal segura. El comando requiere que PHP pueda conectarse a la base mediante `.env`.
8. Abre el sitio por HTTPS, inicia sesión y verifica las operaciones de lectura, escritura, carga de fotografías, filtros y exportaciones.

#### Publicación en InfinityFree u otro alojamiento compartido

La carpeta `infinityfree_upload/` contiene una versión preparada para alojamientos que esperan los archivos del sitio en una carpeta web como `htdocs/`. Conserva su estructura al transferirla:

1. En el panel del proveedor, crea una base MySQL y un usuario. Copia el host, nombre de base, usuario y contraseña que el proveedor asignó; no supongas que el host es `localhost`.
2. Revisa que el servidor PHP/MySQL del proveedor cumpla las versiones requeridas. La base y el motor también deben aceptar las restricciones e índices del esquema SIGA. Si el proveedor no permite alguna sentencia administrativa del SQL, crea la base y el usuario desde el panel y adapta una copia de `sql/sistema_academico_final.sql` para importar solo tablas y datos en esa base. Nunca importes la instrucción `DROP DATABASE` sobre una base con datos que deban conservarse, ni ejecutes `CREATE USER`/`GRANT` si el proveedor administra esas operaciones.
3. Importa el esquema en la base seleccionada usando la herramienta del proveedor, por ejemplo phpMyAdmin. El directorio de carga no contiene necesariamente los scripts SQL; consérvalos en la copia de desarrollo y no publiques el directorio `sql/`.
4. Sube **el contenido** de `infinityfree_upload/` a la carpeta web que indique el proveedor. Mantén el `.htaccess` de la raíz y el subdirectorio `public/`: el primero oculta archivos y carpetas internos y reenvía las solicitudes a `public/`; el segundo envía las rutas de la aplicación a `index.php`. No subas el proyecto local completo ni reemplaces estos archivos de reglas.
5. Crea un archivo `.env` en la raíz de la publicación con las credenciales reales del proveedor, `APP_ENV=production`, `APP_DEBUG=false` y `APP_URL` con el dominio HTTPS definitivo. No subas `.env.example` como configuración de producción. No compartas el contenido de `.env`.
6. Confirma que `vendor/` esté presente en la publicación. La copia de InfinityFree preparada por el proyecto lo incluye; si preparas otra copia, ejecuta Composer localmente y transfiere las dependencias generadas.
7. Asegura que `public/uploads/` exista y sea escribible desde PHP según las herramientas del alojamiento. Si el proveedor no permite ejecutar PHP desde una terminal, no dejes accesible `create-admin.php` por HTTP: el script está diseñado exclusivamente para CLI. Crea el administrador con un acceso CLI autorizado o mediante una operación segura en la base que almacene un hash generado con `password_hash()`.
8. Visita el dominio HTTPS y prueba acceso, búsqueda de alumnos, registro de notas de 0 a 100, clasificación aprobada/reprobada, reportes, filtros y exportaciones. Comprueba también carga de fotografías y selección de tema.

### 12.2. Pasos para utilizar Fat-Free Framework

SIGA usa Fat-Free Framework (F3) como enrutador y renderizador de plantillas. La integración sigue este flujo:

1. **Instalar el framework y dependencias.** Composer lee `composer.json` y `composer.lock`; `vendor/autoload.php` carga Fat-Free, Dotenv y el autoload PSR-4 de la aplicación (`App\` → `app/`).
2. **Inicializar la aplicación.** `public/index.php` carga el autoloader y la configuración, crea la instancia con `Base::instance()` y asigna `DEBUG`, `UI`, `AUTOLOAD`, `TEMP`, `BASE` y `APP_NAME`.
3. **Registrar rutas.** El mismo punto de entrada enlaza métodos HTTP y patrones de ruta con controladores, por ejemplo `GET /alumnos/buscar` con `AlumnoController->buscar`. F3 despacha la solicitud al método correspondiente.
4. **Atender la solicitud en el controlador.** Las clases de `app/Controllers/` aplican autorización, validan los parámetros y formularios y preparan los datos de pantalla. Las acciones de escritura usan los métodos HTTP definidos por la aplicación y validación CSRF donde corresponde.
5. **Consultar modelos y servicios.** Los controladores llaman a `app/Models/` para operar sobre MySQL. `app/Helpers/Database.php` usa PDO; las consultas con datos variables deben usar parámetros preparados. Helpers como `Access`, `Validation` y `Csrf` concentran responsabilidades comunes.
6. **Renderizar la vista.** El controlador guarda valores en el contenedor F3 mediante `$f3->set(...)` e indica la plantilla. Fat-Free renderiza la plantilla de `app/Views/` —normalmente dentro de `layouts/main.html`— y sustituye expresiones y directivas como `{{ @valor }}`, `<repeat>` y `<check>`.
7. **Completar el ciclo.** El punto de entrada ejecuta `$f3->run()`. F3 encuentra la ruta, llama al controlador y devuelve la respuesta HTML, una descarga o una redirección al navegador.

Para añadir una pantalla, define primero la ruta en `public/index.php`, implementa la acción en el controlador apropiado, reutiliza o crea la operación necesaria en el modelo y prepara una plantilla bajo `app/Views/`. Conserva la autorización en el servidor; ocultar un enlace en la interfaz no protege una ruta.

### 12.3. Recomendaciones para uso del framework y mantenimiento

- Mantén controladores enfocados en el flujo HTTP, modelos en acceso a datos y plantillas en presentación. No ejecutes consultas SQL ni lógica de negocio extensa desde las vistas.
- Usa consultas PDO preparadas; valida en el servidor cada entrada, incluso si también tiene validación HTML/JavaScript. Escapa los valores al mostrarlos en HTML.
- Protege cada ruta según su rol con `Access`; para formularios que modifican datos, utiliza `POST` y tokens CSRF. No conviertas una autorización de servidor en una simple condición de menú.
- Mantén las dependencias reproducibles con `composer.lock`. Actualiza dependencias deliberadamente, prueba el sistema y no edites archivos dentro de `vendor/`.
- Configura `APP_DEBUG=false` en producción, publica únicamente por HTTPS y limita el acceso de escritura a las carpetas que realmente lo necesitan. No publiques `.env`, respaldos SQL ni archivos temporales.
- Antes de cada actualización, respalda la base de datos y las fotografías de `public/uploads/`. Prueba migraciones y cambios en una copia de desarrollo; revisa si un script SQL borra o reemplaza datos antes de ejecutarlo.
- Después de publicar, prueba autenticación y permisos con cuentas de ambos roles, la búsqueda con filtros, las notas límite 0/61/100, los reportes y las descargas CSV/DOC.
- Fat-Free compila plantillas en `tmp/`; el directorio debe ser escribible por PHP, pero su contenido puede regenerarse. No uses esa carpeta para archivos de usuario ni para respaldos.
- La aplicación carga Bootstrap, Bootstrap Icons y fuentes desde CDN. Si el alojamiento bloquea esos servicios, la interfaz puede perder estilos, iconos o tipografía; verifica la política del proveedor y prepara recursos locales si es necesario.
- Las preferencias de idioma y tema se guardan en el almacenamiento local del navegador. No se sincronizan entre dispositivos ni se almacenan en la cuenta del usuario.