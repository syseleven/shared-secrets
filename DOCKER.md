# Run dockerized

This Readme is meant to describe, how to easily run the application dockerized.

## Files, you need to prepare

For preparing the application to run within a Docker environment, you could prepare a project directory with three elements:

```
.
├── .env
├── docker-compose.yml
└── shared-secrets
    └── ...
```

`shared-secrets` is the clone of the current Git repository. The content of the other files could look like that:

### `.env`

```sh
# multiline, can hold multiple RSA keys directly concatenated
RSA_PRIVATE_KEYS="-----BEGIN RSA PRIVATE KEY-----
..A
...
...
-----END RSA PRIVATE KEY-----
-----BEGIN RSA PRIVATE KEY-----
..B
...
...
-----END RSA PRIVATE KEY-----"

MYSQL_ROOT_PASS="<SET A PASSWORD FOR MYSQL ROOT!!!>"

MYSQL_PASS="<SET THE MYSQL PASSWORD!!!>"
MYSQL_USER="secrets"
MYSQL_DB="secrets"


SECRET_SHARING_URL="http://localhost:8080/"
IMPRINT_URL="http://localhost:8080/"
IMPRINT_TEXT=""

DEFAULT_TIMEZONE="Europe/Berlin"

DEBUG_MODE="false"
READ_ONLY="false"
SHARE_ONLY="false"
JUMBO_SECRETS="false"

SERVICE_TITLE="Shared-Secrets"

# you won't need change those variables in docker environment
MYSQL_HOST="shared-secrets_db"
MYSQL_PORT="3306"
APACHE_ADDITIONAL="<IfModule mod_rewrite.c>
    <Directory /var/www/html>
        RewriteEngine On
        RewriteBase /

        # prevent access to certain locations
        RewriteRule ^\.git(\/.*)?$    - [R=404,L]
        RewriteRule ^\.gitattributes$ - [R=404,L]
        RewriteRule ^\.gitignore$     - [R=404,L]
        RewriteRule ^\.htaccess$      - [R=404,L]
        RewriteRule ^actions(\/.*)?$  - [R=404,L]
        RewriteRule ^CHANGELOG\.md$   - [R=404,L]
        RewriteRule ^config(\/.*)?$   - [R=404,L]
        RewriteRule ^ENCRYPTION\.md$  - [R=404,L]
        RewriteRule ^lib(\/.*)?$      - [R=404,L]
        RewriteRule ^LICENSE$         - [R=404,L]
        RewriteRule ^pages(\/.*)?$    - [R=404,L]
        RewriteRule ^README\.md$      - [R=404,L]
        RewriteRule ^router\.php$     - [R=404,L]
        RewriteRule ^template(\/.*)?$ - [R=404,L]

        # single entrypoint
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule . /index.php [L]
    </Directory>
</IfModule>"
```

This file is used by `docker-compose` command to provision the containers – the defined variables are used within `docker-compose.yml` and won't be included as a file within the containers.

### `docker-compose.yml`

```yml
---
version: '3'

services:

  database:
    container_name: shared-secrets_db
    image: mariadb
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: "${MYSQL_ROOT_PASS:-secret}"
      MYSQL_USER: "${MYSQL_USER}"
      MYSQL_PASSWORD: "${MYSQL_PASS}"
      MYSQL_DATABASE: "${MYSQL_DB}"
    volumes:
      - ./db:/var/lib/mysql
    logging:
      driver: json-file
      options:
        max-file: '5'
        max-size: 50m
    healthcheck:
     test: ["CMD", "mysqladmin", "ping", "--silent"]

  shared-secrets:
    container_name: shared-secrets
    image:  devopsansiblede/apache:latest
    restart: always
    depends_on:
      database:
        condition: service_healthy
    environment:
      RSA_PRIVATE_KEYS:   "${RSA_PRIVATE_KEYS}"
      MYSQL_ROOT_PASS:    "${MYSQL_ROOT_PASS}"
      MYSQL_USER:         "${MYSQL_USER}"
      MYSQL_PASS:         "${MYSQL_PASS}"
      MYSQL_DB:           "${MYSQL_DB}"
      SECRET_SHARING_URL: "${SECRET_SHARING_URL}"
      IMPRINT_URL:        "${IMPRINT_URL}"
      IMPRINT_TEXT:       "${IMPRINT_TEXT}"
      DEFAULT_TIMEZONE:   "${DEFAULT_TIMEZONE}"
      DEBUG_MODE:         "${DEBUG_MODE}"
      READ_ONLY:          "${READ_ONLY}"
      SHARE_ONLY:         "${SHARE_ONLY}"
      JUMBO_SECRETS:      "${JUMBO_SECRETS}"
      SERVICE_TITLE:      "${SERVICE_TITLE}"
      MYSQL_HOST:         "${MYSQL_HOST}"
      MYSQL_PORT:         "${MYSQL_PORT}"
      APACHE_ADDITIONAL:  "${APACHE_ADDITIONAL}"
    volumes:
      - ./shared-secrets:/var/www/html
    ports:
      - "8090:80"
    logging:
      driver: json-file
      options:
        max-file: '5'
        max-size: 50m

...
```

## Securing your application

Since a Docker host normally does not only publish a single application, you'll have to use a Reverse Proxy. Either you could build up one by NGINX or – my favorite – use [Træfik](https://traefik.io/traefik/) as your solution. Træfik supports Let's Encrypt certificates (HTTP and DNS requested) out of the box.

## Get it up and running

For getting your app copy up and running, simply run:

```sh
docker-compose up -d
```
