## Setup

You may use the following one liner for quick installation
```
git clone git@github.com:nivincp/rest-docker.git && cd rest-docker && ./setup.sh
```

You should have composer, docker and docker-compose in your environment path variables for the one liner script to work and the ports for these services(docker-compose.yml) needs to be available.

It's tested on an Ubuntu 16.04 machine but if you're on a different OS and it's not working please proceed with the following steps.

1. Packages are managed via composer. Start by running a `composer install` in *web* directory.
2. Run `docker-compose up -d` in *root* directory to start the development environment.
3. Run migrations in *web* directory using ``composer run-migrations``
4. App constants are configured in src/config/app.php (No need to change it unless you may want any specific setup).
5. Data is persisted in to Postgres container.
6. Access http://localhost/. You should see ``{"status":"running"}``.

## Tests

```
// to run integration tests
cd web && composer test
```

## Migration commands

```
// to run existing migrations
composer run-migrations

// to create a new migration
composer create-migration MigrationName
```

## Endpoints
| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |
| List   | `GET`       | `/recipes`             | ✘         |
| Search | `GET`       | `/recipes?query`       | ✘         |
| Create | `POST`      | `/recipes`             | ✓         |
| Get    | `GET`       | `/recipes/{id}`        | ✘         |
| Update | `PUT/PATCH` | `/recipes/{id}`        | ✓         |
| Delete | `DELETE`    | `/recipes/{id}`        | ✓         |
| Rate   | `POST`      | `/recipes/{id}/rating` | ✘         |
| Signup | `POST`      | `/users`               | ✘         |
| Login  | `POST`      | `/users/login`         | ✘         |

Accepted query params for search are vegetarian, difficulty, prepTime, name. For example  
``GET /recipes?vegetarian=1&difficulty=1&prepTime=2+hrs&name=llo``

Both listing and search feature of recipes can be paginated via the query param page. For example  
``GET /recipes?page=1``
``GET /recipes?vegetarian=1&difficulty=1&prepTime=2+hrs&name=llo&page=1``

For protected endpoints it's required to pass ``Authentication`` header which can be obtained via user login accessToken response object. The endpoint to create a new user POST ``/users`` and to login it's POST ``/users/login``

For all endpoints the content type header should be ``Content-Type: application/json ``

**Request payload example for user signup**
```
{
    "firstName": "firstName",
    "lastName": "lastName",
    "email": "hello@example.com",
    "password": "pass123"
}
```

**Request payload example for user login**
```
{
    "email": "hello@example.com",
    "password": "pass123"
}
```

**Request payload example to create a new recipe**
```
{
    "name": "hello fresh",
    "prepTime": "2 hrs",
    "difficulty": 1,
    "vegetarian": true
}
```

**Request payload example to rate a recipe**
```
{
    "rating": 1
}
```

Additionaly you may download the endpoints collection to use in Postman using the link https://www.getpostman.com/collections/d27f4ef021b98717a55b 

## Packages used

1. **illuminate/routing**  
Routing is quite flexible and HTTP verbs can be configured easily(src/config/routes.php). Supports middlewares.

2. **illuminate/database**  
Efficient database abstraction layer. Supports Eloquent ORM, relationships management.

3. **illuminate/validation**  
Request validations library, built in support for many validation rules.

4. **firebase/php-jwt**  
Authentication and authoroization is handled via json web tokens as it's stateless and scalable.

5. **robmorgan/phinx**  
Supports database version handling, migrations and make it clear at all times what state a database is in.

6. **phpunit/phpunit**  
Automated executable testing library to ensure that changes don't break existing functionality.

7. **guzzlehttp/guzzle**  
An abstraction layer for http request.  It's a simpler, cleaner and reusable http requests handling client.
# abc
# abc2
# abc2
