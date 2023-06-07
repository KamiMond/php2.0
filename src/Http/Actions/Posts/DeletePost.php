<?php

namespace GeekBrains\LevelTwo\Http\Actions\Posts;

use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use Psr\Log\LoggerInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        public PostsRepositoryInterface      $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface              $logger,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $postUuid = $request->query("uuid");
            $this->postsRepository->getByUUID(new UUID($postUuid));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->delete(new UUID($postUuid));
        $this->logger->info('Post deleted: ' . $postUuid);

        return new SuccessfulResponse([
            'uuid' => (string)$postUuid
        ]);
    }
}