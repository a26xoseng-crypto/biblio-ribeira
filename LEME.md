# Material da práctica: Docker sen Compose

| Ficheiro ou cartafol | Para que serve |
| --- | --- |
| `bd.env.exemplo` | Modelo das variables de contorna da base de datos. Cópiao como `bd.env` e cambia os contrasinais. |
| `web.env.exemplo` | Modelo das variables de contorna da web PHP. Cópiao como `web.env`: os datos deben coincidir cos de `bd.env`. |
| `initdb/01-catalogo.sql` | Script que crea a táboa `libros` e carga o catálogo inicial. |
| `Dockerfile` | Receita da imaxe PHP con Apache e a extensión `pdo_mysql`. |
| `src/index.php` | Páxina PHP que consulta o catálogo na base de datos. |
| `copias/` | Cartafol baleiro onde gardarás a copia de seguridade. |

Executa todas as ordes **dende este cartafol** (o que contén este ficheiro).

Non subas nunca `bd.env` nin `web.env` a un repositorio: conteñen contrasinais.
