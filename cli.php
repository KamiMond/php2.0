<?php

use GeekBrains\LevelTwo\Blog\Commands\Arguments;
use GeekBrains\LevelTwo\Blog\Commands\CreateUserCommand;
use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Person\Name;
use GeekBrains\LevelTwo\Blog\UUID;

include __DIR__ . "/vendor/autoload.php";

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);
$commentsRepository = new SqliteCommentsRepository($connection);

$faker = Faker\Factory::create('ru_RU');

    $name = new Name(
        $faker->firstName(),
        $faker->lastName()
    );
try {
    $user = new User(
        UUID::random(),
        $name,
        $faker->word()
    );

    $post = new Post(
        UUID::random(),
        $user,
        $faker->title(),
        $faker->paragraph()
    );

    $comment = new Comment(
        UUID::random(),
        $post,
        $user,
        $faker->sentence()
    );

} catch (\GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException $e) {
}

echo $user;
echo $post;
echo $comment;

$usersRepository->save($user);
$postsRepository->save($post);
$commentsRepository->save($comment);

$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    echo $e->getMessage();
}





