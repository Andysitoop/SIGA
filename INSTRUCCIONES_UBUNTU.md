# Instrucciones de Instalación en Ubuntu

## Ubicación del Proyecto

Si estás usando WSL, el proyecto está en:
```
/mnt/c/Users/terra/CascadeProjects/sistema-academico
```

## 1. Instalar PHP y Extensiones

```bash
sudo apt update
sudo apt install php8.1 php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-finfo
```

## 2. Instalar MySQL Server

```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

Durante la instalación:
- Configurar contraseña de root
- Seleccionar las opciones recomendadas

## 3. Instalar Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 4. Navegar al Proyecto

```bash
cd /mnt/c/Users/terra/CascadeProjects/sistema-academico
```

## 5. Instalar Dependencias

```bash
composer install
```

## 6. Configurar Archivo .env

```bash
cp .env.example .env
nano .env
```

Editar con tus credenciales:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=sistema_academico
DB_USER=root
DB_PASS=tu_contraseña_mysql

APP_NAME=Sistema Académico
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

NOTA_MINIMA_APROBACION=60
NOTA_MAXIMA=100

MAX_FILE_SIZE=2097152
ALLOWED_IMAGE_TYPES=image/jpeg,image/jpg,image/png

PAGINATION_LIMIT=20
```

## 7. Crear Base de Datos

```bash
sudo mysql -u root -p < sql/sistema_academico.sql
```

O desde el cliente MySQL:
```bash
sudo mysql -u root -p
```
```sql
source /mnt/c/Users/terra/CascadeProjects/sistema-academico/sql/sistema_academico.sql
exit
```

## 8. Configurar Permisos

```bash
chmod -R 755 public/uploads
chmod -R 755 tmp
```

## 9. Ejecutar el Servidor

```bash
cd public
php -S localhost:8000
```

## 10. Acceder al Sistema

Abrir el navegador en:
```
http://localhost:8000
```

## Solución de Problemas Comunes

### Error: "php: command not found"
```bash
sudo apt install php8.1-cli
```

### Error: "mysql: command not found"
```bash
sudo apt install mysql-client
```

### Error de conexión a MySQL
Verificar que MySQL esté corriendo:
```bash
sudo systemctl status mysql
```

Iniciar MySQL si no está corriendo:
```bash
sudo systemctl start mysql
```

### Error de permisos en uploads
```bash
sudo chown -R $USER:$USER public/uploads
chmod -R 755 public/uploads
```

### Error de extensión fileinfo
```bash
sudo apt install php8.1-finfo
```

## Usar Apache (Opcional)

Si prefieres usar Apache en lugar del servidor PHP integrado:

```bash
sudo apt install apache2 libapache2-mod-php8.1
```

Configurar VirtualHost:
```bash
sudo nano /etc/apache2/sites-available/sistema-academico.conf
```

Agregar:
```apache
<VirtualHost *:80>
    ServerName sistema-academico.local
    DocumentRoot /mnt/c/Users/terra/CascadeProjects/sistema-academico/public
    
    <Directory /mnt/c/Users/terra/CascadeProjects/sistema-academico/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sistema-academico_error.log
    CustomLog ${APACHE_LOG_DIR}/sistema-academico_access.log combined
</VirtualHost>
```

Habilitar el sitio:
```bash
sudo a2ensite sistema-academico.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Agregar al archivo hosts:
```bash
sudo nano /etc/hosts
```
Agregar:
```
127.0.0.1 sistema-academico.local
```

Acceder desde:
```
http://sistema-academico.local
```
