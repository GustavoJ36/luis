# Documentación del Proyecto

Este archivo contiene instrucciones básicas para configurar y ejecutar el proyecto localmente.

## Instalación con Docker

Sigue estos pasos para poner en marcha el proyecto utilizando Docker:

1. **Construir y levantar los contenedores:**
   Desde la raíz del proyecto, ejecuta el siguiente comando para construir las imágenes y levantar el entorno:
   ```bash
   docker compose up -d --build
   ```

2. **Acceder al contenedor de la aplicación:**
   Para ejecutar comandos como Artisan o Composer, entra al shell del contenedor:
   ```bash
   docker exec -it laravelreact bash
   ```

3. **Instalar dependencias de PHP:**
   Dentro del contenedor, ejecuta:
   ```bash
   composer install
   ```

## Configuración del Entorno (.env)

Sigue estos pasos para configurar el entorno:

1. **Crear archivo .env:**
   Copia el contenido del archivo de ejemplo `.env.example`:

   ```bash
   cp .env.example .env
   ```

2. **Cambiar el propietario de la carpeta:**
   Luego de agregar el `.env`, se debe cambiar el owner de la carpeta del proyecto:
   ```bash
   sudo chown -R usuario_pc:www-data carpeta_del_proyecto
   ```

3. **Configurar credenciales:**
   Una vez creado el archivo `.env`, ábrelo y configura las credenciales de tu base de datos:

```ini
DB_CONNECTION=mysql
DB_HOST=172.17.0.1
DB_PORT=3306
DB_DATABASE=laravel_react
DB_USERNAME=root
DB_PASSWORD=1234
```

La IP `172.17.0.1` es la dirección IP del host (tu máquina) desde la perspectiva del contenedor Docker. El contenedor de la base de datos está en la misma red que el contenedor de la aplicación, por lo que puede acceder a la base de datos usando `DB_HOST=172.17.0.1`.

Finalmente, genera la clave de la aplicación con el siguiente comando:

```bash
php artisan key:generate
```

**Importante:** Luego de configurar o modificar el archivo `.env`, se recomienda limpiar la caché de la configuración para asegurar que los cambios surtan efecto:

```bash
php artisan config:clear
```

## Migraciones y Seeders

Para crear las tablas en la base de datos y poblar las tablas con datos de prueba (si aplica), ejecuta:

```bash
# Ejecutar solo migraciones
php artisan migrate

# Ejecutar migraciones y seeders
php artisan migrate --seed
```

Si necesitas refrescar la base de datos completa (borrar todo y volver a crear):

```bash
php artisan migrate:fresh --seed
```



