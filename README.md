# National Leadership Centre Alpha Prototype: Based on Drupal

This project is an Alpha phase prototype for the National Leadership Centre (NLC). This prototype is based on [Drupal](https://www.drupal.org/) 8.

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
  ```bash
  $ composer install
  ```
  or
  ```bash
  docker run --rm --interactive --tty \
    -v $PWD:/app \
    -u $(id -u ${USER}):$(id -g ${USER}) \
    composer install --ignore-platform-reqs
  ```
3. **Install the packages required by the GOV.UK Design System base theme.**
  ```bash
  $ cd ./web/themes/contrib/govuk_design_system
  $ npm install
  ```
4. **Add a `settings.local.php` file.**
  … in the `./web/sites/default/` directory. You may like to copy and adapt the `./web/sites/example.settings.local.php` file as a starting point.
5. **Copy-and-paste `.env.example` to `.env` and edit settings to suit your needs.**
  This is the environment for your local Docker. Most settings should be obvious and/or self-evident. The default values are probably fine.
6. **If you want to start with a default pre-populated database:**
  See 'Import an initial MySQL DB' below.
7. **Start it up!**

The `Makefile` and `docker.mk` included in this project provide some easy CLI tools to work with this Docker stack for Drupal.

**To start your Docker stack, from the root directory of your cloned repo:**

```bash
$ make up
```

**To stop your Docker stack:**

```bash
$ make down
```

8. **Install the site**
  Your site should be installed at the `PROJECT_BASE_URL` location, [http://nlc-drupal.docker.localhost](http://nlc-drupal.docker.localhost) by default. Follow the Drupal installation instructions.
  - You should be offered to install from the site configuration, with the 'Use existing configuration' option.
  - To set up the database connection, use the credentials from your `.env` file. The database is NOT at `localhost` — it is at the name of the MariaDB Docker container, `mariadb` in the standard configuration of the `docker-compose.yml` file. 
  - See also the [Configuration Management](#configuration-management) section below for installing config for a local development environment. Although the site configuration is imported by the installer, the taxonomy terms will need to be imported manually.

#### Make commands:

Usage:
```bash
$ make {command}
```
```bash
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

# Working with Drupal

This is a Drupal 8 installation. Here's some guidance on specific aspects of working with Drupal.

## Configuration Management

Configuration in this system is managed using the [Configuration Split](https://www.drupal.org/project/config_split) contrib moduile (which dependes on [Config Filter](https://www.drupal.org/project/config_filter) module). This allows configuration that should be used in specific environments only to be separated out from others.

This installation has two environments:

- general configruation, in `./config/sync`
- local development configuration, in `./config/devel`, with a machine name of `devel`

To enable a particular config with Configuration Split module, add the following to your `settings.php` (or `settings.local.php`) file:

```php
$config['config_split.config_split.MACHINE_NAME']['status'] = TRUE;
```

So, to enable the `devel` config:

```php
$config['config_split.config_split.devel']['status'] = TRUE;
```

### To import configuration:

```bash
$ make drush cim

```

#### To import exported taxonomy terms:

```bash
$ make shell
$ cd ./web
$ drush import-taxonomies --choice=safe

```

(because `make` doesn't pass options to the command)

### Data migration

Ensure nlc_import is installed:

```drush en nlc_import```

View migration status:

```drush ms```

Run a migration:

```drush migrate:import migrate_organisations```

Rollback a migration:

```drush mr migrate_organisations```

##  Gov.uk PaaS

The alpha site can be found on Gov.uk PaaS.

### Setup

Please refer to the official [getting started](https://docs.cloud.service.gov.uk/get_started.html#get-started) documentation.

**Key steps you must have completed:**

1) [Get a GOV.UK PaaS account](https://docs.cloud.service.gov.uk/get_started.html#get-an-account)
2) [Install the Cloud Foundry command line toolkit](https://docs.cloud.service.gov.uk/get_started.html#set-up-the-cloud-foundry-command-line)
3) [Sign in to GOV.UK Cloud Foundry](https://docs.cloud.service.gov.uk/get_started.html#sign-in-to-cloud-foundry)

You must complete these steps before you can move onto deployment, below.

### Deploy

Make sure you're on the correct branch you want to deploy and all the composer dependencies are installed.

To deploy the app you need to run:

````cf push````

### Drush

SSH into the current app:

```cf ssh "nlc-alpha-drupal" -t -c "/tmp/lifecycle/launcher /home/vcap/app bash ''"```

Then you can run drush as normal.