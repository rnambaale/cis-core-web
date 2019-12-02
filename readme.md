[![Build Status](https://travis-ci.org/rnambaale/cis-core-web.svg?branch=master)](https://travis-ci.org/rnambaale/cis-core-web)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rnambaale/cis-core-web/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rnambaale/cis-core-web/?branch=master)
[![StyleCI](https://github.styleci.io/repos/204977143/shield?branch=master)](https://github.styleci.io/repos/204977143)
[![Documentation](https://img.shields.io/badge/API-Documentation-Blue)](https://rnambaale.github.io/cis-core-web)

## Setup

### [Generate SSH Keys](https://git-scm.com/book/en/v2/Git-on-the-Server-Generating-Your-SSH-Public-Key)

`ssh-keygen -t rsa -C "your.email@example.com" -b 4096`

Upload public key; *id_rsa.pub*, to Github for authentication

### [Clone Repo](https://git-scm.com/docs/git-clone)

`# cd /var/www/html/`

`html# git@github.com:rnambaale/cis-core-web.git`

### [Installing Dependencies](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies)

`html# cd cis-core-web`

`cis-core-web# composer install -v`

### Environment Variables

`cis-core-web# cp .env.example .env`

### Application key

`cis-core-web# php artisan key:generate`

### [Running Migrate](https://laravel.com/docs/master/migrations#running-migrations)

**Note**: Take precaution as the following command might `delete` existing database tables.

`php artisan migrate`

### [Directory Permissions](https://laravel.com/docs/master/installation#configuration)

`cis-core-web# chmod 777 -R bootstrap/cache`

`cis-core-web# chmod 777 -R storage`

### [The Public Disk](https://laravel.com/docs/master/filesystem#the-public-disk)

`cis-core-web# php artisan storage:link`

### [Other prerequisites]

Generate JS routes.

`php artisan routes:json`

### Install & compile NodeJS dependencies

`cis-core-web# npm install && npm run dev`

### Local Development Server

`cis-core-web# php artisan serve`

Visit: [`http://127.0.0.1:8000`](http://127.0.0.1:8000)
