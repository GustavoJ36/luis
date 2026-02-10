# Documentación del Proyecto

Este archivo contiene instrucciones básicas para configurar y ejecutar el proyecto localmente.

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

## Migraciones 

Para crear las tablas en la base de datos y poblar las tablas con datos de prueba (si aplica), ejecuta:

```bash
# Ejecutar solo migraciones
php artisan migrate

```

## Running Tests

Para ejecutar las pruebas automatizadas del proyecto, utiliza el siguiente comando:

```bash
php artisan test
```

También puedes usar el script de composer configurado en el proyecto:

```bash
composer test
```
