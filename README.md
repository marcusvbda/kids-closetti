## Prerequisites

Before you begin, make sure you have the following prerequisites installed on your system:

- Docker
- Docker Compose

## Installation


Follow the steps below to set up the WordPress project using Docker:
1. copy .env.example as .env

2. Start the Docker containers:

   ```bash
   docker-compose up -d --force-recreate
   ```

   This command will start the WordPress and MySQL containers defined in the `docker-compose.yml` file.

3. Access phpmyadmin and create a database 

4. set this config in your wp-settings.php
```
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST'));
define('WP_HOME', getenv('WP_HOME'));
define('WP_SITEURL', getenv('WP_SITEURL'));
```