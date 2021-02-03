# userapi

## Setup project

### With docker

Build the container images

```bash
docker-compose build
```

Startup the containers

```bash
docker-compose up
```

Install composer dependencies

```bash
docker-compose exec app /composer.phar install
```

Setup postgres database (only required when using the database adapter)

```bash
docker-compose exec app bin/console doctrine:migrations:migrate
```

Insert test data into database (only required when using the database adapter)

```bash
docker-compose exec app bin/console doctrine:fixtures:load
```

After this the application is ready to use. You can run the tests with the following command

```bash
docker-compose exec app bin/phpunit tests
```

#### Xdebug

To use xdebug you have to create a `docker-compose.override.yaml` with the required xdebug configuration

The following configuration is useful for PHPStorm. You have to update the client_host ip to the 
host ip that hosts xdebug. The serverName in PHP_IDE_CONFIG is used by PHPStorm to store the
file mappings.

```yaml
---
version: '3'
services:
  app:
    environment:
      XDEBUG_MODE: "debug"
      XDEBUG_SESSION: "1"
      XDEBUG_CONFIG: "mode=debug client_host=172.25.149.239 client_port=9003 start_with_request=yes"
      PHP_IDE_CONFIG: "serverName=docker"
```

### Without docker

Install dependencies: 

```bash
cd app
composer install
```

If you want to use the database adapter you have to configure your database connection string in your
.env.local file and run the following commands:

```bash
cd app
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load
```

## Configuration

You can switch between the XML and database storage adapters by changing the class name of the app.dataStorageAdapter
service in the services.yaml. Other storage adapter classes can be created by implementing the 
`App\Data\StorageAdapterInterface` 

## Usage

### With docker

After project initialization you can start up the application with

```bash
docker-compose up
```

You can access the documentation page with your browser on `http://localhost:8000`

### Without docker

```bash
cd app
php -S 0.0.0.0:8000 -t public
```