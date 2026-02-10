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

Para configurar las variables de entorno, primero debes crear el archivo `.env` copiando el contenido del archivo de ejemplo `.env.example`.

Puedes hacerlo manualmente o ejecutar el siguiente comando en la terminal:

```bash
cp .env.example .env
```

Una vez creado el archivo `.env`, aabrelo y configura las credenciales de tu base de datos en las siguientes líneas:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

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



