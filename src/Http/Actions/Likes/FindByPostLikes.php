<?php

namespace GeekBrains\LevelTwo\Http\Actions\Likes;

use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\LikesRepository\LikesPostRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;

class FindByPostLikes implements ActionInterface
{
    public function __construct(
        public LikesPostRepositoryInterface $likesRepository,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query("postUuid");
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $postLikes = $this->likesRepository->getByPostUUID(new UUID($postUuid));
        } catch (LikeNotFoundException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'postUuid' => count($postLikes)
        ]);
    }
}