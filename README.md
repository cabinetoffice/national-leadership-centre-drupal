# National Leadership Centre Alpha Prototype: Based on Drupal

This project is an Alpha phase prototype for the National Leadership Centre (NLC).

## Prototype Objectives

The prototype is primarily focused on the following:

1. **The data structure needed to support a user directory**
  - What person data does the NLC's digital service need?
  - … in particular to support the user directory?
2. **The user interface(s) needed to support a user directory**
  - Given the data and data structure, how does one develop the interface(s) that support users finding people in a directory? 
  - Related to defined user needs and user research, as appropriate 
3. **Ease of set-up, build, etc.**
  - Both the Alpha and Beta phases are short, so any build work needs to be very efficient
  - How easy is it to get the thing in place, develop on it, etc.?
4. **Privacy, security and access controls**
  - How much control does this platform give over privacy and data security?
  - The digital service will be holding personal data, some of which may be sensitive. Does the platform support privacy and access controls over data and publishing?

## Prototype Outcomes

1. Description or outline of the data structure
2. Models of user interface(s), with testing outcomes and feedback
3. Assessment of ease and speed of build — backend; frontend
4. Assessment of privacy, access and data security

# Usage

## Repository Architecture

The repository architecture is based on [Composer template for Drupal projects](https://github.com/drupal-composer/drupal-project). View the README for introductory instructions on usage.

It is presumed that you will use Docker to run this project. This project uses Composer for package management. 

#### Assumptions:

- Docker is installed locally already.
  - Check out the [Docker documentation for an installation guide](https://docs.docker.com/install/), if necessary. 
- You have Composer installed.
  - Check out the [Composer documentation for an installation guide](https://getcomposer.org/download/), if necessary.

### Getting started

#### Installation and configuration

1. **Clone this repository.**
2. **Install packages with Composer.**
  In the root directory of your cloned repo:
  ```
  $ composer install
  ```
3. **Add a `settings.local.php` file.**
  … in the `./web/sites/default/` directory. You may like to copy and adapt the `./web/sites/example.settings.local.php` file as a starting point.
4. **Edit .env settings to suit your needs.*- 
  This is the environment for your local Docker. Most settings should be obvious and/or self-evident. The default values are probably fine.
5. **If you want to start with a default pre-populated database:**
  See 'Import an initial MySQL DB' below.
6. **Start it up!**

The `Makefile` and `docker.mk` included in this project provide some easy CLI tools to work with this Docker stack for Drupal.

**To start your Docker stack:**

```
$ make up
```

**To stop your Docker stack:**

```
$ make down
```

#### Make commands:

Usage:
```
$ make {command}
```
```
Commands:
    up              Start up all container from the current docker-compose.yml 
    stop            Stop all containers for the current docker-compose.yml (docker-compose stop) 
    down            Same as stop
    prune           Stop and remove containers, networks, images, and volumes (docker-compose down)
    ps              List container for the current project (docker ps with filter by name)
    shell           Enter PHP container as default user (docker exec -ti $CID sh)
    drush [command] Execute drush command (runs with -r /var/www/html/web, you can override it via DRUPAL_ROOT=PATH)
    logs [service]  Show containers logs, use [service] to show logs of specific service
```

##### Import an initial MySQL DB into MariaDB:

If you want to import your database, in the volume directory `./mariadb-init` in the root of this repository, put your `.sql` `.sql.gz` `.sh` file(s). All SQL files will be automatically imported once MariaDB container has started. Databases will be created with the names of the `.sql` `.sql.gz` `.sh` file(s).

The default setting in `.env` is connection to a database named `drupal` so the initial import file should be named e.g. `drupal.sql` (see line 22ff).

For other import/export options, see: https://wodby.com/stacks/drupal/docs/local/import-export/ 
