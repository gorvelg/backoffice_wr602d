
# Backoffice WR602D — Docker Setup & Configuration

## Prerequisites

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- Access to a terminal with `git` and `bash`

---

## Project Startup

### 1. Start containers

```yml
version: '3.8'
services:
    web:
        image: mmi3docker/symfony-2024
        container_name: wr602
        hostname: symfony-web
        restart: always
        ports:
            - "8319:80"
        depends_on:
            - db
        volumes:
            - ./www/:/var/www/
            - ./sites/:/etc/apache2/sites-enabled/

    db:
        image: mariadb:10.8
        container_name: wr602-db
        hostname: symfony-db
        ports:
            - "3306:3306"  
        restart: always
        volumes:
            - db-volume:/var/lib/mysql
        environment:
            MYSQL_ROOT_PASSWORD: PASSWORD
            MYSQL_DATABASE: WR602
            MYSQL_USER: WR602User
            MYSQL_PASSWORD: PASSWORD

    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        container_name: wr602-adminsql
        hostname: symfony-adminsql
        restart: always
        ports:
            - "8089:80"
        environment:
            PMA_HOST: db
            MYSQL_ROOT_PASSWORD: PASSWORD
            MYSQL_USER: symfony
            MYSQL_PASSWORD: PASSWORD
            MYSQL_DATABASE: symfony

    maildev:
        image: maildev/maildev
        container_name: wr602-mail
        hostname: symfony-mail
        command: bin/maildev --web 1080 --smtp 1025 --hide-extensions STARTTLS
        restart: always
        ports:
            - "1080:1080"
            - "1025:1025"  # Assure-toi que le port SMTP est bien exposé

volumes:
    db-volume:
```

```bash
docker-compose up -d
```

---

### 2. Access the `web` container

```bash
docker exec -ti wr602 bash
cd /var/www/
```

---

### 3. Clone repositories

```bash
git clone git@github.com:gorvelg/backoffice_wr602d.git
git clone git@github.com:gorvelg/micro-service-wr602d.git
```

---

## Backoffice Configuration

### 4. Install PHP dependencies

```bash
cd /var/www/backoffice_wr602d
composer install
```

---

### 5. Configure Apache

```bash
cd /etc/apache2/sites-available
nano 000-default.conf
```

Paste the following configuration:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/backoffice_wr602d/public

    <Directory /var/www/backoffice_wr602d/public>
        AllowOverride All
        Require all granted

        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php/$1 [QSA,L]
        </IfModule>
    </Directory>

    # Microservice alias
    Alias /microservice /var/www/micro-service-wr602d/public

    <Directory /var/www/micro-service-wr602d/public>
        AllowOverride All
        Require all granted

        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php/$1 [QSA,L]
        </IfModule>
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    FallbackResource /index.php
</VirtualHost>
```

Enable the site and reload Apache:

```bash
a2ensite 000-default.conf
service apache2 reload
```

---

### 6. Create `.env.local`

Create a `.env.local` file in `backoffice_wr602d/`:

```bash
nano .env.local
```

Example content, intended for this docker stack:

```dotenv
DATABASE_URL="mysql://WR602User:PASSWORD@symfony-db:3306/WR602?serverVersion=10.8"

MAILER_APP_URL="http://localhost:8319/microservice"
MAILER_API_HEADER="authorization-api-mailer"
MAILER_API_KEY="ggerggregeg"
```

---

### 7. Initialize the database

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

---

## Microservice Configuration

### 1. Access and install dependencies

```bash
cd /var/www/micro-service-wr602d
composer install
```

---

### 2. Create `.env.local` file

```bash
nano .env.local
```

Paste the following content:

```dotenv
MAILER_NO_REPLY_EMAIL="noreply@game-wr602.fr"
MAILER_REPLY_NAME="support@game-wr602.fr"
MAILER_FROM_NAME="Game WR602"
MAILER_API_HEADER="authorization-api-mailer"
MAILER_API_KEY="ggerggregeg"
```

---


