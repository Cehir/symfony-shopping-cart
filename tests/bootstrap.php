<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// executes the "php bin/console cache:clear" command
passthru(sprintf('APP_ENV=%s php "%s/../bin/console" cache:clear --no-warmup',$_ENV['APP_ENV'], __DIR__));
// create database if not exists
passthru(sprintf('APP_ENV=%s php "%s/../bin/console" --no-debug doctrine:database:create --if-not-exists',$_ENV['APP_ENV'], __DIR__));
// update database schema
passthru(sprintf('APP_ENV=%s php "%s/../bin/console" --no-debug doctrine:schema:update --force',$_ENV['APP_ENV'], __DIR__));

