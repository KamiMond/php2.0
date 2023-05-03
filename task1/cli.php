<?php

use Geekbrains\Leveltwo\Blog\{User, Post, Comment};
use Geekbrains\Leveltwo\Person\{Name, Person};
use Geekbrains\Leveltwo\Blog\Repositories\InMemoryUsersRepository;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;

include __DIR__ . '/vendor/autoload.php';

/* Задание 1
spl_autoload_register('load');
function load($className)
{
$file = $className . ".php";
$file = str_replace("\\", "/", $file);
$file = str_replace("Geekbrains/Leveltwo", "src", $file);
if (file_exists($file)) {
include $file;
}
}
*/


$faker = Faker\Factory::create('ru_RU');

//echo $faker->name() . PHP_EOL;
//echo $faker->realText(rand(50, 100)) . PHP_EOL;

$name = new Name(
    $faker->firstName(),
    $faker->lastName()
);
$user = new User(
    $faker->randomNumber(1, false),
    $name,
    $faker->word()
);

$name = new Name(
    $faker->firstName(),
    $faker->lastName()
);
$user = new User(
    $faker->randomNumber(3, false),
    $name,
    $faker->word()
);

switch ($argv[1]) {
    case 'user':
        echo $user;
        break;
    case 'post':
        $post = new Post(
            $faker->randomNumber(2, false),
            $user,
            $faker->paragraph()
        );
        echo $post;
        break;
    case 'comment':
        $post = new Post(
            $faker->randomNumber(1, false),
            $user,
            $faker->word()
        );
        $comment = new Comment(
            $faker->randomNumber(1, false),
            $user,
            $post,
            $faker->sentence(2)
        );
        echo $comment;
        break;
}