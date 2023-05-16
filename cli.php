<?php

use GeekBrains\LevelTwo\Blog\Commands\Arguments;
use GeekBrains\LevelTwo\Blog\Commands\CreateUserCommand;

$container = require __DIR__ . '/bootstrap.php';

try {

    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));

} catch (Exception $e) {
    echo $e->getMessage();
}
