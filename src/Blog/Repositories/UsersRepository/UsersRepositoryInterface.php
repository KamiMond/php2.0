<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\UsersRepository;

use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function getByUUID(UUID $uuid): User;
    public function getByLogin(string $login): User;
}