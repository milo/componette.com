# Componette

Modern useful #nette addons portal.

[![Gitter](https://img.shields.io/gitter/room/componette/componette.svg)](https://gitter.im/componette)

## Requirements

### Application

* PHP >= 5.6.0
* Nette packages

### Server

* PHP + Composer
* Nginx
* MariaDB/MySQL
* [**Docker**](https://github.com/componette/componette.com/tree/docker)

## Docker

This portal run in Docker container(s). You can see configurations in **docker** branch.

## How to run

- Clone this repo (`git@github.com:componette/componette.com.git`)
- Rename `app/config/config.local.sample` to `config.local.neon` and fill parameters
- Create database and setup tables (and fixtures)
- Run `composer install`
