#!/usr/bin/env php
<?php

use Netdudes\Branchio\Configuration;
use Netdudes\Branchio\Git;
use Netdudes\Branchio\Sites;
use Netdudes\Branchio\Tool\Commands\BuildCommand;
use Netdudes\Branchio\Tool\Commands\RefreshCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $configuration = new Configuration(__DIR__ . '/config/config.yml');
} catch (\Exception $e) {
    echo "Configuration missing, non existent or invalid.";
    echo $e->getMessage();
    exit();
};

$git = new Git(
    $configuration->get('git-directory'),
    $configuration->get('git-remote'),
    $configuration->get('git-private-key')
);

$sites = new Sites($configuration->get('sites-directory'), $git);

$application = new Application();
$application->setName('Sites management tool');
$application->add(new RefreshCommand($sites));
$application->add(new BuildCommand($sites));
$application->run();
