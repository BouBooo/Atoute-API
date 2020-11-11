<?php

/**
 * Required by phpstan doctrine extension.
 */

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotEnv = new Dotenv();
$dotEnv->loadEnv('.env', 'APP_ENV');

$kernel = new Kernel('dev', true);
$kernel->boot();
return $kernel->getContainer()->get('doctrine')->getManager();