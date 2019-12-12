## Anime Store API

API para poder comprar paquetes de series de anime 

##Instrucciones

- Corre `composer install` para instalar las dependencias
- Agregar un .env con la informacion necesaria como la siguiente, en caso de sea por `php artisan serve` usar el localhost:8000
en caso de ser homestead puedes usar el host `animeStore.test`

```
APP_NAME=AnimeStore
APP_ENV=local
APP_KEY=base64:SK7P3df3qyRm6umtdzLY1SE4cVtcKlcfKpun0CloSv4=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=anime_store
DB_USERNAME=postgres
DB_PASSWORD=
```

- Genera la llave del jwt corre el comando `php artisan jwt:secret`
- Una vez tengas creada la base de datos, corre las migraciones con `php artisan migrate:refresh --seed` para igual
correr los seeders.

## Postman

Puedes importar el archivo `AnimeStore.postman_collection.json` y utilizar los endpoints ahi definidos para crear usuarios, logearte
y automaticamente guardar el token de autenticacion.
