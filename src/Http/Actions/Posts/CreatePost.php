<?php

namespace GeekBrains\LevelTwo\Http\Actions\Posts;

use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use GeekBrains\LevelTwo\http\ErrorResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        public PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $newPostUuid = UUID::random();
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField("title"),
                $request->jsonBodyField("text")
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);
        $this->logger->info("Post created: $newPostUuid");
        return new SuccessfulResponse([
            "uuid" => (string)$newPostUuid,
        ]);
    }
}