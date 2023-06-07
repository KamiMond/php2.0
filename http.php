<?php

use GeekBrains\LevelTwo\Http\Actions\Auth\LogIn;
use GeekBrains\LevelTwo\Http\Actions\Auth\LogOut;
use GeekBrains\LevelTwo\Http\Actions\Comments\CreateComment;
use GeekBrains\LevelTwo\Http\Actions\Comments\DeleteComment;
use GeekBrains\LevelTwo\Http\Actions\Likes\CreateLikePost;
use GeekBrains\LevelTwo\Http\Actions\Likes\FindByPostLikes;
use GeekBrains\LevelTwo\Http\Actions\Posts\CreatePost;
use GeekBrains\LevelTwo\Http\Actions\Posts\DeletePost;
use GeekBrains\LevelTwo\Http\Actions\Users\CreateUser;
use GeekBrains\LevelTwo\Http\Actions\Users\FindByLogin;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\Request;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => FindByLogin::class,
        '/post/getLikes' => FindByPostLikes::class,
//        '/comment/getLikes' => FindByCommentLikes::class
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/logout' => LogOut::class,
        '/users/create' => CreateUser::class,
        '/post/create' => CreatePost::class,
        '/post/comment' => CreateComment::class,
        '/post/like' => CreateLikePost::class,
//        '/comment/like' => CreateLikeComment::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
    ],
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}


// Выбираем действие по методу и пути
$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);

} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}
$response->send();