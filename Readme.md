### Setting Up the Symfony Project with Docker

This guide provides detailed steps to set up a Symfony project with Docker, from cloning the repository to testing the application. This project uses MySQL as the database.

#### Prerequisites

- Docker and Docker Compose installed on your machine.
- Git installed on your machine.

#### Step-by-Step Instructions

1. **Clone the Repository**

   Clone the project repository to your local machine:
   ```sh
   git clone https://github.com/yourusername/RestAPI-Assessment.git
   cd RestAPI-Assessment
   ```

2. **Set Up Environment Variables**

   Create a `.env.local` file in the root directory to configure environment-specific variables:
   ```sh
   cp .env .env.local
   ```

   Edit the `.env.local` file to include your database configuration:
   ```sh
   DATABASE_URL="mysql://root:root@db:3306/assessment?serverVersion=5.7"
   ```

3. **Docker Configuration**

   use the `Dockerfile` in the project
   

   there is also `docker-compose.yml` file:
   

4. **Build and Run Docker Containers**

   Build and start the containers:
   ```sh
   docker-compose up --build -d
   ```

5. **Run Migrations**

   Enter the PHP container:
   ```sh
   docker-compose exec php bash
   ```

   Inside the container, run the following commands:
   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. **Generate JWT Keys**

   Generate the keys for JWT authentication:
   ```sh
   mkdir -p config/jwt
   openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
   openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
   ```

   Update your `.env.local` file to include the paths to the JWT keys:
   ```sh
   JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
   JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
   JWT_PASSPHRASE=your_passphrase
   ```

7. **Static Code Analysis**

   Install PHPStan and PHP_CodeSniffer:
   ```sh
   composer require --dev phpstan/phpstan phpstan/extension-installer
   composer require --dev squizlabs/php_codesniffer
   ```

   Create the `phpstan.neon` configuration file:
   ```neon
   includes:
       - vendor/phpstan/phpstan/conf/bleedingEdge.neon

   parameters:
       level: max
       paths:
           - src
           - tests
   ```

   Create the `phpcs.xml` configuration file:
   ```xml
   <?xml version="1.0"?>
   <ruleset name="Project Rules">
       <description>PHP CodeSniffer ruleset for the project</description>
       <rule ref="PSR12"/>
   </ruleset>
   ```

   Run static analysis:
   ```sh
   vendor/bin/phpstan analyse
   vendor/bin/phpcs
   ```

8. **Running Tests**

   To run the PHPUnit tests, execute the following command inside the PHP container:
   ```sh
   php bin/phpunit
   ```

9. **Accessing the Application**

   Open your web browser and go to `http://127.0.0.1:8000` to access the Symfony application.

### Assumptions

- The project repository is available on GitHub.
- Docker and Docker Compose are correctly installed on your machine.
- The MySQL version used is 5.7.
- The project uses JWT for authentication.
- Static code analysis tools PHPStan and PHP_CodeSniffer are used to ensure code quality.

### Summary

This guide provides a comprehensive setup process for a Symfony application using Docker. It includes the steps to clone the repository, configure the environment, build and run Docker containers, perform database migrations, set up JWT authentication, run static code analysis, and execute tests.