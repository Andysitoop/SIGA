SIGA - Paquete para InfinityFree

SUBIDA
1. Sube el CONTENIDO de esta carpeta a htdocs (no la carpeta infinityfree_upload).
2. Confirma que .htaccess se haya subido; algunos clientes FTP ocultan archivos que empiezan por punto.
3. En el Administrador de archivos, copia .env.example como .env y completa DB_PASS
   con la contraseña de MySQL asignada por InfinityFree. No compartas ese archivo.
4. public/uploads/ se crea automáticamente al cargar una foto si no existe.
   Si la carga aún falla, confirma en el Administrador de archivos que esa
   carpeta permita escritura por PHP; no uses permisos 777.

BASE DE DATOS
- Este paquete incluye sql/importar_sistema_academico.sql.
- En phpMyAdmin selecciona if0_43175902_sistema_academico y confirma que no tenga
  tablas ni datos que quieras conservar. El SQL crea las tablas y agrega datos
  de demostración; no lo importes sobre una instalación existente ni por segunda
  vez.
- Importa ese archivo desde la pestaña Importar. No importes directamente
  sql/sistema_academico_final.sql del proyecto: ese archivo borra/crea otra base
  e intenta crear usuarios y permisos MySQL.
- El administrador de la aplicación se crea aparte; este SQL no incluye una
  cuenta ni contraseña de acceso.

SEGURIDAD Y ADMINISTRACION
- No subas ni crees un .env.example con credenciales reales.
- APP_DEBUG debe permanecer en false en el sitio público.
- No uses una contraseña débil o reutilizada para la cuenta administradora.
- El paquete excluye las fotografías locales de public/uploads/ y los temporales.
- Para el usuario MySQL, usa los privilegios que InfinityFree asignó; no ejecutes
  CREATE USER, GRANT ni FLUSH PRIVILEGES en ese hosting.
- Las carpetas app, config, vendor y tmp quedan bloqueadas a solicitudes web por
  .htaccess, pero siguen disponibles internamente para PHP.
- Tras subir, verifica https://sigaapp.freedev.app/login y el acceso a una ruta
  inexistente (debe responder desde la aplicación, no mostrar listado de archivos).
