<?php

namespace GeekBrains\LevelTwo\Http\Actions\Comments;

use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNoPermissionException;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use Psr\Log\LoggerInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;

class DeleteComment implements ActionInterface
{
    public function __construct(
        public CommentsRepositoryInterface $commentsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    ){}

    /**
     * @throws InvalidArgumentException|UserNoPermissionException
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $commentUuid = $request->query("uuid");
            $comment = $this->commentsRepository->getByUUID(new UUID($commentUuid));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        if((string)$comment->getAuthor()->uuid() !== (string)$user->uuid()) {
            throw new UserNoPermissionException(
                "User: " . $user->getLogin() . " does not have permission comment user:" . $comment->getAuthor()->getLogin());
        }
        $this->commentsRepository->delete(new UUID($commentUuid));
        $this->logger->info('Comment deleted: ' . $commentUuid);
        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}