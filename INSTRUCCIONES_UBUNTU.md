# Instalación y ejecución en Ubuntu

Estos pasos son para una instalación local de desarrollo. Para despliegues públicos, usa HTTPS, desactiva la depuración y configura el servidor web para servir únicamente `public/`.

## 1. Instalar requisitos

```bash
sudo apt update
sudo apt install php-cli php-mysql php-mbstring php-xml php-curl php-zip mysql-server composer
```

Comprueba PHP y las extensiones requeridas:

```bash
php -v
php -m | grep -E 'fileinfo|mbstring|pdo_mysql'
```

`fileinfo` suele venir habilitada con PHP en Ubuntu. Si falta, instala el paquete PHP apropiado para tu versión de Ubuntu.

## 2. Instalar dependencias y configurar el entorno

Desde el directorio del proyecto:

```bash
cd ~/Desktop/SIGA
composer install
cp .env.example .env
nano .env
```

Configura `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASS`. La contraseña debe coincidir con la definida para `sistema_app` en los scripts SQL. No uses credenciales de ejemplo fuera de una instalación local.

## 3. Crear o actualizar la base de datos

### Instalación nueva

El script final crea la base, carga datos iniciales y crea/configura el usuario de aplicación. **Borra y vuelve a crear la base de datos; no lo ejecutes si quieres conservar datos.**

```bash
sudo mysql < sql/sistema_academico_final.sql
```

En Ubuntu, MySQL suele autenticar `root` mediante `sudo` y socket local. Si tu servidor está configurado para autenticar con contraseña, usa `mysql -u root -p < sql/sistema_academico_final.sql`.

### Base existente que se debe conservar

Aplica la migración de usuarios y permisos:

```bash
sudo mysql sistema_academico < sql/migrate_users.sql
```

Si no necesitas migrar usuarios y solo quieres otorgar los permisos para eliminaciones permanentes:

```bash
sudo mysql < sql/habilitar_eliminacion.sql
```

Ambos comandos requieren una cuenta MySQL administrativa. Si `sudo mysql` no funciona, solicita a la persona administradora del servidor que ejecute el script.

## 4. Crear el primer administrador

Ejecuta desde la raíz del proyecto:

```bash
php create-admin.php
```

El asistente solicita nombre, correo y contraseña de al menos 12 caracteres. La contraseña se almacena con un hash; no hay una contraseña predeterminada. Después, inicia sesión con la cuenta creada.

## 5. Permitir escritura de fotografías y caché

PHP necesita poder escribir en `public/uploads/` y `tmp/`. Crea las carpetas si faltan:

```bash
mkdir -p public/uploads tmp
```

Para el servidor de desarrollo, concede acceso de escritura al usuario que ejecutará PHP. Para Apache, ajusta el propietario al usuario del servicio (usualmente `www-data`) en esas dos carpetas. Evita permisos globales como `777`; `public/uploads/` contiene fotografías de alumnos, no caché descartable.

## 6. Iniciar la aplicación

```bash
cd public
php -S localhost:8000
```

Abre <http://localhost:8000>. Detén el servidor con `Ctrl+C`.

Para Apache o Nginx, configura `public/` como document root. No publiques la raíz del proyecto: contiene configuración y scripts que no son recursos web.

## Roles

- El administrador puede administrar alumnos, notas, catálogos y cuentas.
- Un usuario normal solo puede consultar las estadísticas del dashboard y ver o descargar reportes.
- Las eliminaciones permanentes necesitan los permisos MySQL `DELETE` declarados en `sql/habilitar_eliminacion.sql`. En cada borrado relacionado, el administrador puede optar por bloquear la operación o eliminar también los registros dependientes.

## Solución de problemas

### `ERROR 1698 (28000): Access denied for user 'root'@'localhost'`

En Ubuntu, intenta ejecutar el script con autenticación por socket:

```bash
sudo mysql < sql/habilitar_eliminacion.sql
```

Ingresa la contraseña de tu usuario Ubuntu si `sudo` la solicita. Si tu cuenta no tiene permisos de `sudo`, pide a un administrador de MySQL que ejecute el comando.

### No se puede conectar a MySQL

```bash
sudo systemctl status mysql
sudo systemctl start mysql
```

Comprueba también que las credenciales `DB_*` de `.env` coincidan con el usuario y la base creados en MySQL.

### `php` o `mysql` no se encuentra

Instala el paquete correspondiente:

```bash
sudo apt install php-cli mysql-client
```

### No se pueden guardar fotografías o plantillas compiladas

Confirma que el usuario que ejecuta PHP tenga permiso de escritura en `public/uploads/` y `tmp/`. No es necesario conservar las plantillas compiladas: Fat-Free las genera de nuevo.

Para información sobre rutas, esquema SQL, roles y variables de configuración, consulta [documentacion.md](documentacion.md).
