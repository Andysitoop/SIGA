# Publicación de SIGA en distintas plataformas

Esta guía describe cómo preparar y publicar SIGA en un servidor propio, hosting compartido o entorno Windows. Para el funcionamiento correcto, cualquier plataforma debe cumplir los requisitos de [documentacion.md](documentacion.md): PHP 8.1 o posterior con `pdo_mysql`, `mbstring` y `fileinfo`, y MySQL 8.0.16 o posterior compatible con InnoDB y las restricciones del esquema.

## Antes de publicar

1. Haz una copia de seguridad de la base de datos y de `public/uploads/` si ya existe información.
2. Prepara una copia limpia del código y conserva `composer.lock`.
3. Instala las dependencias desde la raíz:

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

   Si el servidor no tiene Composer, ejecuta el comando en un entorno local compatible y transfiere `vendor/` junto con la aplicación. No copies las dependencias desde una instalación de otro proyecto o de una versión distinta del código.
4. Crea `.env` a partir de `.env.example` y configúralo para el servidor. Nunca publiques ni compartas sus credenciales. Como mínimo, establece:

   ```dotenv
   DB_HOST=host_mysql_del_proveedor
   DB_PORT=3306
   DB_NAME=nombre_de_base
   DB_USER=usuario_de_base
   DB_PASS=contraseña_real

   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://dominio.example
   NOTA_MINIMA_APROBACION=61
   NOTA_MAXIMA=100
   ```

   Usa los nombres y valores asignados por el proveedor. `DB_HOST` no siempre es `localhost`.
5. Verifica que PHP pueda escribir en `public/uploads/` y `tmp/`, sin asignar permisos globales como `777`.
6. El punto de entrada es `public/index.php`. El servidor debe enviar las rutas de la aplicación a ese archivo y no exponer la raíz completa del código.

## Ubuntu o Debian con Apache

### Instalar el software

```bash
sudo apt update
sudo apt install apache2 mysql-server composer php libapache2-mod-php \
  php-mysql php-mbstring php-xml php-curl php-zip
sudo a2enmod rewrite
sudo systemctl enable --now apache2 mysql
```

Comprueba la versión PHP y las extensiones requeridas:

```bash
php -v
php -m | grep -E 'fileinfo|mbstring|pdo_mysql'
mysql --version
```

### Preparar e importar la aplicación

1. Copia el proyecto fuera de la carpeta pública, por ejemplo a `/var/www/siga`.
2. Ejecuta `composer install --no-dev --optimize-autoloader` desde `/var/www/siga`.
3. Crea `/var/www/siga/.env` con las credenciales de producción.
4. Para una base nueva, importa `sql/sistema_academico_final.sql` usando una cuenta MySQL administrativa:

   ```bash
   sudo mysql < sql/sistema_academico_final.sql
   ```

   **Este script elimina y crea de nuevo `sistema_academico`. No lo ejecutes en una base con datos que quieras conservar.** Para una base existente, respalda primero y aplica únicamente las migraciones pertinentes. En un servidor administrado, crea la base y el usuario con las herramientas del proveedor.
5. Crea el administrador desde CLI:

   ```bash
   cd /var/www/siga
   sudo -u www-data php create-admin.php
   ```

   Si el proceso no puede leer `.env` o conectar a MySQL, revisa las credenciales y permisos antes de continuar.

### Configurar el sitio Apache

Configura el `DocumentRoot` de un VirtualHost para que apunte a `/var/www/siga/public`, habilita `AllowOverride All` para ese directorio y configura el certificado HTTPS. Por ejemplo:

```apache
<VirtualHost *:80>
    ServerName siga.example
    DocumentRoot /var/www/siga/public

    <Directory /var/www/siga/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Usa el VirtualHost TLS/certificado de tu entorno o una herramienta como Certbot; no publiques credenciales de base en la configuración del sitio. El archivo `public/.htaccess` enruta las solicitudes a `index.php`.

Da al usuario del servicio web acceso de escritura solo a las carpetas necesarias. Ajusta los propietarios a la política del servidor; evita hacer escribible todo el repositorio.

## Ubuntu o Debian con Nginx y PHP-FPM

Nginx no procesa `.htaccess`; la regla de rutas se configura en el servidor. Instala Nginx, PHP-FPM y las mismas extensiones PHP y MySQL indicadas arriba. Configura la raíz del sitio como `.../SIGA/public` y envía las rutas no existentes a `index.php`. Un bloque mínimo de referencia es:

```bash
sudo apt update
sudo apt install nginx mysql-server composer php-fpm php-mysql php-mbstring \
  php-xml php-curl php-zip
