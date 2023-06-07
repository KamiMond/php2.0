<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\LikesRepository;

use GeekBrains\LevelTwo\Blog\LikePost;
use GeekBrains\LevelTwo\Blog\UUID;

interface LikesPostRepositoryInterface
{
    public function save(LikePost $like): void;
    public function getByPostUuid(UUID $uuid): array;
}