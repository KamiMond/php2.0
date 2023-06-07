<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostsRepository;

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function getByUUID(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
}