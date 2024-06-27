# Static Analysis Report

## Analysis Tool
We used PHPStan for static analysis.

## PHPStan Configuration
```neon
includes:
    - vendor/phpstan/phpstan/conf/bleedingEdge.neon

parameters:
    level: max
    paths:
        - src
        - tests
    autoload_files:
        - %rootDir%/../../../config/bootstrap.php
    scanFiles:
        - %rootDir%/../../../config/bootstrap.php

##Call to an undefined method App\Controller\LoginController::getDoctrine().

## Fixes Made
## Fixed type hinting issues in src/Entity/User.php.
## Corrected undefined variable in src/Controller/ProductController.php.
## Added necessary null checks in src/Service/UserService.php.