sudo systemctl enable --now nginx mysql
```

Comprueba que PHP-FPM esté activo y ajusta su versión/socket según la versión de PHP instalada:

```bash
systemctl status php*-fpm
php -m | grep -E 'fileinfo|mbstring|pdo_mysql'
```

Configura luego el servidor; este ejemplo usa un socket ilustrativo:

```nginx
server {
    listen 80;
    server_name siga.example;
    root /var/www/siga/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php-fpm.sock;
    }

    location ~ /\. {
        deny all;
    }
}
```

El socket de PHP-FPM varía según la distribución y versión (por ejemplo, puede incluir `php8.2-fpm.sock`); sustituye `fastcgi_pass` por el valor real del servidor. Configura HTTPS antes de aceptar tráfico público. Completa después la instalación de Composer, `.env`, la base, el administrador y permisos descritos en la sección anterior.

## cPanel y otros hostings compartidos

Los paneles cambian entre proveedores. Confirma primero las versiones reales de PHP y MySQL, las extensiones, el método de reescritura de URLs, el soporte de archivos ocultos y si se permite acceso SSH/terminal.

1. En el panel, selecciona PHP 8.1 o posterior y habilita `pdo_mysql`, `mbstring` y `fileinfo`.
2. Crea una base MySQL y un usuario desde el panel; asigna al usuario los privilegios necesarios para las operaciones de la aplicación.
3. Importa el esquema compatible mediante phpMyAdmin. Revisa las sentencias SQL antes de importar: el script de instalación final contiene `DROP DATABASE` y operaciones de creación de usuarios que suelen estar prohibidas o ser destructivas en un hosting compartido. No ejecutes esas instrucciones contra datos que quieras conservar.
4. En Composer local, prepara `vendor/` con `composer install --no-dev --optimize-autoloader` si el hosting no ofrece Composer.
5. Configura el dominio con HTTPS y el directorio público como raíz del sitio. Si el panel exige que la aplicación esté bajo `public_html`, evita dejar allí el código privado sin protección. Es preferible alojar `app/`, `config/`, `vendor/`, `sql/` y `.env` fuera de la raíz pública y exponer únicamente `public/`.
6. Si el proveedor requiere subir la aplicación completa dentro de `public_html`, usa solo la distribución preparada en `infinityfree_upload/`, mantén su `.htaccess` y confirma desde fuera que las rutas bloqueadas no se pueden descargar. No subas el `.env.example` como archivo de configuración.
7. Si tienes SSH, crea el primer administrador con `php create-admin.php` desde la raíz privada del proyecto. Si no tienes CLI, no intentes abrir el script por HTTP: es exclusivo de consola. Usa una función CLI del panel o un procedimiento administrativo seguro que almacene una contraseña generada con `password_hash()` y elimine de inmediato cualquier herramienta temporal.
8. Prueba páginas, formularios, sesiones, fotografías, filtros y exportaciones. Algunos proveedores limitan carga de archivos, memoria, tiempo de ejecución o acceso de salida; consulta sus límites.

Si el proveedor no cumple los requisitos del esquema o no permite reescritura, PDO MySQL, escritura de `uploads/` o configurar secretos fuera del acceso público, no publiques allí una instalación de producción.

## InfinityFree

El directorio `infinityfree_upload/` está preparado para la estructura de hosting compartido del proyecto. No reemplaza el código local: úsalo como paquete de publicación.

1. En el panel del hosting, crea la base y copia el host MySQL, nombre, usuario y contraseña exactamente como se muestran.
2. Comprueba la versión de PHP seleccionada y que estén disponibles `pdo_mysql`, `mbstring` y `fileinfo`. Comprueba también que MySQL acepte las funciones e índices usados por el esquema.
3. Importa la estructura en la base creada desde el panel/phpMyAdmin. Usa el SQL disponible en la copia de desarrollo; el paquete de subida puede no incluir el directorio `sql/`. Elimina o ajusta únicamente las instrucciones administrativas incompatibles con el proveedor y nunca ejecutes `DROP DATABASE` sobre datos que quieras conservar.
4. Sube **el contenido** de `infinityfree_upload/` a la carpeta de sitio indicada por InfinityFree. Mantén la estructura y ambos archivos `.htaccess`:
   - El `.htaccess` de la raíz bloquea el acceso web a carpetas y archivos privados y dirige las solicitudes a `public/`.
   - `public/.htaccess` envía las rutas de la aplicación al controlador frontal.
5. Crea `.env` en la raíz de la publicación, junto a `.htaccess`, con las credenciales MySQL del panel, `APP_ENV=production`, `APP_DEBUG=false` y `APP_URL` con el dominio HTTPS final. No uses valores de ejemplo ni guardes credenciales en el repositorio. Si una credencial real llegó a incluirse en un archivo compartido, cámbiala en el panel y actualiza `.env`.
6. Comprueba que `vendor/` esté presente y que `public/uploads/` exista. Usa el administrador de archivos del proveedor para asignar permisos de escritura siguiendo su política; no establezcas `777`.
7. Si InfinityFree no ofrece terminal, no se puede ejecutar `create-admin.php` por navegador. Crea la cuenta por CLI en un entorno con acceso seguro a esa misma base, o usa un procedimiento administrativo seguro para insertar un hash `password_hash()` mediante phpMyAdmin. No dejes una página PHP temporal que cree cuentas, acepte contraseñas o exponga datos.
8. Visita el dominio HTTPS y comprueba inicio de sesión, permisos, búsqueda, registro de notas, imágenes, reportes, exportaciones y modo claro/oscuro. Si una función no opera por límites del hosting, revisa los registros y las restricciones del proveedor antes de repetir operaciones.

## Windows (XAMPP o IIS)

Se recomienda usar Windows/XAMPP para desarrollo o pruebas. Para producción pública, se prefiere un servidor mantenido con HTTPS y una raíz web privada/configurada correctamente.

### XAMPP para pruebas locales

1. Instala una versión de XAMPP que incluya PHP 8.1 o posterior y una versión compatible de MySQL/MariaDB. XAMPP no implica automáticamente compatibilidad: confirma la versión del servidor y las extensiones `pdo_mysql`, `mbstring` y `fileinfo`.
2. Mantén el repositorio fuera de `htdocs` y prepara las dependencias con Composer desde la raíz del proyecto.
3. Copia `.env.example` a `.env` y configura la conexión MySQL local.
4. Importa el SQL solo en una base de pruebas vacía. El script final borra la base `sistema_academico` antes de recrearla.
5. Para una prueba local sencilla, inicia desde la raíz:

   ```powershell
   php -S localhost:8000 -t public
   ```

   Para crear el administrador, ejecuta `php create-admin.php` desde otra terminal en la raíz. El servidor PHP integrado es para desarrollo, no para producción.

### IIS

1. Instala PHP 8.1 o posterior configurado con FastCGI y las extensiones requeridas; configura MySQL aparte.
2. Mantén el código de la aplicación en una carpeta privada y asigna el sitio IIS a `SIGA\public`, no a la raíz del repositorio.
3. Configura HTTPS y la reescritura de rutas con IIS URL Rewrite para que las solicitudes que no correspondan a archivos existentes lleguen a `index.php`. El `.htaccess` de Apache no se aplica a IIS.
4. Configura las variables de entorno/`.env`, importa la base sin ejecutar instrucciones destructivas sobre datos existentes, crea el administrador por CLI y concede permisos NTFS de escritura al usuario del grupo de aplicaciones únicamente para `public/uploads/` y `tmp/`.
5. Comprueba desde el exterior que `.env`, `config/`, `vendor/`, `sql/` y `app/` no sean descargables.

## Actualizar una publicación existente

1. Habilita temporalmente una ventana de mantenimiento si la actualización afecta datos o esquema.
2. Respalda la base y `public/uploads/`.
3. Revisa el diff y los scripts SQL. No sobrescribas `.env`, fotografías, ni configuraciones específicas del servidor.
4. Publica el código y las dependencias compatibles, manteniendo privadas las carpetas internas.
5. Ejecuta solo las migraciones necesarias; no reimportes el esquema final en una base existente.
6. Limpia las plantillas compiladas dentro de `tmp/` solo si es necesario y permite que Fat-Free las regenere.
7. Verifica acceso con ambos roles, búsqueda y filtros, notas 0/61/100, reportes, CSV/DOC y carga de fotografías.
8. Si falla la actualización, restaura conjuntamente el código, la base y los archivos subidos desde el respaldo compatible.

## Lista de verificación antes de abrir al público

- [ ] PHP y MySQL cumplen las versiones mínimas y las extensiones requeridas están habilitadas.
- [ ] `.env` contiene las credenciales del servidor, `APP_DEBUG=false` y la URL HTTPS correcta; no está disponible por HTTP.
- [ ] El servidor expone solo `public/` y enruta las URLs a `index.php`.
- [ ] El certificado HTTPS funciona y las sesiones/autenticación operan sobre HTTPS.
- [ ] La base y las fotografías cuentan con respaldos; no se ejecutó el SQL destructivo en una base existente.
- [ ] El usuario MySQL tiene los permisos requeridos, sin privilegios administrativos innecesarios.
- [ ] PHP puede escribir en `public/uploads/` y `tmp/`, pero no en todo el código.
- [ ] Existe un administrador con contraseña robusta y no hay herramientas temporales de instalación expuestas.
- [ ] Se probaron los flujos principales y las rutas privadas no exponen archivos de configuración.

Para el funcionamiento del sistema, las rutas disponibles y los permisos de cada rol, consulta [documentacion.md](documentacion.md). Para una instalación local en Ubuntu, consulta [INSTRUCCIONES_UBUNTU.md](INSTRUCCIONES_UBUNTU.md).
