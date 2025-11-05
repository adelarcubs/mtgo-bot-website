<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Laminas\ServiceManager\ServiceManager;

// Load application configuration
$config = require __DIR__ . '/config/container.php';

// Get the container
if (is_callable($config)) {
    $container = $config();
} else if ($config instanceof ServiceManager) {
    $container = $config;
} else {
    throw new RuntimeException('Could not initialize container');
}

// Return the entity manager helper set
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(
    $container->get(EntityManagerInterface::class)
);
